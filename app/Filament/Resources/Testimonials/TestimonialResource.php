<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
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
use Filament\Tables\Table;
use UnitEnum;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string | UnitEnum | null $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 40;

    public static function getNavigationLabel(): string { return __('Testimonials'); }
    public static function getNavigationGroup(): ?string { return __('CMS'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->components([
                TextInput::make('name')->required()->maxLength(120),
                Select::make('kind')
                    ->options(['student' => __('Student'), 'company' => __('Partner Company')])
                    ->required()
                    ->default('student'),
                TextInput::make('sort_order')->numeric()->default(0),
            ]),
            TranslatableTabs::for('role', TextInput::class, label: __('Role / Caption'), required: true),
            TranslatableTabs::for('quote', Textarea::class, label: __('Quote'), required: true,
                componentMods: ['rows' => [5]]),
            Section::make()->columns(2)->components([
                FileUpload::make('avatar_path')
                    ->image()
                    ->avatar()
                    ->disk('public')
                    ->directory('testimonials'),
                Toggle::make('is_published')->default(true)->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_path')->circular()->disk('public')->label(''),
                TextColumn::make('name')->searchable()->weight('bold'),
                TextColumn::make('kind')->badge()->colors(['primary' => 'student', 'success' => 'company']),
                TextColumn::make('role')->label(__('Role'))->formatStateUsing(fn ($state, $record) => $record->t('role'))->limit(40),
                TextColumn::make('sort_order')->label('#'),
                IconColumn::make('is_published')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit'   => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
