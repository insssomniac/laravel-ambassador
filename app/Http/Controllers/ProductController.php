<?php

namespace App\Http\Controllers;

use App\Events\ProductUpdateEvent;
use App\Models\Product;
use App\Services\ProductService;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Str;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::create($request->only('title', 'description', 'image', 'price'));

        event(new ProductUpdateEvent);

        return response($product, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return Product
     */
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $product->update($request->only('title', 'description', 'image', 'price'));

        event(new ProductUpdateEvent);

        return response($product, Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        event(new ProductUpdateEvent);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function frontend()
    {
        if ($products = Cache::get('products_frontend')) {
            return $products;
        }

        $products = Product::all();

        Cache::set('products_frontend', $products, 60 * 30); // 30 min

        return $products;
    }

    public function backend(Request $request)
    {
        $page = $request->input('page', 1);

        /** @var Collection $products */
        $products = Cache::remember('products_backend', 60 * 30, fn() => Product::all());

        if ($search = $request->input('s')) {
            $products = ProductService::filterProducts($products, $search);
        }

        if ($sort = $request->input('sort')) {
            $products = ProductService::sortProducts($products, $sort);
        }

        $total = $products->count();

        return [
            'data' => $products->forPage($page, 9)->values(),
            'meta' => [
                'total' => $total,
                'page' => $page,
                'last_page' => ceil($total / 9)
            ]
        ];
    }


}
