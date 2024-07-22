<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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
        DB::table('role_has_permissions')->truncate();
        Permission::truncate();
        User::truncate();

        Artisan::call('shield:generate --all');

        $user_admin = User::factory()->create([
            'email' => 'user@user.com',
        ]);

        Artisan::call('shield:super-admin', ['--user' => $user_admin->id]);


        $role_cachier = Role::create(['name' => 'cashier']); //cajera

        $permissionStockEntry = Permission::where('name', 'like', '%stock::entry%')
            ->where('name', '!=', 'change_status_stock::entry')->get();

        $role_cachier->givePermissionTo($permissionStockEntry);


        $user_regular = User::factory()->create([
            'email' => 'user@user2.com',
        ]);

        $user_regular->assignRole('cashier');

        User::factory(2)->create()->each(function (User $user) {
            $user->assignRole('cashier');
        });
    }
}
