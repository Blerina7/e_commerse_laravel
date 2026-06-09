<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    //produktet=search
    public function index(Request $request)
{
    $query = Product:: query(); //with([ 'brand','category','images']);  kjo eshte per relationships

    
    if ($request->filled('search')) {
      $terms = collect(preg_split('/[\s,]+/', trim($request->input('search')), -1, PREG_SPLIT_NO_EMPTY))
        ->map(fn($term) => mb_strtolower($term))
        ->values();

      $query->where(function($q) use ($terms) {
        foreach ($terms as $term) {
            $q->where(function($sub) use ($term) {
                $sub->where('name', 'LIKE', "%$term%")
                    ->orWhere('description', 'LIKE', "%$term%");
            });
        }
      });
    }

    if ($request->filled('min_price')) {
        $query->where('base_price', '>=', $request->input('min_price'));
    }

    if ($request->filled('max_price')) {
        $query->where('base_price', '<=', $request->input('max_price'));
    }
    
    if ($request->filled('gender')){
        $query ->where('gender','LIKE', '%' . $request->input('gender') . '%');
    }


    $products = $query->paginate(20);
    return response()->json($products, 200);
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

        //$product= Product::create($data);
        //return response() ->json([$product],201);
     
        $product = new \App\Models\Product();
    
  
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->gender = $request->gender;
        $product->base_price = $request->base_price;
  
        $product->save();

        return response()->json($product, 201);
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
        ],200);
    }
}
