<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// import model
use App\Models\Post;
// import resource
use App\Http\Resources\PostResource;
class PostController extends Controller
{
    public function index()
    {
        // get all posts
        $posts = Post::Latest()->paginate(5);
        // return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $posts);
    }
}
