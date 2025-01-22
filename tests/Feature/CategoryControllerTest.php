<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Collection $categories;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->categories = Category::factory()
            ->count(5)
            ->create([
                'user_id' => $this->user->id,
            ]);

        Category::factory()
            ->count(3)
            ->for(User::factory())
            ->create();
    }

    public function testOnlyAuthenticatedUsersSeeTheListOfCategories(): void
    {
        $response = $this->get(route('categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function testListOfCategoriesCanBeFetched(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)
            ->assertViewIs('categories.index')
            ->assertViewHas('categories', function ($categories): bool {
                return $categories->count() === 5;
            });
    }
}
