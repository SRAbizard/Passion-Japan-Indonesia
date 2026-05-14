<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\User;
use App\Support\StudentDocumentProgress;
use Filament\Resources\Pages\Page;

class ViewStudent extends Page
{
    protected static string $resource = StudentResource::class;
    protected string $view = 'filament.admin.pages.view-student';

    public User $record;

    public function mount(int $record): void
    {
        $this->record = User::with([
            'studentProfile',
            'educations',
            'workExperiences',
            'familyMembers',
            'languages',
            'studentDocuments.verifier',
            'applications.vacancy.visaCategory',
            'applications.vacancy.company',
            'enrollments.course',
            'certificates.course',
        ])->findOrFail($record);
    }

    public function getTitle(): string
    {
        return $this->record->name;
    }

    public function getHeading(): string
    {
        return $this->record->name;
    }

    public function getSubheading(): ?string
    {
        return $this->record->email;
    }

    public function getProgress(): array
    {
        return StudentDocumentProgress::for($this->record);
    }

    public function getProfileCompletion(): int
    {
        return $this->record->studentProfile?->completionPct() ?? 0;
    }
}
