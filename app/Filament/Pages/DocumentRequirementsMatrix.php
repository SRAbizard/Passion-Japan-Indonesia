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
     * Tri-state matrix:
     *   $matrix[<visa_slug>][<doc_type_key>] = 'required' | 'optional' | null/missing
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
            ->mapWithKeys(function ($v) {
                $cell = [];
                foreach ($v->requiredDocumentTypes() as $key) {
                    $cell[$key] = 'required';
                }
                foreach ($v->optionalDocumentTypes() as $key) {
                    // Required wins if (somehow) the same key appears in both
                    if (! isset($cell[$key])) {
                        $cell[$key] = 'optional';
                    }
                }
                return [$v->slug => $cell];
            })->all();
    }

    public function getVisas()
    {
        return VisaCategory::orderBy('sort_order')->get();
    }

    public function getDocumentTypes()
    {
        return DocumentType::active()->ordered()->get();
    }

    /**
     * Cycle a cell: none → required → optional → none.
     */
    public function cycle(string $visaSlug, string $typeKey): void
    {
        $current = $this->matrix[$visaSlug][$typeKey] ?? null;
        $next = match ($current) {
            null       => 'required',
            'required' => 'optional',
            'optional' => null,
            default    => 'required',
        };

        if ($next === null) {
            unset($this->matrix[$visaSlug][$typeKey]);
        } else {
            $this->matrix[$visaSlug][$typeKey] = $next;
        }
    }

    public function setRequiredAllForVisa(string $visaSlug): void
    {
        $this->matrix[$visaSlug] = $this->getDocumentTypes()
            ->mapWithKeys(fn ($t) => [$t->key => 'required'])
            ->all();
    }

    public function clearVisa(string $visaSlug): void
    {
        $this->matrix[$visaSlug] = [];
    }

    public function save(): void
    {
        foreach ($this->matrix as $visaSlug => $cells) {
            $required = [];
            $optional = [];
            foreach ($cells as $key => $state) {
                if ($state === 'required') $required[] = $key;
                if ($state === 'optional') $optional[] = $key;
            }
            VisaCategory::where('slug', $visaSlug)->update([
                'required_documents' => array_values(array_unique($required)),
                'optional_documents' => array_values(array_unique($optional)),
            ]);
        }

        Notification::make()
            ->title(__('Requirements updated'))
            ->body(__('Required documents per visa have been saved.'))
            ->success()
            ->send();

        $this->loadMatrix();
    }

    /**
     * Helpers for the view to count states per visa.
     *
     * @return array{required: int, optional: int}
     */
    public function countsFor(string $visaSlug): array
    {
        $cells = $this->matrix[$visaSlug] ?? [];
        return [
            'required' => count(array_filter($cells, fn ($s) => $s === 'required')),
            'optional' => count(array_filter($cells, fn ($s) => $s === 'optional')),
        ];
    }
}
