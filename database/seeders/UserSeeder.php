<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data user dengan nama lokal Indonesia
        $users = [
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@banyakisland.com',
                'contact' => '+62 812 3456 7890',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@banyakisland.com',
                'contact' => '+62 813 4567 8901',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dewi Sartika',
                'email' => 'dewi.sartika@banyakisland.com',
                'contact' => '+62 814 5678 9012',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ahmad Dahlan',
                'email' => 'ahmad.dahlan@banyakisland.com',
                'contact' => '+62 815 6789 0123',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Kartini Raden Ajeng',
                'email' => 'kartini.raden@banyakisland.com',
                'contact' => '+62 816 7890 1234',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Joko Widodo',
                'email' => 'joko.widodo@banyakisland.com',
                'contact' => '+62 817 8901 2345',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Mega Wati',
                'email' => 'mega.wati@banyakisland.com',
                'contact' => '+62 818 9012 3456',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Habibie Bacharuddin',
                'email' => 'habibie.bacharuddin@banyakisland.com',
                'contact' => '+62 819 0123 4567',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sri Mulyani',
                'email' => 'sri.mulyani@banyakisland.com',
                'contact' => '+62 821 1234 5678',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ridwan Kamil',
                'email' => 'ridwan.kamil@banyakisland.com',
                'contact' => '+62 822 2345 6789',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Anies Baswedan',
                'email' => 'anies.baswedan@banyakisland.com',
                'contact' => '+62 823 3456 7890',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Tri Rismaharini',
                'email' => 'tri.rismaharini@banyakisland.com',
                'contact' => '+62 824 4567 8901',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ganjar Pranowo',
                'email' => 'ganjar.pranowo@banyakisland.com',
                'contact' => '+62 825 5678 9012',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Puan Maharani',
                'email' => 'puan.maharani@banyakisland.com',
                'contact' => '+62 826 6789 0123',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Mahfud MD',
                'email' => 'mahfud.md@banyakisland.com',
                'contact' => '+62 827 7890 1234',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        ];

        // Membuat user-user tersebut
        foreach ($users as $userData) {
            $user = User::create($userData);

            // Assign role 'customer' jika role tersebut ada dan user bukan admin/super_admin
            $isAdminEmail = in_array($userData['email'], [
                'admin@banyakisland.com',
                'admin@admin.com'
            ]);

            if (!$isAdminEmail) {
                // Assign role customer menggunakan safe assignment
                $customerRole = Role::where('name', 'customer')->first();
                if ($customerRole) {
                    $user->safeAssignRole($customerRole);
                }
            }

            $this->command->info("User {$userData['name']} berhasil dibuat dengan email {$userData['email']}");
        }

        $this->command->info('Semua user dengan nama lokal Indonesia berhasil dibuat!');
    }
}
