<x-filament-widgets::widget>
    <x-filament::section>
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-2xl">Checklist
                        KPI</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lacak indikator kinerja dan status
                        penyelesaian Anda</p>
                </div>

                <!-- Filter Controls -->
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <form class="flex flex-col sm:flex-row gap-3 w-full">
                        @if (auth()->user()->role_id != 2)
                            <x-filament::input.wrapper class="w-full sm:w-52">
                                <x-filament::input.select wire:model.live="user_id" class="w-full">
                                    <option value="">--Choose User--</option>
                                    @foreach ($this->getKpis()['users'] as $user)
                                        <option value="{{ $user->id }}">{{ $user->nama_lengkap }}</option>
                                    @endforeach
                                </x-filament::input.select>
                            </x-filament::input.wrapper>
                        @endif

                        <x-filament::input.wrapper class="w-full sm:w-40">
                            <x-filament::input type="month" wire:model.live="month" placeholder="Pilih Bulan"
                                class="w-full" max="{{ now()->format('Y-m') }}" />
                        </x-filament::input.wrapper>
                    </form>

                    @if (auth()->user()->role_id != 2)
                        <x-filament::button size="sm" color="gray" outlined wire:click="downloadReport"
                            class="mt-1 sm:mt-0">
                            <x-filament::icon alias="heroicon-m-arrow-down-tray" class="h-4 w-4 mr-1" />
                            Download
                        </x-filament::button>
                    @endif
                </div>
            </div>
        </div>

        @foreach ($this->getKpis()['groupedKpis'] as $yearMonth => $groupedKpisByCategory)
            @php
                $yearMonthText = Carbon\Carbon::parse($yearMonth)->format('F Y');
                $userKpi = $groupedKpisByCategory->first()->first();
            @endphp

            <div class="mb-6 sm:mb-10">
                <div class="flex justify-between items-center mb-3 sm:mb-4">
                    <h3
                        class="text-base sm:text-lg font-semibold text-primary-700 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30 px-3 py-1.5 sm:px-4 sm:py-2 rounded-md inline-block">
                        {{ $yearMonthText }}
                    </h3>
                </div>

                @foreach ($groupedKpisByCategory as $categoryName => $kpis)
                    <div
                        class="mb-6 sm:mb-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm dark:shadow-gray-900/20 border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div
                            class="bg-gray-50 dark:bg-gray-800/80 p-3 sm:p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-3 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 text-sm sm:text-base">
                                {{ $categoryName }}</h4>
                            <span
                                class="text-xs sm:text-sm font-medium px-2 py-1 sm:px-3 sm:py-1.5 bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-full">
                                Category Percentage: {{ $kpis->first()->percentage }}%
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-fixed">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800/80">
                                        <th scope="col"
                                            class="w-[5%] px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">
                                            Status</th>
                                        <th scope="col"
                                            class="px-3 sm:px-6 py-2 sm:py-4 text-2xs sm:text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">
                                            Description</th>
                                        <th scope="col"
                                            class="w-[10%] px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">
                                            Start</th>
                                        <th scope="col"
                                            class="w-[10%] px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">
                                            End</th>
                                        <th scope="col"
                                            class="w-[5%] px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">
                                            Type</th>
                                        <th scope="col"
                                            class="w-[5%] px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">
                                            Plan</th>
                                        <th scope="col"
                                            class="w-[5%] px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">
                                            Actual</th>
                                        <th scope="col"
                                            class="w-[5%] px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">
                                            Result</th>
                                        <th scope="col"
                                            class="w-[5%] px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($kpis as $kpi)
                                        @foreach ($kpi->kpi_detail->where('is_extra_task', 0) as $kpiDetail)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                                <td class="px-2 sm:px-4 py-2 sm:py-4 flex items-center justify-center">
                                                    @include('filament.components.kpi_action', [
                                                        'kpiDetail' => $kpiDetail,
                                                    ])
                                                </td>
                                                <td
                                                    class="px-3 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm text-gray-900 dark:text-gray-200">
                                                    <p class="font-medium">
                                                        {{ $kpiDetail->kpi_description->description }}
                                                    </p>

                                                    {{-- Display subtasks if they exist (handle both array and JSON string) --}}
                                                    @php
                                                        $subtasksData = null;
                                                        if (!empty($kpiDetail->subtasks)) {
                                                            if (is_string($kpiDetail->subtasks)) {
                                                                $subtasksData = json_decode($kpiDetail->subtasks, true);
                                                            } elseif (is_array($kpiDetail->subtasks)) {
                                                                $subtasksData = $kpiDetail->subtasks;
                                                            }
                                                        }
                                                    @endphp

                                                    @if(!empty($subtasksData) && is_array($subtasksData))
                                                        <div class="mt-2 ml-3 border-l-2 border-gray-300 dark:border-gray-600 pl-2">
                                                            <p class="text-2xs font-medium text-gray-500 dark:text-gray-400 mb-1">Subtasks:</p>
                                                            <ul class="space-y-1">
                                                                @foreach($subtasksData as $subtask)
                                                                    <li class="flex items-center text-2xs text-gray-600 dark:text-gray-400">
                                                                        <svg class="w-2.5 h-2.5 mr-1.5 text-gray-500 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                                                        </svg>
                                                                        {{ $subtask['description'] ?? '' }}
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </td>

                                                {{-- Rest of the columns remain unchanged --}}
                                                <td
                                                    class="px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs text-gray-600 dark:text-gray-400 text-center">
                                                    {{ $kpiDetail->start ? Carbon\Carbon::parse($kpiDetail->start)->format('d M Y') : '-' }}
                                                </td>
                                                <td
                                                    class="px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs text-gray-600 dark:text-gray-400 text-center">
                                                    {{ $kpiDetail->end ? Carbon\Carbon::parse($kpiDetail->end)->format('d M Y') : '-' }}
                                                </td>
                                                <td
                                                    class="px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs text-gray-700 dark:text-gray-300 text-center font-medium">
                                                    {{ $kpiDetail->count_type }}</td>
                                                <td
                                                    class="px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs text-gray-600 dark:text-gray-400 text-center">
                                                    {{ $kpiDetail->value_plan }}</td>
                                                <td
                                                    class="px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs text-gray-700 dark:text-gray-300 text-center font-medium">
                                                    {{ $kpiDetail->value_actual }}</td>
                                                <td class="px-2 sm:px-4 py-2 sm:py-4 text-center">
                                                    @if ($kpiDetail->count_type === 'NON')
                                                        <span
                                                            class="px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-2xs sm:text-xs {{ $kpiDetail->value_result == 1 ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400' }}">
                                                            {{ $kpiDetail->value_result == 1 ? '100%' : number_format($kpiDetail->value_result * 100, 2) . '%' }}
                                                        </span>
                                                    @elseif($kpiDetail->count_type === 'RESULT')
                                                        <span
                                                            class="px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-2xs sm:text-xs {{ $kpiDetail->value_result >= 1 ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400' }}">
                                                            {{ number_format($kpiDetail->value_result * 100, 2) }}%
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-2 sm:px-4 py-2 sm:py-4 text-center">
                                                    @if ($kpiDetail->count_type === 'RESULT' && $kpiDetail->value_result > 0 && $kpiDetail->value_result < 1)
                                                        <x-filament::button size="xs"
                                                            class="text-2xs sm:text-xs px-2 py-1 sm:px-3 sm:py-1.5"
                                                            color="warning"
                                                            wire:click="openExtraTaskModal('{{ $kpiDetail->id }}')">
                                                            Extra Task
                                                        </x-filament::button>
                                                    @endif
                                                </td>
                                            </tr>

                                            @foreach ($kpiDetail->children as $extraTask)
                                                <tr
                                                    class="border-t border-dashed border-gray-200 dark:border-gray-700 bg-blue-50/50 dark:bg-blue-900/10 hover:bg-blue-100/50 dark:hover:bg-blue-900/20 transition-colors">
                                                    <td class="px-2 sm:px-4 py-2 sm:py-4 flex items-center justify-center">
                                                        <span class="flex items-center justify-center h-5 w-5 rounded-full bg-blue-100 dark:bg-blue-900/30">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 text-blue-600 dark:text-blue-400">
                                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 text-2xs sm:text-xs text-blue-700 dark:text-blue-400">
                                                        <div class="flex items-start">
                                                            <div>
                                                                <span class="inline-block px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded text-2xs font-medium mb-1">Extra Task</span>
                                                                <p class="font-medium">{{ $extraTask->kpi_description->description ?? '-' }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-2 sm:px-4 py-2 sm:py-4"></td>
                                                    <td class="px-2 sm:px-4 py-2 sm:py-4"></td>
                                                    <td
                                                        class="px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs text-gray-500 dark:text-gray-400 text-center">
                                                        {{ $extraTask->count_type }}</td>
                                                    <td class="px-2 sm:px-4 py-2 sm:py-4"></td>
                                                    <td
                                                        class="px-2 sm:px-4 py-2 sm:py-4 text-2xs sm:text-xs text-gray-700 dark:text-gray-300 text-center font-medium">
                                                        {{ $extraTask->value_actual }}</td>
                                                    <td class="px-2 sm:px-4 py-2 sm:py-4"></td>
                                                    <td class="px-2 sm:px-4 py-2 sm:py-4 text-center">
                                                        <x-filament::button size="xs"
                                                            class="text-2xs sm:text-xs px-2 py-1 sm:px-3 sm:py-1.5"
                                                            color="danger"
                                                            wire:click="deleteExtraTask('{{ $extraTask->id }}')">
                                                            Delete
                                                        </x-filament::button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                <div
                    class="mt-6 sm:mt-8 p-4 sm:p-6 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-xl shadow-inner dark:shadow-inner-gray-900/10">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                        <div class="mb-3 sm:mb-0">
                            <span class="text-sm sm:text-base font-medium text-gray-600 dark:text-gray-300">Total
                                Score</span>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Kinerja keseluruhan pada
                                bulan {{ $yearMonthText }}</p>
                        </div>
                        <div class="text-left sm:text-right">
                            <span
                                class="text-2xl sm:text-3xl font-bold text-primary-700 dark:text-primary-400">{{ number_format($this->getKpis()['totalScore'] * 100, 2) }}</span>
                            <p class="text-xs sm:text-sm font-medium text-primary-600 dark:text-primary-400">points</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </x-filament::section>

    <x-filament::modal id="updateKpiModal" width="md">
        <x-slot name="header" class="text-xl font-semibold text-gray-800">Update KPI</x-slot>

        <form wire:submit.prevent="submitUpdateKpi" class="space-y-6">
            @if (isset($kpiDetail))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-600">KPI Description</label>
                        <x-filament::input type="text"
                            value="{{ $kpiDetail->kpi_description->description ?? '' }}" disabled
                            class="bg-gray-100 text-gray-700" />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Type</label>
                        <x-filament::input type="text" value="{{ $kpiDetail->count_type ?? '' }}" disabled
                            class="bg-gray-100 text-gray-700" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Month</label>
                        <x-filament::input type="text"
                            value="{{ isset($kpiDetail) && isset($kpiDetail->kpi) ? Carbon\Carbon::parse($kpiDetail->kpi->date)->format('F Y') : '' }}"
                            disabled class="bg-gray-100 text-gray-700" />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Value Plan</label>
                        <x-filament::input type="text"
                            value="{{ isset($kpiDetail) ? number_format($kpiDetail->value_plan, 0, ',', '.') : '' }}"
                            disabled class="bg-gray-100 text-gray-700" />
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Value Actual</label>
                    <x-filament::input type="number" wire:model="value_actual" step="0.01" required
                        class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 transition-all" />
                    @error('value_actual')
                        <span class="text-sm text-danger-600 mt-1">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            <div class="mt-6 flex justify-end gap-4">
                <x-filament::button color="gray" x-on:click="close"
                    class="px-6 py-2 rounded-md text-sm font-medium">
                    Cancel
                </x-filament::button>

                <x-filament::button type="submit" color="success" class="px-6 py-2 rounded-md text-sm font-medium"
                    wire:loading.attr="disabled" wire:target="submitUpdateKpi">
                    <span wire:loading.remove wire:target="submitUpdateKpi">Submit</span>
                    <span wire:loading wire:target="submitUpdateKpi">
                        <x-filament::loading-indicator class="h-4 w-4" />
                        Saving...
                    </span>
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>

    <x-filament::modal id="extraTaskModal" width="md">
        <x-slot name="header">Add Extra Task</x-slot>

        <form wire:submit.prevent="submitExtraTask" id="extraTaskForm">
            <div class="space-y-6 sm:space-y-8 p-4">
                <!-- Hidden Input for Parent ID -->
                <input type="hidden" wire:model="parent_id">

                <!-- Extra Task Description Field -->
                <div class="space-y-2">
                    <label for="description"
                        class="text-xs sm:text-sm font-medium text-gray-950 dark:text-gray-200">Extra Task
                        Description</label>
                    <x-filament::input.wrapper>
                        <x-filament::input wire:model="description" id="description" class="w-full" />
                    </x-filament::input.wrapper>
                </div>

                <!-- Count Type Field -->
                <div class="space-y-2">
                    <label for="count_type"
                        class="text-xs sm:text-sm font-medium text-gray-950 dark:text-gray-200">Count Type</label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="count_type" id="count_type" class="w-full">
                            <option value="NON">NON</option>
                            <option value="RESULT">RESULT</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <!-- Actual Value Field (visible when 'RESULT' is selected) -->
                <div class="{{ $count_type === 'RESULT' ? '' : 'hidden' }}">
                    <div class="space-y-2">
                        <label for="value_actual"
                            class="text-xs sm:text-sm font-medium text-gray-950 dark:text-gray-200">Actual
                            Value</label>
                        <x-filament::input.wrapper>
                            <x-filament::input type="number" wire:model="value_actual" id="value_actual"
                                step="0.01" class="w-full" />
                        </x-filament::input.wrapper>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-4">
                <x-filament::button color="gray" x-on:click="close"
                    class="px-6 py-2 rounded-md text-sm font-medium">
                    Cancel
                </x-filament::button>

                <x-filament::button type="submit" color="success" class="px-6 py-2 rounded-md text-sm font-medium"
                    wire:loading.attr="disabled" wire:target="extraTaskForm">
                    <span wire:loading.remove wire:target="extraTaskForm">Submit</span>
                    <span wire:loading wire:target="extraTaskForm">
                        <x-filament::loading-indicator class="h-4 w-4" />
                        Saving...
                    </span>
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>

</x-filament-widgets::widget>
