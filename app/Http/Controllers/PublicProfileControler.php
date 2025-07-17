<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PublicProfileControler extends Controller
{
    public function show(User $user)
    {
        $posts = $user->posts()->
        where('published_at','<=',now())
        ->latest()->paginate();
        return view('profile.show', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }
}
