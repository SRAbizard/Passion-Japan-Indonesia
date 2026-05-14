<?php

namespace App\Filament\Resources\VisaCategories;

use App\Filament\Resources\VisaCategories\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\VisaCategory;
use BackedEnum;
use App\Models\StudentDocument;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class VisaCategoryResource extends Resource
{
    protected static ?string $model = VisaCategory::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-identification';
    protected static string | UnitEnum | null $navigationGroup = 'Recruitment';
    protected static ?int $navigationSort = 12;

    public static function getNavigationLabel(): string { return __('Visa Categories'); }
    public static function getNavigationGroup(): ?string { return __('Recruitment'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(80)
                    ->live(onBlur: true)->afterStateUpdated(fn ($s, $set) => $set('slug', Str::slug($s))),
                TextInput::make('sort_order')->numeric()->default(0),
                ColorPicker::make('color')->default('#b32510'),
            ]),
            TranslatableTabs::for('name', TextInput::class, label: __('Name'), required: true),
            TranslatableTabs::for('description', Textarea::class, label: __('Description'),
                componentMods: ['rows' => [3]]),

            Section::make(__('Required documents'))
                ->description(__('Documents students must upload when targeting this visa.'))
                ->components([
                    Select::make('required_documents')
                        ->label(__('Required documents'))
                        ->multiple()
                        ->searchable()
                        ->options(collect(StudentDocument::TYPES)
                            ->mapWithKeys(fn ($t) => [$t => __('document.type.'.$t)]))
                        ->columnSpanFull(),
                ]),
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
            TextColumn::make('required_documents')
                ->label(__('Required docs'))
                ->formatStateUsing(fn ($state) => is_array($state) ? count($state).' '.__('types') : '0')
                ->badge()->color('info')->alignCenter(),
        ])->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVisaCategories::route('/'),
            'create' => Pages\CreateVisaCategory::route('/create'),
            'edit'   => Pages\EditVisaCategory::route('/{record}/edit'),
        ];
    }
}
