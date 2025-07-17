<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // \DB::listen(function ($query) {
        //     \Log::info($query->sql);
        // });
        $user = auth()->user();

        $query = Post::
            with(['user', 'media'])->
            where('published_at', '<=', now())->
            withCount('claps')->
            latest();
        if ($user) {
            $ids = $user->following()->pluck('users.id');
            $query->whereIn('user_id', $ids);
        }
        $posts = $query->simplePaginate(5);
        return view('post.index', [
            "posts" => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = Category::get();
        return view(
            'post.create',
            [
                'categories' => $categories,
            ]
        );

    }
    /**
     * Generate a unique slug for the post title.
     *
     * @param string $title
     * @return string
     */
    // private function generateUniqueSlug($title)
    // {
    //     $slug = Str::slug($title);
    //     $originalSlug = $slug;
    //     $count = 1;

    //     while (Post::where('slug', $slug)->exists()) {
    //         $slug = $originalSlug . '-' . $count;
    //         $count++;
    //     }

    //     return $slug;
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostCreateRequest $request)
    {

        $data = $request->validated();
        // $image = $data['image'];
        // unset($data['image']);
        $data['user_id'] = auth()->id();// or auth()->user()->id; or Auth::id();
        // $data['slug'] = $this->generateUniqueSlug($data['title']);
        // $imagePath = $image->store('posts', 'public');
        // $data['image'] = $imagePath;



        $post = Post::create($data);

        $post->addMediaFromRequest('image')
            ->toMediaCollection();

        return redirect()->route('dashboard');
        //
    }



    /**
     * Display the specified resource.
     */
    public function show(string $username, Post $post)
    {
        //
        return view('post.show', [
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        if ($post->user_id !== Auth()->user()->id) {
            abort(403);
        }
        $categories = Category::get();
        return view('post.edit', [
            'post' => $post,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        if ($post->user_id !== Auth()->user()->id) {
            abort(403);

            $data = $request->validated();

            $post->update($data);

            if ($data['image'] ?? false) {
                $post->addMediaFromRequest('image')
                    ->toMediaCollection();
            }

            return redirect()->route('myPosts');
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
        if ($post->user_id != auth()->id()) {
            abort(403);
        }
        $post->delete();

        return redirect()->route('dashboard');
    }

    public function myPosts()
    {
        $user = auth()->user();
        $posts = $user->posts()
            ->with(['user', 'media'])
            ->withCount('claps')
            ->latest()->simplePaginate(5);
        return view('post.index', [
            'posts' => $posts,
        ]);
    }

    public function category(Category $category)
    {
        $user = auth()->user();
        $query = $category->posts()
            ->with(['user', 'media'])
            ->where('published_at', '<=', now())
            ->withCount('claps')
            ->latest();

        if ($user) {
            $ids = $user->following()->pluck('users.id');
            $query->whereIn('user_id', $ids);
        }

        $posts = $query->simplePaginate(5);

        return view('post.index', [
            'posts' => $posts,
        ]);
    }
}