<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TravelPackageResource\Pages;
use App\Filament\Resources\TravelPackageResource\RelationManagers;
use App\Models\TravelPackage;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class TravelPackageResource extends Resource
{
    protected static ?string $model = TravelPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Travel Management';
    protected static ?int $navigationSort = -2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make('Travel Package Information')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Name')
                                    ->autocomplete(false)
                                    ->formatStateUsing(fn (?string $state): ?string => $state ? ucwords($state) : null)
                                    ->mutateDehydratedStateUsing(fn (?string $state): ?string => $state ? ucwords($state) : null),
                                Forms\Components\Textarea::make('description')
                                    ->rows(5)
                                    ->required()
                                    ->label('Description')
                                    ->autocomplete(false),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->label('Base Price')
                                            ->helperText('Base price for standard number of people'),
                                        Forms\Components\TextInput::make('base_person_count')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1)
                                            ->label('Base Person Count')
                                            ->helperText('Number of people for base price'),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('additional_person_price')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('Rp')
                                            ->label('Additional Person Price')
                                            ->helperText('Additional price per extra person'),
                                        Forms\Components\TextInput::make('capacity')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Person')
                                            ->autocomplete(false)
                                            ->label('Maximum Capacity')
                                            ->helperText('Maximum participant capacity'),
                                    ]),
                                Forms\Components\TextInput::make('duration')
                                    ->required()
                                    ->maxLength(255)
                                    ->autocomplete(false)
                                    ->label('Duration')
                                    ->formatStateUsing(fn (?string $state): ?string => $state ? strtoupper($state) : null)
                                    ->mutateDehydratedStateUsing(fn (?string $state): ?string => $state ? strtoupper($state) : null),
                                Forms\Components\Select::make('category_id')
                                    ->label('Category')
                                    ->options(Category::all()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Category Name'),
                                        Forms\Components\Textarea::make('description')
                                            ->required()
                                            ->rows(3)
                                            ->label('Description'),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return Category::create($data)->getKey();
                                    })
                                    ->helperText('Select a category or create a new one'),
                            ])
                            ->columnSpan(['lg' => 1]),
                        Forms\Components\Section::make('Media')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                    ->collection('thumbnail')
                                    ->image()
                                    ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg'])
                                    ->imageEditor()
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1920')
                                    ->imageResizeTargetHeight('1080')
                                    ->label('Thumbnail')
                                    ->required(),

                                SpatieMediaLibraryFileUpload::make('gallery')
                                    ->collection('gallery')
                                    ->multiple()
                                    ->image()
                                    ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg'])
                                    ->imageEditor()
                                    ->reorderable()
                                    ->panelLayout('grid')
                                    ->label('Photo Gallery'),
                            ])
                            ->columnSpan(['lg' => 1]),
                        
                        Forms\Components\Section::make('Itinerary')
                            ->schema([
                                Forms\Components\Repeater::make('itineraries')
                                    ->relationship('itineraries')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('day')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->label('Day')
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('activity')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->label('Activity')
                                                    ->columnSpan(2),
                                            ]),
                                        Forms\Components\Textarea::make('note')
                                            ->rows(3)
                                            ->label('Notes')
                                            ->placeholder('Additional notes for this day...')
                                    ])
                                    ->orderColumn('day')
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['day'] ? "Day {$state['day']}: {$state['activity']}" : null)
                                    ->addActionLabel('Add Day')
                                    ->defaultItems(0)
                            ])
                            ->columnSpan(['lg' => 2]),
                        
                        Forms\Components\Section::make('Inclusions & Exclusions')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Repeater::make('travelIncludes')
                                            ->relationship('travelIncludes')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->label('Inclusion Item')
                                                    ->placeholder('e.g., Hotel accommodation, Meals, Transportation')
                                            ])
                                            ->simple(
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->label('Inclusion Item')
                                            )
                                            ->addActionLabel('Add Inclusion')
                                            ->label('What\'s Included')
                                            ->defaultItems(0),
                                        
                                        Forms\Components\Repeater::make('travelExcludes')
                                            ->relationship('travelExcludes')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->label('Exclusion Item')
                                                    ->placeholder('e.g., International flights, Personal expenses')
                                            ])
                                            ->simple(
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->label('Exclusion Item')
                                            )
                                            ->addActionLabel('Add Exclusion')
                                            ->label('What\'s Not Included')
                                            ->defaultItems(0)
                                    ])
                            ])
                            ->columnSpan(['lg' => 2]),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Base Price')
                    ->prefix('Rp ')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 0))
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_person_count')
                    ->label('Base Person')
                    ->suffix(' person(s)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('additional_person_price')
                    ->label('Additional Price')
                    ->prefix('Rp ')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 0))
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Max Capacity')
                    ->suffix(' person(s)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->collection('thumbnail'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Category'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTravelPackages::route('/'),
            'create' => Pages\CreateTravelPackage::route('/create'),
            'view' => Pages\ViewTravelPackage::route('/{record}'),
            'edit' => Pages\EditTravelPackage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getApiTransformer()
    {
        return \App\Filament\Resources\TravelPackageResource\Api\TravelPackageApiService::class;
    }
}
