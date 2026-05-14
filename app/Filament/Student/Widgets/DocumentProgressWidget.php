<?php

namespace App\Filament\Student\Widgets;

use App\Support\StudentDocumentProgress;
use Filament\Widgets\Widget;

class DocumentProgressWidget extends Widget
{
    protected string $view = 'filament.student.widgets.document-progress';
    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'progress' => StudentDocumentProgress::for(auth()->user()),
        ];
    }
}
