<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::truncate();
        Permission::truncate();
        User::truncate();

        Artisan::call('shield:generate --all');

        $user_admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'user@user.com',
        ]);

        Artisan::call('shield:super-admin', ['--user' => $user_admin->id]);

        // $user_admin->assignRole('super_admin');


        $role = Role::create(['name' => 'cashier']); //cajera

        $user_regular = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@user2.com',
        ]);

        $user_regular->assignRole('cashier');

        User::factory(2)->create()->each(function (User $user) {
            $user->assignRole('cashier');
        });
    }
}
