<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_cashier_is_redirected_from_admin_dashboard(): void
    {
        $kasir = User::factory()->create([
            'role' => User::ROLE_KASIR,
        ]);

        $this->actingAs($kasir)
            ->get('/dashboard')
            ->assertRedirect(route('cashier.transactions.index'));
    }

    public function test_cashier_cannot_access_menu_management(): void
    {
        $kasir = User::factory()->create([
            'role' => User::ROLE_KASIR,
        ]);

        $this->actingAs($kasir)
            ->get('/menu')
            ->assertForbidden();
    }

    public function test_cashier_cannot_access_sales_report(): void
    {
        $kasir = User::factory()->create([
            'role' => User::ROLE_KASIR,
        ]);

        $this->actingAs($kasir)
            ->get('/sales')
            ->assertForbidden();
    }

    public function test_cashier_cannot_access_finance_report(): void
    {
        $kasir = User::factory()->create([
            'role' => User::ROLE_KASIR,
        ]);

        $this->actingAs($kasir)
            ->get('/finance')
            ->assertForbidden();
    }

    public function test_cashier_can_access_transaction_page(): void
    {
        $kasir = User::factory()->create([
            'role' => User::ROLE_KASIR,
        ]);

        $this->actingAs($kasir)
            ->get('/cashier/transaksi')
            ->assertOk();
    }

    public function test_unknown_role_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'supervisor',
        ]);

        $this->actingAs($user)
            ->get('/menu')
            ->assertForbidden();
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $this->get('/menu')
            ->assertRedirect(route('login'));
    }
}