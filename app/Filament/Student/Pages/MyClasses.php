<?php

namespace App\Filament\Student\Pages;

use App\Models\Enrollment;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class MyClasses extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    protected static ?int $navigationSort = 40;
    protected string $view = 'filament.student.pages.my-classes';

    public static function getNavigationLabel(): string { return __('My Classes'); }
    public function getTitle(): string                  { return __('My Classes'); }

    public function getEnrollments()
    {
        return Enrollment::with([
                'course.category',
                'course.chapters' => fn ($q) => $q->where('is_published', true)
                    ->with(['materials' => fn ($qq) => $qq->orderBy('sort_order')]),
            ])
            ->where('user_id', auth()->id())
            ->orderByDesc('last_activity_at')
            ->get();
    }
}
