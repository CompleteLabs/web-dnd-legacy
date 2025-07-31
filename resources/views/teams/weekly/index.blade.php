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
                                    <h3 class="card-title"><strong>Team's Weekly</strong></h3>
                                </div>
                                <div class="card-tools d-flex">
                                    <div class="input-group input-group-sm mr-3" style="width: 220px;">
                                        <form action="/teams/weekly" class="d-inline-flex">
                                            <select class="custom-select col-lg-10 mx-2" name="user">
                                                <option value="">-- User --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->nama_lengkap }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="input-group input-group-sm mr-3 ml-2" style="width: 390px;">
                                        <form action="/teams/weekly" class="d-inline-flex">
                                            <select class="custom-select col-lg-10 mx-2" name="tasktype" id="tasktype">
                                                <option value="">--Choose One--</option>
                                                <option value="1">This Week</option>
                                                <option value="2">Last Week</option>
                                                <option value="3">All</option>
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
                                                    data-target="#addWeekly" data-toggle="tooltip" data-placement="top" title="Add Weekly"><i class="fas fa-plus"></i></button>
                                            </a>
                                        </div>
                                        @if (auth()->user()->role_id != 2)
                                            <div class="input-group-append ml-1">
                                                <a href="#">
                                                    <button class="btn btn-info" data-toggle="modal"
                                                        data-target="#sendWeekly" data-toggle="tooltip" data-placement="top" title="Send Weekly"><i class="fas fa-paper-plane"></i></button>
                                                </a>
                                            </div>
                                            <div class="input-group-append ml-1">
                                                <a href="#">
                                                    <button class="btn btn-primary" data-toggle="modal"
                                                        data-target="#sendWeeklyBulk" data-toggle="tooltip" data-placement="top" title="Send Weekly Bulk"><i class="fas fa-mail-bulk"></i></button>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
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
                            <div class="card-body table-responsive table-striped p-0" style="height: 500px;">
                                <table class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Action</th>
                                            <th>User</th>
                                            <th>Year</th>
                                            <th>Week</th>
                                            <th>Task</th>
                                            <!-- <th>Category</th>
                                            <th>Task Status</th> -->
                                            <th>Type</th>
                                            <th>Plan Result</th>
                                            <th>Actual Result</th>
                                            <th>Status</th>
                                            <th>Task Plan</th>
                                            <th>Tagged By</th>
                                            <th>Added By</th>
                                            <th>Change Task</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($weeklys as $weekly)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                {{-- <td>
                                                    <a href="/weekly/{{ $weekly->id }}" class="badge bg-warning"><span><i
                                                    class="fas fa-edit"></i></span></a>
                                                </td> --}}
                                                <td class="d-flex" style="text-align: center;">
                                                    @if ($weekly->tipe == 'NON')
                                                    <form action="/weekly/change" method="POST">
                                                        @csrf
                                                            <input type="hidden" name="id" value="{{ $weekly->id }}">
                                                            <input type="hidden" name="page" value="teams">
                                                            <input type="hidden" name="tasktype" value="{{ app('request')->input('tasktype') }}">
                                                            <button type="submit" class="btn far fa-check-circle"
                                                                style="color: {{ $weekly->value ? 'green' : 'grey' }};"></button>
                                                        </form>
                                                    @else
                                                        <a href="/weekly/change/result/{{ $weekly->id }}"><i
                                                                class="btn far fa-check-circle"
                                                                style="color: {{ $weekly->value ? 'green' : 'grey' }};"></i></a>
                                                    @endif
                                                    <form action="/weekly/edit/{{ $weekly->id }}" method="GET">
                                                        @csrf
                                                        <button type="submit" class="btn" style="color: rgb(239, 239, 54)"><i
                                                        class="fas fa-edit"></i></button>
                                                        <input type="hidden" name="page" value="teams">
                                                    </form>
                                                    <form action="/weekly/delete" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $weekly->id }}">
                                                        <input type="hidden" name="page" value="teams">
                                                        <button type="submit" class="btn"
                                                        style="color: rgb(204, 26, 26);"><i
                                                        class="fas fa-trash"></i></button>

                                                    </form>
                                                </td>
                                                <td>{{ $weekly->user->nama_lengkap}}</td>
                                                <td>{{ $weekly->year }}</td>
                                                <td>{{ $weekly->week }}</td>
                                                <td>{{ $weekly->task }}</td>
                                                <!-- <td>{{ $weekly->taskcategory->task_category ?? '' }}</td>
                                                <td class="
                                                    @if ($weekly->taskstatus != null)
                                                        @if ($weekly->taskstatus->task_status == 'PROGRESS')
                                                            {{"text-warning"}}
                                                        @elseif ($weekly->taskstatus->task_status == 'DONE')
                                                            {{"text-success"}}
                                                        @elseif ($weekly->taskstatus->task_status == 'PLANNED')
                                                            {{"text-info"}}
                                                        @else
                                                            {{"text-danger"}}
                                                        @endif
                                                    @endif
                                                ">
                                                {{ $weekly->taskstatus->task_status ?? '' }}</td> -->
                                                <td>{{ $weekly->tipe }}</td>
                                                <td>{{ $weekly->value_plan ? number_format($weekly->value_plan, 0, ',', '.') : '-' }}
                                                </td>
                                                <td>{{ $weekly->value_actual ? number_format($weekly->value_actual, 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="
                                                    {{ $weekly->value ? 'text-success' : 'text-warning', }}
                                                ">
                                                {{ $weekly->value ? 'CLOSED' : 'OPEN', }}</td>
                                                <td>{{ !$weekly->is_add ? 'Plan' : 'Extra Task' }}</td>
                                                <td>
                                                    @if ($weekly->tag_id)
                                                        {{ $weekly->tag->nama_lengkap }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($weekly->add_id)
                                                        {{ $weekly->add->nama_lengkap }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($weekly->isupdate)
                                                        <i class="far fa-check-circle" style="color: green;"></i>
                                                    @else
                                                        <i class="far fa-times-circle" style="color: red;"></i>
                                                    @endif
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
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            {{ $weeklys->links() }}
                        </div>
                        <br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row d-inline-flex">
                                    <h3 class="card-title"><strong>Log Activity</strong></h3>
                                </div>
                                <div class="card-tools d-flex">
                                    <div class="input-group input-group-sm mr-3" style="width: 220px;">
                                        <form action="/teams/weekly" class="d-inline-flex">
                                            <select class="custom-select col-lg-10 mx-2" name="user_log">
                                                <option value="">-- User --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->nama_lengkap }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive table-striped p-0" style="height: 500px;">
                                <table class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>User</th>
                                            <th>Activity</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <!-- <th>On-Time Point</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($logs as $log)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>

                                                <td>{{ $log->user->nama_lengkap}}</td>
                                                <td>{{ $log->activity }}</td>
                                                <td>{{ Carbon\Carbon::parse($log->created_at)->format('d M Y') }}</td>
                                                <td>{{ Carbon\Carbon::parse($log->created_at)->format('H:i') }}</td>
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
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            {{ $logs->links() }}
                        </div>
                        <br>
                    </div>
                </div>
        </section>
    </section>


    <!-- Modal Import Weekly -->
    <form action={{ auth()->user()->role->name != 'STAFF' ? '/admin/weekly/import' : '/weekly/import' }} method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="sendWeeklyBulk" tabindex="-1" aria-labelledby="sendWeeklyBulkLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sendWeeklyBulkLabel">Send Weekly</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12">
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipe</label>
                                <br>
                                <select class="custom-select col-lg-12 addtypeweeklybulk" name="type" id="type">
                                    <option value="">-- Choose --</option>
                                        <option value="1">RESULT</option>
                                        <option value="0">NON RESULT</option>
                                </select>
                            </div>
                        </div>
                        @if (auth()->user()->role->nama != 'STAFF')
                            <div class="col-12 mt-3">
                                <label for="userid" class="form-label">Nama</label>
                                <br>
                                <select class="custom-select form-control select2 adduserweeklybulk" id="userid" name="userid[]" multiple>
                                </select>
                            </div>
                        @endif
                        <div class="col-12 mt-3">
                            <label for="formFile" class="form-label">Pilih File</label>
                            <input class="form-control" type="file" id="formFile" name="file">
                            <input type="hidden" name="page" value="teams">
                            <input type="hidden" class="form-control userdivisiweeklybulk" name="userdivisiweeklybulk" id="userdivisiweeklybulk" value="{{ auth()->user()->divisi_id }}">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal -->
    <!-- <form action="/admin/weekly/export" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="exportWeekly" tabindex="-1" aria-labelledby="exportWeeklytLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportWeeklytLabel">Export Weekly</h5>
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
    </form> -->

    <!-- Modal Add Weekly -->
    <form action="/weekly" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="addWeekly" tabindex="-1" aria-labelledby="addWeeklytLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addWeeklytLabel">Add Weekly</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12 ml-4">
                            <input type="checkbox" class="form-check-input" id="extraTaskWeekly" name="is_add">
                            <label class="form-check-label" for="extraTaskWeekly">Extra Task</label>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="year" class="form-label">Year</label>
                            <input type="number" class="form-control" id="year" name="year" value="{{ now()->year }}"
                                required>
                        </div>
                        <div class="mb-3 col-lg-12" id="addWeeklyWeek">
                            <label for="week" class="form-label">Week</label>
                            <input type="number" class="form-control" id="week" name="week" max="52"
                                value="{{ now()->weekOfYear }}">
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="task" class="form-label">Task</label>
                            <input type="text" class="form-control" id="task" name="task" autocomplete="off" required>
                        </div>
                        @if (auth()->user()->wr)
                            <div class="mb-3 col-lg-12 ml-4 d-flex">
                                <input type="checkbox" class="form-check-input" id="resultkWeekly" name="result">
                                <label class="form-check-label" for="resultkWeekly">Result ?</label>
                                <div class="col-md-8">
                                    <input type="number" class="form-control ml-4 value_plan" id="value_plan"
                                        name="value_plan" autocomplete="off">
                                    <span class="ml-4" id="nominal"></span>
                                </div>
                            </div>
                        @endif
                        <div class="mb-3 col-lg-12">
                            <input type="hidden" class="form-control" name="page" value="teams">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">+ Add</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Send Weekly -->
    <form action="/teams/sendWeekly" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="sendWeekly" tabindex="-1" aria-labelledby="sendWeeklytLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sendWeeklytLabel">Send Weekly</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12 ml-4">
                            <input type="checkbox" class="form-check-input" id="extraTaskWeekly" name="is_add">
                            <label class="form-check-label" for="extraTaskWeekly">Extra Task</label>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipe</label>
                                <br>
                                <select class="custom-select col-lg-12 addtype" name="type" id="type">
                                    <option value="">-- Choose --</option>
                                        <option value="1">RESULT</option>
                                        <option value="0">NON RESULT</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">User</label>
                                <br>
                                <select class="custom-select col-lg-12 select2 adduser" name="user_id[]" id="user_id" multiple>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="year" class="form-label">Year</label>
                            <input type="number" class="form-control" id="year" name="year" value="{{ now()->year }}"
                                required>
                        </div>
                        <div class="mb-3 col-lg-12" id="sendWeeklyWeek">
                            <label for="week" class="form-label">Week</label>
                            <input type="number" class="form-control" id="week" name="week" max="52"
                                value="{{ now()->weekOfYear }}">
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="task" class="form-label">Task</label>
                            <input type="text" class="form-control" id="task" name="task" autocomplete="off" required>
                        </div>
                        <div class="mb-3 col-lg-12 ml-4 d-flex inputresult">
                        </div>
                        <div class="mb-3 col-lg-12">
                            <input type="hidden" class="form-control" name="page" value="teams">
                            <input type="hidden" class="form-control userdivisi" name="userdivisi" id="userdivisi" value="{{ auth()->user()->divisi_id }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">+ Add</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
