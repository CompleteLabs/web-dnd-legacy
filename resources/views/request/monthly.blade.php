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
                                        {{ $exist ? 'Existing' : 'Replace' }} Monthly
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
                                            <th>Month</th>
                                            <th>Task</th>
                                            <th>Tipe</th>
                                            <th>Plan Result</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($monthlys as $monthly)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $monthly->user->nama_lengkap }}</td>
                                                <td>{{ date('M-y', $monthly->date / 1000) }}</td>
                                                <td>{{ $monthly->task }}</td>
                                                <td>{{ $monthly->tipe }}</td>
                                                <td>{{ $monthly->value_plan ? number_format($monthly->value_plan, 0, ',', '.') : '-' }}
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
