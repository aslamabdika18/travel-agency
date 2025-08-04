<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete Category')
                ->modalDescription('Are you sure you want to delete this category? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, delete it'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Category Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Category Name')
                            ->size('lg')
                            ->weight('bold'),
                        
                        TextEntry::make('description')
                            ->label('Description')
                            ->prose(),
                        
                        TextEntry::make('travel_packages_count')
                            ->label('Total Travel Packages')
                            ->getStateUsing(fn ($record) => $record->travelPackages()->count())
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(1),
                
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}