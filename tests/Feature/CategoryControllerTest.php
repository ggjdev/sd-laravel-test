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

    public function testOnlyAuthenticatedUsersCanViewCreateCategoriesPage(): void
    {
        $response = $this->get(route('categories.create'));

        $response->assertRedirect(route('login'));
    }

    public function testAuthenticatedUsersCanViewCreateCategoriesPage(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('categories.create'));

        $response->assertStatus(200)
            ->assertViewIs('categories.form');
    }

    public function testOnlyAuthenticatedUsersCanCreateCategories(): void
    {
        $response = $this->post(route('categories.store'), [
            'name' => 'New Category',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function testAuthenticatedUsersCanCreateCategories(): void
    {
        $this->actingAs($this->user);

        $categoryData = [
            'name' => 'New Category',
        ];

        $response = $this->post(route('categories.store', ['category_id' => 1]), $categoryData);

        $response->assertRedirect(route('categories.index'));

        $categoryData['user_id'] = $this->user->id;
        $this->assertDatabaseHas('categories', $categoryData);
    }

    public function testACategoryCannotBeCreatedWithoutAName(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('categories.store'), [
            'name' => null,
        ]);

        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
        ]);
    }

    public function testCategoryNamesMustBeUniqueForAUser(): void
    {
        User::all()->each(function ($user) {
            Category::factory()->create([
                    'user_id' => $user->id,
                    'name' => 'Unique Category',
                ]);
        });

        $this->actingAs($this->user);

        $response = $this->post(route('categories.store'), [
            'name' => 'Unique Category',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'The name has already been taken.',
        ]);
    }

    public function testCategoryNamesCannotBeMoreThan40Characters(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('categories.store'), [
            'name' => str_repeat('a', 41),
        ]);

        $response->assertSessionHasErrors([
            'name' => 'The name may not be greater than 40 characters.',
        ]);
    }
}
