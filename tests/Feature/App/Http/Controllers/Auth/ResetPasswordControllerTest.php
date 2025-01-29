<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SignInController;
use Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = Password::createToken($this->user);
    }

    public function test_it_reset_page_success(): void
    {
        $this->get(action([ResetPasswordController::class, 'page'], ['token' => $this->token]))
            ->assertOk()
            ->assertSee('Восстановление пароля')
            ->assertViewIs('auth.reset-password');
    }

    public function test_it_handle(): void
    {
        $password = '123456789';
        $passwordConfirmation = '123456789';

        Password::shouldReceive('reset')
            ->once()
            ->withSomeOfArgs([
                'email' => $this->user->email,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
                'token' => $this->token,
            ])
            ->andReturn(Password::PASSWORD_RESET);

        $response = $this->post(action([ResetPasswordController::class, 'page'], [
            'email' => $this->user->email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
            'token' => $this->token
        ]));

        $response->assertRedirect(action([SignInController::class, 'page']));
    }
}
