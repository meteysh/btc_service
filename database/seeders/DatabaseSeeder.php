<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Account;
use App\Models\Cashback;
use App\Models\Partner;
use App\Models\Site;
use App\Models\User;
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
        $users = User::factory(3)->has(Cashback::factory())->create();
        foreach ($users as $user) {
            Account::factory()->for($user, 'accountable'
            )->create(['balance' => 111.111]);
        }
        foreach ($users as $user) {
            Account::factory()->for($user->cashback, 'accountable'
            )->create([]);
        }
        $partners = Partner::factory(3)->create();
        foreach ($partners as $partner) {
            Account::factory()->for($partner, 'accountable')->create([]);
        }
        Account::factory()->for(
            Site::factory(), 'accountable'
        )->create([]);
    }
}
