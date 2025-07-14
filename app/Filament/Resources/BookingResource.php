<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\TravelPackage;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Travel Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Booking Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('travel_package_id')
                            ->label('Travel Package')
                            ->relationship('travelPackage', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $package = TravelPackage::find($state);
                                    $personCount = $get('person_count') ?? 1;
                                    if ($package) {
                                        $priceData = $package->calculatePrice($personCount);
                                        $set('base_price', $priceData['base_price']);
                                        $set('additional_price', $priceData['additional_price']);
                                        $set('total_price', $priceData['total_price']);
                                    }
                                }
                            }),

                        Forms\Components\DatePicker::make('booking_date')
                            ->label('Booking Date')
                            ->required(),

                        Forms\Components\TextInput::make('person_count')
                            ->label('Number of Persons')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $packageId = $get('travel_package_id');
                                if ($packageId && $state) {
                                    $package = TravelPackage::find($packageId);
                                    if ($package) {
                                        $priceData = $package->calculatePrice($state);
                                        $set('base_price', $priceData['base_price']);
                                        $set('additional_price', $priceData['additional_price']);
                                        $set('total_price', $priceData['total_price']);
                                    }
                                }
                            }),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('base_price')
                                    ->label('Base Price')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('additional_price')
                                    ->label('Additional Price')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('total_price')
                                    ->label('Total Price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'paid' => 'Paid',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('travelPackage.name')
                    ->label('Travel Package')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking_date')
                    ->label('Booking Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('person_count')
                    ->label('Persons')
                    ->suffix(' person(s)')
                    ->sortable(),

                Tables\Columns\TextColumn::make('base_price')
                    ->label('Base Price')
                    ->prefix('Rp ')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 0))
                    ->sortable(),

                Tables\Columns\TextColumn::make('additional_price')
                    ->label('Additional Price')
                    ->prefix('Rp ')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 0))
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Price')
                    ->prefix('Rp ')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 0))
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'pending',
                        'success' => ['confirmed', 'paid', 'completed'],
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'paid' => 'Paid',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
