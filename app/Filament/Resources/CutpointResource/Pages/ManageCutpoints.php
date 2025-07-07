<?php

namespace App\Filament\Resources\CutpointResource\Pages;

use App\Filament\Resources\CutpointResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCutpoints extends ManageRecords
{
    protected static string $resource = CutpointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
