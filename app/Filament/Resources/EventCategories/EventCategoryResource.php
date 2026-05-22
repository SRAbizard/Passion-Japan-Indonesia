<?php

namespace App\Filament\Resources\EventCategories;

use App\Filament\Resources\EventCategories\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\EventCategory;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class EventCategoryResource extends Resource
{
    protected static ?string $model = EventCategory::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-tag';
    protected static string | UnitEnum | null $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 21;

    public static function getNavigationLabel(): string { return __('Event Categories'); }
    public static function getNavigationGroup(): ?string { return __('CMS'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(80)
                    ->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                TextInput::make('sort_order')->numeric()->default(0),
                ColorPicker::make('color')->default('#b32510'),
            ]),
            TranslatableTabs::for('name', TextInput::class, label: __('Name'), required: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('sort_order')->label('#'),
            TextColumn::make('name')->label(__('Name'))
                ->formatStateUsing(fn ($state, $record) => $record->t('name'))->searchable(),
            TextColumn::make('slug')->copyable(),
            ColorColumn::make('color'),
            TextColumn::make('events_count')->counts('events')->label(__('Events')),
        ])->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEventCategories::route('/'),
            'create' => Pages\CreateEventCategory::route('/create'),
            'edit'   => Pages\EditEventCategory::route('/{record}/edit'),
        ];
    }
}
