<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Support\TranslatableTabs;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
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
 * Final exam relation — surfaces only quizzes of type=final for this
 * course. A course typically has 0 or 1 final exam.
 */
class FinalExamRelationManager extends RelationManager
{
    protected static string $relationship = 'finalExams';   // see Course::finalExams()
    protected static ?string $title       = 'Final exam';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
                TextInput::make('code')->label(__('Code (optional)'))->maxLength(32)
                    ->placeholder('FINAL_N5'),
                Toggle::make('is_published')->default(true)->inline(false),
                TextInput::make('passing_score')->numeric()->minValue(0)->maxValue(100)
                    ->default(70)->suffix('%'),
                TextInput::make('time_limit_minutes')->numeric()->minValue(0)
                    ->placeholder(__('No limit')),
                TextInput::make('max_attempts')->numeric()->minValue(0)->default(0)
                    ->helperText(__('0 = unlimited')),
            ]),
            TranslatableTabs::for('title', TextInput::class, label: __('Title'), required: true),
            TranslatableTabs::for('subtitle', TextInput::class, label: __('Subtitle')),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->label(__('Code'))->badge()->color('gray'),
            TextColumn::make('title')->label(__('Title'))
                ->formatStateUsing(fn ($state, $record) => $record->t('title'))->limit(60),
            TextColumn::make('questions_count')->counts('questions')->label(__('Questions'))->badge(),
            TextColumn::make('passing_score')->suffix('%'),
            IconColumn::make('is_published')->boolean(),
        ])
        ->headerActions([
            CreateAction::make()
                ->label(__('+ Buat Final Exam'))
                ->icon('heroicon-o-plus-circle')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['type'] = 'final';
                    return $data;
                })
                // One final exam per course is the typical case — hide the
                // button once one already exists. Admin can still delete &
                // recreate, or open the underlying quiz to manage questions.
                ->hidden(fn () => $this->ownerRecord->finalExams()->exists()),
        ])
        ->recordActions([
            EditAction::make(),
            Action::make('manage_questions')
                ->label(__('Kelola soal'))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn ($record) => route('filament.admin.resources.quizzes.edit', $record))
                ->color('primary'),
            DeleteAction::make(),
        ]);
    }
}
