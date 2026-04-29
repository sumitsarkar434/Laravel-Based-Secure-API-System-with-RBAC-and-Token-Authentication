<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * List posts (paginated). Users see only their own; admins see all.
     */
    public function index(Request $request): JsonResponse
    {
        $posts = $request->user()->can('admin')
            ? Post::with('user')->latest()->paginate(15)
            : Post::where('user_id', $request->user()->id)->latest()->paginate(15);

        return response()->json($posts);
    }

    /**
     * Create a post.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $post = Post::create([
            'user_id' => $request->user()->id,
            'title'   => $request->title,
            'body'    => $request->body,
        ]);

        return response()->json([
            'message' => 'Post created.',
            'post'    => $post->load('user'),
        ], 201);
    }

    /**
     * Show a single post.
     */
    public function show(Post $post): JsonResponse
    {
        $this->authorize('view', $post);
        return response()->json($post->load('user'));
    }

    /**
     * Update a post.
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);
        $post->update($request->validated());

        return response()->json([
            'message' => 'Post updated.',
            'post'    => $post->fresh('user'),
        ]);
    }

    /**
     * Delete a post.
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        $this->authorize('delete', $post);
        $post->delete();

        return response()->json(['message' => 'Post deleted.']);
    }
}
