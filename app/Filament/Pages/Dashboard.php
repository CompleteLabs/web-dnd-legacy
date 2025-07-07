<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ChecklistKPI;
use App\Filament\Widgets\LeaderboardKPI;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    protected static string $view = 'filament.pages.dashboard';

    public string $activeTab = 'checklist';

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function mount(): void
    {
        // Set the active tab from query parameter if present
        $tab = request()->query('tab');
        if ($tab && in_array($tab, ['checklist', 'leaderboard'])) {
            $this->activeTab = $tab;
        }
    }

    public function getWidgets(): array
    {
        return [
            ChecklistKPI::class,
            LeaderboardKPI::class,
        ];
    }
}
