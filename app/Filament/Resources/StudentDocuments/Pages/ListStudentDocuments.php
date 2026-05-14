<?php

namespace App\Filament\Resources\StudentDocuments\Pages;

use App\Filament\Resources\StudentDocuments\StudentDocumentResource;
use Filament\Resources\Pages\ListRecords;

class ListStudentDocuments extends ListRecords
{
    protected static string $resource = StudentDocumentResource::class;
}
