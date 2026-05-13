<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
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

        Notification::make()
            ->title(__('Settings saved'))
            ->success()
            ->send();
    }
}
