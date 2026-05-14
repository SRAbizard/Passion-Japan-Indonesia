<?php

namespace App\Filament\Resources\StudentDocuments\Pages;

use App\Filament\Resources\StudentDocuments\StudentDocumentResource;
use Filament\Resources\Pages\EditRecord;

class EditStudentDocument extends EditRecord
{
    protected static string $resource = StudentDocumentResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // When admin changes status to verified/rejected, fill verifier+timestamp
        if (in_array(($data['status'] ?? null), ['verified', 'rejected'], true)) {
            $data['verified_by'] = auth()->id();
            $data['verified_at'] = now();
        }
        return $data;
    }
}
