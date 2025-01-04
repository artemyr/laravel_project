<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\SignInController;
use App\Http\Requests\SignInFormRequest;
use Domain\Auth\Models\User;
use Tests\TestCase;

class SignInPasswordControllerTest extends TestCase
{
    //    use RefreshDatabase;

    public function test_it_login_page_success(): void
    {
        $this->get(action([SignInController::class, 'page']))
            ->assertOk()
            ->assertSee('Вход в аккаунт')
            ->assertViewIs('auth.login');
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

        $response = $this->post(action([SignInController::class, 'handle']), $request);

        $response->assertValid()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_it_logout_success(): void
    {
        $user = User::factory()->create([
            'email' => 'user_logout_test@mail.ru',
        ]);

        $this->actingAs($user)
            ->delete(action([SignInController::class, 'logOut']));

        $this->assertGuest();
    }
}
