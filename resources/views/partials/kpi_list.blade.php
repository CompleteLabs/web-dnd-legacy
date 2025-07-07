@if (empty($groupedKpis))
    <div class="alert alert-info text-center">
        <strong>No KPI data available for the selected filters.</strong>
    </div>
@else
    <div class="timeline timeline-inverse">
        @foreach ($groupedKpis as $yearMonth => $groupedKpisByCategory)
            @php
                $yearMonthText = \Carbon\Carbon::parse($yearMonth)->format('F Y');
                $userKpi = $groupedKpisByCategory->first()->first();
            @endphp

            {{-- User Information --}}
            <div class="time-label">
                <span style="background-color: #2A2F4F; color: white;">
                    {{ $userKpi->user->nama_lengkap ?? 'No User' }} - {{ $userKpi->user->position->name ?? '-' }}
                </span>
            </div>

            {{-- Month Label --}}
            <div class="time-label">
                <span style="background-color: #2A2F4F; color: white;">{{ $yearMonthText }}</span>
            </div>

            {{-- KPI Categories --}}
            @foreach ($groupedKpisByCategory as $categoryName => $kpis)
                <div>
                    <div class="timeline-item">
                        <span class="time">Percentage: {{ $kpis->first()->percentage ?? 'N/A' }}%</span>
                        <h3 class="timeline-header"><strong>{{ $categoryName }}</strong></h3>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>KPI Description</th>
                                        <th class="text-center">Start</th>
                                        <th class="text-center">End</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Value Plan</th>
                                        <th class="text-center">Value Actual</th>
                                        <th class="text-center">Value Result</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kpis as $kpi)
                                        @foreach ($kpi->kpi_detail->where('is_extra_task', 0) as $kpiDetail)
                                            <tr data-id="{{ $kpiDetail->id }}">
                                                <td>
                                                    @include('partials.kpi_action', ['kpiDetail' => $kpiDetail])
                                                </td>
                                                <td>{{ $kpiDetail->kpi_description->description ?? '-' }}</td>
                                                <td class="text-center">
                                                    {{ $kpiDetail->start ? Carbon\Carbon::parse($kpiDetail->start)->format('d M Y') : '-' }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $kpiDetail->end ? Carbon\Carbon::parse($kpiDetail->end)->format('d M Y') : '-' }}
                                                </td>
                                                <td class="text-center">{{ $kpiDetail->count_type ?? '-' }}</td>
                                                <td class="text-center">{{ $kpiDetail->value_plan ?? '-' }}</td>
                                                <td class="text-center">{{ $kpiDetail->value_actual ?? '-' }}</td>
                                                <td class="text-center">
                                                    {{ $kpiDetail->value_result ? number_format($kpiDetail->value_result, 2) . '%' : '-' }}
                                                </td>
                                                <td>
                                                    @if ($kpiDetail->count_type === 'RESULT' && $kpiDetail->value_result != 100 && $kpiDetail->value_result != 0)
                                                        <a class="badge badge-warning btn-sm" data-toggle="modal" data-target="#extraTaskModal"
                                                           data-parent-id="{{ $kpiDetail->id }}">
                                                            Ekstra Task
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>

                                            {{-- Extra Tasks --}}
                                            @foreach ($kpiDetail->children as $extraTask)
                                                <tr>
                                                    <td></td>
                                                    <td>[Ekstra Task] {{ $extraTask->kpi_description->description ?? '-' }}</td>
                                                    <td colspan="4"></td>
                                                    <td class="text-center">{{ $extraTask->value_actual }}</td>
                                                    <td></td>
                                                    <td class="text-center">
                                                        <button class="badge badge-danger btn-sm" onclick="deleteExtraTask({{ $extraTask->id }}, this)">
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
@endif
