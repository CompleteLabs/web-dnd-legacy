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
                                    <h3 class="card-title"><strong>Detail Request &raquo;</strong> Task {{ $exist ? 'Existing' : 'Replace' }} Daily
                                    </h3>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0" style="height: 500px;">
                                <table class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>User</th>
                                            <th>Date</th>
                                            <th>Task</th>
                                            <th>Type</th>
                                            <th>Value Plan</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dailys as $daily)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $daily->user->nama_lengkap }}</td>
                                                <td>{{ date('d M Y', $daily->date / 1000) }}</td>
                                                <td>{{ $daily->task }}</td>
                                                <td>{{ $daily->tipe }}</td>
                                                <td>{{ $daily->value_plan }}</td>
                                                <td>{{ $daily->time ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
        </section>
    </section>
@endsection
