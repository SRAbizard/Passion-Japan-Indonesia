<?php

namespace App\Filament\Resources\Galleries;

use App\Filament\Resources\Galleries\Pages;
use App\Filament\Resources\Galleries\RelationManagers\ItemsRelationManager;
use App\Filament\Support\TranslatableTabs;
use App\Models\Gallery;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-photo';
    protected static string | UnitEnum | null $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 25;

    public static function getNavigationLabel(): string { return __('Gallery'); }
    public static function getNavigationGroup(): ?string { return __('CMS'); }
    public static function getModelLabel(): string { return __('Album'); }
    public static function getPluralModelLabel(): string { return __('Albums'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Album info'))->columns(2)->components([
                TextInput::make('slug')->label(__('Slug'))->required()
                    ->unique(ignoreRecord: true)->maxLength(120)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state ?? ''))),
                DatePicker::make('taken_at')->label(__('Taken at')),
                TextInput::make('sort_order')->label(__('Position'))->numeric()->default(0),
                Toggle::make('is_published')->label(__('Published'))->default(true)->inline(false),
            ]),

            TranslatableTabs::for('title', TextInput::class, label: __('Album title'), required: true),
            TranslatableTabs::for('caption', Textarea::class, label: __('Album description'),
                componentMods: ['rows' => [3]]),

            Section::make(__('Cover image'))
                ->description(__('Shown on the album card in the public gallery. If empty, the first item\'s thumbnail is used.'))
                ->components([
                    FileUpload::make('cover_image_path')
                        ->label('')
                        ->image()
                        ->disk('public')
                        ->directory('gallery/covers')
                        ->imageEditor()
                        ->maxSize(8192)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('cover_image_path')->label('')->disk('public')->square(),
            TextColumn::make('title')->label(__('Album'))
                ->formatStateUsing(fn ($state, $record) => $record->t('title') ?: '—')
                ->limit(60)->wrap()->searchable(),
            TextColumn::make('items_count')->counts('items')->label(__('Photos / videos'))->badge()->color('info'),
            TextColumn::make('taken_at')->date('d M Y')->sortable()->toggleable(),
            TextColumn::make('sort_order')->label('#')->sortable(),
            IconColumn::make('is_published')->boolean(),
            TextColumn::make('updated_at')->date('d M Y')->toggleable()->sortable(),
        ])
        ->defaultSort('sort_order')
        ->reorderable('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit'   => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
