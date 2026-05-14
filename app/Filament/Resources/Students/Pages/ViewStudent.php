<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\User;
use App\Notifications\VisaTargetReviewed;
use App\Support\StudentDocumentProgress;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;

class ViewStudent extends Page
{
    protected static string $resource = StudentResource::class;
    protected string $view = 'filament.admin.pages.view-student';

    public User $record;

    /**
     * Filament v5 may inject either the resolved User model (route model
     * binding) OR the raw integer key, depending on resource config.
     * Normalise to an ID and reload with all the relations the view needs.
     */
    public function mount(User|int|string $record): void
    {
        $id = $record instanceof User ? $record->getKey() : (int) $record;

        $this->record = User::with([
            'studentProfile.primaryVisa',
            'studentProfile.visaTargetReviewer',
            'educations',
            'workExperiences',
            'familyMembers',
            'languages',
            'studentDocuments.verifier',
            'applications.vacancy.visaCategory',
            'applications.vacancy.company',
            'enrollments.course',
            'certificates.course',
        ])->findOrFail($id);
    }

    protected function getHeaderActions(): array
    {
        $profile = $this->record->studentProfile;
        $hasPending = $profile && in_array($profile->visa_target_status, ['pending', 'changed'], true);

        return [
            Action::make('confirm_visa')
                ->label(__('Confirm visa target'))
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn () => $hasPending)
                ->requiresConfirmation()
                ->modalHeading(fn () => __('Confirm :name as :visa?', [
                    'name' => $this->record->name,
                    'visa' => $profile?->primaryVisa?->t('name') ?? '—',
                ]))
                ->action(function () use ($profile) {
                    $profile->update([
                        'visa_target_status'      => 'confirmed',
                        'visa_target_reviewed_at' => now(),
                        'visa_target_reviewed_by' => auth()->id(),
                    ]);
                    $this->record->notify(new VisaTargetReviewed($profile->fresh()));
                    Notification::make()->title(__('Visa target confirmed'))->success()->send();
                    $this->mount($this->record);
                }),

            Action::make('reject_visa')
                ->label(__('Reject visa target'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $hasPending)
                ->form([
                    Textarea::make('reason')
                        ->label(__('Reason'))
                        ->required()
                        ->rows(3)
                        ->placeholder(__('Tell the student why this visa is not a fit, and what to try instead.')),
                ])
                ->action(function (array $data) use ($profile) {
                    $profile->update([
                        'visa_target_status'      => 'rejected',
                        'visa_target_reviewed_at' => now(),
                        'visa_target_reviewed_by' => auth()->id(),
                        'visa_target_notes'       => $data['reason'],
                    ]);
                    $this->record->notify(new VisaTargetReviewed($profile->fresh()));
                    Notification::make()->title(__('Visa target rejected'))->warning()->send();
                    $this->mount($this->record);
                }),
        ];
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
