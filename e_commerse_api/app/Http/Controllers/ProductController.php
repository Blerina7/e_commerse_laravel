<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    //te gjithe produktet
    public function index()
    {
        $query=Product::query();
       


        $products= Product::paginate(20);
    
        return response() ->json($products,200 );
    }

   //ruaj ne db
    public function store(Request $request)
    {
        $data=$request-> validate([
            'name' => 'required|string|min:3',
            'slug' => 'required|string|min:3|unique:products,slug',
            'description' =>'string|min:3|nullable',
            'base_price' =>'numeric|min:0|required',
            'sale_price' =>'numeric|min:0|nullable',
            'gender' =>'string|required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $data['photo'] = $path;
        }

        $product= Product::create($data);
        return response() ->json([$product],201);
          
    }

   //trego 1 produkt specifik
    public function show(Product $product)
    {
       return response() ->json($product->load(['variants', 'images', 'brand', 'category']),200 );
    }

   //updeton 1 produkt specifik
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3',
            'slug' => 'required|string|unique:products,slug,' . $product->id,
            'description' =>'string|min:3',
            'base_price' =>'numeric|min:0',
            'sale_price' =>'numeric|min:0',
            'gender' =>'string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
        ]  
        );

        
        

        if ($request->hasFile('photo')) {
            if ($product->photo) {
                Storage::disk('public')->delete($product->photo);
            }
            $path = $request->file('photo')->store('photos', 'public');

            $data['photo'] = $path;
        } else {
            unset($data['photo']);
        }

        

        $product ->update($data);
        return response()->json([
        'message'  => 'Product updated',  
        'product'=>$product
        ]);
    }

   //delete the product
    public function destroy(Product $product)
    { 
        if ($product->photo) {
            Storage::disk('public')->delete($product->photo);
        }

        $product->delete();
        return response()->json([
        'message'=>'Product was deltetd with success*'
        ],204);
    }
}
