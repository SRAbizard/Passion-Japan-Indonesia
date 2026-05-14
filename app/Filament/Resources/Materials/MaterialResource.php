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
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static string | UnitEnum | null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string { return __('Lessons'); }
    public static function getNavigationGroup(): ?string { return __('Learning'); }
    public static function getModelLabel(): string { return __('Lesson'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
                Select::make('chapter_id')->label(__('Chapter'))->required()
                    ->options(fn () => Chapter::with('course')->get()->mapWithKeys(fn ($c) => [$c->id => ($c->course?->t('title') ?? '?').' › '.$c->t('title')]))
                    ->searchable(),
                Select::make('type')
                    ->options([
                        'video' => __('Video'),
                        'pdf'   => __('PDF'),
                        'text'  => __('Text / Reading'),
                    ])->default('text')->required()->live(),
                TextInput::make('sort_order')->numeric()->default(0),
                TextInput::make('duration_minutes')->numeric()->suffix(__('min')),
                Toggle::make('is_free_preview')->label(__('Free preview'))->inline(false),
            ]),
            TranslatableTabs::for('title', TextInput::class, label: __('Title'), required: true),
            Section::make(__('Content'))->components([
                TextInput::make('video_url')->url()->visible(fn ($get) => $get('type') === 'video')
                    ->prefix('https://')->placeholder('https://www.youtube.com/embed/...'),
                FileUpload::make('pdf_path')->disk('public')->directory('materials/pdfs')
                    ->acceptedFileTypes(['application/pdf'])
                    ->visible(fn ($get) => $get('type') === 'pdf'),
                TranslatableTabs::for('content', RichEditor::class, label: __('Text content'))
                    ->visible(fn ($get) => $get('type') === 'text'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('sort_order')->label('#'),
            TextColumn::make('chapter.course.title')->label(__('Course'))
                ->formatStateUsing(fn ($state, $record) => $record->chapter?->course?->t('title') ?? '—')->limit(30),
            TextColumn::make('chapter.title')->label(__('Chapter'))
                ->formatStateUsing(fn ($state, $record) => $record->chapter?->t('title') ?? '—')->limit(30),
            TextColumn::make('title')->label(__('Title'))
                ->formatStateUsing(fn ($state, $record) => $record->t('title'))->limit(40)->wrap(),
            TextColumn::make('type')->badge()
                ->colors(['warning' => 'video', 'info' => 'pdf', 'gray' => 'text']),
            TextColumn::make('duration_minutes')->suffix(' '.__('min'))->toggleable(),
            IconColumn::make('is_free_preview')->boolean()->label(__('Free')),
        ])
        ->defaultSort('sort_order')
        ->reorderable('sort_order')
        ->filters([
            SelectFilter::make('type')->options(['video' => __('Video'), 'pdf' => __('PDF'), 'text' => __('Text / Reading')]),
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
