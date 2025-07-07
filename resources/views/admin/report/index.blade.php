@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row d-inline-flex">
                                    <h3 class="card-title"><strong>Report Week {{ $now ? $now->weekOfYear : now()->weekOfYear }} - Periode :
                                        {{ $now ? $now->format('d M') :  now()->startOfWeek()->format('d M') }} -
                                        {{ $now ? $now->endOfWeek()->format('d M Y') : now()->endOfWeek()->format('d M Y') }}
                                    </strong></h3>
                                </div>
                                <div class="card-tools d-flex">
                                    <div class="input-group input-group-sm mr-3" style="width: 400px;">
                                        <form action="/admin/report" class="d-inline-flex">
                                            <select class="custom-select col-lg-5 mx-1" name="divisi_id" id="divisi_id"
                                                required>
                                                <option value="">--Choose Divisi--</option>
                                                @foreach ($divisis as $divisi)
                                                    <option value="{{ $divisi->id }}">{{ $divisi->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="number" name="year" class="form-control mr-1 float-right"
                                                placeholder="tahun" value="{{ now()->year }}" min="2022" required>
                                            <input type="number" name="week" class="form-control mr-1 float-right"
                                                placeholder="tahun" value="{{ now()->weekOfYear }}" min="1" max="52"
                                                required>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    @if (auth()->user()->role_id == 1)
                                        <div class="input-group input-group-sm" style="width: 100px;">
                                            <div class="input-group-append ml-1">
                                                <a href="#">
                                                    <button class="btn btn-primary" data-toggle="modal"
                                                        data-target="#exportReport" data-toggle="tooltip" data-placement="top" title="Export Report"><i class="fas fa-file-alt" style="color: white"></i></button>
                                                </a>
                                            </div>
                                            <div class="input-group-append ml-1">
                                                <a href="#">
                                                    <button class="btn btn-warning" data-toggle="modal"
                                                        data-target="#broadcast" data-toggle="tooltip" data-placement="top" title="Broadcast Message"><i class="fas fa-bullhorn" style="color: white"></i></button>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0" style="height: 500px;">
                                <table class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>User</th>
                                            <th>Dept</th>
                                            <th>Divisi</th>
                                            <th>Senin</th>
                                            <th>Selasa</th>
                                            <th>Rabu</th>
                                            <th>Kamis</th>
                                            <th>Jumat</th>
                                            <th>Sabtu</th>
                                            <th>Minggu</th>
                                            <th>Daily Point</th>
                                            <th>Ontime Point</th>
                                            <th>Weekly Actual</th>
                                            <th>Weekly Point</th>
                                            {{-- <th>Monthly Point</th> --}}
                                            <th>Point KPI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($reports as $report)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $report['user']->nama_lengkap }}</td>
                                                <td>{{ $report['user']->area->name }}</td>
                                                <td>{{ $report['user']->divisi->name }}</td>
                                                <td>{{ $report['act']['Mon'] == '0/0' ? '-' : $report['act']['Mon'] }}
                                                </td>
                                                <td>{{ $report['act']['Tue'] == '0/0' ? '-' : $report['act']['Tue'] }}
                                                </td>
                                                <td>{{ $report['act']['Wed'] == '0/0' ? '-' : $report['act']['Wed'] }}
                                                </td>
                                                <td>{{ $report['act']['Thu'] == '0/0' ? '-' : $report['act']['Thu'] }}
                                                </td>
                                                <td>{{ $report['act']['Fri'] == '0/0' ? '-' : $report['act']['Fri'] }}
                                                </td>
                                                <td>{{ $report['act']['Sat'] == '0/0' ? '-' : $report['act']['Sat'] }}
                                                </td>
                                                <td>{{ $report['act']['Sun'] == '0/0' ? '-' : $report['act']['Sun'] }}
                                                </td>
                                                <td>{{ number_format($report['daily'], 1, ',', ' ') }}</td>
                                                <td>{{ number_format($report['ontime_point'], 1, ',', ' ') }}</td>
                                                <td>{{ $report['actw'] }}</td>
                                                <td>{{ number_format($report['weekly'], 1, ',', ' ') }}</td>
                                                {{-- <td>{{ number_format($report['monthly'], 1, ',', ' ') }}</td> --}}
                                                <td>{{ number_format($report['total'], 1, ',', ' ') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </section>
    </section>

    <!-- Modal -->
    <form action="/admin/report/export" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="exportReport" tabindex="-1" aria-labelledby="exportReportLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportReportLabel">Export Report</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mt-3">
                                <label for="divisi_id" class="form-label col-lg-12 ">Divisi</label>
                                <select class="custom-select col-lg-12" name="divisi_id" id="divisi_id" required>
                                    @foreach ($divisis as $divisi)
                                        <option value="{{ $divisi->id }}">{{ $divisi->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 mt-3">
                                <label for="year" class="form-label">Tahun</label>
                                <input type="number" class="form-control" id="year" name="year" min="2022" max="2025"
                                    step="1" value="{{ now()->year }}" required>
                            </div>
                            <div class="col-6 mt-3">
                                <label for="week" class="form-label">Minggu</label>
                                <input type="number" class="form-control" id="week" name="week" min="1"
                                    max="{{ now()->weekOfYear }}" step="1" value="{{ now()->weekOfYear - 1 }}"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Export</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal -->
    <form action="/broadcast" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="broadcast" tabindex="-1" aria-labelledby="broadcastLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="broadcasttLabel">Broadcast</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12">
                            <label for="content" class="form-label">Content</label>
                            <input type="text" class="form-control" id="content" name="content" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
