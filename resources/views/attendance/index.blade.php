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
                        @if (session('importMessage'))
                            <div class="alert alert-info">
                                {{ session('importMessage') }}
                            </div>
                        @endif

                        @if (session('skippedDetails') && count(session('skippedDetails')) > 0)
                            <div class="alert alert-warning">
                                <p>Data berikut dilewati karena sudah ada atau tidak valid:</p>
                                <ul>
                                    @foreach (session('skippedDetails') as $skipped)
                                        <li>{{ $skipped['nama_lengkap'] }} - Periode: {{ $skipped['periode'] }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title mt-2"><strong>{{ $title }}</strong></h3>
                                <div class="card-tools">
                                    <a href="{{ route('attendance.create') }}" class="btn btn-success mr-1"
                                        data-toggle="tooltip" data-placement="top" title="Add Attendance">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                    <button class="btn btn-success mr-1" data-toggle="modal" data-target="#importModal"
                                        data-toggle="tooltip" data-placement="top" title="Import User">
                                        <i class="fa fa-upload" style="color: white"></i>
                                    </button>
                                    <a href="{{ url('attendance/download') }}" class="btn btn-warning" data-toggle="tooltip"
                                        data-placement="top" title="Download Template">
                                        <i class="fas fa-file-alt" style="color: white"></i>
                                    </a>
                                    <a href="{{ route('attendance.export') }}" class="btn btn-success" data-toggle="tooltip"
                                        data-placement="top" title="Eksport Kehadiran">
                                        <i class="fas fa-download" style="color: white"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0" style="height: 500px;">
                                <table class="table table-hover table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Nama Lengkap</th>
                                            <th>Periode</th>
                                            <th>Hari Kerja</th>
                                            <th>Keterlambatan < 30 Menit</th>
                                            <th>Keterlambatan > 30 Menit</th>
                                            <th>Sakit/Izin</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attendances as $attendance)
                                            <tr>
                                                <td>{{ $attendance->user->nama_lengkap ?? '-' }}</td>
                                                <td>{{ $attendance->periode }}</td>
                                                <td>{{ $attendance->work_days }}</td>
                                                <td>{{ $attendance->late_less_30 }}</td>
                                                <td>{{ $attendance->late_more_30 }}</td>
                                                <td>{{ $attendance->sick_days }}</td>
                                                <td>
                                                    <a href="{{ route('attendance.show', $attendance->id) }}"
                                                        class="btn btn-info btn-sm">Detail</a>
                                                    <a href="{{ route('attendance.edit', $attendance->id) }}"
                                                        class="btn btn-warning btn-sm">Edit</a>
                                                    <form action="{{ route('attendance.destroy', $attendance->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                    </form>
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
            </div>
        </section>
    </section>
    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Absensi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('attendance.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Upload File Excel:</label>
                            <input type="file" class="form-control" name="file" required>
                        </div>
                        <button type="submit" class="btn btn-success">Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
