<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->markAsRead();
        return $data;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Sender'))
                    ->columns(2)
                    ->components([
                        TextEntry::make('name')->label(__('Name')),
                        TextEntry::make('email')->label(__('Email'))->copyable(),
                        TextEntry::make('phone')->label(__('Phone'))->placeholder('—'),
                        TextEntry::make('locale')->label(__('Locale'))->badge(),
                    ]),
                Section::make(__('Message'))
                    ->components([
                        TextEntry::make('subject')->label(__('Subject'))->weight('bold'),
                        TextEntry::make('message')->label(__('Message'))->columnSpanFull(),
                    ]),
                Section::make(__('Meta'))
                    ->columns(3)
                    ->components([
                        TextEntry::make('created_at')->label(__('Received'))->dateTime('d M Y H:i'),
                        TextEntry::make('ip_address')->label(__('IP'))->placeholder('—'),
                        TextEntry::make('user_agent')->label(__('User agent'))->placeholder('—')->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label(__('Reply via email'))
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('primary')
                ->url(fn () => 'mailto:'.$this->record->email.'?subject=Re:%20'.rawurlencode($this->record->subject))
                ->openUrlInNewTab(),
            Action::make('whatsapp')
                ->label('WhatsApp')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->url(fn () => $this->record->phone
                    ? 'https://wa.me/'.preg_replace('/\D/', '', $this->record->phone)
                    : null)
                ->visible(fn () => filled($this->record->phone))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
