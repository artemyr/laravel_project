<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\SignInController;
use App\Http\Requests\SignInFormRequest;
use Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignInPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_login_page_success(): void
    {
        $this->get(action([SignInController::class, 'page']))
            ->assertOk()
            ->assertSee('Вход в аккаунт')
            ->assertViewIs('auth.login');
    }

    public function test_it_sign_in_success(): void
    {
        $password = '12345679';

        $testUser = User::factory()->create([
            'email' => 'test@mail.ru',
            'password' => bcrypt($password),
        ]);

        $request = SignInFormRequest::factory()->create([
            'email' => $testUser->email,
            'password' => $password,
        ]);

        $response = $this->post(action([SignInController::class, 'handle']), $request);

        $response->assertValid()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($testUser);
    }

    public function test_it_handle_fail(): void
    {
        $request = SignInFormRequest::factory()->create([
            'email' => 'test@mail.ru',
            'password' => str()->random(10)
        ]);

        $this->post(action([SignInController::class, 'handle']), $request)
            ->assertInvalid(['email']);

        $this->assertGuest();
    }

    public function test_it_logout_success(): void
    {
        $testUser = User::factory()->create([
            'email' => 'test@mail.ru',
            'password' => '123456789',
        ]);

        $this->actingAs($testUser)
            ->delete(action([SignInController::class, 'logOut']));

        $this->assertGuest();
    }

    public function test_it_logout_guest_middleware_fail(): void
    {
        $this->delete(action([SignInController::class, 'logOut']))
            ->assertRedirect(route('home'));
    }
}
