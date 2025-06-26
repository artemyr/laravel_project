<?php

namespace App\Http\Controllers;

use Domain\Catalog\ViewModels\BrandViewModel;
use Domain\Catalog\ViewModels\CategoryViewModel;
use Domain\Product\Models\Product;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class HomeController extends Controller
{
    public function __invoke(): Factory|View|Application
    {
        $products = Product::query()
            ->homePage()
            ->get();

        return view('index', [
            'categories' => CategoryViewModel::make()
                ->homePage(),
            'products' => $products,
            'brands' => BrandViewModel::make()
                ->homePage(),
        ]);
    }
}
