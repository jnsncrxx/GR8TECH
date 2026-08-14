<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public const GR8TECH_ID = '10000000-0000-4000-8000-000000000001';

    public const NORTHSTAR_ID = '10000000-0000-4000-8000-000000000002';

    public function run(): void
    {
        $companies = [
            [
                'id' => self::GR8TECH_ID,
                'code' => 'GR8TECH',
                'name' => 'GR8 TECH ENTERPRISE INC.',
                'description' => 'Primary demonstration company for the GR8TECH HRIS.',
                'address' => 'Main Office',
                'country' => 'Philippines',
                'email' => 'hr@gr8tech.example',
                'is_active' => true,
                'cutoff_day_1' => 10,
                'cutoff_day_2' => 25,
            ],
            [
                'id' => self::NORTHSTAR_ID,
                'code' => 'NPI',
                'name' => 'Northstar People Innovations Inc.',
                'description' => 'Secondary company used to validate company-scoped records.',
                'address' => 'Northstar Office',
                'country' => 'Philippines',
                'email' => 'hr@northstar.example',
                'is_active' => true,
                'cutoff_day_1' => 10,
                'cutoff_day_2' => 25,
            ],
        ];

        foreach ($companies as $attributes) {
            $company = Company::query()->firstOrNew(['code' => $attributes['code']]);
            if (! $company->exists) {
                $company->id = $attributes['id'];
            }

            unset($attributes['id']);
            $company->fill($attributes)->save();
        }

        $this->command?->info('Demo companies seeded.');
    }
}
