<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('when', 'upcoming')->toString();
        $base = Event::query()->published()->with('category');

        $events = match ($filter) {
            'past' => $base->past()->orderByDesc('starts_at'),
            default => $base->upcoming()->orderBy('starts_at'),
        };

        return view('pages.event.index', [
            'events'     => $events->paginate(9)->withQueryString(),
            'categories' => EventCategory::orderBy('sort_order')->withCount('events')->get(),
            'filter'     => $filter,
        ]);
    }

    public function show(string $slug): View
    {
        $event = Event::published()->where('slug', $slug)->with('category')->firstOrFail();

        return view('pages.event.show', compact('event'));
    }
}
