<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Admin::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin'
,            'email' => 'admin@admin.com',
            'password' => Hash::make('1234')
        ]);
    }
}
