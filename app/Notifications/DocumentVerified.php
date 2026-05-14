<?php

namespace App\Notifications;

use App\Models\StudentDocument;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentVerified extends Notification
{
    use Queueable;

    public function __construct(public StudentDocument $document) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $verified = $this->document->status === 'verified';

        return FilamentNotification::make()
            ->title($verified ? __('Document verified') : __('Document needs attention'))
            ->body($verified
                ? __('Your :type document has been verified.', ['type' => \App\Models\DocumentType::labelFor($this->document->type)])
                : __('Your :type document was rejected. :notes', [
                    'type'  => \App\Models\DocumentType::labelFor($this->document->type),
                    'notes' => $this->document->notes ?: '',
                ]))
            ->icon($verified ? 'heroicon-o-check-badge' : 'heroicon-o-exclamation-triangle')
            ->iconColor($verified ? 'success' : 'danger')
            ->getDatabaseMessage();
    }
}
