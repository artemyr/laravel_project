<?php

namespace Domain\Auth\Traits;

trait TestUser
{
    public static function getTestUser(): static
    {
        $exists = static::query()
            ->where('email', self::TEST_USER_EMAIL)
            ->exists();

        if ($exists) {
            return static::query()
                ->where('email', self::TEST_USER_EMAIL)
                ->first();
        } else {
            return static::factory()->create([
                'email' => static::TEST_USER_EMAIL,
                'password' => bcrypt(self::TEST_USER_PASSWORD)
            ]);
        }
    }

    public static function resetTestUserCredentials()
    {
        $testUser = self::getTestUser();
        $testUser->password  = bcrypt(self::TEST_USER_PASSWORD);
        $testUser->save();
    }
}
