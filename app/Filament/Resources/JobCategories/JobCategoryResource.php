<?php

namespace App\Filament\Resources\JobCategories;

use App\Filament\Resources\JobCategories\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\JobCategory;
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

class JobCategoryResource extends Resource
{
    protected static ?string $model = JobCategory::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-tag';
    protected static string | UnitEnum | null $navigationGroup = 'Recruitment';
    protected static ?int $navigationSort = 11;

    public static function getNavigationLabel(): string { return __('Job Categories'); }
    public static function getNavigationGroup(): ?string { return __('Recruitment'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(80)
                    ->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('icon')->default('heroicon-o-briefcase')->helperText(__('Heroicon name, e.g. heroicon-o-truck')),
                ColorPicker::make('color')->default('#b32510'),
                TextInput::make('sort_order')->numeric()->default(0),
            ]),
            TranslatableTabs::for('name', TextInput::class, label: __('Name'), required: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('sort_order')->label('#'),
            TextColumn::make('name')->label(__('Name'))->formatStateUsing(fn ($state, $record) => $record->t('name'))->searchable(),
            TextColumn::make('slug')->copyable(),
            ColorColumn::make('color'),
            TextColumn::make('vacancies_count')->counts('vacancies')->label(__('Jobs')),
        ])->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListJobCategories::route('/'),
            'create' => Pages\CreateJobCategory::route('/create'),
            'edit'   => Pages\EditJobCategory::route('/{record}/edit'),
        ];
    }
}
