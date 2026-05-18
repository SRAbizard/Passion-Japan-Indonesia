<?php

namespace App\Filament\Resources\Chapters\RelationManagers;

use App\Filament\Support\TranslatableTabs;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Chapter quizzes — inline create/edit. "Manage questions" jumps into
 * the standalone QuizResource edit page for the heavy question repeater.
 */
class QuizzesRelationManager extends RelationManager
{
    protected static string $relationship = 'quizzes';
    protected static ?string $title       = 'Quiz';
    protected static ?string $modelLabel  = 'Quiz';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
                TextInput::make('code')->label(__('Code (optional)'))
                    ->placeholder('LSN401')->maxLength(32),
                TextInput::make('sort_order')->label(__('Position'))->numeric()->default(0),
                TextInput::make('passing_score')->label(__('Passing score'))
                    ->numeric()->minValue(0)->maxValue(100)->default(70)->suffix('%'),
                TextInput::make('time_limit_minutes')->label(__('Time limit (min)'))
                    ->numeric()->minValue(0)->placeholder(__('No limit')),
                TextInput::make('max_attempts')->label(__('Max attempts'))
                    ->numeric()->minValue(0)->default(0)->helperText(__('0 = unlimited')),
                Toggle::make('is_published')->default(true)->inline(false),
            ]),
            TranslatableTabs::for('title', TextInput::class, label: __('Title'), required: true),
            TranslatableTabs::for('subtitle', TextInput::class, label: __('Subtitle (optional)')),
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
            TextColumn::make('questions_count')->counts('questions')->label(__('Questions'))->badge(),
            TextColumn::make('passing_score')->suffix('%')->label(__('Pass')),
            IconColumn::make('is_published')->boolean(),
        ])
        ->defaultSort('sort_order')
        ->reorderable('sort_order')
        ->headerActions([
            CreateAction::make()->label(__('+ Tambah Quiz'))->icon('heroicon-o-plus-circle')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['type']      = 'chapter';
                    $data['course_id'] = $this->ownerRecord->course_id;
                    return $data;
                }),
        ])
        ->recordActions([
            EditAction::make(),
            Action::make('manage_questions')
                ->label(__('Kelola soal'))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn ($record) => route('filament.admin.resources.quizzes.edit', $record))
                ->color('primary'),
            DeleteAction::make(),
        ])
        ->toolbarActions([
            BulkActionGroup::make([DeleteBulkAction::make()]),
        ]);
    }
}
