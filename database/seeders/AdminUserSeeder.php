<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::where('email', 'akshofficial43@gmail.com')
            ->update(['role' => 'admin']);
            
        $this->command->info('User akshofficial43@gmail.com updated to admin role');
    }
}