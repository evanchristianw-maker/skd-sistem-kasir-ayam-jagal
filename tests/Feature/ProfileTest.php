<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_not_available(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/profile')
            ->assertNotFound();
    }

    public function test_profile_update_is_not_available(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch('/profile', [
                'nama_lengkap' => 'Nama Baru',
                'username' => 'username_baru',
            ])
            ->assertNotFound();
    }

    public function test_profile_delete_is_not_available(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }
}