<?php
namespace App\Filament\Resources\Applications\Pages;

use App\Filament\Resources\Applications\ApplicationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditApplication extends EditRecord
{
    protected static string $resource = ApplicationResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Stamp the reviewer when status changes from submitted
        if (($data['status'] ?? null) !== 'submitted') {
            $data['reviewed_at'] = now();
            $data['reviewed_by'] = auth()->id();
        }
        return $data;
    }
}
