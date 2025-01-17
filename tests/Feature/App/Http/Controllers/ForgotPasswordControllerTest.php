<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Requests\ForgotPasswordFormRequest;
use Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_forgot_page_success(): void
    {
        $this->get(action([ForgotPasswordController::class, 'page']))
            ->assertOk()
            ->assertSee('Забыли пароль')
            ->assertViewIs('auth.forgot-password');
    }

    /**
     * TODO improve
     */
    public function test_it_forgot_password_success(): void
    {
        Event::fake();
        Notification::fake();

        $testUser = User::getTestUser();

        $request = ForgotPasswordFormRequest::factory()->create([
            'email' => $testUser->email
        ]);

        $response = $this->post(action([ForgotPasswordController::class, 'handle']), $request);

        $response->assertValid();

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $request['email'],
        ]);

        $response->assertRedirect(route('home'));
    }
}
