<?php

namespace App\Filament\Resources\Galleries;

use App\Filament\Resources\Galleries\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\Gallery;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
    public static function getModelLabel(): string { return __('Gallery item'); }
    public static function getPluralModelLabel(): string { return __('Gallery'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Basics'))->columns(2)->components([
                TextInput::make('slug')->label(__('Slug'))->required()
                    ->unique(ignoreRecord: true)->maxLength(120)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state ?? ''))),
                \Filament\Forms\Components\Select::make('category')
                    ->label(__('Category'))
                    ->options(Gallery::categoryOptions())
                    ->default('general')
                    ->native(false)
                    ->required()
                    ->helperText(__('Determines which tab this item appears under on the public gallery.')),
                DatePicker::make('taken_at')->label(__('Taken at')),
                TextInput::make('sort_order')->label(__('Position'))->numeric()->default(0),
                Toggle::make('is_published')->label(__('Published'))->default(true)->inline(false)->columnSpanFull(),
            ]),

            TranslatableTabs::for('title', TextInput::class, label: __('Title')),
            TranslatableTabs::for('caption', Textarea::class, label: __('Caption'),
                componentMods: ['rows' => [3]]),

            Section::make(__('Media'))->components([
                Radio::make('type')
                    ->label(__('Media type'))
                    ->options([
                        'image'   => __('Photo'),
                        'video'   => __('Uploaded video (mp4)'),
                        'youtube' => __('YouTube link'),
                    ])
                    ->default('image')
                    ->inline()
                    ->required()
                    ->live(),

                FileUpload::make('image_path')
                    ->label(fn (Get $get) => $get('type') === 'image' ? __('Photo') : __('Thumbnail / poster (optional)'))
                    ->image()
                    ->disk('public')
                    ->directory('gallery')
                    ->imageEditor()
                    ->maxSize(8192)
                    ->helperText(fn (Get $get) => $get('type') === 'image'
                        ? __('The main image shown in the gallery.')
                        : __('Shown on the gallery card before the video plays. Optional but recommended.')),

                FileUpload::make('video_path')
                    ->label(__('Video file (mp4)'))
                    ->disk('public')
                    ->directory('gallery/videos')
                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                    ->maxSize(102400)
                    ->previewable(false)
                    ->openable()
                    ->downloadable()
                    ->visible(fn (Get $get) => $get('type') === 'video'),

                TextInput::make('youtube_url')
                    ->label(__('YouTube URL'))
                    ->url()
                    ->placeholder('https://www.youtube.com/watch?v=...')
                    ->helperText(__('Any YouTube watch / share / shorts URL — we extract the ID automatically.'))
                    ->visible(fn (Get $get) => $get('type') === 'youtube'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image_path')->label('')->disk('public')->square(),
            TextColumn::make('title')->label(__('Title'))
                ->formatStateUsing(fn ($state, $record) => $record->t('title') ?: '—')
                ->limit(50)->wrap(),
            TextColumn::make('category')->label(__('Category'))->badge()
                ->color('info')
                ->formatStateUsing(fn ($state) => $state ? __(Gallery::CATEGORIES[$state] ?? $state) : '—'),
            TextColumn::make('type')->badge()
                ->colors(['primary' => 'image', 'warning' => 'video', 'danger' => 'youtube']),
            TextColumn::make('taken_at')->date('d M Y')->sortable()->toggleable(),
            TextColumn::make('sort_order')->label('#')->sortable(),
            IconColumn::make('is_published')->boolean(),
            TextColumn::make('updated_at')->date('d M Y')->toggleable()->sortable(),
        ])
        ->defaultSort('sort_order')
        ->reorderable('sort_order')
        ->filters([
            SelectFilter::make('category')->label(__('Category'))
                ->options(Gallery::categoryOptions()),
            SelectFilter::make('type')->options([
                'image' => __('Photo'), 'video' => __('Video'), 'youtube' => __('YouTube'),
            ]),
        ]);
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
