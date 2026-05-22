<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $activeCategory = $request->string('category')->toString() ?: null;

        $items = Gallery::published()
            ->when($activeCategory, fn ($q) => $q->where('category', $activeCategory))
            ->orderBy('sort_order')
            ->orderByDesc('taken_at')
            ->orderByDesc('id')
            ->paginate(24)
            ->withQueryString();

        // Count per category for the tab badges. Only show tabs that
        // actually have items so the strip doesn't lie.
        $counts = Gallery::published()
            ->selectRaw('category, COUNT(*) as c')
            ->groupBy('category')
            ->pluck('c', 'category')
            ->all();

        return view('pages.gallery.index', compact('items', 'activeCategory', 'counts'));
    }
}
