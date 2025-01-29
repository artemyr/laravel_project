<?php

namespace Tests\Feature\Auth\DTOs;

use App\Http\Requests\SignUpFormRequest;
use Domain\Auth\DTOs\NewUserDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewUserDTOTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_instance_created_from_form_request(): void
    {
        $dto = NewUserDTO::fromRequest(new SignUpFormRequest([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'password',
        ]));

        $this->assertInstanceOf(NewUserDTO::class, $dto);
    }
}
