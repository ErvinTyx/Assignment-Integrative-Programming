<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCreateRequest;
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
        with(['user','media'])->
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
    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostCreateRequest $request)
    {

        $data = $request->validated();
        // $image = $data['image'];
        // unset($data['image']);
        $data['user_id'] = auth()->id();// or auth()->user()->id; or Auth::id();
        $data['slug'] = $this->generateUniqueSlug($data['title']);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }

    public function category(Category $category)
    {
        $posts = $category->posts()
        ->with(['user','media'])
        ->withCount('claps')
        ->latest()->simplePaginate(5);
        return view('post.index', [
            'posts' => $posts,
        ]);
    }
}