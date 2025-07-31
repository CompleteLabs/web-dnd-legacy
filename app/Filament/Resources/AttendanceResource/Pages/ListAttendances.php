<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use App\Imports\AttendanceImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;
    protected static ?string $title = "Kehadiran";

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('import')
                ->icon('heroicon-s-arrow-up-tray')
                ->color('gray')
                ->form([
                    FileUpload::make('file')
                        ->label('Upload File Excel:')
                        ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']),
                ])
                ->action(function (array $data) {
                    return $this->processImport($data);
                })
                ->modalWidth('md')
                ->modalHeading('Import Data Absensi')
                ->modalSubmitActionLabel('Import'),
        ];
    }

    public function processImport(array $data)
    {
        try {
            // Check if file exists in the data
            if (!isset($data['file']) || empty($data['file'])) {
                Notification::make()
                    ->title('Error: No file was uploaded')
                    ->danger()
                    ->send();
                return;
            }

            // Debug the file data - Fix: Pass as array context
            \Log::info('File data:', ['file' => $data['file']]);

            // Try a few different approaches to get the file path
            if (is_array($data['file']) && count($data['file']) > 0) {
                // If it's an array of files, take the first one
                $filePath = $data['file'][0];
            } else {
                // Otherwise use the value directly
                $filePath = $data['file'];
            }

            // Try different methods to get the actual file
            if (Storage::disk('public')->exists($filePath)) {
                $fullPath = Storage::disk('public')->path($filePath);
            } elseif (Storage::disk('local')->exists($filePath)) {
                $fullPath = Storage::disk('local')->path($filePath);
            } else {
                // If all else fails, try to use the path directly
                $fullPath = $filePath;

                // Check if it looks like a URL/uploaded path and get the file directly
                if (filter_var($filePath, FILTER_VALIDATE_URL) || strpos($filePath, 'livewire-tmp') !== false) {
                    $import = new AttendanceImport();
                    Excel::import($import, $filePath);

                    // If we make it here, we successfully imported without using a local file path
                    goto summarize_import;
                }
            }

            // Check if file exists at the path
            if (!file_exists($fullPath)) {
                \Log::error('File not found at path: ' . $fullPath);
                \Log::info('Original file data: ', ['data' => $data['file']]);

                Notification::make()
                    ->title('Error: File not found. Please try uploading again.')
                    ->body('Technical details: File path could not be resolved correctly.')
                    ->danger()
                    ->send();
                return;
            }

            $import = new AttendanceImport();
            Excel::import($import, $fullPath);

            summarize_import:

            // Check if getImportSummary method exists
            if (!method_exists($import, 'getImportSummary')) {
                Notification::make()
                    ->title('Error: Import summary method not found')
                    ->danger()
                    ->send();
                return;
            }

            $summary = $import->getImportSummary();

            if ($summary['importedCount'] > 0) {
                $message = "Data Kehadiran berhasil diimport. {$summary['importedCount']} data ditambahkan, {$summary['skippedCount']} data dilewati.";
                Notification::make()
                    ->title($message)
                    ->success()
                    ->send();
            } else {
                $message = "Tidak ada data yang diimport. Semua data ({$summary['skippedCount']}) sudah ada atau tidak valid.";
                Notification::make()
                    ->title($message)
                    ->warning()
                    ->send();
            }

            // Store skipped details in session for reference if needed
            session()->flash('skippedDetails', $summary['skippedDetails']);

        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error('Import Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            // Send a notification with the error message
            Notification::make()
                ->title('Error during import: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
