<?php

namespace App\Filament\Student\Resources\Documents\Pages;

use App\Filament\Student\Resources\Documents\DocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label(__('Upload document'))->icon('heroicon-o-arrow-up-tray')];
    }
}
