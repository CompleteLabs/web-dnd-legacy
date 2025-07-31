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
                                    <h3 class="card-title"><strong>Daily</strong></h3>
                                </div>
                                @if (auth()->user()->role_id == 1)
                                    <div class="card-tools d-flex">
                                        <div class="input-group input-group-sm" style="width: 300px;">
                                            <form action="/admin/daily" class="d-inline-flex">
                                                <select class="custom-select col-lg-12 mx-2" name="divisi_id" id="divisi_id"
                                                    required>
                                                    <option value="">--Choose Divisi--</option>
                                                    @foreach ($divisis as $divisi)
                                                        <option value="{{ $divisi->id }}">{{ $divisi->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-default">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="input-group input-group-sm" style="width: 400px;">
                                            <form action="/admin/daily" class="d-inline-flex">
                                                <input type="text" class="form-control float-right" value="" name="date"
                                                    id="tanggal" required>
                                                <input type="text" name="name" class="form-control mx-2 float-right"
                                                    placeholder="Name" required>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-default">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="input-group input-group-sm ml-2" style="width: 90px;">
                                            <div class="input-group-append">
                                                <a href="#">
                                                    <button class="btn btn-success" data-toggle="modal"
                                                    data-target="#importDaily" data-toggle="tooltip" data-placement="top" title="Import Daily"><i class="fa fa-upload" style="color: white"></i></button>
                                                </a>
                                            </div>
                                            <div class="input-group-append ml-1">
                                                <a href="#">
                                                    <button class="btn btn-primary" data-toggle="modal"
                                                        data-target="#exportDaily" data-toggle="tooltip" data-placement="top" title="Export Daily"><i class="fa fa-download"></i></button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="card-tools d-flex">
                                        <div class="input-group input-group-sm mr-3" style="width: 380px;">
                                            <form action="/daily" class="d-inline-flex">
                                                <select class="custom-select col-lg-10 mx-2" name="tasktype" id="tasktype"
                                                    required>
                                                    <option value="">--Choose One--</option>
                                                    <option value="1">Today</option>
                                                    <option value="2">Yesterday</option>
                                                    <option value="3">This Week</option>
                                                    <option value="4">All</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-default">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </form>
                                            <div class="input-group-append ml-5">
                                                <a href="#">
                                                    <button class="btn btn-success" data-toggle="modal"
                                                        data-target="#addDaily" data-toggle="tooltip" data-placement="top" title="Add Daily"><i class="fas fa-plus"></i></button>
                                                </a>
                                            </div>
                                            <div class="input-group-append ml-1">
                                                <a href="#">
                                                    <button class="btn btn-success" data-toggle="modal"
                                                        data-target="#importDaily" data-toggle="tooltip" data-placement="top" title="Import Daily"><i class="fa fa-upload" style="color: white"></i></button>
                                                </a>
                                            </div>
                                            <div class="input-group-append ml-1">
                                                <a href="/daily/template">
                                                    <button class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Download Template"><i class="fas fa-file-alt" style="color: white"></i></button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
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
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0" style="height: 500px;">
                                <table class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            @if (auth()->user()->role_id == 1)
                                                <th>User</th>
                                                <th>Dept</th>
                                                <th>Divisi</th>
                                            @else
                                                <th>Action</th>
                                            @endif
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Task</th>
                                            <th>Type</th>
                                            <th>Plan Result</th>
                                            <th>Actual Result</th>
                                            <!-- <th>Value</th> -->
                                            <!-- <th>status_non</th>
                                            <th>status_result</th> -->
                                            <th>Status</th>
                                            <th>Task Plan</th>
                                            <th>Tagged By</th>
                                            <th>Added By</th>
                                            @if (auth()->user()->role_id == 1)
                                                <th>Status</th>
                                            @endif
                                            <th>On-Time Point</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dailys as $daily)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>

                                                @if (auth()->user()->role_id == 1)
                                                    <td>{{ $daily->user->nama_lengkap }}</td>
                                                    <td>{{ $daily->user->area->name }}</td>
                                                    <td>{{ $daily->user->divisi->name }}</td>
                                                    {{-- <td>
                                                    <a href="/admin/daily/{{ $daily->id }}"
                                                        class="badge bg-warning"><span><i
                                                                class="fas fa-edit"></i></span></a>
                                                </td> --}}
                                                @else
                                                    <td class="d-flex" style="text-align: center;">
                                                        <form action="/daily/change" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $daily->id }}">
                                                            <input type="hidden" name="tasktype"
                                                                value="{{ app('request')->input('tasktype') }}">
                                                            <button type="submit" class="btn far fa-check-circle"
                                                                style="color: {{ $daily->status ? 'green' : 'grey' }};"></button>
                                                        </form>
                                                        <a href="/daily/edit/{{ $daily->id }}"><i
                                                                class="btn fas fa-edit"
                                                                style="color: rgb(239, 239, 54)"></i></a>
                                                        <form action="/daily/delete" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $daily->id }}">
                                                            <button type="submit" class="btn"
                                                                style="color: rgb(204, 26, 26);"><i
                                                                    class="fas fa-trash"></i></button>

                                                        </form>
                                                    </td>
                                                @endif
                                                <td>{{ date('d M Y', $daily->date / 1000) }}</td>
                                                <td>{{ $daily->time ?? '-' }}</td>
                                                <td>{{ $daily->task }}</td>
                                                <td>{{ $daily->tipe }}</td>
                                                <td>{{ $daily->value_plan }}</td>
                                                <td>{{ $daily->value_actual }}</td>
                                                <!-- <td>{{ $daily->value }}</td> -->
                                                <!-- <td>{{ $daily->status }}</td>
                                                <td>{{ $daily->status_result }}</td> -->
                                                <td>{{ $daily->status ? 'CLOSED' : 'OPEN', }}</td>
                                                <td>{{ $daily->isplan ? 'Plan' : 'Extra Task' }}</td>
                                                <td>
                                                    @if ($daily->tag_id)
                                                        {{ $daily->tag->nama_lengkap }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($daily->add_id)
                                                        {{ $daily->add->nama_lengkap }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                @if (auth()->user()->role_id == 1)
                                                    <td>{{ $daily->status ? 'Closed' : 'Open' }}</td>
                                                @endif
                                                <td>{{ $daily->ontime }}</td>
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
                @if (count($dailys) == 100)
                    <div class="d-flex justify-content-center">
                        {{ $dailys->links() }}
                    </div>
                @endif
        </section>
    </section>


    <!-- Modal -->
    <form action={{ auth()->user()->role_id == 1 ? '/admin/daily/import' : '/daily/import' }} method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="importDaily" tabindex="-1" aria-labelledby="importDailyLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importDailyLabel">Import Daily</h5>
                    </div>
                    <div class="modal-body">
                        @if (auth()->user()->role_id == 1)
                            <div class="col-12 mt-3">
                                <label for="userid" class="form-label">Nama</label>
                                <select class="custom-select form-control" id="userid" name="userid[]">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-12 mt-3">
                            <label for="formFile" class="form-label">Pilih File</label>
                            <input class="form-control" type="file" id="formFile" name="file">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Import</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal -->
    <form action="/admin/daily/export" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="exportDaily" tabindex="-1" aria-labelledby="exportDailytLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportDailytLabel">Export Daily</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6 mt-3">
                                <label for="year" class="form-label">Tahun</label>
                                <input type="number" class="form-control" id="year" name="year" min="2022" max="2025"
                                    step="1" value="{{ now()->year }}" required>
                            </div>
                            <div class="col-6 mt-3">
                                <label for="week" class="form-label">Minggu</label>
                                <input type="number" class="form-control" id="week" name="week" min="1"
                                    max="{{ now()->weekOfYear + 1 }}" step="1" value="{{ now()->weekOfYear }}"
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
    <form action="/daily" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="addDaily" tabindex="-1" aria-labelledby="addDailytLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDailytLabel">Add Daily</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12 ml-4">
                            <input type="checkbox" class="form-check-input" id="extraTaskDaily" name="isplan">
                            <label class="form-check-label" for="extraTaskDaily">Extra Task</label>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="mb-3 col-lg-12" id="addDailyTime">
                            <label for="time" class="form-label">Time</label>
                            <input type="time" class="form-control" id="time" name="time">
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="task" class="form-label">Task</label>
                            <input type="text" class="form-control" id="task" name="task" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
