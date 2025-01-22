<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        User::all()->each(function ($user) {
            Category::factory()
                ->count(3)
                ->create([
                    'user_id' => $user->id,
                ]);
        });
    }
}
