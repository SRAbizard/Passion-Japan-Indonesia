<?php

namespace App\Filament\Student\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class MyProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-identification';
    protected static ?int $navigationSort = 10;
    protected string $view = 'filament.student.pages.my-profile';

    public ?array $data = [];

    public static function getNavigationLabel(): string { return __('My Profile'); }
    public function getTitle(): string                  { return __('My Profile'); }

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();
        // Ensure profile row exists
        $user->profile();
        $user->refresh()->load('studentProfile', 'educations', 'workExperiences', 'familyMembers', 'languages');

        $this->form->fill([
            'name'  => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'studentProfile' => $user->studentProfile?->only([
                'full_name', 'nickname', 'gender', 'birthdate', 'birthplace', 'religion', 'marital_status',
                'id_number', 'passport_number', 'passport_expires_at',
                'address', 'city', 'province', 'postal_code',
                'emergency_contact_name', 'emergency_contact_relation', 'emergency_contact_phone',
                'height_cm', 'weight_kg', 'blood_type', 'allergies', 'medical_conditions',
                'smoker', 'drinker', 'photo_path',
            ]) ?? [],
            'educations'      => $user->educations->map->only(['level','institution','major','start_year','end_year','gpa','is_current'])->all(),
            'workExperiences' => $user->workExperiences->map->only(['company','position','start_date','end_date','is_current','description'])->all(),
            'familyMembers'   => $user->familyMembers->map->only(['relation','name','occupation','phone','address'])->all(),
            'languages'       => $user->languages->map->only(['language','proficiency','certificate_number','taken_at','certificate_path'])->all(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('profile-tabs')->columnSpanFull()->tabs([

                    Tab::make(__('Account'))->icon('heroicon-o-user')->schema([
                        Section::make()->columns(2)->components([
                            TextInput::make('name')->label(__('Display name'))->required()->maxLength(120),
                            TextInput::make('email')->label(__('Email'))->email()->disabled()
                                ->helperText(__('Contact admin to change email')),
                            TextInput::make('phone')->label(__('Phone'))->tel()->maxLength(32),
                        ]),
                        Section::make(__('Photo'))->components([
                            FileUpload::make('studentProfile.photo_path')->label(__('Profile photo'))
                                ->image()->avatar()->disk('public')->directory('students/photos')->imageEditor(),
                        ]),
                    ]),

                    Tab::make(__('Biodata'))->icon('heroicon-o-identification')->schema([
                        Section::make(__('Personal information'))->columns(2)->components([
                            TextInput::make('studentProfile.full_name')->label(__('Full legal name'))->maxLength(160),
                            TextInput::make('studentProfile.nickname')->label(__('Nickname'))->maxLength(80),
                            Select::make('studentProfile.gender')->label(__('Gender'))
                                ->options(['male' => __('Male'), 'female' => __('Female')])->native(false),
                            DatePicker::make('studentProfile.birthdate')->label(__('Birth date'))
                                ->maxDate(now())->displayFormat('d M Y'),
                            TextInput::make('studentProfile.birthplace')->label(__('Birth place'))->maxLength(120),
                            TextInput::make('studentProfile.religion')->label(__('Religion'))->maxLength(40),
                            Select::make('studentProfile.marital_status')->label(__('Marital status'))
                                ->options([
                                    'single'   => __('Single'),
                                    'married'  => __('Married'),
                                    'divorced' => __('Divorced'),
                                    'widowed'  => __('Widowed'),
                                ])->native(false),
                        ]),
                        Section::make(__('ID & Passport'))->columns(2)->components([
                            TextInput::make('studentProfile.id_number')->label(__('ID / KTP number'))->maxLength(32),
                            TextInput::make('studentProfile.passport_number')->label(__('Passport number'))->maxLength(32),
                            DatePicker::make('studentProfile.passport_expires_at')->label(__('Passport expires'))->displayFormat('d M Y'),
                        ]),
                    ]),

                    Tab::make(__('Address'))->icon('heroicon-o-home')->schema([
                        Section::make()->columns(2)->components([
                            Textarea::make('studentProfile.address')->label(__('Street address'))->rows(2)->columnSpanFull(),
                            TextInput::make('studentProfile.city')->label(__('City'))->maxLength(80),
                            TextInput::make('studentProfile.province')->label(__('Province'))->maxLength(80),
                            TextInput::make('studentProfile.postal_code')->label(__('Postal code'))->maxLength(16),
                        ]),
                        Section::make(__('Emergency contact'))->columns(2)->components([
                            TextInput::make('studentProfile.emergency_contact_name')->label(__('Contact name'))->maxLength(120),
                            TextInput::make('studentProfile.emergency_contact_relation')->label(__('Relation'))->maxLength(40),
                            TextInput::make('studentProfile.emergency_contact_phone')->label(__('Contact phone'))->tel()->maxLength(32),
                        ]),
                    ]),

                    Tab::make(__('Education'))->icon('heroicon-o-academic-cap')->schema([
                        Repeater::make('educations')->label('')
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['institution'] ?? __('New entry'))
                            ->components([
                                Select::make('level')->label(__('Level'))
                                    ->options([
                                        'sd'=>'SD','smp'=>'SMP','sma'=>'SMA','smk'=>'SMK',
                                        'd1'=>'D1','d3'=>'D3','s1'=>'S1','s2'=>'S2','s3'=>'S3','other'=>__('Other'),
                                    ])->required()->native(false),
                                TextInput::make('institution')->label(__('Institution'))->required()->maxLength(160),
                                TextInput::make('major')->label(__('Major / Field'))->maxLength(120),
                                TextInput::make('gpa')->label(__('GPA'))->maxLength(16),
                                TextInput::make('start_year')->label(__('Start year'))->numeric()->minValue(1950)->maxValue(2100),
                                TextInput::make('end_year')->label(__('End year'))->numeric()->minValue(1950)->maxValue(2100),
                                Toggle::make('is_current')->label(__('Currently studying'))->inline(false),
                            ])
                            ->defaultItems(0),
                    ]),

                    Tab::make(__('Work experience'))->icon('heroicon-o-briefcase')->schema([
                        Repeater::make('workExperiences')->label('')
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['company'] ?? __('New entry'))
                            ->components([
                                TextInput::make('company')->label(__('Company'))->required()->maxLength(160),
                                TextInput::make('position')->label(__('Position'))->required()->maxLength(120),
                                DatePicker::make('start_date')->label(__('Start date')),
                                DatePicker::make('end_date')->label(__('End date')),
                                Toggle::make('is_current')->label(__('Currently working'))->inline(false),
                                Textarea::make('description')->label(__('Description'))->rows(3)->columnSpanFull(),
                            ])
                            ->defaultItems(0),
                    ]),

                    Tab::make(__('Family'))->icon('heroicon-o-users')->schema([
                        Repeater::make('familyMembers')->label('')
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? __('New entry'))
                            ->components([
                                Select::make('relation')->label(__('Relation'))
                                    ->options([
                                        'father'=>__('Father'),'mother'=>__('Mother'),'guardian'=>__('Guardian'),
                                        'sibling'=>__('Sibling'),'spouse'=>__('Spouse'),'child'=>__('Child'),'other'=>__('Other'),
                                    ])->required()->native(false),
                                TextInput::make('name')->label(__('Name'))->required()->maxLength(160),
                                TextInput::make('occupation')->label(__('Occupation'))->maxLength(120),
                                TextInput::make('phone')->label(__('Phone'))->tel()->maxLength(32),
                                Textarea::make('address')->label(__('Address'))->rows(2)->columnSpanFull(),
                            ])
                            ->defaultItems(0),
                    ]),

                    Tab::make(__('Languages'))->icon('heroicon-o-language')->schema([
                        Repeater::make('languages')->label('')
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => ($state['language'] ?? __('Language')) . ' — ' . ($state['proficiency'] ?? ''))
                            ->components([
                                TextInput::make('language')->label(__('Language'))->required()->placeholder('Japanese / English')->maxLength(80),
                                TextInput::make('proficiency')->label(__('Proficiency'))->placeholder('JLPT N3 / IELTS 6.5')->maxLength(80),
                                TextInput::make('certificate_number')->label(__('Certificate number'))->maxLength(80),
                                DatePicker::make('taken_at')->label(__('Date taken'))->displayFormat('d M Y'),
                                FileUpload::make('certificate_path')->label(__('Certificate file'))
                                    ->disk('public')->directory('students/language-certs')
                                    ->acceptedFileTypes(['application/pdf','image/jpeg','image/png'])->columnSpanFull(),
                            ])
                            ->defaultItems(0),
                    ]),

                    Tab::make(__('Health'))->icon('heroicon-o-heart')->schema([
                        Section::make()->columns(3)->components([
                            TextInput::make('studentProfile.height_cm')->label(__('Height (cm)'))->numeric()->minValue(50)->maxValue(250),
                            TextInput::make('studentProfile.weight_kg')->label(__('Weight (kg)'))->numeric()->minValue(20)->maxValue(300),
                            Select::make('studentProfile.blood_type')->label(__('Blood type'))
                                ->options(['A'=>'A','B'=>'B','AB'=>'AB','O'=>'O'])->native(false),
                        ]),
                        Section::make()->components([
                            Textarea::make('studentProfile.allergies')->label(__('Allergies'))->rows(2),
                            Textarea::make('studentProfile.medical_conditions')->label(__('Medical conditions'))->rows(2),
                            Toggle::make('studentProfile.smoker')->label(__('Smoker'))->inline(false),
                            Toggle::make('studentProfile.drinker')->label(__('Drinks alcohol'))->inline(false),
                        ]),
                    ]),

                ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Save changes'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        /** @var User $user */
        $user = auth()->user();

        // Update User core fields
        $user->update([
            'name'  => $state['name'] ?? $user->name,
            'phone' => $state['phone'] ?? null,
        ]);

        // Update 1:1 profile
        $profile = $user->profile();
        $profile->update($state['studentProfile'] ?? []);

        // Sync 1:n relations: educations
        $this->syncRelation($user->educations(), $state['educations'] ?? []);
        $this->syncRelation($user->workExperiences(), $state['workExperiences'] ?? []);
        $this->syncRelation($user->familyMembers(), $state['familyMembers'] ?? []);
        $this->syncRelation($user->languages(), $state['languages'] ?? []);

        Notification::make()
            ->title(__('Profile updated'))
            ->success()
            ->send();
    }

    /**
     * Wipe the relation and recreate from form rows.
     * Simple but reliable for a profile page.
     */
    private function syncRelation($relation, array $items): void
    {
        $relation->delete();
        foreach ($items as $item) {
            // Drop empty items
            $hasContent = collect($item)->filter(fn ($v) => $v !== null && $v !== '' && $v !== false)->isNotEmpty();
            if ($hasContent) {
                $relation->create($item);
            }
        }
    }
}
