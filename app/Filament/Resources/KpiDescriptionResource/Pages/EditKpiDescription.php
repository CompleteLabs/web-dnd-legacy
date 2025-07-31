<?php

namespace App\Filament\Resources\KpiDescriptionResource\Pages;

use App\Filament\Resources\KpiDescriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKpiDescription extends EditRecord
{
    protected static string $resource = KpiDescriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
