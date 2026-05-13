<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $posts = Post::query()
            ->published()
            ->with('category', 'author')
            ->when($request->string('category')->isNotEmpty(),
                fn ($q, $c) => $q->whereHas('category', fn ($q) => $q->where('slug', $request->string('category'))))
            ->when($request->string('q')->isNotEmpty(), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(function ($q) use ($term) {
                    $q->where('title->id', 'like', $term)
                      ->orWhere('title->en', 'like', $term)
                      ->orWhere('title->ja', 'like', $term)
                      ->orWhere('excerpt->id', 'like', $term)
                      ->orWhere('excerpt->en', 'like', $term);
                });
            })
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('pages.blog.index', [
            'posts'      => $posts,
            'categories' => PostCategory::orderBy('sort_order')->withCount('posts')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::published()->where('slug', $slug)->with('category', 'author')->firstOrFail();

        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->when($post->post_category_id, fn ($q, $cid) => $q->where('post_category_id', $cid))
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('pages.blog.show', compact('post', 'related'));
    }
}
