<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = $request->user()->categories;

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('categories.form');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $userCategories = $request->user()->categories();

        $category = $userCategories->where('id', $request->get('category_id'))
            ->first();

        if (!$category) {
            $category = $userCategories->make();
        }

        $category->fill($validated);
        $category->save();

        return redirect(route('categories.index'))->with('status', 'Category Saved');
    }

    public function edit(Request $request, Category $category): View
    {
        abort_if($category->user_id !== $request->user()->id, 404);

        return view('categories.form', [
            'category' => $category,
        ]);
    }
}
