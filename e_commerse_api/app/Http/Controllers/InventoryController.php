<?php
namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
   //merr variantet per 1 produkt specifik
    public function getByProduct(ProductVariant $productId)
    {
        $variants = ProductVariant::where('product_id', $productId)
            ->available()        //  scopeAvailable() nga modeli
            ->orderBy('size')
            ->get();

        return response()->json($variants, 200);
    }

   //ruan te ri 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'     => 'required|exists:product,id',
            'size'           => 'required|string|max:20',
            'color'          => 'required|string|max:50',
            'color_hex'      => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'stock_quantity' => 'required|integer|min:0',
            'price_cents'    => 'required|integer|min:0',
            'price_override' => 'nullable|integer|min:0',                      
            'sku'            => 'nullable|string|unique:product_variant,sku',  
        ]);

        $variant = ProductVariant::create([
            ...$validated,
            'is_available' => $validated['stock_quantity'] > 0,
        ]);

        return response()->json([
            'message' => 'Variant created!',
            'variant' => $variant,
        ], 201);
    }

   //varianti me cmimin final
    public function show(ProductVariant $id)
    {
        $variant = ProductVariant::with('product')->findOrFail($id);

        return response()->json([
            'variant'     => $variant,
            'final_price' => $variant->final_price,    //  fusha nga modeli
            'in_stock'    => $variant->isInStock(),     //  metoda nga modeli
        ]);
    }

  //updeton variantin
    public function update(Request $request, $id)
    {
        $variant = ProductVariant::findOrFail($id);

        $validated = $request->validate([
            'size'           => 'sometimes|string|max:20',
            'color'          => 'sometimes|string|max:50',
            'color_hex'      => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'stock_quantity' => 'sometimes|integer|min:0',
            'price_cents'    => 'sometimes|integer|min:0',
            'price_override' => 'nullable|integer|min:0',
            'sku'            => 'nullable|string|unique:product_variant,sku,' . $id,
        ]);

        if (isset($validated['stock_quantity'])) {
            $validated['is_available'] = $validated['stock_quantity'] > 0;
        }

        $variant->update($validated);

        return response()->json([
            'message' => 'Variant updated!',
            'variant' => $variant,
        ]);
    }

   //pasi behet porosia ulet stocku
    public function decrementStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::findOrFail($id);

        if (!$variant->isInStock() || $variant->stock_quantity < $request->quantity) {
            return response()->json([
                'message'   => 'Stoku i pamjaftueshëm.',
                'available' => $variant->stock_quantity,
            ], 422);
        }

        $variant->decrement('stock_quantity', $request->quantity);
        $variant->refresh(); // merr vleren ne DB pas dekrementit

        if ($variant->stock_quantity === 0) {
            $variant->update(['is_available' => false]);
        }

        return response()->json([
            'message'   => 'Stock updated.',
            'remaining' => $variant->stock_quantity,
            'in_stock'  => $variant->isInStock(),
        ]);
    }

   //fshi variantin
    public function destroy($id)
    {
        $variant = ProductVariant::findOrFail($id);

        // nuk lejon fshirjen nese ka porosi aktive 
        if ($variant->orderItems()->exists()) {
            return response()->json([
                'message' => 'This variant can not be deleted(It is ordered).',
            ], 409);
        }

        $variant->delete();

        return response()->json(['message' => 'Variant deleted.']);
    }
}
