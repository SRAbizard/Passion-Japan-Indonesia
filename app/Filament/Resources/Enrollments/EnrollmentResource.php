<?php

namespace App\Filament\Resources\Enrollments;

use App\Filament\Resources\Enrollments\Pages;
use App\Models\Course;
use App\Models\Enrollment;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    protected static string | UnitEnum | null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string { return __('Enrollments'); }
    public static function getNavigationGroup(): ?string { return __('Learning'); }
    public static function canCreate(): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->components([
                Select::make('status')
                    ->options(collect(Enrollment::STATUSES)->mapWithKeys(fn ($s) => [$s => __('enrollment.status.'.$s)]))
                    ->required()->native(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('user.name')->label(__('Student'))->searchable()->weight('bold'),
            TextColumn::make('course.title')->label(__('Course'))
                ->formatStateUsing(fn ($state, $record) => $record->course?->t('title') ?? '—')
                ->searchable()->limit(40),
            TextColumn::make('progress_pct')->label(__('Progress'))->suffix('%')
                ->color(fn ($state) => $state >= 100 ? 'success' : ($state > 0 ? 'info' : 'gray')),
            TextColumn::make('status')->badge()
                ->formatStateUsing(fn ($state) => __('enrollment.status.'.$state))
                ->color(fn ($state) => Enrollment::STATUS_COLORS[$state] ?? 'gray'),
            TextColumn::make('started_at')->dateTime('d M Y')->toggleable(),
            TextColumn::make('completed_at')->dateTime('d M Y')->toggleable(),
            TextColumn::make('last_activity_at')->dateTime('d M Y H:i')->sortable(),
        ])
        ->defaultSort('last_activity_at', 'desc')
        ->filters([
            SelectFilter::make('status')
                ->options(collect(Enrollment::STATUSES)->mapWithKeys(fn ($s) => [$s => __('enrollment.status.'.$s)])),
            SelectFilter::make('course_id')->label(__('Course'))
                ->options(fn () => Course::all()->mapWithKeys(fn ($c) => [$c->id => $c->t('title')])),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
            'edit'  => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
