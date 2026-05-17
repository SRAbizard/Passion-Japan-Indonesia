<?php

namespace App\Filament\Resources\Quizzes\RelationManagers;

use App\Filament\Support\TranslatableTabs;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Manage Dokkai reading passages attached to a quiz. Each passage can
 * be referenced by multiple Dokkai questions (the JLPT pattern: one
 * paragraph + 3-5 questions about it).
 */
class PassagesRelationManager extends RelationManager
{
    protected static string $relationship = 'passages';

    protected static ?string $title = 'Reading passages (Dokkai)';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
                TextInput::make('sort_order')->numeric()->default(0)->label(__('Sort order')),
            ]),
            TranslatableTabs::for('title', TextInput::class,
                label: __('Title (optional, for admin reference)')),
            TranslatableTabs::for('content', Textarea::class,
                label: __('Passage content'),
                required: true,
                componentMods: ['rows' => [10]]),
            TranslatableTabs::for('translation', Textarea::class,
                label: __('Translation / notes (optional, shown to student after submit)'),
                componentMods: ['rows' => [4]]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('sort_order')->label('#'),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->formatStateUsing(fn ($state, $record) => $record->t('title') ?: '—')
                    ->limit(40),
                TextColumn::make('content')
                    ->label(__('Preview'))
                    ->formatStateUsing(fn ($state, $record) => \Str::limit(strip_tags($record->t('content')), 80))
                    ->wrap(),
                TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label(__('Used by'))
                    ->badge(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
