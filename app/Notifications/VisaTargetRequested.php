<?php

namespace App\Notifications;

use App\Models\StudentProfile;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VisaTargetRequested extends Notification
{
    use Queueable;

    public function __construct(public StudentProfile $profile) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $studentName = $this->profile->user?->name ?? __('a student');
        $visaName    = $this->profile->primaryVisa?->t('name') ?? __('a visa');
        $isChange    = $this->profile->visa_target_status === 'changed';

        return FilamentNotification::make()
            ->title($isChange
                ? __('Visa target change requested')
                : __('New visa target request'))
            ->body(__(':student → :visa', ['student' => $studentName, 'visa' => $visaName]))
            ->icon($isChange ? 'heroicon-o-arrow-path' : 'heroicon-o-flag')
            ->iconColor('warning')
            ->actions([
                \Filament\Notifications\Actions\Action::make('review')
                    ->label(__('Review'))
                    ->url(\App\Filament\Resources\Students\StudentResource::getUrl('view', ['record' => $this->profile->user_id])),
            ])
            ->getDatabaseMessage();
    }
}
