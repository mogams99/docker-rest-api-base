<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::all();

        return JsonResponseHelper::sanitize(200, 'Product List', ProductResource::collection($products));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:products,name',
            'price' => 'required|numeric|min:1',
            'stock' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return JsonResponseHelper::sanitize(422, 'Validation failed', [], $validator->errors());
        }

        $product = Product::create($request->all());

        return JsonResponseHelper::sanitize(201, 'Product created successfully', new ProductResource($product));
    }

    public function show($id): JsonResponse
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return JsonResponseHelper::sanitize(404, 'Product not found');
        }

        return JsonResponseHelper::sanitize(200, 'Product Detail', new ProductResource($product));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return JsonResponseHelper::sanitize(404, 'Product not found');
        }

        $product->name = $request->name ?? $product->name;
        $product->price = $request->price ?? $product->price;
        $product->stock = $request->stock ?? $product->stock;
        $product->save();

        return JsonResponseHelper::sanitize(200, 'Product updated successfully', new ProductResource($product));
    }

    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return JsonResponseHelper::sanitize(404, 'Product not found');
        }

        $product->delete();

        return JsonResponseHelper::sanitize(200, 'Product deleted successfully', new ProductResource($product));
    }
}
