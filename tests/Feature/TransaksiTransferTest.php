<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\RekeningBank;
use App\Models\Transaksi;
use App\Models\User;
use App\Notifications\TransactionCompleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TransaksiTransferTest extends TestCase
{
    use RefreshDatabase;

    private function buatAdmin(): User
    {
        return User::create([
            'username' => 'admin_test',
            'nama_lengkap' => 'Admin Pengujian',
            'password' => 'password123',
            'role' => 'admin',
        ]);
    }

    private function buatKasir(string $username = 'kasir_test'): User
    {
        return User::create([
            'username' => $username,
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

    private function buatRekening(bool $aktif = true): RekeningBank
    {
        return RekeningBank::create([
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234567890',
            'atas_nama' => 'PT Ayam Jagal',
            'status_aktif' => $aktif,
        ]);
    }

    private function payloadTransfer(Menu $menu, ?int $rekeningId): array
    {
        return [
            'metode_pembayaran' => 'transfer',
            'uang_dibayarkan' => null,
            'rekening_bank_id' => $rekeningId,
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 1,
                ],
            ],
        ];
    }

    public function test_transfer_membuat_transaksi_pending(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $response = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $response->assertCreated();

        $this->assertDatabaseHas('transaksis', [
            'metode_pembayaran' => 'transfer',
            'status' => 'menunggu_konfirmasi',
            'status_pembayaran' => 'menunggu_konfirmasi',
            'rekening_bank_id' => $rekening->id,
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234567890',
            'atas_nama' => 'PT Ayam Jagal',
        ]);
    }

    public function test_transfer_membutuhkan_rekening(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();

        $response = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, null)
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('rekening_bank_id');
    }

    public function test_rekening_nonaktif_ditolak(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening(false);

        $response = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('rekening_bank_id');
    }

    public function test_snapshot_rekening_tersimpan(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $response = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $response->assertCreated();

        $rekening->update([
            'nama_bank' => 'BCA BERUBAH',
            'nomor_rekening' => '9999',
            'atas_nama' => 'NAMA BARU',
        ]);

        $this->assertDatabaseHas('transaksis', [
            'rekening_bank_id' => $rekening->id,
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234567890',
            'atas_nama' => 'PT Ayam Jagal',
        ]);
    }

    public function test_kasir_dapat_mengonfirmasi_transaksi_miliknya(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $create = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $transaksiId = $create->json('data.id');

        $response = $this->actingAs($kasir)->postJson(
            "/transaksi/{$transaksiId}/konfirmasi"
        );

        $response->assertOk();

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksiId,
            'status' => 'selesai',
            'status_pembayaran' => 'terkonfirmasi',
            'dikonfirmasi_oleh' => $kasir->id,
        ]);
    }

    public function test_kasir_tidak_dapat_mengonfirmasi_transaksi_kasir_lain(): void
    {
        $kasir1 = $this->buatKasir('kasir_satu');
        $kasir2 = $this->buatKasir('kasir_dua');
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $create = $this->actingAs($kasir1)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $transaksiId = $create->json('data.id');

        $response = $this->actingAs($kasir2)->postJson(
            "/transaksi/{$transaksiId}/konfirmasi"
        );

        $response->assertForbidden();
    }

    public function test_admin_dapat_mengonfirmasi_transaksi_kasir(): void
    {
        $admin = $this->buatAdmin();
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $create = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $transaksiId = $create->json('data.id');

        $response = $this->actingAs($admin)->postJson(
            "/transaksi/{$transaksiId}/konfirmasi"
        );

        $response->assertOk();

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksiId,
            'status' => 'selesai',
            'dikonfirmasi_oleh' => $admin->id,
        ]);
    }

    public function test_konfirmasi_kedua_ditolak(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $create = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $transaksiId = $create->json('data.id');

        $this->actingAs($kasir)->postJson(
            "/transaksi/{$transaksiId}/konfirmasi"
        )->assertOk();

        $response = $this->actingAs($kasir)->postJson(
            "/transaksi/{$transaksiId}/konfirmasi"
        );

        $response->assertUnprocessable();
    }

    public function test_kasir_dapat_membatalkan_transaksi_miliknya(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $create = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $transaksiId = $create->json('data.id');

        $response = $this->actingAs($kasir)->postJson(
            "/transaksi/{$transaksiId}/batalkan"
        );

        $response->assertOk();

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksiId,
            'status' => 'dibatalkan',
            'status_pembayaran' => 'dibatalkan',
        ]);
    }

    public function test_kasir_tidak_dapat_membatalkan_transaksi_kasir_lain(): void
    {
        $kasir1 = $this->buatKasir('kasir_satu');
        $kasir2 = $this->buatKasir('kasir_dua');
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $create = $this->actingAs($kasir1)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $transaksiId = $create->json('data.id');

        $response = $this->actingAs($kasir2)->postJson(
            "/transaksi/{$transaksiId}/batalkan"
        );

        $response->assertForbidden();
    }

    public function test_admin_dapat_membatalkan_transaksi_kasir(): void
    {
        $admin = $this->buatAdmin();
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $create = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $transaksiId = $create->json('data.id');

        $response = $this->actingAs($admin)->postJson(
            "/transaksi/{$transaksiId}/batalkan"
        );

        $response->assertOk();
    }

    public function test_pembatalan_kedua_ditolak(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $create = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $transaksiId = $create->json('data.id');

        $this->actingAs($kasir)->postJson(
            "/transaksi/{$transaksiId}/batalkan"
        )->assertOk();

        $response = $this->actingAs($kasir)->postJson(
            "/transaksi/{$transaksiId}/batalkan"
        );

        $response->assertUnprocessable();
    }

    public function test_transaksi_selesai_tidak_dapat_dibatalkan(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $create = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $transaksiId = $create->json('data.id');

        $this->actingAs($kasir)->postJson(
            "/transaksi/{$transaksiId}/konfirmasi"
        )->assertOk();

        $response = $this->actingAs($kasir)->postJson(
            "/transaksi/{$transaksiId}/batalkan"
        );

        $response->assertUnprocessable();
    }

    public function test_notifikasi_tidak_dikirim_sebelum_konfirmasi(): void
    {
        Notification::fake();

        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        )->assertCreated();

        Notification::assertNothingSent();
    }

    public function test_notifikasi_dikirim_setelah_konfirmasi(): void
    {
        Notification::fake();

        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $create = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $transaksiId = $create->json('data.id');

        $this->actingAs($kasir)->postJson(
            "/transaksi/{$transaksiId}/konfirmasi"
        )->assertOk();

        Notification::assertSentTo($kasir, TransactionCompleted::class);
    }

    public function test_pending_tidak_masuk_laporan_penjualan(): void
    {
        $admin = $this->buatAdmin();
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        )->assertCreated();

        $response = $this->actingAs($admin)->get('/sales');

        $response->assertInertia(fn ($page) =>
            $page->component('Admin/Reports/Index')
                ->has('transactions', 0)
        );
    }

    public function test_dibatalkan_tidak_masuk_laporan_penjualan(): void
    {
        $admin = $this->buatAdmin();
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();
        $rekening = $this->buatRekening();

        $create = $this->actingAs($kasir)->postJson(
            '/transaksi',
            $this->payloadTransfer($menu, $rekening->id)
        );

        $transaksiId = $create->json('data.id');

        $this->actingAs($kasir)->postJson(
            "/transaksi/{$transaksiId}/batalkan"
        )->assertOk();

        $response = $this->actingAs($admin)->get('/sales');

        $response->assertInertia(fn ($page) =>
            $page->component('Admin/Reports/Index')
                ->has('transactions', 0)
        );
    }

    public function test_tunai_dan_qris_tetap_berhasil(): void
    {
        $kasir = $this->buatKasir();
        $menu = $this->buatMenu();

        $this->actingAs($kasir)->postJson('/transaksi', [
            'metode_pembayaran' => 'tunai',
            'uang_dibayarkan' => 15000,
            'items' => [['menu_id' => $menu->id, 'jumlah' => 1]],
        ])->assertCreated();

        $this->actingAs($kasir)->postJson('/transaksi', [
            'metode_pembayaran' => 'qris',
            'uang_dibayarkan' => null,
            'items' => [['menu_id' => $menu->id, 'jumlah' => 1]],
        ])->assertCreated();

        $this->assertDatabaseCount('transaksis', 2);
    }
}