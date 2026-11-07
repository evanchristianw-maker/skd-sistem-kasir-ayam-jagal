<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiMenuNonaktifTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_nonaktif_ditolak_dalam_transaksi(): void
    {
        $kasir = User::create([
            'username' => 'kasir_test',
            'nama_lengkap' => 'Kasir Pengujian',
            'password' => 'password123',
            'role' => 'kasir',
        ]);

        $menu = Menu::create([
            'nama_menu' => 'Ayam Bakar Test',
            'kategori' => 'Makanan',
            'harga' => 20000,
            'status_menu' => false,
        ]);

        $response = $this->actingAs($kasir)->postJson('/transaksi', [
            'metode_pembayaran' => 'tunai',
            'uang_dibayarkan' => 20000,
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 1,
                ],
            ],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('items');

        $this->assertDatabaseCount('transaksis', 0);
        $this->assertDatabaseCount('detail_transaksis', 0);
    }

    public function test_menu_aktif_dapat_digunakan_dalam_transaksi(): void
    {
        $kasir = User::create([
            'username' => 'kasir_aktif_test',
            'nama_lengkap' => 'Kasir Pengujian Aktif',
            'password' => 'password123',
            'role' => 'kasir',
        ]);

        $menu = Menu::create([
            'nama_menu' => 'Ayam Geprek Test',
            'kategori' => 'Makanan',
            'harga' => 15000,
            'status_menu' => true,
        ]);

        $response = $this->actingAs($kasir)->postJson('/transaksi', [
            'metode_pembayaran' => 'tunai',
            'uang_dibayarkan' => 30000,
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 2,
                ],
            ],
        ]);

        $response->assertCreated();

        $response->assertJson([
            'message' => 'Transaksi berhasil dibuat.',
            'data' => [
                'metode_pembayaran' => 'tunai',
                'status' => 'selesai',
            ],
        ]);

        $this->assertDatabaseCount('transaksis', 1);
        $this->assertDatabaseCount('detail_transaksis', 1);

        $this->assertDatabaseHas('transaksis', [
            'user_id' => $kasir->id,
            'total_harga' => 30000,
            'metode_pembayaran' => 'tunai',
            'uang_dibayarkan' => 30000,
            'kembalian' => 0,
            'status' => 'selesai',
        ]);

        $this->assertDatabaseHas('detail_transaksis', [
            'menu_id' => $menu->id,
            'jumlah' => 2,
            'harga_satuan' => 15000,
            'subtotal' => 30000,
        ]);
    }
}