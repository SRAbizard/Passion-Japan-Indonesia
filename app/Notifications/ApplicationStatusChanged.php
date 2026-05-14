<?php

namespace App\Notifications;

use App\Models\Application;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Application $application,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title(__('Application status updated'))
            ->body(__('Your application for ":title" is now ":status".', [
                'title'  => $this->application->jobVacancy?->t('title') ?? __('a position'),
                'status' => __('application.status.'.$this->newStatus),
            ]))
            ->icon('heroicon-o-briefcase')
            ->iconColor(match ($this->newStatus) {
                'offered', 'accepted'   => 'success',
                'rejected', 'withdrawn' => 'danger',
                'interview_scheduled'   => 'warning',
                default                 => 'info',
            })
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label(__('View'))
                    ->url(fn () => $this->application->jobVacancy
                        ? route('job.show', $this->application->jobVacancy->slug)
                        : '#'),
            ])
            ->getDatabaseMessage();
    }

    public function toMail(object $notifiable): MailMessage
    {
        $title = $this->application->jobVacancy?->t('title') ?? __('your application');

        return (new MailMessage)
            ->subject(__('Application status updated — :title', ['title' => $title]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('Your application for ":title" has been updated.', ['title' => $title]))
            ->line(__('New status: :status', ['status' => __('application.status.'.$this->newStatus)]))
            ->action(__('View application'), $this->application->jobVacancy
                ? route('job.show', $this->application->jobVacancy->slug)
                : url('/'));
    }
}
