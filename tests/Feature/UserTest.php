<?php

namespace Tests\Feature;

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
   use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_register_new_user_successfully(): void
    {
        Event::fake(UserRegistered::class);

        $user = [
            'id' => 1,
            'name' => 'Milos',
            'email' => 'test@email.com',
            'password' => '12345678'
        ];

        $response = $this->postJson('api/register', $user);

        $response->assertCreated();
        Event::assertDispatched(function (UserRegistered $event) use ($user) {
            return $event->user->id === $user['id'];
        });
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user',
            ],
        ]);
    }

    public function test_missing_parameter_to_register_new_user(): void 
    {
        $user = [
            'id' => 1,
            'name' => 'Milos',
            'email' => '',
            'password' => '12345678'
        ];

        $response = $this->postJson('api/register', $user);

        $response->assertStatus(422);
    }

    public function test_login_user_successfully(): void
    {
        $user = User::create([
            'name' => 'Milos',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->postJson('api/login', ['email' =>'test@test.com', 'password' => 'password']);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user',
                'access_token'
            ],
        ]);
    }

    public function test_invalid_credentials_to_login_user(): void
    {
        $user = User::create([
            'name' => 'Milos',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->postJson('api/login', ['email' =>'test2@test.com', 'password' => 'password']);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
        ]);
    }

    public function test_user_logout(): void
    {
        $user = User::create([
            'name' => 'Milos',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->actingAs($user)->postJson('api/logout');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
        ]);
    }
}
