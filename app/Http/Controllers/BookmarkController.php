<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return $user->bookmarks()->with('user', 'category')->latest()->get();
    }

    public function store(Post $post)
    {
        $user = Auth::user();

        if (!$user->bookmarks()->where('post_id', $post->id)->exists()) {
            $user->bookmarks()->attach($post->id);
        }

        return response()->json(['message' => 'Post bookmarked']);
    }

    public function destroy(Post $post)
    {
        $user = Auth::user();
        $user->bookmarks()->detach($post->id);

        return response()->json(['message' => 'Bookmark removed']);
    }
}
