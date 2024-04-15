<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\IncomeGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IncomeGroupTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_check_are_income_groups_empty_for_auth_user_account(): void
    {
        $user = User::factory()->create();

        $account = Account::factory()->create();

        $response = $this->actingAs($user)->getJson("api/accounts/$account->id/income-groups");

        $response->assertStatus(200);

        $response->assertExactJson([
            'success' => true,
            'message' => 'No income groups for this account.',
            'data' => []
        ]);
    }

    public function test_get_all_income_groups_for_auth_user_account(): void
    {
        $user = User::factory()->create();

        $account = Account::factory()->create();

        $incomeGroup = IncomeGroup::factory()->create();

        $response = $this->actingAs($user)->getJson("api/accounts/$account->id/income-groups");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'income_groups',
                'pagination'
            ]
        ]);
    }

    public function test_create_new_income_group_for_auth_user_account(): void 
    {
        $user = User::factory()->create();

        $account = Account::factory()->create();

        $response = $this->actingAs($user)->postJson("api/accounts/$account->id/income-groups", ['name' => 'kirija', 'account_id' => $account->id]);

        $response->assertStatus(201);

        $incomeGroupData = $response->json('data');

        $this->assertNotNull($incomeGroupData);

        $this->assertDatabaseHas('income_groups', ['id'=> $incomeGroupData['id'], 'account_id' => $incomeGroupData['account_id'], 'name' => $incomeGroupData['name']]);
      
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function test_show_one_income_group_for_auth_user_account(): void 
    {
        $user = User::factory()->create();

        $account = Account::factory()->create();

        $incomeGroup = IncomeGroup::factory()->create();

        $response = $this->actingAs($user)->getJson("api/accounts/$account->id/income-groups/$incomeGroup->id");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function test_update_income_group_for_auth_user_account(): void
    {
        $user = User::factory()->create();

        $account = Account::factory()->create();

        $incomeGroup = IncomeGroup::factory()->create();

        $response = $this->actingAs($user)->patchJson("api/accounts/$account->id/income-groups/$incomeGroup->id", ['name' => 'soping']);

        $response->assertStatus(200);

        $incomeGroupData = $response->json('data');

        $this->assertNotNull($incomeGroupData);

        $this->assertDatabaseHas('income_groups', ['id'=> $incomeGroupData['id'], 'account_id' => $incomeGroupData['account_id'], 'name' => $incomeGroupData['name']]);
      
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function test_delete_income_group_for_auth_user_account(): void
    {
        $user = User::factory()->create();

        $account = Account::factory()->create();

        $incomeGroup = IncomeGroup::factory()->create();

        $response = $this->actingAs($user)->deleteJson("api/accounts/$account->id/income-groups/$incomeGroup->id");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }
}
