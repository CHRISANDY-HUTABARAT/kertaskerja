<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Admin',
                'email'    => 'admin@witel-sumut.com',
                'role'     => 'admin',
                'password' => Hash::make('Adm!nWitelSumut2025'),
            ],
            [
                'name'     => 'Government',
                'email'    => 'gov@witel-sumut.com',
                'role'     => 'gov',
                'password' => Hash::make('G0v#WitelSumut2025'),
            ],
            [
                'name'     => 'SOE',
                'email'    => 'soe@witel-sumut.com',
                'role'     => 'soe',
                'password' => Hash::make('S0e$WitelSumut2025'),
            ],
            [
                'name'     => 'SME',
                'email'    => 'sme@witel-sumut.com',
                'role'     => 'sme',
                'password' => Hash::make('Sm3&WitelSumut2025'),
            ],
            [
                'name'     => 'Private',
                'email'    => 'private@witel-sumut.com',
                'role'     => 'private',
                'password' => Hash::make('Priv@teWitelSumut2025'),
            ],
            [
                'name'     => 'Collection',
                'email'    => 'collection@witel-sumut.com',
                'role'     => 'collection',
                'password' => Hash::make('C0ll3ct!onWitelSumut2025'),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name'     => $user['name'],
                    'role'     => $user['role'],
                    'password' => $user['password'],
                ]
            );
        }
    }
}
