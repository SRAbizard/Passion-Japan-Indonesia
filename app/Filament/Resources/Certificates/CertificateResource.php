<?php

namespace App\Filament\Resources\Certificates;

use App\Filament\Resources\Certificates\Pages;
use App\Models\Certificate;
use App\Models\Course;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-trophy';
    protected static string | UnitEnum | null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string { return __('Certificates'); }
    public static function getNavigationGroup(): ?string { return __('Learning'); }
    public static function getModelLabel(): string { return __('Certificate'); }
    public static function getPluralModelLabel(): string { return __('Certificates'); }

    public static function canCreate(): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('certificate_number')->label(__('Number'))
                ->searchable()->copyable()->weight('bold'),
            TextColumn::make('user.name')->label(__('Student'))->searchable(),
            TextColumn::make('course.title')->label(__('Course'))
                ->formatStateUsing(fn ($state, $record) => $record->course?->t('title') ?? '—')
                ->limit(40),
            TextColumn::make('final_score')->suffix('%')->placeholder('—')->label(__('Score')),
            TextColumn::make('issued_at')->dateTime('d M Y')->sortable()->label(__('Issued')),
        ])
        ->defaultSort('issued_at', 'desc')
        ->filters([
            SelectFilter::make('course_id')->label(__('Course'))
                ->options(fn () => Course::all()->mapWithKeys(fn ($c) => [$c->id => $c->t('title')])),
        ])
        ->recordActions([
            Action::make('download')
                ->label(__('Download'))
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (Certificate $record) => route('certificate.download', $record->certificate_number))
                ->openUrlInNewTab(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificates::route('/'),
        ];
    }
}
