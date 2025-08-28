<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    
    public function mySubscriptions()
    {
        return Auth::user()->subscriptions()->select('id','name','email')->get();
    }

   
    public function mySubscribers()
    {
        return Auth::user()->subscribers()->select('id','name','email')->get();
    }

   
    public function subscribe(User $user)
    {
        $authUser = Auth::user();

        if ($authUser->id === $user->id) {
            return response()->json(['message' => 'You cannot subscribe to yourself'], 400);
        }

        if (!$authUser->subscriptions()->where('subscribed_to_id', $user->id)->exists()) {
            $authUser->subscriptions()->attach($user->id);
        }

        return response()->json(['message' => 'Subscribed to user']);
    }

    public function unsubscribe(User $user)
    {
        $authUser = Auth::user();
        $authUser->subscriptions()->detach($user->id);

        return response()->json(['message' => 'Unsubscribed']);
    }
}
