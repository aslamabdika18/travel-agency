<?php

namespace App\Filament\Resources\TravelPackageResource\Pages;

use App\Filament\Resources\TravelPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTravelPackage extends EditRecord
{
    protected static string $resource = TravelPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
