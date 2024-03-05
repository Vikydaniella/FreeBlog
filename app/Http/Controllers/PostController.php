<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Jobs\SendPostNotification;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;


class PostController extends Controller
{
    /**
    * Store a newly created post.
    * @param  \App\Http\Requests\PostRequest  $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function store(PostRequest $request)
    {$validatedData = $request->validated();
        $authorId = auth()->id();
        $validatedData['author_id'] = $authorId;
        $post = Post::create($validatedData);
        SendPostNotification::dispatch($post, $request->content);
        return response()->json($post, HttpStatus::SUCCESS_CREATED);}

    /**
    * Display a listing of all posts.
    * @return \Illuminate\Http\Resources\Json\PostResourceCollection
    */
    public function index()
    {
        $posts = Post::with('author')->get();
        return PostResource::collection($posts);
    }

    /**
    * Display a specified post.
    * @param  int  $id
    * @return \App\Http\Resources\PostResource
    */
    public function show($id)
    {
        $post = Post::findOrFail($id);
        return new PostResource($post);
    }

    /**
    * Get all posts authored by the currently authenticated user (Author's dashboiard)
    * and returns them as a JSON response.
    * @return \Illuminate\Http\JsonResponse
    */
    public function dashboard()
    {
        $posts = Post::where('author_id', Auth::id())->get();
        return response()->json(['posts' => $posts]);
    }

    /**
    * Update tahe specified post.
    * @param  \App\Http\Requests\PostRequest  $request
    * @param  int  $id
    * @return \Illuminate\Http\JsonResponse|
    */
    public function update(PostRequest $request, $id)
    {
        $post = Post::findOrFail($id);
        if ($post->author_id != Auth::id()) {
            return response()->json(['error' => 'You are not authorized to edit this post.'], HttpStatus::UNPROCESSABLE_ENTITY);
        }
        $validatedData = $request->validated();
        $post->update($validatedData);
        return new PostResource($post);
    }

    /**
    * Delete a specified post.
    * @param  int  $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if ($post->author_id != Auth::id() || $post->status != 'draft') {
            return response()->json(['error' => 'You are not authorized to delete this post.'], HttpStatus::UNPROCESSABLE_ENTITY);
        }
        $post->delete();
        return response()->json(['message' => 'Post deleted successfully.'], HttpStatus::SUCCESS_CREATED);
    }
}
