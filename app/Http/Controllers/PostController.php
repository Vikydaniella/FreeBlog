<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Helpers\HttpStatus;
use App\Http\Requests\PostRequest;
use App\Jobs\SendPostNotification;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
    * Store a newly created post.
    * @param  \App\Http\Requests\PostRequest  $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function store(PostRequest $request)
    {
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'status' => $request->status,
            'author_id' => $request->author_id
            ]);

        if ($post) {
            $user = User::find(1);
            SendPostNotification::dispatch($user, $post);
            return response()->json([
                'status' => HttpStatus::SUCCESS_CREATED,
                'message' => 'Post created Successfully',
                'data' => $post
            ]);
        }  
            return response()->json([
                'status' => HttpStatus::UNPROCESSABLE_ENTITY,
                'message' => 'Can not create post'
            ]);
    }

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
        $post = Post::find($id);
       if($post){
        return response()->json([
            'status' => HttpStatus::SUCCESS_CREATED,
            'message' => 'Successful',
            'data' => new PostResource($post)
        ]);
        }
        return response()->json([
            'status' => HttpStatus::UNPROCESSABLE_ENTITY,
            'message' => 'Can not see this post'
        ]);
    }

    /**
    * Get all posts authored by the currently authenticated user (Author's dashboiard)
    * and returns them as a JSON response.
    * @return \Illuminate\Http\JsonResponse
    */
    public function dashboard()
    {
        $posts = Post::where('author_id', Auth::id())->get();
        if($posts){
            return response()->json([
                'status' => HttpStatus::SUCCESS_CREATED,
                'message' => 'Successful',
                'data' => ['posts' => $posts]
            ]);
            }
            return response()->json([
                'status' => HttpStatus::UNPROCESSABLE_ENTITY,
                'message' => 'Can not see these post'
            ]);
    }

    /**
    * Update tahe specified post.
    * @param  \App\Http\Requests\PostRequest  $request
    * @param  int  $id
    * @return \Illuminate\Http\JsonResponse|
    */
         
    public function update(PostRequest $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'status' => HttpStatus::UNPROCESSABLE_ENTITY,
                'message' => 'Post not found.'
            ]);
        }
        if ($post->author_id != Auth::id()) {
            return response()->json([
                'status' => HttpStatus::UNPROCESSABLE_ENTITY,
                'message' => 'You are not authorized to edit this post.'
            ]);
        }
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'status' => $request->status,
            'author_id' => $request->author_id
        ]);
        return response()->json([
            'status' => HttpStatus::SUCCESS_CREATED,
            'message' => 'Post updated successfully',
            'data' => new PostResource($post)
        ]);
    }

    /**
    * Delete a specified post.
    * @param  int  $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function destroy(Post $post, $id)
{
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'status' => HttpStatus::UNPROCESSABLE_ENTITY,
                'message' => 'Post not found.'
            ]);
        }
        if ($post->author_id != Auth::id() || $post->status != 'draft') {
            return response()->json([
                'status' => HttpStatus::UNPROCESSABLE_ENTITY,
                'message' => 'You are not authorized to delete this post or the post is not in draft status.'
            ]);
        }
        $post->delete();

        return response()->json([
            'status' => HttpStatus::SUCCESS_CREATED,
            'message' => 'Post deleted successfully'
        ]);
}
}
