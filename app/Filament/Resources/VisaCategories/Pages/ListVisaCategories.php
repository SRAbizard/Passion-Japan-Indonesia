<?php
namespace App\Filament\Resources\VisaCategories\Pages;

use App\Filament\Resources\VisaCategories\VisaCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVisaCategories extends ListRecords
{
    protected static string $resource = VisaCategoryResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
