<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\AuthController;
use App\Http\Requests\ForgotPasswordFormRequest;
use App\Http\Requests\ResetPasswordFormRequest;
use App\Http\Requests\SignInFormRequest;
use App\Http\Requests\SignUpFormRequest;
use App\Listeners\SendEmailNewUserListener;
use App\Models\User;
use App\Notifications\NewUserNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
//    use RefreshDatabase;

    // region Вход в аккаунт
    public function test_it_login_page_success(): void
    {
        $this->get(action([AuthController::class, 'index']))
            ->assertOk()
            ->assertSee('Вход в аккаунт')
            ->assertViewIs('auth.index');
    }

    public function test_it_sign_in_success(): void
    {
        $password = '123456789';

        $user = User::factory()->create([
            'email' => 'user_login_test@mail.ru',
            'password' => bcrypt($password),
        ]);

        $request = SignInFormRequest::factory()->create([
            'email' => $user->email,
            'password' => $password,
        ]);

        $response = $this->post(action([AuthController::class, 'signIn']), $request);

        $response->assertValid()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }
    // endregion

    // region Регистрация
    public function test_it_sign_up_page_success(): void
    {
        $this->get(action([AuthController::class, 'signUp']))
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.sign-up');
    }

    public function test_it_store_success(): void
    {
        Event::fake();
        Notification::fake();

        $request = SignUpFormRequest::factory()->create([
            'email' => 'user_store_test@mail.ru',
            'password' => '123456789',
            'password_confirmation' => '123456789'
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => $request['email']
        ]);

        $response = $this->post(
            action([AuthController::class, 'store']),
            $request
        );

        $response->assertValid();

        $this->assertDatabaseHas('users', [
            'email' => $request['email']
        ]);

        $user = User::query()
            ->where('email', $request['email'])
            ->first();

        Event::assertDispatched(Registered::class);
        Event::assertListening(Registered::class, SendEmailNewUserListener::class);

        $event = new Registered($user);
        $listener = new SendEmailNewUserListener();
        $listener->handle($event);

        Notification::assertSentTo($user, NewUserNotification::class);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('home'));
    }
    // endregion

    // region Выход из аккаунта
    public function test_it_logout_success(): void
    {
        $user = User::factory()->create([
            'email' => 'user_logout_test@mail.ru',
        ]);

        $this->actingAs($user)
            ->delete(action([AuthController::class, 'logOut']));

        $this->assertGuest();
    }
    // endregion

    // region Забыл пароль
    public function test_it_forgot_page_success(): void
    {
        $this->get(action([AuthController::class, 'forgot']))
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

        $password = '123456789';
        $user = User::factory()->create([
            'email' => 'user_forgot_password_test@mail.ru',
            'password' => bcrypt($password),
        ]);

        $request = ForgotPasswordFormRequest::factory()->create([
            'email' => 'user_forgot_password_test@mail.ru',
        ]);

        $response = $this->post(action([AuthController::class, 'forgotPassword']), $request);

        $response->assertValid();

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $request['email'],
        ]);

        $response->assertRedirect(route('home'));
    }

    public function test_it_reset_page_success(): void
    {
        $this->get(action([AuthController::class, 'reset']))
            ->assertOk()
            ->assertSee('Восстановление пароля')
            ->assertViewIs('auth.reset-password');
    }

    public function test_it_reset_password_success(): void
    {
        Event::fake();
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user_reset_password_test@mail.ru',
        ]);
        $user = User::query()
            ->where('email', 'user_reset_password_test@mail.ru')
            ->first();

        $token = Password::createToken($user);

        $request = ResetPasswordFormRequest::factory()->create([
            'email' => 'user_reset_password_test@mail.ru',
            'token' => $token,
            'password' => '987654321',
            'password_confirmation' => '987654321',
        ]);

        $response = $this->post(action([AuthController::class, 'resetPassword']), $request);

        $response->assertValid();

        $response->assertRedirect(route('login'));
    }
    // endregion

    /**
     * TODO password githubCallback
     */
}
