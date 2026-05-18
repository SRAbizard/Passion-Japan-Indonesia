<?php

namespace App\Filament\Resources\Courses;

use App\Filament\Resources\Courses\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    protected static string | UnitEnum | null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string { return __('Courses'); }
    public static function getNavigationGroup(): ?string { return __('Learning'); }
    public static function getModelLabel(): string { return __('Course'); }

    public static function getGloballySearchableAttributes(): array
    {
        return ['slug', 'title->id', 'title->en', 'title->ja'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->t('title');
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            __('Level')    => __('level.'.$record->level),
            __('Category') => $record->category?->t('name') ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Basics'))->columns(2)->components([
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(120)
                    ->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state ?? ''))),
                Select::make('course_category_id')->label(__('Category'))
                    ->options(fn () => CourseCategory::orderBy('sort_order')->get()->mapWithKeys(fn ($c) => [$c->id => $c->t('name')]))
                    ->searchable(),
                Select::make('instructor_id')->label(__('Instructor'))
                    ->options(fn () => User::role(['admin', 'superadmin'])->pluck('name', 'id'))
                    ->searchable(),
                Select::make('level')
                    ->options([
                        'beginner'     => __('Beginner'),
                        'elementary'   => __('Elementary'),
                        'intermediate' => __('Intermediate'),
                        'advanced'     => __('Advanced'),
                    ])->default('beginner'),
                TextInput::make('duration_days')->numeric()->default(365)->suffix(__('days')),
                DateTimePicker::make('published_at')->seconds(false)->default(now()),
            ]),
            TranslatableTabs::for('title', TextInput::class, label: __('Title'), required: true),
            TranslatableTabs::for('subtitle', TextInput::class, label: __('Subtitle')),
            TranslatableTabs::for('description', RichEditor::class, label: __('Description'), required: true),
            TranslatableTabs::for('what_you_learn', Textarea::class, label: __('What you will learn'),
                componentMods: ['rows' => [5], 'helperText' => [__('One bullet per line.')]]),
            TranslatableTabs::for('prerequisites', Textarea::class, label: __('Prerequisites'),
                componentMods: ['rows' => [3]]),
            Section::make(__('Media'))->columns(2)->components([
                FileUpload::make('thumbnail_path')->image()->disk('public')->directory('courses')->imageEditor(),
                TextInput::make('intro_video_url')->url()->prefix('https://')->helperText(__('YouTube or Vimeo embed URL.')),
            ]),
            Section::make(__('Pricing & status'))->columns(4)->components([
                TextInput::make('price')->numeric()->default(0),
                Select::make('currency')->options(['IDR' => 'IDR', 'JPY' => 'JPY', 'USD' => 'USD'])->default('IDR'),
                Toggle::make('is_free')->inline(false),
                Toggle::make('is_featured')->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('thumbnail_path')->disk('public')->square()->label(''),
            TextColumn::make('title')->label(__('Title'))
                ->formatStateUsing(fn ($state, $record) => $record->t('title'))
                ->searchable(query: fn ($q, $search) => $q->where('title->id','like',"%{$search}%")->orWhere('title->en','like',"%{$search}%"))
                ->limit(50)->wrap(),
            TextColumn::make('category.name')->label(__('Category'))
                ->formatStateUsing(fn ($state, $record) => $record->category?->t('name') ?? '—')->badge(),
            TextColumn::make('level')->badge()
                ->formatStateUsing(fn ($state) => __(\Illuminate\Support\Str::title($state))),
            TextColumn::make('chapters_count')->counts('chapters')->label(__('Chapters'))->badge()->color('gray'),
            TextColumn::make('enrollments_count')->counts('enrollments')->label(__('Enrolled'))->badge()->color('success'),
            IconColumn::make('is_featured')->boolean()->label('★'),
            IconColumn::make('is_free')->boolean()->label(__('Free')),
            TextColumn::make('published_at')->dateTime('d M Y')->sortable()->toggleable(),
        ])
        ->defaultSort('published_at', 'desc')
        ->filters([
            SelectFilter::make('course_category_id')->label(__('Category'))
                ->options(fn () => CourseCategory::all()->mapWithKeys(fn ($c) => [$c->id => $c->t('name')])),
            SelectFilter::make('level')->options(['beginner' => 'Beginner', 'elementary' => 'Elementary', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced']),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ChaptersRelationManager::class,
            RelationManagers\FinalExamRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit'   => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
