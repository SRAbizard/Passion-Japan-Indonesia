<?php

namespace App\Filament\Resources\Chapters\RelationManagers;

use App\Filament\Support\TranslatableTabs;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Lessons inside a chapter — inline create/edit via modal.
 * Form mirrors MaterialResource::form() but adapted for relation context.
 */
class MaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'materials';
    protected static ?string $title       = 'Materi / Lesson';
    protected static ?string $modelLabel  = 'Materi';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
                TextInput::make('code')->label(__('Code (optional)'))
                    ->placeholder('N42601')->maxLength(32),
                Select::make('type')->label(__('Lesson type'))
                    ->options([
                        'text'  => __('Text / Reading'),
                        'video' => __('Video'),
                        'embed' => __('Embed (Genially / Canva / iframe)'),
                        'pdf'   => __('PDF'),
                    ])->default('text')->required()->live()->native(false),
                TextInput::make('sort_order')->label(__('Position'))->numeric()->default(0),
                TextInput::make('duration_minutes')->label(__('Duration'))->numeric()->suffix(__('min')),
                Toggle::make('is_free_preview')->label(__('Free preview'))->inline(false)->columnSpanFull(),
            ]),
            TranslatableTabs::for('title', TextInput::class, label: __('Title'), required: true),
            Section::make(__('Content'))->components([
                TextInput::make('video_url')->label(__('Video URL'))->url()
                    ->visible(fn (Get $get) => $get('type') === 'video')
                    ->prefix('https://')->placeholder('https://www.youtube.com/embed/...'),
                TextInput::make('embed_url')->label(__('Embed URL'))->url()
                    ->visible(fn (Get $get) => $get('type') === 'embed')
                    ->prefix('https://')->placeholder('https://view.genial.ly/...'),
                FileUpload::make('pdf_path')->label(__('PDF file'))
                    ->disk('public')->directory('materials/pdfs')
                    ->acceptedFileTypes(['application/pdf'])->maxSize(20480)
                    ->visible(fn (Get $get) => $get('type') === 'pdf'),
                TranslatableTabs::for('content', RichEditor::class, label: __('Text content'))
                    ->visible(fn (Get $get) => $get('type') === 'text'),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('sort_order')->label('#'),
            TextColumn::make('code')->label(__('Code'))->badge()->color('gray')->toggleable(),
            TextColumn::make('title')->label(__('Title'))
                ->formatStateUsing(fn ($state, $record) => $record->t('title'))
                ->limit(60)->wrap(),
            TextColumn::make('type')->badge()
                ->colors(['warning' => 'video', 'info' => 'pdf', 'success' => 'embed', 'gray' => 'text']),
            IconColumn::make('is_free_preview')->boolean()->label(__('Free')),
        ])
        ->defaultSort('sort_order')
        ->reorderable('sort_order')
        ->headerActions([
            CreateAction::make()->label(__('+ Tambah Materi'))->icon('heroicon-o-plus-circle'),
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
