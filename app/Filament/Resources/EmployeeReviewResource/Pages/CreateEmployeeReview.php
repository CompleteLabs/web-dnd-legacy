<?php

namespace App\Filament\Resources\EmployeeReviewResource\Pages;

use App\Filament\Resources\EmployeeReviewResource;
use App\Models\EmployeeReview;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeeReview extends CreateRecord
{
    protected static string $resource = EmployeeReviewResource::class;

    protected function beforeCreate(): void
    {
        // Get the form data
        $data = $this->form->getState();

        // Check if a record with the same user_id and periode already exists
        $exists = EmployeeReview::where('user_id', $data['user_id'])
            ->where('periode', $data['periode'])
            ->exists();

        if ($exists) {
            // If duplicate found, show error notification
            Notification::make()
                ->title('Data sudah ada')
                ->body('User ini sudah memiliki data kehadiran untuk periode yang sama.')
                ->danger()
                ->send();

            // Halt the creation process
            $this->halt();
        }
    }

    // Optionally, you can customize the redirect after creation
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
