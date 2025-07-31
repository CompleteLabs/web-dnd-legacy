@if ($kpiDetail->count_type === 'NON')
    <form wire:submit.prevent="changeKpiStatus('{{ $kpiDetail->id }}', 'monthly')">
        <x-filament::icon-button
            type="submit"
            icon="heroicon-o-check-circle"
            color="{{ $kpiDetail->value_result != null ? 'success' : 'danger' }}"
        />
    </form>
@elseif ($kpiDetail->count_type === 'RESULT')
    <x-filament::icon-button
        icon="heroicon-o-check-circle"
        color="{{ $kpiDetail->value_result != null ? 'success' : 'danger' }}"
        wire:click="openUpdateModal('{{ $kpiDetail->id }}')"
    />
@endif