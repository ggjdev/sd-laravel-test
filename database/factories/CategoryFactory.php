<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    protected static $counter = 1;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => 'Category ' . self::$counter++,
        ];
    }
}
