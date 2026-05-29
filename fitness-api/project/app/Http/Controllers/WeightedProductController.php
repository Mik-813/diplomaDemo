<?php

namespace App\Http\Controllers;

use App\Http\Resources\WeightedProductResource;
use App\Models\WeightedProduct;
use Illuminate\Http\Request;

class WeightedProductController extends Controller
{
    public function index(Request $request)
    {
        $weightedProducts = WeightedProduct::whereHas('product', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->with('product')->get();

        return WeightedProductResource::collection($weightedProducts);
    }

    public function show(Request $request, WeightedProduct $weightedProduct)
    {
        if ($weightedProduct->product->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return new WeightedProductResource($weightedProduct->load('product'));
    }

    public function destroy(Request $request, WeightedProduct $weightedProduct)
    {
        if ($weightedProduct->product->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $weightedProduct->delete();

        return response()->noContent();
    }
}