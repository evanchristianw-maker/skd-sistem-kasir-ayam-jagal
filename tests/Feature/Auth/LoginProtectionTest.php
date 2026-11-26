<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginProtectionTest extends TestCase
{
    use RefreshDatabase;

    private function throttleKey(string $username): string
    {
        return strtolower($username) . '|' . request()->ip();
    }

    public function test_login_is_blocked_after_five_failed_attempts(): void
    {
        User::factory()->create([
            'username' => 'kasir_test',
            'password' => 'password',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'username' => 'kasir_test',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post('/login', [
            'username' => 'kasir_test',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('username');

        $this->assertTrue(
            RateLimiter::tooManyAttempts($this->throttleKey('kasir_test'), 5)
        );
    }

    public function test_successful_login_resets_failed_attempts(): void
    {
        User::factory()->create([
            'username' => 'kasir_test',
            'password' => 'password',
        ]);

        $key = $this->throttleKey('kasir_test');

        RateLimiter::hit($key, 60);
        RateLimiter::hit($key, 60);
        RateLimiter::hit($key, 60);

        $this->post('/login', [
            'username' => 'kasir_test',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertSame(0, RateLimiter::attempts($key));
    }

    public function test_session_id_is_regenerated_after_successful_login(): void
    {
        User::factory()->create([
            'username' => 'kasir_test',
            'password' => 'password',
        ]);

        $this->get('/login');
        $oldSessionId = session()->getId();

        $this->post('/login', [
            'username' => 'kasir_test',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertNotSame($oldSessionId, session()->getId());
    }

    public function test_logout_invalidates_session_and_regenerates_csrf_token(): void
    {
        $user = User::factory()->create();

        $this->get('/login');
        $oldSessionId = session()->getId();
        $oldToken = csrf_token();

        $response = $this->actingAs($user)
            ->withSession(['test_data' => 'sensitive'])
            ->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
        $this->assertNotSame($oldSessionId, session()->getId());
        $this->assertNotSame($oldToken, csrf_token());
        $this->assertNull(session('test_data'));
    }
}
