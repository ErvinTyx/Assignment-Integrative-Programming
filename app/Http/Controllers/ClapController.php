<?php

namespace App\Http\Controllers;
use App\Models\Post;

use Illuminate\Http\Request;

class ClapController extends Controller
{
    public function clap(Post $post)
    {
        // Check if the user has already clapped for the post
        $hasClapped = auth()->user()->hasClapped($post);

        if ($hasClapped) {
            // If the user has already clapped, remove the clap
            $post->claps()->where('user_id', auth()->id())->delete();
        } else {
            $post->claps()->create([
                'user_id' => auth()->id(),
            ]);
        }
        return response()->json([
            'clapsCount' => $post->claps()->count(),
        ]);
    }
}
