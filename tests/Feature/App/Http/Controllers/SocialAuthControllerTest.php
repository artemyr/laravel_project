<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\SocialAuthController;
use Database\Factories\Domain\Auth\Models\UserFactory;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Laravel\Socialite\Facades\Socialite;
use Mockery\MockInterface;
use Tests\TestCase;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class SocialAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private function mockSocialiteCallback(string|int $githubId): MockInterface
    {
        $user = $this->mock(SocialiteUser::class, function (MockInterface $m) use ($githubId) {
            $m->shouldReceive('getId')
                ->once()
                ->andReturn($githubId);

            $m->shouldReceive('getName')
                ->once()
                ->andReturn(str()->random(10));

            $m->shouldReceive('getEmail')
                ->once()
                ->andReturn('test_github@test.com');
        });

        Socialite::shouldReceive('driver->user')
            ->once()
            ->andReturn($user);

        return $user;
    }

    private function callbackRequest(): TestResponse
    {
        return $this->get(
            action(
                [SocialAuthController::class, 'callback'],
                ['driver' => 'github']
            )
        );
    }

    public function test_it_github_callback_created_user_success(): void
    {
        $githubId = str()->random(10);

        $this->assertDatabaseMissing('users', [
            'github_id' => $githubId,
        ]);

        $this->mockSocialiteCallback($githubId);

        $this->callbackRequest()
            ->assertRedirect(route('home'));

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'github_id' => $githubId
        ]);
    }

    public function test_it_authenticated_by_existing_user(): void
    {
        $githubId = str()->random(10);

        UserFactory::new()->create([
            'github_id' => $githubId
        ]);

        $this->assertDatabaseHas('users', [
            'github_id' => $githubId
        ]);

        $this->mockSocialiteCallback($githubId);

        $this->callbackRequest()
            ->assertRedirect(route('home'));

        $this->assertAuthenticated();
    }

    public function test_it_driver_not_found_exception(): void
    {
        $this->expectException(DomainException::class);

        $this->withoutExceptionHandling()
            ->get(
                action(
                    [SocialAuthController::class, 'redirect'],
                    ['driver' => 'vk']
                )
            );

        $this->withoutExceptionHandling()
            ->get(
                action(
                    [SocialAuthController::class, 'callback'],
                    ['driver' => 'vk']
                )
            );
    }
}
