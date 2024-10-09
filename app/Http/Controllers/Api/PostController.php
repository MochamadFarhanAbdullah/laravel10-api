<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// import model
use App\Models\Post;

// import resource
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Validator;
// import storage (data image)
use Illuminate\Support\Facades\Storage;

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

    public function show($id){
        // find post by id
        $post = Post::find($id);

        // return single post as a resource
        return new PostResource(true,'Detail Data Post', $post);
    }

    public function update(Request $request, $id)
    {
        // define validation rules
        $validator = Validator::make($request->all(),
        [
            'title'=> 'required',
            'content' => 'required',
        ]);
        
        // check if validation fails
        if($validator->fails()){
            return response()->json($validator->errors(),442);
        }

        // findpost by id
        $post = Post::find($id);
        
        // check if image is not empty
        if($request->hasFile('image')){
            // upload image
            $image = $request->files('image');
            $image-> storeAs('public/posts', $image->hashName());

            // delete old image
            Storage::delete('public/posts/'. $post->image);

            // update post with new image
            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content,
            ]);
        } else{
            // update post without image
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }
        return new PostResource(true,'Data Post Berhasil Dirubah', $post);
    }
}
