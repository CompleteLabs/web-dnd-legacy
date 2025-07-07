@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
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
                        <div class="card card-white">
                            <!-- /.card-header -->
                            <div class="card-header">
                                <h3 class="card-title"><strong>SHOW KPI &raquo;</strong> {{ $kpi->user->position->name ?? '' }}</h3>
                                <div class="card-tools">
                                <a href="/kpi/{{ $kpi->id }}/edit" data-toggle="tooltip" data-placement="top" title="Edit KPI" class="btn btn-tool btn-sm" id="btn-edit-kpi" data-kpi-id="{{ $kpi->id }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12" style="margin-bottom: 30px;">
                                    <table class="table table-hover" id="reqbar_table">
                                        <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>KPI</th>
                                            <th>Start</th>
                                            <th>End</th>
                                            <th>Count Type</th>
                                            <th>Value Plan</th>
                                            <th>Value Actual</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($kpi->kpi_detail as $detail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $detail->kpi_description->description ?? ''}}</td>
                                                <td>{{ $detail->start == null ? ' ' : Carbon\Carbon::parse($detail->start)->format('d M Y') }}</td>
                                                <td>{{ $detail->end == null ? ' ' : Carbon\Carbon::parse($detail->end)->format('d M Y') }}</td>
                                                <td>{{ $detail->count_type }}</td>
                                                <td>{{ $detail->value_plan }}</td>
                                                <td>{{ $detail->value_actual }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </section>
    </section>
    <!-- /.content -->
@endsection
