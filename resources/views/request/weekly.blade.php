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
                                    <h3 class="card-title"><strong>Detail Request &raquo;</strong> Task
                                        {{ $exist ? 'Existing' : 'Replace' }} Weekly
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
                                            <th>Year</th>
                                            <th>Week</th>
                                            <th>Task</th>
                                            <th>Tipe</th>
                                            <th>Plan Result</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($weeklys as $weekly)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $weekly->user->nama_lengkap }}</td>
                                                <td>{{ $weekly->year }}</td>
                                                <td>{{ $weekly->week }}</td>
                                                <td>{{ $weekly->task }}</td>
                                                <td>{{ $weekly->tipe }}</td>
                                                <td>{{ $weekly->value_plan ? number_format($weekly->value_plan, 0, ',', '.') : '-' }}
                                                </td>
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
