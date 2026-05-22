<?php

namespace App\Filament\Resources\Galleries\RelationManagers;

use App\Filament\Support\TranslatableTabs;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Photo/video items inside a Gallery album. Admin creates album once,
 * then bulk-uploads many items here. Each item type swaps the form
 * inputs (image upload, mp4 upload, or YouTube URL) and carries its
 * own translatable caption.
 */
class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title       = 'Photos & videos';
    protected static ?string $modelLabel  = 'Item';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
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
                    ->live()
                    ->columnSpanFull(),

                TextInput::make('sort_order')->label(__('Position'))->numeric()->default(0),
                Toggle::make('is_published')->label(__('Published'))->default(true)->inline(false),
            ]),

            // Multi-upload photo: when admin picks "Photo", we allow
            // selecting many files at once. Each becomes a separate
            // item row on save (the multiple() + reorderable() combo).
            FileUpload::make('image_path')
                ->label(__('Photo'))
                ->image()
                ->disk('public')
                ->directory('gallery')
                ->imageEditor()
                ->maxSize(8192)
                ->visible(fn (Get $get) => $get('type') === 'image'),

            FileUpload::make('video_path')
                ->label(__('Video file (mp4)'))
                ->disk('public')
                ->directory('gallery/videos')
                ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                ->maxSize(1024000) // 1 GB
                ->previewable(false)
                ->openable()
                ->downloadable()
                ->visible(fn (Get $get) => $get('type') === 'video'),

            TextInput::make('youtube_url')
                ->label(__('YouTube URL'))
                ->url()
                ->placeholder('https://www.youtube.com/watch?v=...')
                ->visible(fn (Get $get) => $get('type') === 'youtube'),

            TranslatableTabs::for('caption', Textarea::class, label: __('Caption / description'),
                componentMods: ['rows' => [3]]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image_path')->label('')->disk('public')->square()
                ->defaultImageUrl(fn ($record) => $record->thumbnail_url),
            TextColumn::make('caption')->label(__('Caption'))
                ->formatStateUsing(fn ($state, $record) => $record->t('caption') ?: '—')
                ->limit(60)->wrap(),
            TextColumn::make('type')->badge()
                ->colors(['primary' => 'image', 'warning' => 'video', 'danger' => 'youtube']),
            TextColumn::make('sort_order')->label('#'),
            IconColumn::make('is_published')->boolean(),
        ])
        ->defaultSort('sort_order')
        ->reorderable('sort_order')
        ->headerActions([
            CreateAction::make()->label(__('+ Tambah Foto / Video'))->icon('heroicon-o-plus-circle'),
        ])
        ->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])
        ->toolbarActions([
            BulkActionGroup::make([DeleteBulkAction::make()]),
        ]);
    }
}
