<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage users',
            'manage kriteria',
            'manage lokasi',
            'manage observasi',
            'manage penilaian',
            'process perhitungan',
            'view hasil',
            'view dashboard',
            'view rekomendasi'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        $manajer = Role::firstOrCreate(['name' => 'manajer']);
        $manajer->givePermissionTo([
            'manage users',
            'manage kriteria',
            'manage lokasi',
            'manage observasi',
            'manage penilaian',
            'process perhitungan',
            'view hasil'
        ]);

        $direktur = Role::firstOrCreate(['name' => 'direktur']);
        $direktur->givePermissionTo([
            'view dashboard',
            'view rekomendasi'
        ]);

        // Create / Update Manajer User
        $userManajer = User::updateOrCreate(
            ['email' => 'manajer@saungaqiqah.com'],
            [
                'name' => 'Bapak Manajer',
                'username' => 'manajer',
                'password' => Hash::make('manajer123'),
            ]
        );
        $userManajer->assignRole($manajer);


        // Create / Update Direktur User
        $userDirektur = User::updateOrCreate(
            ['email' => 'direktur@saungaqiqah.com'],
            [
                'name' => 'Bapak Direktur',
                'username' => 'direktur',
                'password' => Hash::make('direktur123'),
            ]
        );
        $userDirektur->assignRole($direktur);
    }
}
