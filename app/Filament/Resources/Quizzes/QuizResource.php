<?php

namespace App\Filament\Resources\Quizzes;

use App\Filament\Resources\Quizzes\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Quiz;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Quiz admin — simplified for ArkaLearn/Dicoding-style courses.
 *
 * Removed:
 *  - JLPT 5-section taxonomy (Choukai/Dokkai/Bunpou/Kotoba/Kanji) from
 *    questions — the ArkaLearn pattern is to split sections into their
 *    own chapters (e.g. "Pt 1", "Choukai") rather than tagging questions.
 *  - Passages relation manager — same rationale.
 *
 * Admin still has audio + image per question, but they're plain
 * optional uploads now, not section-gated.
 *
 * Navigation: hidden from the sidebar. Reached via Chapter → Quizzes
 * relation manager (chapter quizzes) or Course → Final exam relation
 * manager (final exam).
 */
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
    public static function shouldRegisterNavigation(): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Basics'))->columns(2)->components([
                TextInput::make('code')
                    ->label(__('Code (optional)'))
                    ->placeholder('LSN401')
                    ->maxLength(32)
                    ->helperText(__('Short admin-facing code, e.g. "LSN401" or "N426QZ".'))
                    ->columnSpan(1),

                Toggle::make('is_published')->label(__('Published'))->default(true)->inline(false)->columnSpan(1),

                // Context selectors — usually pre-filled by the parent relation
                // manager. Kept visible for the standalone /admin/quizzes/* page.
                Radio::make('type')
                    ->label(__('Quiz type'))
                    ->options([
                        'chapter' => __('Chapter quiz'),
                        'final'   => __('Final exam (course-wide)'),
                    ])
                    ->default('chapter')
                    ->inline()
                    ->required()
                    ->live()
                    ->columnSpanFull(),

                Select::make('course_id')
                    ->label(__('Course'))
                    ->options(fn () => Course::all()->mapWithKeys(fn ($c) => [$c->id => $c->t('title')]))
                    ->searchable()
                    ->required()
                    ->live(),

                Select::make('chapter_id')
                    ->label(__('Chapter'))
                    ->options(function (Get $get) {
                        $courseId = $get('course_id');
                        if (! $courseId) return [];
                        return Chapter::where('course_id', $courseId)
                            ->orderBy('sort_order')
                            ->get()
                            ->mapWithKeys(fn ($ch) => [$ch->id => '#'.$ch->sort_order.' — '.$ch->t('title')]);
                    })
                    ->searchable()
                    ->visible(fn (Get $get) => $get('type') === 'chapter')
                    ->required(fn (Get $get) => $get('type') === 'chapter')
                    ->helperText(__('Final exams leave this empty.')),
            ]),

            TranslatableTabs::for('title', TextInput::class, label: __('Title'), required: true),
            TranslatableTabs::for('subtitle', TextInput::class, label: __('Subtitle (optional)'),
                componentMods: ['helperText' => [__('Small line below the title on the quiz intro page.')]]),

            Section::make(__('Settings'))->columns(4)->collapsible()->collapsed()->components([
                TextInput::make('passing_score')
                    ->label(__('Passing score'))
                    ->numeric()->minValue(0)->maxValue(100)
                    ->default(70)->suffix('%'),
                TextInput::make('time_limit_minutes')
                    ->label(__('Time limit (min)'))
                    ->numeric()->minValue(0)
                    ->placeholder(__('No limit')),
                TextInput::make('max_attempts')
                    ->label(__('Max attempts'))
                    ->numeric()->minValue(0)->default(0)
                    ->helperText(__('0 = unlimited')),
                TextInput::make('sort_order')
                    ->label(__('Position in chapter'))
                    ->numeric()->default(0)
                    ->helperText(__('Lower = earlier in the timeline.')),
            ]),

            Section::make(__('Questions'))
                ->components([
                    Repeater::make('questions')
                        ->relationship('questions')
                        ->orderColumn('sort_order')
                        ->columns(2)
                        ->components([
                            TranslatableTabs::for('question', Textarea::class, label: __('Question'), required: true),

                            FileUpload::make('image_path')
                                ->label(__('Image (optional)'))
                                ->image()
                                ->disk('public')
                                ->directory('quiz-images')
                                ->maxSize(2048)
                                ->imageEditor()
                                ->helperText(__('Attach when the question references a visual (kanji, photo, diagram).'))
                                ->columnSpanFull(),

                            FileUpload::make('audio_path')
                                ->label(__('Audio (optional)'))
                                ->disk('public')
                                ->directory('quiz-audio')
                                ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-m4a', 'audio/mp4', 'audio/ogg'])
                                ->maxSize(10240)
                                ->previewable(false)
                                ->openable()
                                ->downloadable()
                                ->helperText(__('Use for listening (Choukai) questions.'))
                                ->columnSpan(1),

                            TextInput::make('max_audio_plays')
                                ->label(__('Max plays'))
                                ->numeric()->minValue(0)->default(0)
                                ->helperText(__('0 = unlimited (only matters if audio is attached).'))
                                ->columnSpan(1),

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
                                        ->helperText(__('a, b, c, ...')),
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
                                ->helperText(__('Must match one of the choice keys above.')),
                            TextInput::make('points')
                                ->label(__('Points'))
                                ->numeric()->minValue(1)->default(1),
                        ])
                        ->itemLabel(function (array $state): ?string {
                            $q = $state['question']['id'] ?? $state['question']['en'] ?? $state['question']['ja'] ?? __('New question');
                            return \Str::limit(strip_tags($q), 70);
                        })
                        ->collapsible()
                        ->collapsed()
                        ->cloneable()
                        ->defaultItems(0)
                        ->reorderableWithButtons(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->label(__('Code'))->badge()->color('gray'),
            TextColumn::make('title')->label(__('Title'))
                ->formatStateUsing(fn ($state, $record) => $record->t('title'))
                ->searchable()->limit(50)->wrap(),
            TextColumn::make('type')->label(__('Type'))
                ->badge()
                ->color(fn (string $state) => $state === 'final' ? 'warning' : 'gray')
                ->formatStateUsing(fn (string $state) => $state === 'final' ? __('Final exam') : __('Chapter')),
            TextColumn::make('course.title')->label(__('Course'))
                ->formatStateUsing(fn ($state, $record) => $record->course?->t('title') ?? '—')
                ->limit(30),
            TextColumn::make('chapter.title')->label(__('Chapter'))
                ->formatStateUsing(fn ($state, $record) => $record->chapter
                    ? '#'.$record->chapter->sort_order.' '.$record->chapter->t('title')
                    : '—')
                ->limit(30),
            TextColumn::make('questions_count')->counts('questions')->badge()->label(__('Questions')),
            TextColumn::make('passing_score')->suffix('%')->label(__('Pass %')),
            TextColumn::make('attempts_count')->counts('attempts')->badge()->color('info')->label(__('Attempts')),
            IconColumn::make('is_published')->boolean()->label(__('Published')),
            TextColumn::make('updated_at')->dateTime('d M Y')->toggleable()->sortable(),
        ])
        ->filters([
            SelectFilter::make('course_id')->label(__('Course'))
                ->options(fn () => Course::all()->mapWithKeys(fn ($c) => [$c->id => $c->t('title')])),
            SelectFilter::make('type')->label(__('Type'))
                ->options(['chapter' => __('Chapter'), 'final' => __('Final exam')]),
        ])
        ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        // PassagesRelationManager intentionally removed — see class doc.
        return [];
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
