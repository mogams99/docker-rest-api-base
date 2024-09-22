<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with('orderProducts.product')->get();

        return JsonResponseHelper::sanitize(200, 'Order List', OrderResource::collection($orders));
    }

    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $order = Order::create([
                'code' => 'INV-' . Str::random(10),
            ]);

            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);

                if (is_null($product)) {
                    return JsonResponseHelper::sanitize(404, 'Product not found');
                }

                if ($product->stock < $productData['quantity']) {
                    return JsonResponseHelper::sanitize(400, 'Product out of stock');
                }

                $remainingStock = $product->stock - $productData['quantity'];
                
                $product->stock = $remainingStock;
                $product->sold += $productData['quantity'];
                $product->save();

                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $productData['id'],
                    'quantity' => $productData['quantity'],
                    'remain_stock' => $remainingStock,
                ]);
            }

            DB::commit();

            return JsonResponseHelper::sanitize(200, 'Order created', new OrderResource($order->load('orderProducts.product')));
        } catch (\Exception $e) {
            DB::rollBack();

            return JsonResponseHelper::sanitize(500, 'Failed to create order', [], $e->getMessage());
        }
    }

    public function show($id): JsonResponse
    {
        $order = Order::with('orderProducts.product')->find($id);

        if (is_null($order)) {
            return JsonResponseHelper::sanitize(404, 'Order not found');
        }

        return JsonResponseHelper::sanitize(200, 'Order Detail', new OrderResource($order));
    }

    public function destroy($id): JsonResponse
    {
        try {
            DB::beginTransaction();

            // ? cari order berdasarkan ID
            $order = Order::with('orderProducts.product')->find($id);

            if (is_null($order)) {
                return JsonResponseHelper::sanitize(404, 'Order not found');
            }

            // ? mengembalikan stok produk sebelum menghapus order
            foreach ($order->orderProducts as $orderProduct) {
                $product = $orderProduct->product;

                if ($product) {
                    // ? kembalikan stok dan kurangi sold
                    $product->stock += $orderProduct->quantity;
                    $product->sold -= $orderProduct->quantity;
                    $product->save();
                }

                // ? hapus hubungan order dan produk
                $orderProduct->delete();
            }

            // ? hapus order
            $order->delete();

            DB::commit();

            return JsonResponseHelper::sanitize(200, 'Order deleted successfully', new OrderResource($order->refresh()));
        } catch (\Exception $e) {
            DB::rollBack();

            return JsonResponseHelper::sanitize(500, 'Failed to delete order', [], $e->getMessage());
        }
    }
}
