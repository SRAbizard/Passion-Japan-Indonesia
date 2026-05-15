<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use UnitEnum;

class SiteSettings extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string | UnitEnum | null $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public static function getNavigationLabel(): string { return __('Website Settings'); }
    public static function getNavigationGroup(): ?string { return __('CMS'); }
    public function getTitle(): string { return __('Website Settings'); }

    private const KEYS = [
        // group => [keys]
        'contact' => ['contact.email', 'contact.phone', 'contact.whatsapp'],
        'social'  => ['social.instagram', 'social.facebook', 'social.tiktok'],
        'stats'   => ['stats.students', 'stats.workers', 'stats.companies'],
    ];

    public function mount(): void
    {
        $data = [];
        foreach (self::KEYS as $group => $keys) {
            foreach ($keys as $key) {
                $data[$this->keyToFormName($key)] = Setting::get($key, $this->defaultFor($key));
            }
        }
        // Offices are stored as a JSON array under the key contact.offices
        $offices = Setting::get('contact.offices', config('passion.contact.offices', []));
        $data['contact__offices'] = is_array($offices) ? $offices : [];

        // Company profile PDF path
        $data['company__profile_pdf_path'] = Setting::get('company.profile_pdf_path');

        // Theme audio file path
        $data['audio__theme_path'] = Setting::get('audio.theme_path');

        // Hero slideshow images (4 slots)
        $slides = Setting::get('hero.slides', []);
        $data['hero__slides'] = is_array($slides) ? $slides : [];

        $this->form->fill($data);
    }

    private function keyToFormName(string $key): string
    {
        return str_replace('.', '__', $key);
    }

    private function defaultFor(string $key): ?string
    {
        return data_get(config('passion'), $key) ?? null;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('settings')->tabs([
                Tab::make(__('Contact'))->icon('heroicon-o-phone')->schema([
                    Section::make()->columns(1)->components([
                        TextInput::make('contact__email')->label(__('Email'))->email(),
                        TextInput::make('contact__phone')->label(__('Phone')),
                        TextInput::make('contact__whatsapp')->label('WhatsApp')->helperText(__('Digits only, no spaces or plus sign.')),
                    ]),
                ]),
                Tab::make(__('Social media'))->icon('heroicon-o-share')->schema([
                    Section::make()->columns(1)->components([
                        TextInput::make('social__instagram')->url()->prefix('https://'),
                        TextInput::make('social__facebook')->url()->prefix('https://'),
                        TextInput::make('social__tiktok')->url()->prefix('https://'),
                    ]),
                ]),
                Tab::make(__('Stats'))->icon('heroicon-o-chart-bar')->schema([
                    Section::make()->columns(3)->components([
                        TextInput::make('stats__students')->label(__('Trained Students')),
                        TextInput::make('stats__workers')->label(__('Workers')),
                        TextInput::make('stats__companies')->label(__('Partner Companies')),
                    ]),
                ]),
                Tab::make(__('Company'))->icon('heroicon-o-building-office-2')->schema([
                    Section::make(__('Company Profile'))
                        ->description(__('Upload the company profile document. Visitors can download it from the About page.'))
                        ->components([
                            FileUpload::make('company__profile_pdf_path')
                                ->label(__('Company Profile file (PDF / DOC / image)'))
                                ->disk('public')
                                ->directory('company')
                                ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'])
                                ->maxSize(10240)
                                ->columnSpanFull(),
                        ]),
                ]),
                Tab::make(__('Hero slideshow'))->icon('heroicon-o-photo')->schema([
                    Section::make(__('Hero background slides'))
                        ->description(__('Upload up to 4 background images for the homepage hero. Empty slots fall back to default Japan photos.'))
                        ->components([
                            Repeater::make('hero__slides')
                                ->label('')
                                ->itemLabel(fn (array $state): ?string =>
                                    filled($state['image_path'] ?? null) ? basename($state['image_path']) : __('New slide'))
                                ->minItems(0)
                                ->maxItems(4)
                                ->collapsible()
                                ->reorderable()
                                ->components([
                                    FileUpload::make('image_path')
                                        ->label(__('Background image'))
                                        ->image()
                                        ->disk('public')
                                        ->directory('hero')
                                        ->imageEditor()
                                        ->maxSize(5120),
                                ])
                                ->defaultItems(0)
                                ->addActionLabel(__('Add slide')),
                        ]),
                ]),
                Tab::make(__('Theme audio'))->icon('heroicon-o-musical-note')->schema([
                    Section::make(__('Background music'))
                        ->description(__('Upload an MP3/OGG file. Plays muted on the homepage; visitors can unmute via the floating speaker button.'))
                        ->components([
                            FileUpload::make('audio__theme_path')
                                ->label(__('Theme song'))
                                ->disk('public')
                                ->directory('audio')
                                ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/ogg', 'audio/wav'])
                                ->maxSize(10240)
                                ->columnSpanFull(),
                        ]),
                ]),
                Tab::make(__('Offices'))->icon('heroicon-o-map-pin')->schema([
                    Section::make()
                        ->description(__('Offices shown in the homepage footer, About, and Contact pages. Each address links to Google Maps when clicked. Leave Maps URL blank to auto-search by address.'))
                        ->components([
                            Repeater::make('contact__offices')
                                ->label('')
                                ->columns(2)
                                ->itemLabel(fn (array $state): ?string =>
                                    trim(($state['city'] ?? '').', '.($state['country'] ?? ''), ', ') ?: __('New office'))
                                ->collapsible()
                                ->reorderable()
                                ->components([
                                    TextInput::make('city')->label(__('City'))->required()->maxLength(120),
                                    TextInput::make('country')->label(__('Country'))->maxLength(80),
                                    TextInput::make('address')->label(__('Full address'))->columnSpanFull()
                                        ->placeholder(__('Street, district, city, postal code'))
                                        ->helperText(__('Used for Google Maps search if Maps URL is empty.')),
                                    TextInput::make('maps_url')->label(__('Google Maps URL (optional)'))->columnSpanFull()
                                        ->url()
                                        ->placeholder('https://maps.google.com/?q=...')
                                        ->helperText(__('Leave blank to auto-generate from address.')),
                                ])
                                ->defaultItems(0)
                                ->addActionLabel(__('Add office')),
                        ]),
                ]),
            ])->columnSpanFull(),
        ])->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Save'))
                ->color('primary')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        foreach (self::KEYS as $group => $keys) {
            foreach ($keys as $key) {
                $value = $state[$this->keyToFormName($key)] ?? null;
                Setting::set($key, $value, $group);
            }
        }

        // Offices: array of {city, country, address, maps_url}
        $offices = collect($state['contact__offices'] ?? [])
            ->map(fn ($o) => [
                'city'     => $o['city'] ?? null,
                'country'  => $o['country'] ?? null,
                'address'  => $o['address'] ?? null,
                'maps_url' => $o['maps_url'] ?? null,
            ])
            ->filter(fn ($o) => filled($o['city']))
            ->values()
            ->all();
        Setting::set('contact.offices', $offices, 'contact');

        // Company profile single-file upload
        Setting::set('company.profile_pdf_path', $state['company__profile_pdf_path'] ?? null, 'company');

        // Theme audio single-file upload
        Setting::set('audio.theme_path', $state['audio__theme_path'] ?? null, 'audio');

        // Hero slideshow images
        $slides = collect($state['hero__slides'] ?? [])
            ->map(fn ($s) => ['image_path' => $s['image_path'] ?? null])
            ->filter(fn ($s) => filled($s['image_path']))
            ->values()
            ->all();
        Setting::set('hero.slides', $slides, 'hero');

        Notification::make()
            ->title(__('Settings saved'))
            ->success()
            ->send();
    }
}
