<?php
namespace App\Filament\Resources\VisaCategories\Pages;

use App\Filament\Resources\VisaCategories\VisaCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVisaCategory extends EditRecord
{
    protected static string $resource = VisaCategoryResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
