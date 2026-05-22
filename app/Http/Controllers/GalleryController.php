<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Album list — shows each Gallery as a card with cover + item count.
     */
    public function index(Request $request): View
    {
        $albums = Gallery::published()
            ->withCount(['publishedItems as items_count'])
            ->with(['publishedItems' => fn ($q) => $q->limit(1)])  // for cover fallback
            ->having('items_count', '>', 0)                         // hide empty albums
            ->orderBy('sort_order')
            ->orderByDesc('taken_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('pages.gallery.index', compact('albums'));
    }

    /**
     * Single album — grid of all its items with a lightbox.
     */
    public function show(string $slug): View
    {
        $album = Gallery::published()
            ->where('slug', $slug)
            ->with(['publishedItems'])
            ->firstOrFail();

        return view('pages.gallery.show', compact('album'));
    }
}
