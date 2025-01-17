<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\SignUpController;
use App\Http\Requests\SignUpFormRequest;
use App\Listeners\SendEmailNewUserListener;
use App\Notifications\NewUserNotification;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SignUpPasswordControllerTest extends TestCase
{
    //    use RefreshDatabase;

    public function test_it_sign_up_page_success(): void
    {
        $this->get(action([SignUpController::class, 'page']))
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.sign-up');
    }

    public function test_it_store_success(): void
    {
        Event::fake();
        Notification::fake();

        $exists = User::query()
            ->where('email', User::TEST_USER_EMAIL)
            ->exists();

        if ($exists) {
            User::query()
                ->where('email', User::TEST_USER_EMAIL)
                ->delete();
        }

        $request = SignUpFormRequest::factory()->create([
            'email' => User::TEST_USER_EMAIL,
            'password' => User::TEST_USER_PASSWORD,
            'password_confirmation' => User::TEST_USER_PASSWORD
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => $request['email']
        ]);

        $response = $this->post(
            action([SignUpController::class, 'handle']),
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
}
