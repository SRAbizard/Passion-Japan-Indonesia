<?php

namespace App\Filament\Resources\Chapters;

use App\Filament\Resources\Chapters\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\Chapter;
use App\Models\Course;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ChapterResource extends Resource
{
    protected static ?string $model = Chapter::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string | UnitEnum | null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string { return __('Chapters'); }
    public static function getNavigationGroup(): ?string { return __('Learning'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
                Select::make('course_id')->label(__('Course'))->required()
                    ->options(fn () => Course::orderBy('id')->get()->mapWithKeys(fn ($c) => [$c->id => $c->t('title')]))
                    ->searchable(),
                TextInput::make('sort_order')->numeric()->default(0),
                Toggle::make('is_published')->default(true)->inline(false),
            ]),
            TranslatableTabs::for('title', TextInput::class, label: __('Title'), required: true),
            TranslatableTabs::for('description', Textarea::class, label: __('Description'),
                componentMods: ['rows' => [3]]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('sort_order')->label('#'),
            TextColumn::make('course.title')->label(__('Course'))
                ->formatStateUsing(fn ($state, $record) => $record->course?->t('title') ?? '—')->limit(40),
            TextColumn::make('title')->label(__('Title'))
                ->formatStateUsing(fn ($state, $record) => $record->t('title'))->limit(50)->wrap(),
            TextColumn::make('materials_count')->counts('materials')->label(__('Lessons'))->badge(),
            IconColumn::make('is_published')->boolean(),
        ])
        ->defaultSort('sort_order')
        ->reorderable('sort_order')
        ->filters([
            SelectFilter::make('course_id')->label(__('Course'))
                ->options(fn () => Course::orderBy('id')->get()->mapWithKeys(fn ($c) => [$c->id => $c->t('title')])),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListChapters::route('/'),
            'create' => Pages\CreateChapter::route('/create'),
            'edit'   => Pages\EditChapter::route('/{record}/edit'),
        ];
    }
}
