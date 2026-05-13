<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\Faq;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static string | UnitEnum | null $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string { return __('FAQs'); }
    public static function getNavigationGroup(): ?string { return __('CMS'); }
    public static function getModelLabel(): string { return __('FAQ'); }
    public static function getPluralModelLabel(): string { return __('FAQs'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
                TextInput::make('sort_order')->numeric()->default(0)->required(),
                Toggle::make('is_published')->default(true)->inline(false),
            ]),
            TranslatableTabs::for('question', TextInput::class, label: __('Question'), required: true),
            TranslatableTabs::for('answer', Textarea::class, label: __('Answer'), required: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->label('#')->sortable(),
                TextColumn::make('question')
                    ->label(__('Question'))
                    ->formatStateUsing(fn ($state, $record) => $record->t('question'))
                    ->searchable()
                    ->limit(80),
                IconColumn::make('is_published')->boolean(),
                TextColumn::make('updated_at')->dateTime('d M Y H:i')->sortable()->toggleable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit'   => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
