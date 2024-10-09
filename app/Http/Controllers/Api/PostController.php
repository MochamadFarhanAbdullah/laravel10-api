<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// import model
use App\Models\Post;

// import resource
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        // get all posts
        $posts = Post::latest()->paginate(5);
        // return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $posts);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),442);
        }

        // upload image
        $image = $request->file('image');
        $image -> storeAs('public/posts', $image->hashName());
        
        if (!$request->has('content')) {
            return response()->json(['error' => 'Content is missing'], 400);
        }
        
        // create post
        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content, // rk eror bjir
        ]);

        // return response
        return new PostResource(true, 'Data Post Berhasil Ditambahkan', $post);
    }
}
