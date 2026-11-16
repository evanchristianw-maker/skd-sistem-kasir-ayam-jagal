<?php

namespace Tests\Feature;

use App\Models\RekeningBank;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekeningBankTest extends TestCase
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

    private function buatKasir(): User
    {
        return User::create([
            'username' => 'kasir_test',
            'nama_lengkap' => 'Kasir Pengujian',
            'password' => 'password123',
            'role' => 'kasir',
        ]);
    }

    public function test_admin_dapat_membuka_halaman_rekening_bank(): void
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)->get('/rekening-bank');

        $response->assertOk();
    }

    public function test_kasir_tidak_dapat_membuka_halaman_rekening_bank(): void
    {
        $kasir = $this->buatKasir();

        $response = $this->actingAs($kasir)->get('/rekening-bank');

        $response->assertForbidden();
    }

    public function test_admin_dapat_menambah_rekening(): void
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)->post('/rekening-bank', [
            'nama_bank' => 'BCA',
            'nomor_rekening' => '0123456789',
            'atas_nama' => 'PT Ayam Jagal',
        ]);

        $response->assertRedirect(route('rekening-bank.index'));

        $this->assertDatabaseHas('rekening_banks', [
            'nama_bank' => 'BCA',
            'nomor_rekening' => '0123456789',
            'atas_nama' => 'PT Ayam Jagal',
            'status_aktif' => 1,
        ]);
    }

    public function test_validasi_backend_berfungsi(): void
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)->post('/rekening-bank', [
            'nama_bank' => '',
            'nomor_rekening' => '',
            'atas_nama' => '',
        ]);

        $response->assertSessionHasErrors(['nama_bank', 'nomor_rekening', 'atas_nama']);
    }

    public function test_admin_dapat_mengedit_rekening_termasuk_status(): void
    {
        $admin = $this->buatAdmin();

        $rekening = RekeningBank::create([
            'nama_bank' => 'BRI',
            'nomor_rekening' => '1111',
            'atas_nama' => 'Lama',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($admin)->put("/rekening-bank/{$rekening->id}", [
            'nama_bank' => 'Mandiri',
            'nomor_rekening' => '2222',
            'atas_nama' => 'Baru',
            'status_aktif' => false,
        ]);

        $response->assertRedirect(route('rekening-bank.index'));

        $this->assertDatabaseHas('rekening_banks', [
            'id' => $rekening->id,
            'nama_bank' => 'Mandiri',
            'nomor_rekening' => '2222',
            'atas_nama' => 'Baru',
            'status_aktif' => 0,
        ]);
    }

    public function test_toggle_status_rekening(): void
    {
        $admin = $this->buatAdmin();

        $rekening = RekeningBank::create([
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234',
            'atas_nama' => 'PT',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($admin)->patch("/rekening-bank/{$rekening->id}/status", []);

        $response->assertRedirect(route('rekening-bank.index'));

        $this->assertDatabaseHas('rekening_banks', [
            'id' => $rekening->id,
            'status_aktif' => 0,
        ]);
    }

    public function test_hapus_rekening_belum_digunakan_berhasil(): void
    {
        $admin = $this->buatAdmin();

        $rekening = RekeningBank::create([
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234',
            'atas_nama' => 'PT',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($admin)->delete("/rekening-bank/{$rekening->id}");

        $response->assertRedirect(route('rekening-bank.index'));

        $this->assertSoftDeleted('rekening_banks', [
            'id' => $rekening->id,
        ]);
    }

    public function test_hapus_rekening_sudah_digunakan_ditolak(): void
    {
        $admin = $this->buatAdmin();
        $kasir = $this->buatKasir();

        $rekening = RekeningBank::create([
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234',
            'atas_nama' => 'PT',
            'status_aktif' => true,
        ]);

        Transaksi::create([
            'nomor_transaksi' => 'TRX-TEST-001',
            'user_id' => $kasir->id,
            'rekening_bank_id' => $rekening->id,
        ]);

        $response = $this->actingAs($admin)->delete("/rekening-bank/{$rekening->id}");

        $response->assertRedirect(route('rekening-bank.index'));

        $this->assertDatabaseHas('rekening_banks', [
            'id' => $rekening->id,
            'deleted_at' => null,
        ]);
    }

    public function test_kasir_tidak_dapat_mengakses_endpoint_admin(): void
    {
        $kasir = $this->buatKasir();
        $admin = $this->buatAdmin();

        $rekening = RekeningBank::create([
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234',
            'atas_nama' => 'PT',
            'status_aktif' => true,
        ]);

        $this->actingAs($kasir)->post('/rekening-bank', [
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234',
            'atas_nama' => 'PT',
        ])->assertForbidden();

        $this->actingAs($kasir)->put("/rekening-bank/{$rekening->id}", [
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234',
            'atas_nama' => 'PT',
        ])->assertForbidden();

        $this->actingAs($kasir)->delete("/rekening-bank/{$rekening->id}")->assertForbidden();
    }
}