<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\TaxBracket;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seed_is_company_scoped_complete_and_rerunnable(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $gr8tech = Company::query()->where('code', 'GR8TECH')->firstOrFail();
        $northstar = Company::query()->where('code', 'NPI')->firstOrFail();

        $this->assertSame(2, Company::query()->count());
        $this->assertSame(5, Department::query()->where('company_id', $gr8tech->id)->count());
        $this->assertSame(2, Department::query()->where('company_id', $northstar->id)->count());
        $this->assertSame(9, Employee::query()->where('company_id', $gr8tech->id)->count());
        $this->assertSame(1, Employee::query()->where('company_id', $northstar->id)->count());
        $this->assertSame(10, Account::query()->count());
        $this->assertSame(9, Position::query()->count());
        $this->assertSame(6, TaxBracket::query()->count());

        $this->assertDatabaseHas('accounts', ['email' => 'admin@gr8tech.example', 'role' => 'admin']);
        $this->assertDatabaseHas('accounts', ['email' => 'hr@gr8tech.example', 'role' => 'hr']);
        $this->assertDatabaseHas('accounts', ['email' => 'manager@gr8tech.example', 'role' => 'manager']);
        $this->assertDatabaseHas('accounts', ['email' => 'employee@gr8tech.example', 'role' => 'employee']);
        $this->assertTrue(Hash::check('Password123!', Account::query()->where('email', 'admin@gr8tech.example')->value('password')));

        $this->assertNotNull(
            Department::query()->where('department_id', 'DEPT-GR8-HR')->value('manager_id'),
        );
    }
}
