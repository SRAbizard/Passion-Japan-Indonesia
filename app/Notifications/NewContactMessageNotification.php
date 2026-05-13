<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public ContactMessage $contact) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $reviewUrl = url('/admin/contact-messages/'.$this->contact->id);

        return (new MailMessage)
            ->subject('['.config('app.name').'] '.__('New contact message:').' '.$this->contact->subject)
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('A new contact message has been submitted on the public website.'))
            ->line('**'.__('From').':** '.$this->contact->name.' <'.$this->contact->email.'>')
            ->line('**'.__('Phone').':** '.($this->contact->phone ?: '—'))
            ->line('**'.__('Subject').':** '.$this->contact->subject)
            ->line('**'.__('Message').':**')
            ->line($this->contact->message)
            ->action(__('Open in admin'), $reviewUrl)
            ->line(__('Sent from :ip on :time.', [
                'ip'   => $this->contact->ip_address ?: 'unknown IP',
                'time' => $this->contact->created_at->format('Y-m-d H:i'),
            ]));
    }
}
