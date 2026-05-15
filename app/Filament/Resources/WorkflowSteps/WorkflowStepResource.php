<?php

namespace App\Filament\Resources\WorkflowSteps;

use App\Filament\Resources\WorkflowSteps\Pages;
use App\Filament\Support\TranslatableTabs;
use App\Models\VisaCategory;
use App\Models\VisaWorkflowStep;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use UnitEnum;

class WorkflowStepResource extends Resource
{
    protected static ?string $model = VisaWorkflowStep::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-flag';
    protected static string | UnitEnum | null $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 80;

    public static function getNavigationLabel(): string  { return __('Homepage Workflow'); }
    public static function getNavigationGroup(): ?string { return __('CMS'); }
    public static function getModelLabel(): string       { return __('Workflow step'); }
    public static function getPluralModelLabel(): string { return __('Workflow steps'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->components([
                Select::make('visa_category_id')
                    ->label(__('Visa'))
                    ->options(fn () => VisaCategory::orderBy('sort_order')->get()
                        ->mapWithKeys(fn ($v) => [$v->id => $v->t('name')]))
                    ->required()
                    ->searchable()
                    ->native(false)
                    ->columnSpan(1),
                TextInput::make('sort_order')
                    ->label(__('Sort order'))
                    ->numeric()->required()->default(1)
                    ->columnSpan(1),
                TextInput::make('icon')
                    ->label(__('Heroicon (fallback)'))
                    ->placeholder('heroicon-o-document-magnifying-glass')
                    ->maxLength(80)
                    ->helperText(__('Used when no image is uploaded.'))
                    ->columnSpan(1),
            ]),

            TranslatableTabs::for('title', TextInput::class, label: __('Step title'), required: true),

            Section::make(__('Illustration'))->components([
                FileUpload::make('icon_path')
                    ->label(__('Image (square works best)'))
                    ->image()
                    ->disk('public')
                    ->directory('workflow-steps')
                    ->imageEditor()
                    ->imageEditorAspectRatios(['1:1'])
                    ->maxSize(2048)
                    ->columnSpanFull(),
            ]),

            Section::make(__('Optional badge'))
                ->description(__('Show a small coloured tag below the step title (e.g. "Deposit", "Stage 1 Payment", eligibility note).'))
                ->columns(2)
                ->components([
                    Select::make('badge_color')
                        ->label(__('Badge colour'))
                        ->options([
                            'brand'   => __('Brand red'),
                            'warning' => __('Amber (deposit / warning)'),
                            'info'    => __('Sky (eligibility)'),
                            'success' => __('Emerald (good news)'),
                        ])
                        ->placeholder(__('No badge'))
                        ->native(false),
                    TranslatableTabs::for('badge_label', TextInput::class, label: __('Badge label')),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->label('#')->sortable()->width(50),
                ImageColumn::make('icon_path')->label(__('Image'))->disk('public')->circular()->size(40),
                TextColumn::make('title')
                    ->label(__('Step'))
                    ->formatStateUsing(fn ($state, $record) => $record->t('title'))
                    ->wrap()->weight('bold')
                    ->searchable(query: function ($q, $search) {
                        $q->where('title->id', 'like', "%{$search}%")
                          ->orWhere('title->en', 'like', "%{$search}%")
                          ->orWhere('title->ja', 'like', "%{$search}%");
                    }),
                TextColumn::make('icon')->label(__('Heroicon'))->fontFamily('mono')->color('gray')->toggleable(),
                TextColumn::make('badge_label')
                    ->label(__('Badge'))
                    ->formatStateUsing(fn ($state, $record) => $record->badge_label ? $record->t('badge_label') : null)
                    ->placeholder('—')
                    ->badge()
                    ->color(fn ($state, $record) => match ($record->badge_color) {
                        'warning' => 'warning',
                        'info'    => 'info',
                        'success' => 'success',
                        'brand'   => 'primary',
                        default   => 'gray',
                    }),
                TextColumn::make('visa.name')
                    ->label(__('Visa'))
                    ->formatStateUsing(fn ($state, $record) => $record->visa?->t('name') ?? '—')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
            ])
            ->defaultGroup('visa.name')
            ->groups([
                Group::make('visa.name')
                    ->label(__('Visa'))
                    ->getTitleFromRecordUsing(fn ($record) => $record->visa?->t('name') ?? '—')
                    ->collapsible(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('visa_category_id')
                    ->label(__('Visa'))
                    ->options(fn () => VisaCategory::orderBy('sort_order')->get()
                        ->mapWithKeys(fn ($v) => [$v->id => $v->t('name')])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWorkflowSteps::route('/'),
            'create' => Pages\CreateWorkflowStep::route('/create'),
            'edit'   => Pages\EditWorkflowStep::route('/{record}/edit'),
        ];
    }
}
