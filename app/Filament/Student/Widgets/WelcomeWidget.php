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
        $user?->load('studentProfile');

        // Profile completion: prefer detailed StudentProfile fields if present
        $percent = $user?->studentProfile
            ? $user->studentProfile->completionPct()
            : 0;

        return [
            'user'        => $user,
            'percent'     => $percent,
            'verified'    => $user?->hasVerifiedEmail() ?? false,
            'profileUrl'  => \App\Filament\Student\Pages\Profile::getUrl(),
        ];
    }
}
