<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class ProductController extends Controller
{
    public function __invoke(Product $product): Factory|View|Application
    {
        $product->load(['optionValues.option']);

        $options = $product->optionValues->mapToGroups(function ($item) {
            return [$item->option->title => $item];
        });

        session()->put('also.' . $product->id, $product->id);

        $viewedProductsIds = session()->get('also');
        unset($viewedProductsIds[$product->id]);

        $viewedProducts = Product::query()
            ->whereIn('id', $viewedProductsIds)
            ->get();

        return view('product.show', compact('product','options','viewedProducts'));
    }
}
