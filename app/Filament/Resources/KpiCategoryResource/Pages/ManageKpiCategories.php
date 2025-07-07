<?php

namespace App\Filament\Resources\KpiCategoryResource\Pages;

use App\Filament\Resources\KpiCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageKpiCategories extends ManageRecords
{
    protected static string $resource = KpiCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
