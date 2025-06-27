<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\ForgotPasswordController;
use Domain\Auth\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    private function testing_credentials()
    {
        return [
            'email' => 'test@mail.ru',
        ];
    }

    public function test_it_forgot_page_success(): void
    {
        $this->get(action([ForgotPasswordController::class, 'page']))
            ->assertOk()
            ->assertSee('Забыли пароль')
            ->assertViewIs('auth.forgot-password');
    }

    public function test_it_handle_success(): void
    {
        $user = User::factory()->create($this->testing_credentials());

        $this->post(action([ForgotPasswordController::class, 'handle']), $this->testing_credentials())
            ->assertRedirect();

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_it_handle_fail(): void
    {
        $this->assertDatabaseMissing('users', $this->testing_credentials());

        $this->post(action([ForgotPasswordController::class, 'handle']), $this->testing_credentials())
            ->assertInvalid(['email']);

        Notification::assertNothingSent();
    }
}
