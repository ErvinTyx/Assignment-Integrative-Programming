<?php

namespace App\Http\Controllers;

use app\Models\User;

class FollowerController extends Controller
{
    //
    public function followUnfollow(User $user)
    {
        // Toggle the follow status between the authenticated user and the specified user
        $user->followers()->toggle(auth()->user());

        return response()->json([
            'followersCount' => $user->followers()->count(),
        ]);
    }
}
