<?php

namespace App\Notifications;

use App\Models\StudentProfile;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisaTargetReviewed extends Notification
{
    use Queueable;

    public function __construct(public StudentProfile $profile) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $confirmed = $this->profile->visa_target_status === 'confirmed';
        $visaName  = $this->profile->primaryVisa?->t('name') ?? '—';

        return FilamentNotification::make()
            ->title($confirmed
                ? __('Visa target confirmed!')
                : __('Visa target rejected'))
            ->body($confirmed
                ? __('Your :visa target is approved. Time to upload your documents.', ['visa' => $visaName])
                : __('Your :visa target was not approved. Check the admin notes and try again.', ['visa' => $visaName]))
            ->icon($confirmed ? 'heroicon-o-check-badge' : 'heroicon-o-exclamation-triangle')
            ->iconColor($confirmed ? 'success' : 'danger')
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label(__('View details'))
                    ->url(\App\Filament\Student\Pages\MyVisaTarget::getUrl(panel: 'student')),
            ])
            ->getDatabaseMessage();
    }

    public function toMail(object $notifiable): MailMessage
    {
        $confirmed = $this->profile->visa_target_status === 'confirmed';
        $visaName  = $this->profile->primaryVisa?->t('name') ?? '—';

        $msg = (new MailMessage)
            ->subject($confirmed
                ? __('Your visa target is confirmed — :visa', ['visa' => $visaName])
                : __('Your visa target needs revision — :visa', ['visa' => $visaName]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]));

        if ($confirmed) {
            $msg->line(__('Your visa target ":visa" has been confirmed by an admin.', ['visa' => $visaName]))
                ->line(__('You can now upload the required documents for this visa.'))
                ->action(__('Upload documents'), url('/dashboard/documents'));
        } else {
            $msg->line(__('Your visa target ":visa" was not approved.', ['visa' => $visaName]));
            if ($this->profile->visa_target_notes) {
                $msg->line(__('Admin notes: :notes', ['notes' => $this->profile->visa_target_notes]));
            }
            $msg->action(__('Choose a different visa'), url('/dashboard/my-visa-target'));
        }

        return $msg;
    }
}
