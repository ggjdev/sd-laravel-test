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
    private User $secondUser;
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

        $this->secondUser = User::factory()->create();
        Category::factory()
            ->count(3)
            ->create([
                'user_id' => $this->secondUser->id,
            ]);
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
    public function testOnlyAuthenticatedUsersCanViewEditCategoriesPage(): void
    {
        $category = $this->categories->first();
        $response = $this->get(route('categories.edit', $category));

        $response->assertRedirect(route('login'));
    }

    public function testAuthenticatedUsersCanViewEditCategoriesPage(): void
    {
        $this->actingAs($this->user);

        $category = $this->categories->first();
        $response = $this->get(route('categories.edit', $category));

        $response->assertStatus(200)
            ->assertViewIs('categories.form');
    }

    public function testAUserCannotEditAnotherUsersCategory(): void
    {
        $this->actingAs($this->user);

        $category = $this->secondUser->categories->first();
        $response = $this->get(route('categories.edit', $category));

        $response->assertStatus(404);
    }

    public function testOnlyAuthenticatedUsersCanUpdateCategories(): void
    {
        $category = $this->categories->first();
        $response = $this->post(route('categories.store', ['category_id' => $category->id]), [
            'name' => 'Updated Category',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function testAuthenticatedUsersCanUpdateCategories(): void
    {
        $this->actingAs($this->user);

        $categoryData = [
            'name' => 'Updated Category',
        ];

        $category = $this->categories->first();
        $response = $this->post(
            route('categories.store', ['category_id' => $category->id]),
            $categoryData
        );

        $response->assertRedirect(route('categories.index'));

        $categoryData['user_id'] = $this->user->id;
        $this->assertDatabaseHas('categories', $categoryData);
    }

    public function testAUserCannotUpdateAnotherUsersCategory(): void
    {
        $this->actingAs($this->user);

        $category = $this->secondUser->categories->first();
        $response = $this->post(route('categories.store', ['category_id' => $category->id]),[
            'name' => 'Updated Category',
        ]);

        $response->assertRedirect(route('categories.index'));

        // The second user's category should not be updated
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
            'user_id' => $this->secondUser->id,
            'name' => 'Updated Category',
        ]);

        // A new category should be created for the authenticated user
        $this->assertDatabaseHas('categories', [
            'user_id' => $this->user->id,
            'name' => 'Updated Category',
        ]);
    }

    public function testACategoryCannotBeUpdatedWithAnEmptyName(): void
    {
        $this->actingAs($this->user);

        $category = $this->categories->first();
        $response = $this->post(route('categories.store', ['category_id' => $category->id]), [
            'name' => null,
        ]);

        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
        ]);
    }

    public function testCategoryNamesMustBeUniqueForAUserWhenUpdated(): void
    {
        User::all()->each(function ($user) {
            Category::factory()->create([
                    'user_id' => $user->id,
                    'name' => 'Unique Category',
                ]);
        });

        $this->actingAs($this->user);

        $category = $this->categories->first();
        $response = $this->post(route('categories.store', ['category_id' => $category->id]), [
            'name' => 'Unique Category',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'The name has already been taken.',
        ]);
    }

    public function testACategoryCanBeUpdatedWithTheSameName(): void
    {
        $this->actingAs($this->user);

        $category = $this->categories->first();
        $response = $this->post(route('categories.store', ['category_id' => $category->id]), [
            'name' => $category->name,
        ]);

        $response->assertRedirect(route('categories.index'));

        $categoryData['user_id'] = $this->user->id;
        $this->assertDatabaseHas('categories', $categoryData);
    }

    public function testCategoryNamesCannotBeMoreThan40CharactersWhenUpdated(): void
    {
        $this->actingAs($this->user);

        $category = $this->categories->first();
        $response = $this->post(route('categories.store', ['category_id' => $category->id]), [
            'name' => str_repeat('a', 41),
        ]);

        $response->assertSessionHasErrors([
            'name' => 'The name may not be greater than 40 characters.',
        ]);
    }

    public function testACategoryCanHaveTheSameNameAsAnotherUser(): void
    {
        Category::factory()->create([
            'user_id' => $this->secondUser->id,
            'name' => 'User 2 Category',
        ]);

        $this->actingAs($this->user);

        $categoryData = [
            'name' => 'User 2 Category',
        ];

        $category = $this->categories->first();
        $response = $this->post(
            route('categories.store', ['category_id' => $category->id]),
            $categoryData
        );

        $response->assertRedirect(route('categories.index'));

        $categoryData['user_id'] = $this->user->id;
        $this->assertDatabaseHas('categories', $categoryData);
    }
}
