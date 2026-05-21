<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $items = Gallery::published()
            ->orderBy('sort_order')
            ->orderByDesc('taken_at')
            ->orderByDesc('id')
            ->paginate(24);

        return view('pages.gallery.index', compact('items'));
    }
}
