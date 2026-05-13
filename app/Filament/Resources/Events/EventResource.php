<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\Event;
use App\Models\EventCategory;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
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

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string | UnitEnum | null $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 20;

    public static function getNavigationLabel(): string { return __('Events'); }
    public static function getNavigationGroup(): ?string { return __('CMS'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Basics'))->columns(2)->components([
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(120)
                    ->live(onBlur: true)->afterStateUpdated(fn ($s, $set) => $set('slug', Str::slug($s))),
                Select::make('event_category_id')
                    ->label(__('Category'))
                    ->options(fn () => EventCategory::all()->mapWithKeys(fn ($c) => [$c->id => $c->t('name')]))
                    ->searchable(),
                DateTimePicker::make('starts_at')->seconds(false)->required(),
                DateTimePicker::make('ends_at')->seconds(false),
                DateTimePicker::make('published_at')->seconds(false)->default(now()),
                Toggle::make('is_featured')->inline(false),
            ]),
            TranslatableTabs::for('title', TextInput::class, label: __('Title'), required: true),
            TranslatableTabs::for('description', RichEditor::class, label: __('Description'), required: true),
            Section::make()->columns(2)->components([
                TranslatableTabs::for('organizer', TextInput::class, label: __('Organizer')),
                TranslatableTabs::for('location', TextInput::class, label: __('Location')),
            ]),
            Section::make()->columns(2)->components([
                FileUpload::make('image_path')->image()->disk('public')->directory('events')->imageEditor(),
                TextInput::make('registration_url')->url()->placeholder('https://…'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')->disk('public')->square()->label(''),
                TextColumn::make('title')->label(__('Title'))
                    ->formatStateUsing(fn ($state, $record) => $record->t('title'))
                    ->limit(50)->wrap()
                    ->searchable(query: fn ($q, $search) => $q->where('title->id','like',"%{$search}%")->orWhere('title->en','like',"%{$search}%")),
                TextColumn::make('category.name')->label(__('Category'))
                    ->formatStateUsing(fn ($state, $record) => $record->category?->t('name') ?? '—')
                    ->badge(),
                TextColumn::make('starts_at')->dateTime('d M Y H:i')->sortable(),
                IconColumn::make('is_featured')->boolean()->label('★'),
            ])
            ->defaultSort('starts_at', 'desc')
            ->filters([
                SelectFilter::make('event_category_id')
                    ->label(__('Category'))
                    ->options(fn () => EventCategory::all()->mapWithKeys(fn ($c) => [$c->id => $c->t('name')])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit'   => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
