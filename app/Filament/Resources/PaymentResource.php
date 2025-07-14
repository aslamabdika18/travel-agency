<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Travel Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\Select::make('booking_id')
                            ->label('Booking')
                            ->relationship('booking', 'id')
                            ->getOptionLabelFromRecordUsing(fn (Booking $record) => "#{$record->id} - {$record->user->name} - {$record->travelPackage->name}")
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\DatePicker::make('payment_date')
                            ->label('Payment Date')
                            ->required(),

                        Forms\Components\TextInput::make('total_price')
                            ->label('Amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),

                        Forms\Components\Select::make('payment_status')
                            ->label('Payment Status')
                            ->options([
                                'Unpaid' => 'Unpaid',
                                'Paid' => 'Paid',
                                'Failed' => 'Failed',
                            ])
                            ->default('Unpaid')
                            ->required(),

                        Forms\Components\TextInput::make('gateway_transaction_id')
                            ->label('Gateway Transaction ID')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('gateway_status')
                            ->label('Gateway Status')
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking.id')
                    ->label('Booking ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('booking.user.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('booking.travelPackage.name')
                    ->label('Travel Package')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Amount')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'Failed',
                        'warning' => 'Unpaid',
                        'success' => 'Paid',
                    ]),



                Tables\Columns\TextColumn::make('gateway_transaction_id')
                    ->label('Transaction ID')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('gateway_status')
                    ->label('Gateway Status')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'Unpaid' => 'Unpaid',
                        'Paid' => 'Paid',
                        'Failed' => 'Failed',
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
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
