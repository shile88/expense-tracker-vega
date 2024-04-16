<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Income;
use App\Models\IncomeGroup;
use App\Models\User;
use Database\Seeders\ScheduleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertNull;

class IncomeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_are_auth_user_account_incomes_empty(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create();
        $incomeGroup = IncomeGroup::factory()->create();

        $response = $this->actingAs($user)->getJson("api/accounts/$account->id/income-groups/$incomeGroup->id/incomes");
        
        $response->assertStatus(200);
        $response->assertExactJson([
            'success' => true,
            'message' => 'No income data.',
            'data' => []
        ]);
    }

    public function test_does_auth_user_account_has_incomes(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create();
        $incomeGroup = IncomeGroup::factory()->create();
        $this->seed(ScheduleSeeder::class);
        $income = Income::factory()->create();

        $response = $this->actingAs($user)->getJson("api/accounts/$account->id/income-groups/$incomeGroup->id/incomes");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'incomes',
                'pagination'
            ]
        ]);
    }

    public function test_create_income_with_specific_income_group_for_auth_user_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create();
        $incomeGroup = IncomeGroup::factory()->create();
        $this->seed(ScheduleSeeder::class);

        $response = $this->actingAs($user)->postJson(
            "api/accounts/$account->id/income-groups/$incomeGroup->id/incomes",
            ['income_group_id' => $incomeGroup->id, 'amount' => 100, 'schedule_id' => 1]
        );

        $response->assertStatus(201);
        $incomeData = $response->json('data');
        $this->assertDatabaseHas('incomes',
            [
                'income_group_id' => $incomeData['income_group_id'],
                'amount' => $incomeData['amount'],
                'schedule_id' => $incomeData['schedule_id']
            ]
        );
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function test_show_income_with_specific_income_group_for_auth_user_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create();
        $incomeGroup = IncomeGroup::factory()->create();
        $this->seed(ScheduleSeeder::class);
        $income = Income::factory()->create();

        $response = $this->actingAs($user)->getJson("api/accounts/$account->id/income-groups/$incomeGroup->id/incomes/$income->id");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function test_update_income_with_specific_income_group_for_auth_user_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create();
        $incomeGroup = IncomeGroup::factory()->create();
        $this->seed(ScheduleSeeder::class);
        $income = Income::factory()->create();

        $response = $this->actingAs($user)->getJson(
            "api/accounts/$account->id/income-groups/$incomeGroup->id/incomes/$income->id",
            ['amount' => 1000]
        );

        $response->assertStatus(200);
        $incomeData = $response->json('data');
        $this->assertDatabaseHas('incomes',['amount' => $incomeData['amount']]);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function test_delete_income_with_specific_income_group_for_auth_user_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create();
        $incomeGroup = IncomeGroup::factory()->create();
        $this->seed(ScheduleSeeder::class);
        $income = Income::factory()->create();

        $response = $this->actingAs($user)->deleteJson("api/accounts/$account->id/income-groups/$incomeGroup->id/incomes/$income->id");

        $response->assertStatus(200);
        $this->assertSoftDeleted($income);
        $response->assertJsonStructure([
            'success',
            'message',
        ]);
    }
}
