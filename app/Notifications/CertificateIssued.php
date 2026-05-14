<?php

namespace App\Notifications;

use App\Models\Certificate;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateIssued extends Notification
{
    use Queueable;

    public function __construct(public Certificate $certificate) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title(__('Certificate earned!'))
            ->body(__('You completed ":course" — your certificate is ready.', [
                'course' => $this->certificate->course?->t('title') ?? __('a course'),
            ]))
            ->icon('heroicon-o-trophy')
            ->iconColor('success')
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label(__('View certificate'))
                    ->url(fn () => route('certificate.show', $this->certificate->certificate_number)),
            ])
            ->getDatabaseMessage();
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your certificate is ready — :course', [
                'course' => $this->certificate->course?->t('title') ?? __('your course'),
            ]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('Congratulations on completing :course!', [
                'course' => $this->certificate->course?->t('title') ?? __('your course'),
            ]))
            ->line(__('Your certificate number is :number', ['number' => $this->certificate->certificate_number]))
            ->action(__('View & download certificate'), route('certificate.show', $this->certificate->certificate_number));
    }
}
