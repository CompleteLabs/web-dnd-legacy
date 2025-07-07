@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                                style="color: white;"><span aria-hidden="true">&times;</span></button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                                style="color: white;"><span aria-hidden="true">&times;</span></button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header p-2">
                            <div class="row d-inline-flex ml-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link" href="/dash-kpi"
                                            style="color: #917FB3;">Dashboard</a></li>
                                    <li class="nav-item"><a class="nav-link active" href="/dash-monthly"
                                            style="background-color: #917FB3;">KPI</a></li>
                                            <li class="nav-item"><a class="nav-link" href="/leaderboard" style="color: #917FB3;">Leaderboard</a></li>
                                </ul>
                            </div>
                            <div class="card-tools d-flex align-items-center">
                                <div class="input-group input-group-sm mr-3 mt-1"
                                    style="width: {{ auth()->user()->role_id != 2 ? '450px' : '320px' }};">
                                    <form action="/dash-monthly" class="d-inline-flex">
                                        @if (auth()->user()->role_id != 2)
                                            <select class="custom-select col-lg-5 mx-2" name="user_id" id="user_id">
                                                <option value="">--Choose User--</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->nama_lengkap }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        <input type="text" id="monthpicker" name="month"
                                            class="form-control {{ auth()->user()->role_id != 2 ? 'col-lg-4' : 'col-lg-12' }} mr-1"
                                            placeholder="Choose Month">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                @if (auth()->user()->role_id != 2)
                                    <div class="input-group input-group-sm mr-3 mt-1" style="width: 30px;">
                                        <a href="" data-toggle="modal" data-target="#exportKpi" data-toggle="tooltip"
                                            data-placement="top" title="Download Report" class="btn btn-tool btn-sm">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div>
                                    <div class="timeline timeline-inverse">
                                        @foreach ($groupedKpis as $yearMonth => $groupedKpisByCategory)
                                            @php
                                                $yearMonthText = \Carbon\Carbon::parse($yearMonth)->format('F Y');
                                            @endphp

                                            @php
                                                $userKpi = $groupedKpisByCategory->first()->first();
                                            @endphp

                                            <div class="time-label">
                                                @if ($userKpi)
                                                    <span
                                                        style="background-color: #2A2F4F; color: white;">{{ $userKpi->user->nama_lengkap }}
                                                        - {{ $userKpi->user->position->name ?? '-' }}</span>
                                                @else
                                                    <span style="background-color: #2A2F4F; color: white;">No KPIs
                                                        available</span>
                                                @endif
                                            </div>

                                            {{-- Time label for each month --}}
                                            <div class="time-label">
                                                <span
                                                    style="background-color: #2A2F4F; color: white;">{{ $yearMonthText }}</span>
                                            </div>

                                            {{-- KPIs by Category --}}
                                            @foreach ($groupedKpisByCategory as $categoryName => $kpis)
                                                <div>
                                                    <!-- <i class="fas fa-check bg-success"></i> -->
                                                    <div class="timeline-item">
                                                        <span class="time">Percentage:
                                                            {{ $kpis->first()->percentage }}%</span>
                                                        <h3 class="timeline-header"><strong>{{ $categoryName }}</strong>
                                                        </h3>
                                                        <div class="table-responsive">
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 2%;"></th>
                                                                        <th>KPI Description</th>
                                                                        <th class="text-center" style="width: 10%;">Start
                                                                        </th>
                                                                        <th class="text-center" style="width: 10%;">End</th>
                                                                        <th class="text-center" style="width: 10%;">Type
                                                                        </th>
                                                                        <th class="text-center" style="width: 10%;">Value
                                                                            Plan</th>
                                                                        <th class="text-center" style="width: 10%;">Value
                                                                            Actual</th>
                                                                        <th class="text-center" style="width: 10%;">Value
                                                                            Result</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($kpis as $kpi)
                                                                        @foreach ($kpi->kpi_detail->where('is_extra_task', 0) as $kpiDetail)
                                                                            <!-- Ambil data utama -->
                                                                            <tr>
                                                                                <td>
                                                                                    @include(
                                                                                        'partials.kpi_action',
                                                                                        [
                                                                                            'kpiDetail' => $kpiDetail,
                                                                                        ]
                                                                                    )
                                                                                </td>
                                                                                <td>{{ $kpiDetail->kpi_description->description }}
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    {{ $kpiDetail->start == null ? '-' : Carbon\Carbon::parse($kpiDetail->start)->format('d M Y') }}
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    {{ $kpiDetail->end == null ? '-' : Carbon\Carbon::parse($kpiDetail->end)->format('d M Y') }}
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    {{ $kpiDetail->count_type }}</td>
                                                                                <td class="text-center">
                                                                                    {{ $kpiDetail->value_plan }}</td>
                                                                                <td class="text-center">
                                                                                    {{ $kpiDetail->value_actual }}</td>
                                                                                @if ($kpiDetail->count_type === 'NON')
                                                                                    <td class="text-center">
                                                                                        {{ $kpiDetail->value_result == 1 ? '100%' : number_format($kpiDetail->value_result, 2) . '%' }}
                                                                                    </td>
                                                                                @elseif ($kpiDetail->count_type === 'RESULT')
                                                                                    <td class="text-center">
                                                                                        {{ number_format($kpiDetail->value_result * 100, 2) }}%
                                                                                    </td>
                                                                                    @if ($kpiDetail->value_result > 0 && $kpiDetail->value_result < 1)
                                                                                        <td>
                                                                                            <button
                                                                                                class="badge badge-warning btn-sm"
                                                                                                data-id="{{ $kpiDetail->id }}"
                                                                                                onclick="openExtraTaskModal(this)">Ekstra
                                                                                                Task</button>
                                                                                        </td>
                                                                                    @endif
                                                                                @endif
                                                                            </tr>

                                                                            <!-- Data Ekstra Task -->
                                                                            @foreach ($kpiDetail->children as $extraTask)
                                                                                <tr>
                                                                                    <td></td>
                                                                                    <td>[Ekstra Task]
                                                                                        {{ $extraTask->kpi_description->description ?? '-' }}
                                                                                    </td>
                                                                                    <td></td>
                                                                                    <td></td>
                                                                                    <td class="text-center">
                                                                                        {{ $extraTask->count_type }}</td>
                                                                                    <td></td>
                                                                                    <td class="text-center">
                                                                                        {{ $extraTask->value_actual }}</td>
                                                                                    <td></td>
                                                                                    <td class="text-center">
                                                                                        <button type="button"
                                                                                            class="badge badge-danger btn-sm"
                                                                                            onclick="deleteExtraTask('{{ $extraTask->id }}')">
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
                                        {{-- Placeholder for the gray icon --}}
                                        <div>
                                            <i class="fas fa-exclamation-circle"
                                                style="background-color: #917FB3; color: white;"></i>
                                        </div>

                                        <!-- RESULTS -->
                                        @foreach ($groupedKpis as $yearMonth => $groupedKpisByCategory)
                                            @include('partials.kpi_results', [
                                                'groupedKpis' => $groupedKpis,
                                                'totalScore' => $totalScore,
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>

    @include('modals.export_kpi', ['divisions' => $divisions])

    <!-- Modal Template -->
    <div class="modal fade" id="extraTaskModal" tabindex="-1" aria-labelledby="extraTaskModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="extraTaskForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="extraTaskModalLabel">Tambahkan Ekstra Task</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="parent_id" name="parent_id">
                        <div class="row">
                            <div class="mb-3 col-12">
                                <label class="form-label">Deskripsi Ekstra Task</label>
                                <input class="form-control" id="description" name="description">
                            </div>
                            <div class="mb-3 col-12">
                                <label class="form-label">Count Type</label>
                                <select name="count_type" class="form-control" id="count_type">
                                    <option value="NON">NON</option>
                                    <option value="RESULT">RESULT</option>
                                </select>
                            </div>
                            <div class="mb-3 col-12 d-none" id="value_actual_group">
                                <label class="form-label">Nilai Aktual</label>
                                <input type="number" class="form-control" id="value_actual" name="value_actual"
                                    step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-success" onclick="submitExtraTask()">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <input type="hidden" id="modalKpiId">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalLabel">Update KPI</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label class="form-label">KPI Desc</label>
                            <input type="text" class="form-control" id="modalDescription" disabled>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label class="form-label">Tipe</label>
                            <input type="text" class="form-control" id="modalCountType" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label class="form-label">Month</label>
                            <input type="text" class="form-control" id="modalMonth" readonly>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label class="form-label">Value Plan</label>
                            <input type="text" class="form-control" id="modalValuePlan" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label class="form-label">Value Actual</label>
                            <input type="number" class="form-control" id="modalValueActual" value="0" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success mt-3" onclick="submitResultUpdate()">Submit</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('footer')
    <script>
        // Fungsi untuk Membuka Modal
        function openExtraTaskModal(button) {
            const parentId = button.getAttribute('data-id');
            const modal = document.getElementById('extraTaskModal');

            // Set parent_id untuk modal
            document.getElementById('parent_id').value = parentId;

            // Reset form saat modal dibuka
            document.getElementById('extraTaskForm').reset();
            document.getElementById('value_actual_group').classList.add('d-none');

            // Tampilkan modal
            $(modal).modal('show');
        }

        // Event Listener untuk Dropdown count_type
        document.addEventListener('change', function(event) {
            if (event.target && event.target.id === 'count_type') {
                const countType = event.target.value;
                const valueActualGroup = document.getElementById('value_actual_group');

                if (countType === 'RESULT') {
                    valueActualGroup.classList.remove('d-none');
                } else {
                    valueActualGroup.classList.add('d-none');
                }
            }
        });

        // Fungsi untuk Submit Ekstra Task
        function submitExtraTask() {
            const form = document.getElementById('extraTaskForm');
            const formData = new FormData(form);

            fetch('/extra-task/store', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // alert('Ekstra task berhasil ditambahkan!');
                        location.reload(); // Refresh halaman
                    } else {
                        alert('Gagal menambahkan ekstra task!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan!');
                });
        }

        // Fungsi untuk Menghapus Ekstra Task
        function deleteExtraTask(extraTaskId) {
            if (!confirm('Yakin ingin menghapus?')) return;

            fetch(`/extra-task/${extraTaskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // alert(data.message);
                        location.reload(); // Refresh halaman
                    } else {
                        alert(data.message || 'Gagal menghapus ekstra task!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus!');
                });
        }
    </script>
@endsection
