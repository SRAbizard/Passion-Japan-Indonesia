<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Support\TranslatableTabs;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Chapters of a course — replaces the standalone Chapters sidebar entry.
 * Admin can create/edit chapters inline in modals from the course page.
 */
class ChaptersRelationManager extends RelationManager
{
    protected static string $relationship = 'chapters';
    protected static ?string $title       = 'Bab / Chapter';
    protected static ?string $modelLabel  = 'Bab';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
                TextInput::make('sort_order')->label(__('Position'))->numeric()->default(0),
                Select::make('unlock_mode')
                    ->label(__('Lesson access'))
                    ->options([
                        'free'       => __('Free — all open'),
                        'sequential' => __('Sequential — locked progression'),
                    ])
                    ->default('free')
                    ->required()
                    ->native(false),
                Toggle::make('is_published')->default(true)->inline(false)->columnSpanFull(),
            ]),
            TranslatableTabs::for('title', TextInput::class, label: __('Title'), required: true),
            TranslatableTabs::for('description', Textarea::class, label: __('Description'),
                componentMods: ['rows' => [3]]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('sort_order')->label('#'),
            TextColumn::make('title')->label(__('Title'))
                ->formatStateUsing(fn ($state, $record) => $record->t('title'))->limit(60)->wrap(),
            TextColumn::make('materials_count')->counts('materials')->label(__('Lessons'))->badge()->color('gray'),
            TextColumn::make('quizzes_count')->counts('quizzes')->label(__('Quizzes'))->badge()->color('info'),
            TextColumn::make('unlock_mode')->label(__('Access'))->badge()
                ->color(fn ($state) => $state === 'sequential' ? 'warning' : 'gray')
                ->formatStateUsing(fn ($state) => $state === 'sequential' ? __('Sequential') : __('Free')),
            IconColumn::make('is_published')->boolean(),
        ])
        ->defaultSort('sort_order')
        ->reorderable('sort_order')
        ->headerActions([
            CreateAction::make()->label(__('+ Tambah Bab'))->icon('heroicon-o-plus-circle'),
        ])
        ->recordActions([
            EditAction::make()->label(__('Edit'))->modalHeading(__('Edit chapter')),
            \Filament\Actions\Action::make('manage')
                ->label(__('Kelola materi & quiz'))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn ($record) => route('filament.admin.resources.chapters.edit', $record))
                ->color('primary'),
            DeleteAction::make(),
        ])
        ->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }
}
