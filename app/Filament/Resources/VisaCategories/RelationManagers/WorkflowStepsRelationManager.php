<?php

namespace App\Filament\Resources\VisaCategories\RelationManagers;

use App\Filament\Support\TranslatableTabs;
use App\Models\VisaWorkflowStep;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkflowStepsRelationManager extends RelationManager
{
    protected static string $relationship = 'workflowSteps';

    protected static ?string $title = null; // use translatable below

    public static function getTitle($ownerRecord, ?string $pageClass = null): string
    {
        return __('Workflow steps');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->components([
                TextInput::make('sort_order')
                    ->label(__('Sort order'))
                    ->numeric()->default(fn () => ($this->getOwnerRecord()->workflowSteps()->max('sort_order') ?? 0) + 1)
                    ->required(),
                Select::make('icon')
                    ->label(__('Heroicon (fallback)'))
                    ->placeholder('heroicon-o-document-magnifying-glass')
                    ->helperText(__('Used when no image is uploaded. Pick from heroicons.com.')),
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->label('#')->sortable()->width(50),
                ImageColumn::make('icon_path')->label(__('Image'))->disk('public')->circular()->size(40)
                    ->defaultImageUrl(null),
                TextColumn::make('title')
                    ->label(__('Step'))
                    ->formatStateUsing(fn ($state, $record) => $record->t('title'))
                    ->wrap()->weight('bold'),
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
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                CreateAction::make()->label(__('Add step'))->icon('heroicon-o-plus'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
