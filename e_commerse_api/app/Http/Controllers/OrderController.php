<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductVariant; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

       public function index(Request $request)
    {
        $orders = Order::with('products')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();
        return response()->json($orders, 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => ['required', 'exists:product_variant,id'], 
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $items = $data['items'];

        return DB::transaction(function () use ($request, $items) {
            $variantIds = collect($items)->pluck('product_variant_id')->unique()->values();

          
            $variants = ProductVariant::with('product')
                ->whereIn('id', $variantIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

       
            foreach ($items as $item) {
                $variant = $variants->get($item['product_variant_id']);

                if (!$variant) {
                    return response()->json(['message' => 'The product was not found.'], 404);
                }

                $requestedQty = (int) $item['quantity'];

            
                if ($requestedQty > (int) $variant->stock_quantity) { 
                    return response()->json([
                        'message' => "Not enough quantity fot {$variant->product->name} (Number: {$variant->size}). In stock: {$variant->stock_quantity}"
                    ], 422);
                }
            }

            $totalCents = 0;
            $orderItems = [];
            $now = now();

            
            foreach ($items as $item) {
                $variant = $variants->get($item['product_variant_id']);
                $qty = (int) $item['quantity'];

                $price = $variant->product->price_cents ?? ($variant->product->base_price * 100); 
                $totalCents += $price * $qty;

                $orderItems[] = [
                    'order_id' => null, 
                    'product_id' => $variant->product_id, 
                    'quantity' => $qty,
                    'unit_price_cents' => $price,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

               
                $variant->decrement('stock_quantity', $qty); 
            }

          
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_cents' => $totalCents,
                'status' => 'pending',
            ]);

            foreach ($orderItems as &$orderItem) {
                $orderItem['order_id'] = $order->id;
            }

            
            DB::table('order_product')->insert($orderItems);

            return response()->json([
                'message' => 'The order was successful!',
                'order_id' => $order->id,
            ], 201);
        });
    }


      public function destroy(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not an admin.'], 403);
        }

        $order->delete();

        return response()->json(['message' => 'The order was deleted.'], 200);
    }
}