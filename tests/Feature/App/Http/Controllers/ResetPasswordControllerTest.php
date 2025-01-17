<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Requests\ResetPasswordFormRequest;
use Domain\Auth\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    //    use RefreshDatabase;

    public function test_it_reset_page_success(): void
    {
        $this->get(action([ResetPasswordController::class, 'page']))
            ->assertOk()
            ->assertSee('Восстановление пароля')
            ->assertViewIs('auth.reset-password');
    }

    public function test_it_reset_password_success(): void
    {
        Event::fake();
        Notification::fake();

        $testUser = User::getTestUser();

        $token = Password::createToken($testUser);

        $request = ResetPasswordFormRequest::factory()->create([
            'email' => $testUser->email,
            'token' => $token,
            'password' => '987654321',
            'password_confirmation' => '987654321',
        ]);

        $response = $this->post(action([ResetPasswordController::class, 'handle']), $request);

        $response->assertValid();

        $response->assertRedirect(route('login'));
    }
    // endregion

    /**
     * TODO password githubCallback
     */
}
