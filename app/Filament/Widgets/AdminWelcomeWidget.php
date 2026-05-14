<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Enrollment;
use App\Models\StudentDocument;
use App\Models\User;
use Filament\Widgets\Widget;

class AdminWelcomeWidget extends Widget
{
    protected string $view = 'filament.widgets.admin-welcome';
    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'user'             => auth()->user(),
            'studentCount'     => User::role('student')->count(),
            'pendingApps'      => Application::whereIn('status', ['submitted','under_review'])->count(),
            'pendingDocs'      => StudentDocument::where('status', 'pending')->count(),
            'unreadMessages'   => ContactMessage::whereNull('read_at')->count(),
            'activeEnrollments'=> Enrollment::whereIn('status', ['enrolled','in_progress'])->count(),
            'certsIssued'      => Certificate::count(),
        ];
    }
}
