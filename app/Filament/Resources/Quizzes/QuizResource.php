<?php

namespace App\Filament\Resources\Quizzes;

use App\Filament\Resources\Quizzes\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\Course;
use App\Models\Quiz;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static string | UnitEnum | null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string { return __('Quizzes'); }
    public static function getNavigationGroup(): ?string { return __('Learning'); }
    public static function getModelLabel(): string { return __('Quiz'); }
    public static function getPluralModelLabel(): string { return __('Quizzes'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Settings'))->columns(2)->components([
                Select::make('course_id')
                    ->label(__('Course'))
                    ->options(fn () => Course::all()->mapWithKeys(fn ($c) => [$c->id => $c->t('title')]))
                    ->searchable()
                    ->required(),
                TextInput::make('passing_score')
                    ->label(__('Passing score'))
                    ->numeric()->minValue(0)->maxValue(100)
                    ->default(70)->suffix('%'),
                TextInput::make('time_limit_minutes')
                    ->label(__('Time limit (minutes)'))
                    ->numeric()->minValue(0)
                    ->placeholder(__('No limit')),
                TextInput::make('max_attempts')
                    ->label(__('Max attempts'))
                    ->numeric()->minValue(0)->default(0)
                    ->helperText(__('0 = unlimited')),
                Toggle::make('is_published')->label(__('Published'))->default(true)->inline(false),
            ]),

            TranslatableTabs::for('title', TextInput::class, label: __('Title'), required: true),
            TranslatableTabs::for('description', Textarea::class, label: __('Description')),

            Section::make(__('Questions'))->components([
                Repeater::make('questions')
                    ->relationship('questions')
                    ->orderColumn('sort_order')
                    ->columns(2)
                    ->components([
                        TranslatableTabs::for('question', Textarea::class, label: __('Question'), required: true),
                        Repeater::make('choices')
                            ->label(__('Choices'))
                            ->columnSpanFull()
                            ->minItems(2)
                            ->maxItems(6)
                            ->components([
                                TextInput::make('key')
                                    ->label(__('Key'))
                                    ->required()
                                    ->maxLength(8)
                                    ->placeholder('a')
                                    ->helperText(__('Short identifier (a, b, c, ...)')),
                                TextInput::make('text.id')
                                    ->label(__('Text (ID)'))
                                    ->required(),
                                TextInput::make('text.en')
                                    ->label(__('Text (EN)')),
                                TextInput::make('text.ja')
                                    ->label(__('Text (JA)')),
                            ])
                            ->columns(4)
                            ->defaultItems(2),
                        TextInput::make('correct_answer')
                            ->label(__('Correct answer key'))
                            ->required()
                            ->maxLength(8)
                            ->helperText(__('Must match one of the choice keys above')),
                        TextInput::make('points')
                            ->label(__('Points'))
                            ->numeric()->minValue(1)->default(1),
                    ])
                    ->itemLabel(fn (array $state): ?string =>
                        $state['question']['id'] ?? $state['question']['en'] ?? __('New question')
                    )
                    ->collapsible()
                    ->defaultItems(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label(__('Title'))
                ->formatStateUsing(fn ($state, $record) => $record->t('title'))
                ->searchable()->limit(50)->wrap(),
            TextColumn::make('course.title')->label(__('Course'))
                ->formatStateUsing(fn ($state, $record) => $record->course?->t('title') ?? '—')
                ->limit(40),
            TextColumn::make('questions_count')->counts('questions')->badge()->label(__('Questions')),
            TextColumn::make('passing_score')->suffix('%')->label(__('Pass %')),
            TextColumn::make('attempts_count')->counts('attempts')->badge()->color('info')->label(__('Attempts')),
            IconColumn::make('is_published')->boolean()->label(__('Published')),
            TextColumn::make('updated_at')->dateTime('d M Y')->toggleable()->sortable(),
        ])
        ->filters([
            SelectFilter::make('course_id')->label(__('Course'))
                ->options(fn () => Course::all()->mapWithKeys(fn ($c) => [$c->id => $c->t('title')])),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQuizzes::route('/'),
            'create' => Pages\CreateQuiz::route('/create'),
            'edit'   => Pages\EditQuiz::route('/{record}/edit'),
        ];
    }
}
