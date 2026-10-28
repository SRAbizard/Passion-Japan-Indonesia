<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Support\Legal;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use UnitEnum;

/**
 * Admin → Legal & Fees: privacy policy, terms, the anti-fraud notice and
 * the programme fee table. Editors open prefilled with the bundled
 * defaults so there is always a complete text to start from.
 */
class LegalSettings extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-scale';
    protected static string | UnitEnum | null $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 91;

    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    private const LANGS = ['id' => 'Indonesia', 'en' => 'English', 'ja' => '日本語'];

    public static function getNavigationLabel(): string { return __('Legal & Fees'); }
    public static function getNavigationGroup(): ?string { return __('CMS'); }
    public function getTitle(): string { return __('Legal & Fees'); }

    public function mount(): void
    {
        $data = [];
        foreach (['privacy', 'terms'] as $doc) {
            $stored = (array) Setting::get("legal.{$doc}", []);
            foreach (array_keys(self::LANGS) as $lang) {
                $data["{$doc}_{$lang}"] = filled(strip_tags((string) ($stored[$lang] ?? '')))
                    ? $stored[$lang]
                    : Legal::defaultDocument($doc, $lang);
            }
        }

        $fraud = (array) Setting::get('legal.fraud_points', []);
        $note  = (array) Setting::get('legal.fees_note', []);
        foreach (array_keys(self::LANGS) as $lang) {
            $data["fraud_{$lang}"]     = filled($fraud[$lang] ?? null) ? $fraud[$lang] : implode("\n", Legal::defaultFraudPoints($lang));
            $data["fees_note_{$lang}"] = $note[$lang] ?? null;
        }
        $data['fees'] = (array) Setting::get('legal.fees', []);

        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('legal')->columnSpanFull()->tabs([
                Tab::make(__('Privacy Policy'))->icon('heroicon-o-lock-closed')->schema([
                    $this->documentEditors('privacy', __('Shown at /privacy-policy. Review it with your legal adviser; the starting text follows the Indonesian Personal Data Protection Law (UU PDP).')),
                ]),
                Tab::make(__('Terms & Conditions'))->icon('heroicon-o-document-text')->schema([
                    $this->documentEditors('terms', __('Shown at /terms and linked from the registration form.')),
                ]),
                Tab::make(__('Anti-fraud notice'))->icon('heroicon-o-shield-exclamation')->schema([
                    Section::make()
                        ->description(__('One warning per line. Shown on the Programme Fees page and next to every job, together with your official channels from Website Settings → Contact. Make sure every line matches company policy.'))
                        ->columns(3)
                        ->components(collect(self::LANGS)->map(fn ($label, $lang) =>
                            Textarea::make("fraud_{$lang}")->label($label)->rows(9)->required($lang === 'id')
                        )->values()->all()),
                ]),
                Tab::make(__('Programme Fees'))->icon('heroicon-o-banknotes')->schema([
                    Section::make(__('Fee table'))
                        ->description(__('Listed at /fees. Leave empty to show "the fee breakdown is being prepared". Names and notes fall back to Indonesian.'))
                        ->components([
                            Repeater::make('fees')->label(__('Fee table'))->hiddenLabel()
                                ->columns(3)
                                ->collapsible()
                                ->reorderable()
                                ->defaultItems(0)
                                ->itemLabel(fn (array $state): ?string => trim(($state['name_id'] ?? '').' — '.($state['amount'] ?? ''), ' —') ?: null)
                                ->addActionLabel(__('Add fee'))
                                ->components([
                                    TextInput::make('name_id')->label(__('Item').' (Indonesia)')->required()->maxLength(120),
                                    TextInput::make('name_en')->label(__('Item').' (English)')->maxLength(120),
                                    TextInput::make('name_ja')->label(__('Item').' (日本語)')->maxLength(120),
                                    TextInput::make('amount')->label(__('Amount'))->placeholder('Rp 5.000.000')->maxLength(60)->columnSpanFull(),
                                    TextInput::make('note_id')->label(__('Notes').' (Indonesia)')->maxLength(200),
                                    TextInput::make('note_en')->label(__('Notes').' (English)')->maxLength(200),
                                    TextInput::make('note_ja')->label(__('Notes').' (日本語)')->maxLength(200),
                                ]),
                        ]),
                    Section::make(__('Note under the table'))
                        ->columns(3)
                        ->components(collect(self::LANGS)->map(fn ($label, $lang) =>
                            Textarea::make("fees_note_{$lang}")->label($label)->rows(4)
                        )->values()->all()),
                ]),
            ]),
        ])->statePath('data');
    }

    private function documentEditors(string $doc, string $hint): Tabs
    {
        return Tabs::make("{$doc}-langs")->tabs(
            collect(self::LANGS)->map(fn ($label, $lang) => Tab::make($label)->schema([
                RichEditor::make("{$doc}_{$lang}")->label(strtoupper($lang))->hiddenLabel()->helperText($lang === 'id' ? $hint : null)
                    ->required($lang === 'id')
                    ->toolbarButtons([['h2', 'h3', 'bold', 'italic', 'link'], ['bulletList', 'orderedList'], ['undo', 'redo']]),
            ]))->values()->all()
        );
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label(__('Save'))->color('primary')->submit('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $langs = array_keys(self::LANGS);

        foreach (['privacy', 'terms'] as $doc) {
            $new = collect($langs)->mapWithKeys(fn ($l) => [$l => (string) ($state["{$doc}_{$l}"] ?? '')])->all();
            if ($new != (array) Setting::get("legal.{$doc}", [])) {
                Setting::set("legal.{$doc}", $new, 'legal');
                Setting::set("legal.{$doc}_updated_at", now()->toDateString(), 'legal');
            }
        }

        Setting::set('legal.fraud_points', collect($langs)->mapWithKeys(fn ($l) => [$l => trim((string) ($state["fraud_{$l}"] ?? ''))])->all(), 'legal');
        Setting::set('legal.fees_note', collect($langs)->mapWithKeys(fn ($l) => [$l => trim((string) ($state["fees_note_{$l}"] ?? ''))])->all(), 'legal');
        Setting::set('legal.fees', collect($state['fees'] ?? [])
            ->map(fn ($f) => collect(['name_id', 'name_en', 'name_ja', 'amount', 'note_id', 'note_en', 'note_ja'])
                ->mapWithKeys(fn ($k) => [$k => trim((string) ($f[$k] ?? ''))])->all())
            ->filter(fn ($f) => $f['name_id'] !== '')
            ->values()->all(), 'legal');

        Notification::make()->title(__('Settings saved'))->success()->send();
    }
}
