<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAccessControlMutationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_cannot_create_menu(): void
    {
        $kasir = User::factory()->create([
            'role' => User::ROLE_KASIR,
        ]);

        $this->actingAs($kasir)
            ->post('/menu', [])
            ->assertForbidden();
    }

    public function test_cashier_cannot_create_bank_account(): void
    {
        $kasir = User::factory()->create([
            'role' => User::ROLE_KASIR,
        ]);

        $this->actingAs($kasir)
            ->post('/rekening-bank', [])
            ->assertForbidden();
    }
}