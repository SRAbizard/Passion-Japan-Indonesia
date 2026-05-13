<?php

namespace App\Filament\Student\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected string $view = 'filament.student.widgets.welcome-widget';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        /** @var User $user */
        $user = auth()->user();

        $profileFields = [
            'name'        => filled($user?->name),
            'email'       => filled($user?->email) && $user?->hasVerifiedEmail(),
            'phone'       => filled($user?->phone),
            'avatar'      => filled($user?->avatar_path),
            'locale'      => filled($user?->locale),
        ];

        $completed = collect($profileFields)->filter()->count();
        $total     = count($profileFields);
        $percent   = $total > 0 ? (int) round($completed / $total * 100) : 0;

        return [
            'user'     => $user,
            'percent'  => $percent,
            'verified' => $user?->hasVerifiedEmail() ?? false,
        ];
    }
}
