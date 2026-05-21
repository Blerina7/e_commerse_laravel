<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class FirstController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return  Post::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data=$request-> validate([
            'title' =>'required|string|min:2',
            'body' =>['required|string|min:2']
        ]);
        $data['author_id']=1;
        $post= Post::create($data);
        return response() ->json([$post,201]);
          
            
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response() ->json($post)
        ;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $data=$request-> validate([
            'title' =>'required|string|min:2',
            'body' =>['required|string|min:2']
        ]);

        $post->update($data);

        return $post;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post ->delete();
        return response()->noContent();
    }
}
