<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = $request->user()->categories;

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }
}
