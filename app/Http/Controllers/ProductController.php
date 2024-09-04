<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function fetchCategories()
    {
        $categories = Category::get();
        return response()->json($categories);
    }

    public function fetchBrands()
    {
        $brands = Brand::get();
        return response()->json($brands);
    }
}
