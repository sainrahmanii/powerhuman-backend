<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Responsibility;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create();
        Company::factory(10)->create();
        Team::factory(50)->create();
        Role::factory(50)->create();
        Responsibility::factory(200)->create();
        Employee::factory(1000)->create();
    }
}
