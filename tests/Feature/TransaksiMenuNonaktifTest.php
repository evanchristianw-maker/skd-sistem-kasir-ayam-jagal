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
        // Membuat akun kasir khusus pengujian
        $kasir = User::create([
            'username' => 'kasir_test',
            'nama_lengkap' => 'Kasir Pengujian',
            'password' => 'password123',
            'role' => 'kasir',
        ]);

        // Membuat menu nonaktif khusus pengujian
        $menu = Menu::create([
            'nama_menu' => 'Ayam Bakar Test',
            'kategori' => 'Makanan',
            'harga' => 20000,
            'status_menu' => false,
        ]);

        // Mengirim transaksi menggunakan menu nonaktif
        $response = $this->actingAs($kasir)->postJson('/transaksi', [
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 1,
                ],
            ],
        ]);

        // Memastikan transaksi ditolak
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('items');

        // Memastikan tidak ada transaksi yang tersimpan
        $this->assertDatabaseCount('transaksis', 0);

        // Memastikan tidak ada detail transaksi
        $this->assertDatabaseCount('detail_transaksis', 0);
    }

    public function test_menu_aktif_dapat_digunakan_dalam_transaksi(): void
    {
        // Membuat akun kasir khusus pengujian
        $kasir = User::create([
            'username' => 'kasir_aktif_test',
            'nama_lengkap' => 'Kasir Pengujian Aktif',
            'password' => 'password123',
            'role' => 'kasir',
        ]);

        // Membuat menu aktif khusus pengujian
        $menu = Menu::create([
            'nama_menu' => 'Ayam Geprek Test',
            'kategori' => 'Makanan',
            'harga' => 15000,
            'status_menu' => true,
        ]);

        // Mengirim transaksi menggunakan menu aktif
        $response = $this->actingAs($kasir)->postJson('/transaksi', [
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 2,
                ],
            ],
        ]);

        // Memastikan transaksi berhasil dibuat
        $response->assertCreated();

        $response->assertJson([
            'message' => 'Transaksi berhasil dibuat.',
        ]);

        // Memastikan transaksi tersimpan
        $this->assertDatabaseCount('transaksis', 1);

        // Memastikan detail transaksi tersimpan
        $this->assertDatabaseCount('detail_transaksis', 1);

        // Memastikan total transaksi benar
        $this->assertDatabaseHas('transaksis', [
            'user_id' => $kasir->id,
            'total_harga' => 30000,
        ]);

        // Memastikan jumlah dan subtotal benar
        $this->assertDatabaseHas('detail_transaksis', [
            'menu_id' => $menu->id,
            'jumlah' => 2,
            'harga_satuan' => 15000,
            'subtotal' => 30000,
        ]);
    }
}