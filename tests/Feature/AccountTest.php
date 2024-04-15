<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_all_accounts_for_auth_user(): void
    {
        $user = User::create([
            'name' => 'Milos',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);

        $account = Account::create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson('api/accounts');

        $response->assertStatus(200);
        $this->assertDatabaseHas('accounts', ['id' => $account->id]);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'accounts',
                'pagination'
            ],
        ]);
    }

    public function test_create_new_account_for_auth_user(): void
    {
        $user = User::create([
            'name' => 'Milos',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->actingAs($user)->postJson('api/accounts');

        $response->assertStatus(201);

        $accountData = $response->json('data');

        $this->assertNotNull($accountData);

        $this->assertDatabaseHas('accounts', ['id'=> $accountData['id'], 'user_id' => $accountData['user']['id']]);
      
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function test_show_account_for_auth_user(): void
    {
        $user = User::create([
            'name' => 'Milos',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);

        $account = Account::create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson('api/accounts/1');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function test_update_account_for_auth_user(): void 
    {
        $user = User::create([
            'name' => 'Milos',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);

        $account = Account::create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->patchJson('api/accounts/1', ['balance' => 100]);

        $response->assertStatus(200);

        $accountData = $response->json('data');

        $this->assertNotNull($accountData);

        $this->assertDatabaseHas('accounts', ['balance' => $accountData['account']['balance']]);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'account'
            ]
        ]);
    }

    public function test_delete_account_for_auth_user(): void 
    {
        $user = User::create([
            'name' => 'Milos',
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);

        $account = Account::create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->deleteJson('api/accounts/1');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }
}
