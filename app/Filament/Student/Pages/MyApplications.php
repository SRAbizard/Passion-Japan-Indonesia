<?php

namespace App\Filament\Student\Pages;

use App\Models\Application;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyApplications extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?int $navigationSort = 30;
    protected string $view = 'filament.student.pages.my-applications';

    public static function getNavigationLabel(): string { return __('My Applications'); }
    public function getTitle(): string                  { return __('My Applications'); }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Application::query()
                ->with('jobVacancy.company', 'jobVacancy.visaCategory')
                ->where('user_id', auth()->id()))
            ->columns([
                TextColumn::make('jobVacancy.title')->label(__('Position'))
                    ->formatStateUsing(fn ($state, $record) => $record->jobVacancy?->t('title') ?? '—')
                    ->searchable()->limit(60)->wrap()->weight('bold'),
                TextColumn::make('jobVacancy.company.name')->label(__('Company'))->searchable(),
                TextColumn::make('jobVacancy.visaCategory.name')->label(__('Visa'))
                    ->formatStateUsing(fn ($state, $record) => $record->jobVacancy?->visaCategory?->t('name') ?? '—')
                    ->badge()->color('info'),
                TextColumn::make('status')->label(__('Status'))->badge()
                    ->formatStateUsing(fn ($state) => __('application.status.'.$state))
                    ->color(fn ($state) => match ($state) {
                        'submitted' => 'gray',
                        'under_review' => 'info',
                        'interview_scheduled' => 'warning',
                        'offered', 'accepted' => 'success',
                        'rejected', 'withdrawn' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime('d M Y')->label(__('Applied'))->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(['submitted','under_review','interview_scheduled','offered','accepted','rejected','withdrawn'])
                        ->mapWithKeys(fn ($s) => [$s => __('application.status.'.$s)])),
            ])
            ->recordUrl(fn ($record) => $record->jobVacancy ? route('job.show', $record->jobVacancy->slug) : null)
            ->emptyStateHeading(__('No applications yet'))
            ->emptyStateDescription(__('Browse the job board to find your next role.'))
            ->emptyStateActions([
                \Filament\Actions\Action::make('browse')
                    ->label(__('Browse jobs'))
                    ->url(fn () => route('job.index'))
                    ->icon('heroicon-o-magnifying-glass'),
            ]);
    }
}
