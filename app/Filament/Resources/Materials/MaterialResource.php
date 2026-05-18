<?php

namespace App\Filament\Resources\Materials;

use App\Filament\Resources\Materials\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\Chapter;
use App\Models\Material;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Lesson admin (Material model). Hidden from sidebar — reached via
 * Chapter → Lessons relation manager. Form supports 4 types:
 *   - text  → rich text content (translatable)
 *   - video → upload mp4 or paste YouTube/Vimeo URL
 *   - embed → paste Genially / Canva / iframe URL
 *   - pdf   → upload PDF file
 */
class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static string | UnitEnum | null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string { return __('Lessons'); }
    public static function getNavigationGroup(): ?string { return __('Learning'); }
    public static function getModelLabel(): string { return __('Lesson'); }
    public static function shouldRegisterNavigation(): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
                TextInput::make('code')
                    ->label(__('Code (optional)'))
                    ->placeholder('N42601')
                    ->maxLength(32)
                    ->helperText(__('Short admin-facing code, e.g. "N42601_Kata Kerja".'))
                    ->columnSpan(1),

                Select::make('chapter_id')->label(__('Chapter'))->required()
                    ->options(fn () => Chapter::with('course')->get()->mapWithKeys(fn ($c) => [$c->id => ($c->course?->t('title') ?? '?').' › '.$c->t('title')]))
                    ->searchable()
                    ->columnSpan(1),

                Select::make('type')
                    ->label(__('Lesson type'))
                    ->options([
                        'text'  => __('Text / Reading'),
                        'video' => __('Video'),
                        'embed' => __('Embed (Genially / Canva / iframe)'),
                        'pdf'   => __('PDF'),
                    ])->default('text')->required()->live()->native(false),

                TextInput::make('sort_order')->label(__('Position'))->numeric()->default(0),
                TextInput::make('duration_minutes')->label(__('Duration'))->numeric()->suffix(__('min')),
                Toggle::make('is_free_preview')->label(__('Free preview'))->inline(false),
            ]),

            TranslatableTabs::for('title', TextInput::class, label: __('Title'), required: true),

            // Type-specific content — only the matching field renders, so no
            // outer Section wrapper (TranslatableTabs already renders its own
            // card for the rich-text content).
            TextInput::make('video_url')
                ->label(__('Video URL'))
                ->url()
                ->visible(fn (Get $get) => $get('type') === 'video')
                ->prefix('https://')
                ->placeholder('https://www.youtube.com/embed/...')
                ->helperText(__('YouTube embed URL or any direct mp4 link.')),

            TextInput::make('embed_url')
                ->label(__('Embed URL'))
                ->url()
                ->visible(fn (Get $get) => $get('type') === 'embed')
                ->prefix('https://')
                ->placeholder('https://view.genial.ly/...')
                ->helperText(__('Paste the embed URL from Genially, Canva, or any iframe-friendly source.')),

            FileUpload::make('pdf_path')
                ->label(__('PDF file'))
                ->disk('public')->directory('materials/pdfs')
                ->acceptedFileTypes(['application/pdf'])
                ->maxSize(20480)
                ->visible(fn (Get $get) => $get('type') === 'pdf'),

            TranslatableTabs::for('content', RichEditor::class, label: __('Text content'))
                ->visible(fn (Get $get) => $get('type') === 'text'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('sort_order')->label('#'),
            TextColumn::make('code')->label(__('Code'))->badge()->color('gray')->toggleable(),
            TextColumn::make('chapter.course.title')->label(__('Course'))
                ->formatStateUsing(fn ($state, $record) => $record->chapter?->course?->t('title') ?? '—')->limit(30),
            TextColumn::make('chapter.title')->label(__('Chapter'))
                ->formatStateUsing(fn ($state, $record) => $record->chapter?->t('title') ?? '—')->limit(30),
            TextColumn::make('title')->label(__('Title'))
                ->formatStateUsing(fn ($state, $record) => $record->t('title'))->limit(40)->wrap(),
            TextColumn::make('type')->badge()
                ->colors(['warning' => 'video', 'info' => 'pdf', 'success' => 'embed', 'gray' => 'text']),
            TextColumn::make('duration_minutes')->suffix(' '.__('min'))->toggleable(),
            IconColumn::make('is_free_preview')->boolean()->label(__('Free')),
        ])
        ->defaultSort('sort_order')
        ->reorderable('sort_order')
        ->filters([
            SelectFilter::make('type')->options([
                'text'  => __('Text'),
                'video' => __('Video'),
                'embed' => __('Embed'),
                'pdf'   => __('PDF'),
            ]),
            SelectFilter::make('chapter_id')->label(__('Chapter'))
                ->options(fn () => Chapter::with('course')->get()->mapWithKeys(fn ($c) => [$c->id => ($c->course?->t('title') ?? '?').' › '.$c->t('title')])),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit'   => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }
}
