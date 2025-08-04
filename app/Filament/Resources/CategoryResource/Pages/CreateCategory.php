<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Category created successfully!';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure name is properly formatted
        if (isset($data['name'])) {
            $data['name'] = ucwords(strtolower($data['name']));
        }
        
        return $data;
    }
}