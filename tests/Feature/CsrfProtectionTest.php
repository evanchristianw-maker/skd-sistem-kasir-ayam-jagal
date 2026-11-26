<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\RekeningBank;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsrfProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Enforce CSRF verification by disabling unit test bypass
        $this->app->instance(
            PreventRequestForgery::class,
            new class($this->app, $this->app['encrypter']) extends PreventRequestForgery {
                protected function runningUnitTests(): bool
                {
                    return false;
                }
            }
        );
    }

    public function test_get_request_is_allowed_without_csrf_token(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_post_transaksi_without_csrf_token_is_rejected_with_419(): void
    {
        $user = User::factory()->create([
            'role' => 'kasir',
        ]);

        $response = $this->actingAs($user)
            ->post('/transaksi', [
                'metode_pembayaran' => 'tunai',
                'nominal_bayar' => 50000,
                'items' => [],
            ], [
                'Sec-Fetch-Site' => 'cross-site',
            ]);

        $response->assertStatus(419);
    }

    public function test_post_transaksi_with_invalid_csrf_token_is_rejected_with_419(): void
    {
        $user = User::factory()->create([
            'role' => 'kasir',
        ]);

        $this->withSession(['_token' => 'valid-server-session-token']);

        $response = $this->actingAs($user)
            ->withHeader('X-CSRF-TOKEN', 'invalid-attacker-token')
            ->post('/transaksi', [
                'metode_pembayaran' => 'tunai',
                'nominal_bayar' => 50000,
                'items' => [],
            ], [
                'Sec-Fetch-Site' => 'cross-site',
            ]);

        $response->assertStatus(419);
    }

    public function test_post_transaksi_with_valid_csrf_token_in_header_passes_csrf_check(): void
    {
        $user = User::factory()->create([
            'role' => 'kasir',
        ]);

        $sessionToken = 'valid-csrf-session-token-1234567890';
        $this->withSession(['_token' => $sessionToken]);

        $response = $this->actingAs($user)
            ->withHeader('X-CSRF-TOKEN', $sessionToken)
            ->postJson('/transaksi', [
                'metode_pembayaran' => 'tunai',
                'nominal_bayar' => 50000,
                'items' => [],
            ], [
                'Sec-Fetch-Site' => 'cross-site',
            ]);

        // Status is 422 (validation error on items) instead of 419, confirming CSRF check passed
        $this->assertNotEquals(419, $response->getStatusCode());
    }

    public function test_post_transaksi_with_valid_csrf_token_in_body_passes_csrf_check(): void
    {
        $user = User::factory()->create([
            'role' => 'kasir',
        ]);

        $sessionToken = 'valid-csrf-session-token-1234567890';
        $this->withSession(['_token' => $sessionToken]);

        $response = $this->actingAs($user)
            ->post('/transaksi', [
                '_token' => $sessionToken,
                'metode_pembayaran' => 'tunai',
                'nominal_bayar' => 50000,
                'items' => [],
            ], [
                'Sec-Fetch-Site' => 'cross-site',
            ]);

        $this->assertNotEquals(419, $response->getStatusCode());
    }

    public function test_admin_post_menu_without_csrf_token_is_rejected(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post('/menu', [
                'nama' => 'Ayam Goreng Sambal',
                'harga' => 25000,
                'stok' => 10,
            ], [
                'Sec-Fetch-Site' => 'cross-site',
            ]);

        $response->assertStatus(419);
    }

    public function test_admin_put_menu_without_csrf_token_is_rejected(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $menu = Menu::create([
            'nama_menu' => 'Ayam Goreng',
            'kategori' => 'Makanan',
            'harga' => 20000,
            'status_menu' => true,
        ]);

        $response = $this->actingAs($admin)
            ->put("/menu/{$menu->id}", [
                'nama_menu' => 'Ayam Bakar Madu',
                'kategori' => 'Makanan',
                'harga' => 30000,
            ], [
                'Sec-Fetch-Site' => 'cross-site',
            ]);

        $response->assertStatus(419);
    }

    public function test_admin_delete_menu_without_csrf_token_is_rejected(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $menu = Menu::create([
            'nama_menu' => 'Ayam Goreng',
            'kategori' => 'Makanan',
            'harga' => 20000,
            'status_menu' => true,
        ]);

        $response = $this->actingAs($admin)
            ->delete("/menu/{$menu->id}", [], [
                'Sec-Fetch-Site' => 'cross-site',
            ]);

        $response->assertStatus(419);
    }

    public function test_admin_patch_menu_status_without_csrf_token_is_rejected(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $menu = Menu::create([
            'nama_menu' => 'Ayam Goreng',
            'kategori' => 'Makanan',
            'harga' => 20000,
            'status_menu' => true,
        ]);

        $response = $this->actingAs($admin)
            ->patch("/menu/{$menu->id}/status", [], [
                'Sec-Fetch-Site' => 'cross-site',
            ]);

        $response->assertStatus(419);
    }

    public function test_admin_rekening_bank_without_csrf_token_is_rejected(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post('/rekening-bank', [
                'nama_bank' => 'BCA',
                'nomor_rekening' => '1234567890',
                'atas_nama' => 'Kasir Jagal',
            ], [
                'Sec-Fetch-Site' => 'cross-site',
            ]);

        $response->assertStatus(419);
    }

    public function test_logout_endpoint_without_csrf_token_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/logout', [], [
                'Sec-Fetch-Site' => 'cross-site',
            ]);

        $response->assertStatus(419);
    }
}
