<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Posts\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\Post;
use App\Models\PostCategory;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-newspaper';
    protected static string | UnitEnum | null $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 10;
    protected static ?string $recordTitleAttribute = 'slug';

    public static function getNavigationLabel(): string { return __('Blog Posts'); }
    public static function getNavigationGroup(): ?string { return __('CMS'); }
    public static function getModelLabel(): string { return __('Post'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Basics'))->columns(2)->components([
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(120)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                Select::make('post_category_id')
                    ->label(__('Category'))
                    ->options(fn () => PostCategory::all()->mapWithKeys(fn ($c) => [$c->id => $c->t('name')]))
                    ->searchable(),
                DateTimePicker::make('published_at')->seconds(false)->default(now()),
                Toggle::make('is_featured')->inline(false),
            ]),
            TranslatableTabs::for('title', TextInput::class, label: __('Title'), required: true),
            TranslatableTabs::for('excerpt', Textarea::class, label: __('Excerpt'), required: true,
                componentMods: ['rows' => [3], 'maxLength' => [500]]),
            TranslatableTabs::for('body', RichEditor::class, label: __('Body'), required: true),
            Section::make(__('Media & meta'))->columns(2)->components([
                FileUpload::make('thumbnail_path')->image()->disk('public')->directory('posts')->imageEditor(),
                TagsInput::make('tags')->placeholder(__('Add a tag and press Enter')),
            ]),
            Section::make(__('SEO'))->collapsed()->columns(1)->components([
                TranslatableTabs::for('seo_title', TextInput::class, label: __('SEO Title')),
                TranslatableTabs::for('seo_description', Textarea::class, label: __('SEO Description'),
                    componentMods: ['rows' => [2], 'maxLength' => [200]]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_path')->disk('public')->square()->label(''),
                TextColumn::make('title')->label(__('Title'))
                    ->formatStateUsing(fn ($state, $record) => $record->t('title'))
                    ->searchable(query: fn ($q, $search) => $q->where('title->id', 'like', "%{$search}%")->orWhere('title->en', 'like', "%{$search}%"))
                    ->limit(60)->wrap(),
                TextColumn::make('category.name')->label(__('Category'))
                    ->formatStateUsing(fn ($state, $record) => $record->category?->t('name') ?? '—')
                    ->badge(),
                IconColumn::make('is_featured')->boolean()->label('★'),
                TextColumn::make('published_at')->dateTime('d M Y')->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('post_category_id')
                    ->label(__('Category'))
                    ->options(fn () => PostCategory::all()->mapWithKeys(fn ($c) => [$c->id => $c->t('name')])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
