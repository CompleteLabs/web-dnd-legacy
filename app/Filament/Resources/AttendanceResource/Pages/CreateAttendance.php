<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function beforeCreate(): void
    {
        // Get the form data
        $data = $this->form->getState();

        // Check if a record with the same user_id and periode already exists
        $exists = Attendance::where('user_id', $data['user_id'])
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
