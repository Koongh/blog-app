<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewPostNotification;

class PostController extends Controller
{
    
    public function index(Request $request)
    {
        try {
            $query = Post::with('user', 'category')->latest();

            
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            
            if ($request->has('category')) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('name', $request->category);
                });
            }

            $perPage = $request->get('per_page', 10);

            return response()->json($query->paginate($perPage));
        } catch (\Exception $e) {
            Log::error("Post Index Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch posts'], 500);
        }
    }

    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'title'       => 'required|string|max:255',
                'content'     => 'required|string',
            ]);

            $post = Post::create([
                'user_id'     => Auth::id(),
                'category_id' => $validated['category_id'],
                'title'       => $validated['title'],
                'content'     => $validated['content'],
            ]);

            
            $subscribers = Auth::user()->subscribers;
            foreach ($subscribers as $subscriber) {
                $subscriber->notify(new NewPostNotification($post));
            }

            return response()->json([
                'message' => 'Post created successfully',
                'post'    => $post->load('user', 'category'),
            ], 201);
        } catch (\Exception $e) {
            Log::error("Post Store Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to create post'], 500);
        }
    }

    public function show(Post $post)
    {
        try {
            return response()->json($post->load('user', 'category'));
        } catch (\Exception $e) {
            Log::error("Post Show Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch post'], 500);
        }
    }

    public function update(Request $request, Post $post)
    {
        try {
            $this->authorizeAccess($post);

            $validated = $request->validate([
                'category_id' => 'sometimes|exists:categories,id',
                'title'       => 'sometimes|string|max:255',
                'content'     => 'sometimes|string',
            ]);

            $post->update($validated);

            return response()->json([
                'message' => 'Post updated successfully',
                'post'    => $post->load('user', 'category'),
            ]);
        } catch (\Exception $e) {
            Log::error("Post Update Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to update post'], 500);
        }
    }

    
    public function destroy(Post $post)
    {
        try {
            $this->authorizeAccess($post);

            $post->delete();

            return response()->json(['message' => 'Post deleted successfully']);
        } catch (\Exception $e) {
            Log::error("Post Destroy Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to delete post'], 500);
        }
    }

    
    public function userPosts(User $user, Request $request)
    {
        try {
            $query = $user->posts()->with('category')->latest();

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            $perPage = $request->get('per_page', 10);

            return response()->json($query->paginate($perPage));
        } catch (\Exception $e) {
            Log::error("User Posts Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch user posts'], 500);
        }
    }

    
    private function authorizeAccess(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}
