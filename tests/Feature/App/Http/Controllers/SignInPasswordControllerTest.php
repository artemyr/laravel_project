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
        User::resetTestUserCredentials();
        $testUser = User::getTestUser();

        $request = SignInFormRequest::factory()->create([
            'email' => $testUser->email,
            'password' => User::TEST_USER_PASSWORD,
        ]);

        $response = $this->post(action([SignInController::class, 'handle']), $request);

        $response->assertValid()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($testUser);
    }

    public function test_it_logout_success(): void
    {
        $testUser = User::getTestUser();

        $this->actingAs($testUser)
            ->delete(action([SignInController::class, 'logOut']));

        $this->assertGuest();
    }
}
