<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages;
use App\Models\User;
use App\Models\VisaCategory;
use App\Support\StudentDocumentProgress;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StudentResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static string | UnitEnum | null $navigationGroup = 'Students';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string { return __('Students'); }
    public static function getNavigationGroup(): ?string { return __('Students'); }
    public static function getModelLabel(): string      { return __('Student'); }
    public static function getPluralModelLabel(): string { return __('Students'); }

    public static function canCreate(): bool { return false; }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles', fn ($q) => $q->where('name', 'student'))
            ->withCount(['applications', 'studentDocuments', 'enrollments']);
    }

    public static function form(Schema $schema): Schema
    {
        // Students aren't edited from this resource — admin uses
        // StudentDocumentResource and other tools for that. The form
        // is required by Filament but never rendered (no Edit page).
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('Name'))->searchable()->weight('bold'),
                TextColumn::make('email')->label(__('Email'))->searchable()->copyable(),
                TextColumn::make('phone')->label(__('Phone'))->placeholder('—')->toggleable(),

                TextColumn::make('document_progress')
                    ->label(__('Documents'))
                    ->state(function (User $r): string {
                        $p = StudentDocumentProgress::for($r);
                        return $p['verified_count'].' / '.$p['required_count'];
                    })
                    ->description(function (User $r): ?string {
                        $p = StudentDocumentProgress::for($r);
                        if ($p['required_count'] === 0) return __('No requirements');
                        return $p['pct'].'% '.__('verified')
                            .($p['using_default'] ? ' · '.__('default set') : '');
                    })
                    ->badge()
                    ->color(function (User $r) {
                        $p = StudentDocumentProgress::for($r);
                        return match (true) {
                            $p['pct'] >= 100 => 'success',
                            $p['pct'] >= 50  => 'info',
                            $p['pct'] > 0    => 'warning',
                            default          => 'gray',
                        };
                    }),

                TextColumn::make('target_visas')
                    ->label(__('Target visa'))
                    ->state(function (User $r): string {
                        $slugs = StudentDocumentProgress::targetVisaSlugs($r);
                        if (empty($slugs)) return '—';
                        return VisaCategory::whereIn('slug', $slugs)->get()
                            ->map(fn ($v) => $v->t('name'))
                            ->join(', ');
                    })
                    ->badge()->color('info')
                    ->wrap(),

                TextColumn::make('applications_count')->label(__('Applications'))
                    ->numeric()->alignCenter()->sortable(),
                TextColumn::make('enrollments_count')->label(__('Courses'))
                    ->numeric()->alignCenter()->sortable()->toggleable(),
                TextColumn::make('student_documents_count')->label(__('Docs uploaded'))
                    ->numeric()->alignCenter()->sortable()->toggleable(),

                TextColumn::make('email_verified_at')->label(__('Verified'))
                    ->dateTime('d M Y')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label(__('Registered'))
                    ->dateTime('d M Y')->sortable(),
                TextColumn::make('last_login_at')->label(__('Last login'))
                    ->dateTime('d M Y H:i')->placeholder('—')->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('visa')
                    ->label(__('Targeting visa'))
                    ->options(fn () => VisaCategory::orderBy('sort_order')->get()
                        ->mapWithKeys(fn ($v) => [$v->slug => $v->t('name')]))
                    ->query(function (Builder $q, array $data) {
                        if (! filled($data['value'])) return $q;
                        return $q->whereHas('applications.vacancy.visaCategory',
                            fn ($qq) => $qq->where('slug', $data['value']));
                    }),
                SelectFilter::make('email_verified')
                    ->label(__('Email status'))
                    ->options(['verified' => __('Verified'), 'unverified' => __('Unverified')])
                    ->query(function (Builder $q, array $data) {
                        if ($data['value'] === 'verified')   return $q->whereNotNull('email_verified_at');
                        if ($data['value'] === 'unverified') return $q->whereNull('email_verified_at');
                        return $q;
                    }),
                SelectFilter::make('completion')
                    ->label(__('Document completion'))
                    ->options([
                        'complete'   => __('Complete (100%)'),
                        'in_progress'=> __('In progress (1-99%)'),
                        'none'       => __('Not started (0%)'),
                    ]),
            ])
            ->recordUrl(fn (User $r) => Pages\ViewStudent::getUrl(['record' => $r->id]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'view'  => Pages\ViewStudent::route('/{record}'),
        ];
    }
}
