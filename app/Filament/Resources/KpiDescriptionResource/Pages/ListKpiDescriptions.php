<?php

namespace App\Filament\Resources\KpiDescriptionResource\Pages;

use App\Filament\Resources\KpiDescriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKpiDescriptions extends ListRecords
{
    protected static string $resource = KpiDescriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
