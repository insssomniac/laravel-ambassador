<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Str;

class ProductService
{
    public static function sortProducts(Collection $products, string $sort)
    {
        if ($sort == 'asc') {
            return $products->sortBy([
                fn ($a, $b) => $a['price'] <=> $b['price']
            ]);
        }

        if ($sort == 'desc') {
            return $products->sortBy([
                fn ($a, $b) => $b['price'] <=> $a['price']
            ]);
        }
    }

    public static function filterProducts(Collection $products, string $filter)
    {
        return $products->filter(
            fn(Product $product) => Str::contains($product->title, $filter) || Str::contains($product->description, $filter)
        );
    }

}
