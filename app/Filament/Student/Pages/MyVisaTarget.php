<?php

namespace App\Filament\Student\Pages;

use App\Models\StudentProfile;
use App\Models\User;
use App\Models\VisaCategory;
use App\Notifications\VisaTargetRequested;
use BackedEnum;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class MyVisaTarget extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-flag';
    protected static ?int $navigationSort = 11;
    protected string $view = 'filament.student.pages.my-visa-target';

    public ?array $data = [];

    public static function getNavigationLabel(): string { return __('Visa Target'); }
    public function getTitle(): string                  { return __('Visa Target'); }

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $user->profile(); // ensure exists
        $user->refresh()->load('studentProfile.primaryVisa');

        $this->form->fill([
            'primary_visa_category_id' => $user->studentProfile?->primary_visa_category_id,
            'visa_target_notes'        => null, // request notes, blank by default
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Choose your visa target'))
                    ->description(__('This determines which documents you need to upload. Your choice will be reviewed by an admin.'))
                    ->components([
                        Radio::make('primary_visa_category_id')
                            ->label('')
                            ->options(fn () => VisaCategory::orderBy('sort_order')->get()
                                ->mapWithKeys(fn ($v) => [$v->id => $v->t('name')]))
                            ->descriptions(fn () => VisaCategory::orderBy('sort_order')->get()
                                ->mapWithKeys(fn ($v) => [$v->id => $v->t('description') ?: ''])
                                ->all())
                            ->required()
                            ->live(),
                        Textarea::make('visa_target_notes')
                            ->label(__('Notes for the admin (optional)'))
                            ->rows(3)
                            ->placeholder(__('Tell us about your background, motivation, or any special considerations.'))
                            ->maxLength(1000),
                    ]),
            ])
            ->statePath('data');
    }

    public function getStudentProfile(): ?StudentProfile
    {
        return auth()->user()->studentProfile;
    }

    public function submit(): void
    {
        $state = $this->form->getState();

        $user = auth()->user();
        $profile = $user->profile();

        // Track if visa is being changed vs first request
        $isChange = $profile->visa_target_status === 'confirmed'
            && $profile->primary_visa_category_id !== (int) $state['primary_visa_category_id'];

        $newStatus = $isChange ? 'changed' : 'pending';

        $profile->update([
            'primary_visa_category_id' => $state['primary_visa_category_id'],
            'visa_target_status'       => $newStatus,
            'visa_target_requested_at' => now(),
            'visa_target_notes'        => $state['visa_target_notes'] ?: $profile->visa_target_notes,
            // Clear previous review when re-requesting
            'visa_target_reviewed_at'  => null,
            'visa_target_reviewed_by'  => null,
        ]);

        // Notify all admins
        $admins = User::role(['superadmin', 'admin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new VisaTargetRequested($profile->fresh()));
        }

        Notification::make()
            ->title(__('Request submitted'))
            ->body(__('An admin will review your visa target shortly.'))
            ->success()
            ->send();

        // Reload form with the new state
        $this->mount();
    }
}
