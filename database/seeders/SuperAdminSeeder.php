<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membersihkan cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Membuat role dan permission
        $this->createRolesAndPermissions();

        // Membuat user super admin dengan email yang sudah terverifikasi
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@banyakisland.com',
            'password' => Hash::make('password123!'),
            'email_verified_at' => now(),
        ]);

        // Assign role super_admin ke user
        $superAdminRole = Role::where('name', 'super_admin')->first();

        if ($superAdminRole) {
            $superAdmin->assignRole($superAdminRole);
            $this->command->info('Super Admin berhasil dibuat dengan email yang sudah terverifikasi.');
        } else {
            $this->command->error('Role super_admin tidak ditemukan. Terjadi kesalahan saat membuat role.');
        }
    }

    /**
     * Membuat role dan permission yang diperlukan
     */
    protected function createRolesAndPermissions(): void
    {
        // Definisi role dan permission
        $rolesWithPermissions = [
            [
                'name' => 'super_admin',
                'guard_name' => 'web',
                'permissions' => [
                    // Booking permissions
                    'view_booking', 'view_any_booking', 'create_booking', 'update_booking', 'restore_booking', 'restore_any_booking',
                    'replicate_booking', 'reorder_booking', 'delete_booking', 'delete_any_booking', 'force_delete_booking', 'force_delete_any_booking',

                    // Destination permissions
                    'view_destination', 'view_any_destination', 'create_destination', 'update_destination', 'restore_destination', 'restore_any_destination',
                    'replicate_destination', 'reorder_destination', 'delete_destination', 'delete_any_destination', 'force_delete_destination', 'force_delete_any_destination',

                    // Travel Package permissions
                    'view_travel_package', 'view_any_travel_package', 'create_travel_package', 'update_travel_package', 'restore_travel_package', 'restore_any_travel_package',
                    'replicate_travel_package', 'reorder_travel_package', 'delete_travel_package', 'delete_any_travel_package', 'force_delete_travel_package', 'force_delete_any_travel_package',

                    // Payment permissions
                    'view_payment', 'view_any_payment', 'create_payment', 'update_payment', 'restore_payment', 'restore_any_payment',
                    'replicate_payment', 'reorder_payment', 'delete_payment', 'delete_any_payment', 'force_delete_payment', 'force_delete_any_payment',

                    // Review permissions
                    'view_review', 'view_any_review', 'create_review', 'update_review', 'restore_review', 'restore_any_review',
                    'replicate_review', 'reorder_review', 'delete_review', 'delete_any_review', 'force_delete_review', 'force_delete_any_review',

                    // Role permissions
                    'view_role', 'view_any_role', 'create_role', 'update_role', 'delete_role', 'delete_any_role',

                    // Token permissions
                    'view_token', 'view_any_token', 'create_token', 'update_token', 'restore_token', 'restore_any_token',
                    'replicate_token', 'reorder_token', 'delete_token', 'delete_any_token', 'force_delete_token', 'force_delete_any_token',

                    // User permissions
                    'view_user', 'view_any_user', 'create_user', 'update_user', 'restore_user', 'restore_any_user',
                    'replicate_user', 'reorder_user', 'delete_user', 'delete_any_user', 'force_delete_user', 'force_delete_any_user',

                    // Page permissions
                    'page_ManageSetting', 'page_MyProfilePage'
                ]
            ],
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'permissions' => [
                    // Booking permissions
                    'view_booking', 'view_any_booking', 'create_booking', 'update_booking',

                    // Package Destination permissions
                    'view_package_destination', 'view_any_package_destination', 'create_package_destination', 'update_package_destination',

                    // Travel Package permissions
                    'view_travel_package', 'view_any_travel_package', 'create_travel_package', 'update_travel_package',

                    // Payment permissions
                    'view_payment', 'view_any_payment', 'create_payment', 'update_payment',

                    // Review permissions
                    'view_review', 'view_any_review', 'create_review', 'update_review',

                    // Page permissions
                    'page_ManageSetting', 'page_MyProfilePage'
                ]
            ],
            [
                'name' => 'customer',
                'guard_name' => 'web',
                'permissions' => [
                    // Booking permissions for customers
                    'view_booking', 'create_booking', 'update_booking',

                    // Travel Package permissions for customers
                    'view_travel_package',

                    // Payment permissions for customers
                    'view_payment', 'create_payment',

                    // Review permissions for customers
                    'view_review', 'create_review', 'update_review',
                ]
            ]
        ];

        // Membuat role dan permission
        foreach ($rolesWithPermissions as $roleData) {
            $role = Role::firstOrCreate([
                'name' => $roleData['name'],
                'guard_name' => $roleData['guard_name'],
            ]);

            $permissions = collect($roleData['permissions'])
                ->map(function ($permission) use ($roleData) {
                    return Permission::firstOrCreate([
                        'name' => $permission,
                        'guard_name' => $roleData['guard_name'],
                    ]);
                })
                ->all();

            $role->syncPermissions($permissions);
        }

        // Membuat direct permissions
        $directPermissions = [
            // Export permissions
            ['name' => 'export_booking', 'guard_name' => 'web'],
            ['name' => 'export_travel_package', 'guard_name' => 'web'],
            ['name' => 'export_payment', 'guard_name' => 'web'],
            ['name' => 'export_review', 'guard_name' => 'web'],
            ['name' => 'export_user', 'guard_name' => 'web'],

            // API permissions for Booking
            ['name' => 'booking:create_booking', 'guard_name' => 'web'],
            ['name' => 'booking:update_booking', 'guard_name' => 'web'],
            ['name' => 'booking:delete_booking', 'guard_name' => 'web'],
            ['name' => 'booking:pagination_booking', 'guard_name' => 'web'],
            ['name' => 'booking:detail_booking', 'guard_name' => 'web'],

            // API permissions for Travel Package
            ['name' => 'travel_package:create_travel_package', 'guard_name' => 'web'],
            ['name' => 'travel_package:update_travel_package', 'guard_name' => 'web'],
            ['name' => 'travel_package:delete_travel_package', 'guard_name' => 'web'],
            ['name' => 'travel_package:pagination_travel_package', 'guard_name' => 'web'],
            ['name' => 'travel_package:detail_travel_package', 'guard_name' => 'web'],

            // API permissions for Payment
            ['name' => 'payment:create_payment', 'guard_name' => 'web'],
            ['name' => 'payment:update_payment', 'guard_name' => 'web'],
            ['name' => 'payment:delete_payment', 'guard_name' => 'web'],
            ['name' => 'payment:pagination_payment', 'guard_name' => 'web'],
            ['name' => 'payment:detail_payment', 'guard_name' => 'web'],

            // API permissions for Review
            ['name' => 'review:create_review', 'guard_name' => 'web'],
            ['name' => 'review:update_review', 'guard_name' => 'web'],
            ['name' => 'review:delete_review', 'guard_name' => 'web'],
            ['name' => 'review:pagination_review', 'guard_name' => 'web'],
            ['name' => 'review:detail_review', 'guard_name' => 'web'],

            // API permissions for User
            ['name' => 'user:create_user', 'guard_name' => 'web'],
            ['name' => 'user:update_user', 'guard_name' => 'web'],
            ['name' => 'user:delete_user', 'guard_name' => 'web'],
            ['name' => 'user:pagination_user', 'guard_name' => 'web'],
            ['name' => 'user:detail_user', 'guard_name' => 'web'],
        ];

        foreach ($directPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => $permission['guard_name'],
            ]);
        }

        $this->command->info('Role dan Permission berhasil dibuat.');
    }
}
