<?php

namespace App\Filament\Pages;

use App\Models\DocumentType;
use App\Models\VisaCategory;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use UnitEnum;

class DocumentRequirementsMatrix extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-table-cells';
    protected static string | UnitEnum | null $navigationGroup = 'Students';
    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.document-requirements-matrix';

    /**
     * Matrix state: ['<visa_slug>' => ['ktp', 'paspor', ...], ...]
     */
    public array $matrix = [];

    public static function getNavigationLabel(): string { return __('Document Requirements Matrix'); }
    public static function getNavigationGroup(): ?string { return __('Students'); }
    public function getTitle(): string                 { return __('Document Requirements Matrix'); }

    public function mount(): void
    {
        $this->loadMatrix();
    }

    public function loadMatrix(): void
    {
        $this->matrix = VisaCategory::orderBy('sort_order')->get()
            ->mapWithKeys(fn ($v) => [$v->slug => $v->requiredDocumentTypes()])
            ->all();
    }

    public function getVisas()
    {
        return VisaCategory::orderBy('sort_order')->get();
    }

    public function getDocumentTypes()
    {
        return DocumentType::active()->ordered()->get();
    }

    public function toggle(string $visaSlug, string $typeKey): void
    {
        $current = $this->matrix[$visaSlug] ?? [];
        $this->matrix[$visaSlug] = in_array($typeKey, $current, true)
            ? array_values(array_diff($current, [$typeKey]))
            : array_merge($current, [$typeKey]);
    }

    public function save(): void
    {
        foreach ($this->matrix as $visaSlug => $typeKeys) {
            VisaCategory::where('slug', $visaSlug)->update([
                'required_documents' => array_values(array_unique($typeKeys)),
            ]);
        }

        Notification::make()
            ->title(__('Requirements updated'))
            ->body(__('Required documents per visa have been saved.'))
            ->success()
            ->send();

        $this->loadMatrix();
    }

    public function selectAllForVisa(string $visaSlug): void
    {
        $this->matrix[$visaSlug] = $this->getDocumentTypes()->pluck('key')->all();
    }

    public function clearVisa(string $visaSlug): void
    {
        $this->matrix[$visaSlug] = [];
    }
}
