<?php

namespace App\Http\Controllers;

use App\Http\Resources\LinkResource;
use App\Models\Link;
use App\Models\LinkProduct;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index($id)
    {
        $links = Link::whereUserId($id)->with('orders')->get();

        return LinkResource::collection($links);
    }

    public function store(Request $request)
    {
        $link = Link::create([
            'user_id' => $request->user()->id,
            'code' => \Str::random(12)
        ]);

        foreach ($request->input('products') as $product_id) {
            LinkProduct::create([
                'link_id' => $link->id,
                'product_id' => $product_id
            ]);
        }

        return $link;
    }

    public function show($code)
    {
        return Link::whereCode($code)->with('user', 'products')->first();
    }

}
