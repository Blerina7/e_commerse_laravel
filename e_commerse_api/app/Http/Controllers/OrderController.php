<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
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
            'items.*.product_id' => ['required', 'exists:products,id'], // Ndryshuar nga 'uuid' në 'exists' nëse përdor ID normale (1,2,3)
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $items = $data['items'];

        return DB::transaction(function () use ($request, $items) {
            $productIds = collect($items)->pluck('product_id')->unique()->values();

           
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

           
            foreach ($items as $item) {
                $product = $products->get($item['product_id']);

                if (!$product) {
                    return response()->json(['message' => 'Produkti nuk u gjet.'], 404);
                }

                $requestedQty = (int) $item['quantity'];

               
                if ($requestedQty > (int) $product->stock) { 
                    return response()->json([
                        'message' => "Not enough quantity{$product->name}.In stock: {$product->stock}"
                    ], 422);
                }
            }

            $totalCents = 0;
            $orderItems = [];
            $now = now();

           
            foreach ($items as $item) {
                $product = $products->get($item['product_id']);
                $qty = (int) $item['quantity'];

                
                $price = $product->price_cents ?? ($product->base_price * 100); 
                $totalCents += $price * $qty;

                $orderItems[] = [
                    'order_id' => null, 
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price_cents' => $price,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

               
                $product->decrement('stock', $qty); 
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
                'message' => 'Porosia u krye me sukses!',
                'order_id' => $order->id,
            ], 201);
        });
    }

    
    public function destroy(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'I paautorizuar.'], 403);
        }

        $order->delete();

        return response()->json(['message' => 'Porosia u fshi me sukses.'], 200);
    }
}