<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Thread;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        // Get threads in this category
        $threads = Thread::where('category_id', $category->id)
            ->with(['user', 'category', 'forum'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get subcategories
        $subcategories = Category::where('parent_id', $category->id)
            ->orderBy('order')
            ->get();
        
        return view('categories.show', compact('category', 'threads', 'subcategories'));
    }
}
