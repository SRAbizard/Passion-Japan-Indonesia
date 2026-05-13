<?php

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static string | UnitEnum | null $navigationGroup = 'CMS';

    public static function getNavigationLabel(): string
    {
        return __('Contact Messages');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('CMS');
    }

    public static function getNavigationBadge(): ?string
    {
        $unread = static::getModel()::whereNull('read_at')->count();
        return $unread > 0 ? (string) $unread : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view'  => ViewContactMessage::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // messages only come from the public form
    }
}
