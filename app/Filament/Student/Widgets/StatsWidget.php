<?php

namespace App\Filament\Student\Widgets;

use App\Models\Application;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\StudentDocument;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsWidget extends BaseWidget
{
    protected ?string $heading = null;

    protected function getStats(): array
    {
        $userId = auth()->id();

        $apps      = Application::where('user_id', $userId)->count();
        $appsActive = Application::where('user_id', $userId)
            ->whereNotIn('status', ['rejected', 'withdrawn'])->count();

        $enrollments = Enrollment::where('user_id', $userId)->count();
        $inProgress  = Enrollment::where('user_id', $userId)->whereIn('status', ['enrolled','in_progress'])->count();

        $docsPending  = StudentDocument::where('user_id', $userId)->where('status', 'pending')->count();
        $docsVerified = StudentDocument::where('user_id', $userId)->where('status', 'verified')->count();

        $certs = Certificate::where('user_id', $userId)->count();

        return [
            Stat::make(__('Applications'), $apps)
                ->description($appsActive > 0
                    ? __(':count active', ['count' => $appsActive])
                    : __('None active'))
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color($appsActive > 0 ? 'info' : 'gray')
                ->url(\App\Filament\Student\Pages\MyApplications::getUrl()),

            Stat::make(__('Courses enrolled'), $enrollments)
                ->description($inProgress > 0
                    ? __(':count in progress', ['count' => $inProgress])
                    : __('None active'))
                ->descriptionIcon('heroicon-m-book-open')
                ->color($inProgress > 0 ? 'success' : 'gray')
                ->url(\App\Filament\Student\Pages\MyClasses::getUrl()),

            Stat::make(__('Documents'), $docsVerified.' / '.($docsVerified + $docsPending))
                ->description($docsPending > 0
                    ? __(':count pending verification', ['count' => $docsPending])
                    : __('All verified'))
                ->descriptionIcon('heroicon-m-document-check')
                ->color($docsPending > 0 ? 'warning' : 'success')
                ->url(\App\Filament\Student\Resources\Documents\DocumentResource::getUrl('index')),

            Stat::make(__('Certificates'), $certs)
                ->description($certs > 0 ? __('Well done!') : __('Complete a course to earn one'))
                ->descriptionIcon('heroicon-m-trophy')
                ->color($certs > 0 ? 'success' : 'gray'),
        ];
    }
}
