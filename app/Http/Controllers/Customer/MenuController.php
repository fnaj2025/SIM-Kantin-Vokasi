<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function landing()
    {
        $topMenus = MenuItem::where('is_available', true)->with('category')->take(6)->get();
        return view('customer.landing', compact('topMenus'));
    }

    public function index()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $menuItems = MenuItem::where('is_available', true)->with('category')->get();

        return view('customer.index', compact('categories', 'menuItems'));
    }
}
