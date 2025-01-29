<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Domain\Catalog\Models\Brand;
use Domain\Catalog\Models\Category;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class HomeController extends Controller
{
    public function __invoke(): Factory|View|Application
    {
        $categories = Category::query()
            ->homePage()
            ->get();

        $products = Product::query()
            ->homePage()
            ->get();

        $brands = Brand::query()
            ->homePage()
            ->get();


        return view('index', compact('categories', 'products', 'brands'));
    }
}
