<?php

namespace Domain\Auth\Traits;

trait TestUser
{
    public static function getTestUserEmail(): string
    {
        return 'test@mail.ru';
    }

    public static function getTestUserPassword()
    {
        return '123456789';
    }

    public static function getTestUser(): static
    {
        $exists = static::query()
            ->where('email', static::getTestUserEmail())
            ->exists();

        if ($exists) {
            return static::query()
                ->where('email', static::getTestUserEmail())
                ->first();
        } else {
            return static::factory()->create([
                'email' => static::getTestUserEmail(),
                'password' => bcrypt(static::getTestUserPassword())
            ]);
        }
    }
}
