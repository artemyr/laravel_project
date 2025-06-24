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

        $also = Product::query()
            ->where(function ($q) use ($product) {
                $q->whereIn('id', session('also'))
                    ->where('id','!=', $product->id);
            })
            ->get();

        return view('product.show', compact('product','options','also'));
    }
}
