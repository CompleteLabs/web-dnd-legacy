@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header p-2">
                            <div class="row d-inline-flex ml-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link" href="/dash-kpi">Dashboard</a></li>
                                    <!-- <li class="nav-item"><a class="nav-link" href="/dash-daily">Daily</a></li>
                                    <li class="nav-item"><a class="nav-link active" href="/dash-weekly">Weekly</a></li> -->
                                    <li class="nav-item"><a class="nav-link" href="/dash-monthly">Monthly</a></li>
                                </ul>
                            </div>
                            <div class="card-tools">
                                <div class="input-group input-group-sm mr-3 mt-1" style="width: {{ auth()->user()->role_id != 2 ? '450px' : '320px' }};">
                                    <form action="/dash-weekly" class="d-inline-flex">
                                        @if (auth()->user()->role_id != 2)
                                            <select class="custom-select col-lg-6 mx-2" name="user_id" id="user_id">
                                                <option value="">--Choose User--</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->nama_lengkap }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        <input type="text" id="monthpicker" name="month" class="form-control {{ auth()->user()->role_id != 2 ? 'col-lg-4' : 'col-lg-12' }} mr-1" placeholder="Choose Month">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div>
                                    <div class="timeline timeline-inverse">
                                        @php
                                            // Assuming $groupedKpis is the array of collections
                                            $firstCollection = reset($groupedKpis);
                                            $userKpi = $firstCollection ? $firstCollection->first() : null;
                                        @endphp

                                        <div class="time-label">
                                            @if ($userKpi)
                                                <span class="bg-primary">{{ $userKpi->first()->user->nama_lengkap ?? '-' }} - {{ $userKpi->first()->user->position->name ?? '-' }}</span>
                                            @else
                                                <!-- <span class="bg-primary"></span> -->
                                            @endif
                                        </div>
                                        
                                        @foreach ($groupedKpis as $date => $groupedKpisByCategory)
                                            {{-- Time label --}}
                                            <div class="time-label">
                                                <span class="bg-warning">Week {{ $date }}</span>
                                            </div>

                                            {{-- KPIs by Category --}}
                                            @foreach ($groupedKpisByCategory as $categoryName => $kpis)
                                                <div>
                                                    <!-- <i class="fas fa-check bg-success"></i> -->
                                                    <div class="timeline-item">
                                                        <span class="time">{{ $kpis->first()->percentage }}%</span>
                                                        <h3 class="timeline-header"><strong>{{ $categoryName }}</strong></h3>
                                                        <div class="table-responsive">
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 2%;"></th>
                                                                        <th>KPI Description</th>
                                                                        <th class="text-center" style="width: 10%;">Type</th>
                                                                        <th class="text-center" style="width: 10%;">Value Plan</th>
                                                                        <th class="text-center" style="width: 10%;">Value Actual</th>
                                                                        <th class="text-center" style="width: 10%;">Value Result</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($kpis as $kpi)
                                                                        @foreach ($kpi->kpi_detail as $kpiDetail)
                                                                            <tr>
                                                                                <td>
                                                                                    @if ($kpiDetail->count_type === 'NON')
                                                                                        <form action="/dash/change" method="POST" id="nonCountForm">
                                                                                            @csrf
                                                                                            <input type="hidden" name="id" value="{{ $kpiDetail->id }}">
                                                                                            <input type="hidden" name="type" value="weekly">
                                                                                            <button type="submit" class="btn far fa-check-circle" style="color: {{ $kpiDetail->value_result != null ? 'green' : 'grey' }};"></button>
                                                                                        </form>
                                                                                    @elseif ($kpiDetail->count_type === 'RESULT')
                                                                                        <!-- Add your modal trigger here to open the modal and let the user choose an option -->
                                                                                        <!-- Once the user selects an option, you can submit the appropriate form with JavaScript or handle it through another form submission action. -->
                                                                                        <button type="button" class="btn far fa-check-circle" style="color: {{ $kpiDetail->value_result != null ? 'green' : 'grey' }};" data-toggle="modal" data-target="#changeStatus{{ $kpiDetail->id }}"></button>
                                                                                        <!-- Modal Edit -->
                                                                                        <div class="modal fade" id="changeStatus{{ $kpiDetail->id }}" tabindex="-1" role="dialog" aria-labelledby="changeStatusLabel" aria-hidden="true">
                                                                                            <div class="modal-dialog modal-lg" role="document">
                                                                                                <div class="modal-content">
                                                                                                    <div class="modal-header">
                                                                                                        <h5 class="modal-title" id="changeStatusLabel">Update KPI</h5>
                                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                            <span aria-hidden="true">&times;</span>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    <div class="modal-body">
                                                                                                        <!-- Add the form or other content you want inside the modal body -->
                                                                                                        <form action="/dash/change" method="POST">
                                                                                                            @csrf
                                                                                                            <input type="hidden" name="id" value="{{ $kpiDetail->id }}">
                                                                                                            <input type="hidden" name="type" value="weekly">
                                                                                                            <div class="row">
                                                                                                                <div class="mb-3 col-lg-6">
                                                                                                                    <label for="kpi_description_id" class="form-label">KPI Desc</label>
                                                                                                                    <input type="text" class="form-control" id="kpi_description_id" name="kpi_description_id"
                                                                                                                        value="{{ $kpiDetail->kpi_description->description }}" disabled>
                                                                                                                </div>
                                                                                                                <div class="mb-3 col-lg-6">
                                                                                                                    <label for="count_type" class="form-label">Tipe</label>
                                                                                                                    <input type="text" class="form-control" id="count_type" name="count_type"
                                                                                                                        value="{{ $kpiDetail->count_type }}" disabled>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row">
                                                                                                                <div class="mb-3 col-lg-6">
                                                                                                                    <label for="week" class="form-label">Week</label>
                                                                                                                    <input type="text" class="form-control" id="week" name="week"
                                                                                                                        value="{{ $date }}" readonly>
                                                                                                                </div>
                                                                                                                <div class="mb-3 col-lg-6">
                                                                                                                    <label for="value_plan" class="form-label">Value Plan</label>
                                                                                                                    <input type="text" class="form-control" name="value_plan"
                                                                                                                        value="{{ number_format($kpiDetail->value_plan, 0, ',', '.') }}" readonly>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row">
                                                                                                                <div class="mb-3 col-lg-6">
                                                                                                                    <label for="value_actual" class="form-label">Value Actual</label>
                                                                                                                    <input type="number" class="form-control" id="value_actual" name="value_actual"
                                                                                                                        value="0" required>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <button type="submit" class="btn btn-success mt-3">Submit</button>
                                                                                                        </form>
                                                                                                    </div>
                                                                                                    <div class="modal-footer">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $kpiDetail->kpi_description->description }}</td>
                                                                                <td class="text-center">{{ $kpiDetail->count_type }}</td>
                                                                                <td class="text-center">{{ $kpiDetail->value_plan }}</td>
                                                                                <td class="text-center">{{ $kpiDetail->value_actual }}</td>
                                                                                <td class="text-center">{{ $kpiDetail->value_result }}</td>
                                                                            </tr>
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
                                            <i class="fas fa-exclamation-circle bg-primary"></i>
                                        </div>

                                        <!-- RESULTS -->
                                        <div>
                                            <div class="timeline-item">
                                                <h3 class="timeline-header"><strong>YOUR RESULTS !</strong></h3>
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>KPI Category</th>
                                                                <th class="text-center" style="width: 10%;">Percentage</th>
                                                                <th class="text-center" style="width: 10%;">Actual</th>
                                                                <th class="text-center" style="width: 10%;">Score</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($groupedKpis as $date => $groupedKpisByCategory)
                                                                {{-- KPIs by Category --}}
                                                                @foreach ($groupedKpisByCategory as $categoryName => $kpis)
                                                                    @foreach ($kpis as $kpi)
                                                                        <tr>
                                                                            <td>Week {{ $date }} - {{ $kpi->kpi_category->name }}</td>
                                                                            <td class="text-center">{{ $kpi->percentage }}%</td>
                                                                             <td class="text-center">{{ $kpi->actualCount }}</td>
                                                                            <td class="text-center">{{ $kpi->score }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endforeach
                                                            @endforeach
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                <td><strong>FINAL SCORE</strong></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td class="text-center"><strong>{{ $totalScore * 100 }}%</strong></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
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
    <!-- /.content -->
    <!-- /.content-wrapper -->
@endsection
