<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiPembayaranTest extends TestCase
{
    use RefreshDatabase;

    private function buatKasir(): User
    {
        return User::create([
            'username' => 'kasir_test',
            'nama_lengkap' => 'Kasir Pengujian',
            'password' => 'password123',
            'role' => 'kasir',
        ]);
    }

    private function buatMenu(): Menu
    {
        return Menu::create([
            'nama_menu' => 'Ayam Geprek Test',
            'kategori' => 'Makanan',
            'harga' => 15000,
            'status_menu' => true,
        ]);
    }

    private function payloadTransaksi(
        Menu $menu,
        string $metode = 'tunai',
        ?int $uang = 15000
    ): array {
        return [
            'metode_pembayaran' => $metode,
            'uang_dibayarkan' => $uang,
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 1,
                ],
            ],
        ];
    }

    public function test_pembayaran_tunai_dengan_uang_pas(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();

        $response = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransaksi($menu, 'tunai', 15000)
        );

        $response->assertCreated();

        $this->assertDatabaseHas('transaksis', [
            'user_id' => $kasir->id,
            'total_harga' => 15000,
            'metode_pembayaran' => 'tunai',
            'uang_dibayarkan' => 15000,
            'kembalian' => 0,
            'status' => 'selesai',
        ]);
    }

    public function test_pembayaran_tunai_dengan_kembalian(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();

        $response = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransaksi($menu, 'tunai', 20000)
        );

        $response->assertCreated();

        $this->assertDatabaseHas('transaksis', [
            'total_harga' => 15000,
            'uang_dibayarkan' => 20000,
            'kembalian' => 5000,
        ]);
    }

    public function test_pembayaran_tunai_dengan_uang_kurang_ditolak(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();

        $response = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransaksi($menu, 'tunai', 10000)
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('uang_dibayarkan');

        $this->assertDatabaseCount('transaksis', 0);
        $this->assertDatabaseCount('detail_transaksis', 0);
    }

    public function test_pembayaran_qris_berhasil(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();

        $response = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransaksi($menu, 'qris', null)
        );

        $response->assertCreated();

        $this->assertDatabaseHas('transaksis', [
            'total_harga' => 15000,
            'metode_pembayaran' => 'qris',
            'uang_dibayarkan' => 15000,
            'kembalian' => 0,
            'status' => 'selesai',
        ]);
    }

    public function test_pembayaran_transfer_berhasil(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();

        $rekening = \App\Models\RekeningBank::create([
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234567890',
            'atas_nama' => 'PT Ayam Jagal',
            'status_aktif' => true,
        ]);

        $payload = $this->payloadTransaksi($menu, 'transfer', null);
        $payload['rekening_bank_id'] = $rekening->id;

        $response = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $payload
        );

        $response->assertCreated();

        $this->assertDatabaseHas('transaksis', [
            'total_harga' => 15000,
            'metode_pembayaran' => 'transfer',
            'uang_dibayarkan' => 15000,
            'kembalian' => 0,
            'status' => 'menunggu_konfirmasi',
            'status_pembayaran' => 'menunggu_konfirmasi',
        ]);
    }

    public function test_metode_pembayaran_tidak_valid_ditolak(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();

        $response = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransaksi($menu, 'paylater', 15000)
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('metode_pembayaran');

        $this->assertDatabaseCount('transaksis', 0);
        $this->assertDatabaseCount('detail_transaksis', 0);
    }

    public function test_menu_duplikat_dalam_satu_transaksi_ditolak(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();

        $response = $this->actingAs($kasir)->postJson('/transaksi', [
            'metode_pembayaran' => 'tunai',
            'uang_dibayarkan' => 30000,
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 1,
                ],
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 1,
                ],
            ],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('items.1.menu_id');

        $this->assertDatabaseCount('transaksis', 0);
        $this->assertDatabaseCount('detail_transaksis', 0);
    }

    
    public function test_transaksi_di_rollback_jika_penyimpanan_detail_gagal(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();

        // Membuat trigger SQLite untuk memaksa penyimpanan detail gagal.
        DB::statement("
            CREATE TRIGGER gagal_simpan_detail
            BEFORE INSERT ON detail_transaksis
            BEGIN
                SELECT RAISE(ABORT, 'Simulasi kegagalan penyimpanan detail');
            END;
        ");

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($kasir)->postJson(
                '/transaksi',
                $this->payloadTransaksi($menu, 'tunai', 15000)
            );

            $this->fail('Seharusnya penyimpanan detail menghasilkan QueryException.');
        } catch (QueryException $exception) {
            // Kegagalan database memang diharapkan dalam test ini.
            $this->assertStringContainsString(
                'Simulasi kegagalan penyimpanan detail',
                $exception->getMessage()
            );
        }

        // Header dan detail harus sama-sama tidak tersimpan.
        $this->assertDatabaseCount('transaksis', 0);
        $this->assertDatabaseCount('detail_transaksis', 0);
    }
}