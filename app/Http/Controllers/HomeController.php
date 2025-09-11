<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application homepage.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get featured/recent products
        $featuredProducts = Product::where('status', 'available')
            ->with('images')
            ->latest()
            ->take(8)
            ->get();
            
        // Get category groups for the homepage
        $categoryGroups = Category::where('parent_id', null)
            ->with('children')
            ->get();
            
        return view('home', compact('featuredProducts', 'categoryGroups'));
    }
}