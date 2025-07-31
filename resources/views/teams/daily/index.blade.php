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
                                    <h3 class="card-title"><strong>Team's Daily</strong></h3>
                                </div>
                                <div class="card-tools d-flex">
                                    <div class="input-group input-group-sm mr-3" style="width: 220px;">
                                        <form action="/teams/daily" class="d-inline-flex">
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
                                        <form action="/teams/daily" class="d-inline-flex">
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
                                        @if (auth()->user()->role_id != 2)
                                            <div class="input-group-append ml-1">
                                                <a href="#">
                                                    <button class="btn btn-info" data-toggle="modal"
                                                        data-target="#sendDaily" data-toggle="tooltip" data-placement="top" title="Send Daily"><i class="fas fa-paper-plane"></i></button>
                                                </a>
                                            </div>
                                            <div class="input-group-append ml-1">
                                                <a href="#">
                                                    <button class="btn btn-primary" data-toggle="modal"
                                                        data-target="#sendDailyBulk" data-toggle="tooltip" data-placement="top" title="Send Daily Bulk"><i class="fas fa-mail-bulk"></i></button>
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
                                            @if (auth()->user()->role_id == 1)
                                            <th>Dept</th>
                                            <th>Divisi</th>
                                            @else
                                            <th>Action</th>
                                            <th>User</th>
                                            @endif
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Task</th>
                                            <!-- <th>Category</th>
                                            <th>Status</th> -->
                                            <!-- <th>Type</th>
                                            <th>Plan Result</th>
                                            <th>Actual Result</th> -->
                                            <th>Status</th>
                                            <th>Task Plan</th>
                                            <th>Tagged By</th>
                                            <th>Added By</th>
                                            @if (auth()->user()->role_id == 1)
                                                <th>Status</th>
                                            @endif
                                            <!-- <th>On-Time Point</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dailys as $daily)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>

                                                @if (auth()->user()->role_id == 1)
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
                                                            <input type="hidden" name="page" value="teams">
                                                            <input type="hidden" name="tasktype"
                                                                value="{{ app('request')->input('tasktype') }}">
                                                            <button type="submit" class="btn far fa-check-circle"
                                                            style="color: {{ $daily->status ? 'green' : 'grey' }};"></button>
                                                        </form>
                                                        <form action="/daily/edit/{{ $daily->id }}" method="GET">
                                                            @csrf
                                                            <button type="submit" class="btn" style="color: rgb(239, 239, 54)"><i
                                                                    class="fas fa-edit"
                                                                    ></i></button>
                                                            <input type="hidden" name="page" value="teams">
                                                        </form>
                                                        <form action="/daily/delete" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $daily->id }}">
                                                            <input type="hidden" name="page" value="teams">
                                                            <button type="submit" class="btn"
                                                            style="color: rgb(204, 26, 26);"><i
                                                            class="fas fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                    @endif
                                                <td>{{ $daily->user->nama_lengkap }}</td>
                                                <td>{{ date('d M Y', $daily->date / 1000) }}</td>
                                                <td>{{ $daily->time ?? '-' }}</td>
                                                <td>{{ $daily->task }}</td>
                                                <!-- <td>{{ $daily->taskcategory->task_category ?? '' }}</td>
                                                <td class="
                                                    @if ($daily->taskstatus != null)
                                                        @if ($daily->taskstatus->task_status == 'PROGRESS')
                                                            {{"text-warning"}}
                                                        @elseif ($daily->taskstatus->task_status == 'DONE')
                                                            {{"text-success"}}
                                                        @elseif ($daily->taskstatus->task_status == 'PLANNED')
                                                            {{"text-info"}}
                                                        @else
                                                            {{"text-danger"}}
                                                        @endif
                                                    @endif
                                                "
                                                >{{ $daily->taskstatus->task_status ?? '' }}</td> -->
                                                <!-- <td>{{ $daily->tipe }}</td>
                                                <td>{{ $daily->value_plan }}</td>
                                                <td>{{ $daily->value_actual }}</td> -->
                                                <!-- KALAU JADI PAKE DAILY RESULT GANTI $daily->value -->
                                                <td class="
                                                    {{ $daily->status ? 'text-success' : 'text-warning', }}
                                                ">{{ $daily->status ? 'CLOSED' : 'OPEN', }}</td>
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
                                                <!-- <td>{{ $daily->ontime }}</td> -->
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
                            {{ $dailys->links() }}
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
                                        <form action="/teams/daily" class="d-inline-flex">
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


    <!-- Modal Import Daily -->
    <form action={{ auth()->user()->role->name != 'STAFF' ? '/admin/daily/import' : '/daily/import' }} method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="sendDailyBulk" tabindex="-1" aria-labelledby="sendDailyBulkLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sendDailyBulkLabel">Send Daily</h5>
                    </div>
                    <div class="modal-body">
                        @if (auth()->user()->role->name != 'STAFF')
                            <div class="col-12 mt-3">
                                <label for="userid" class="form-label">Nama</label>
                                <br>
                                <select class="custom-select form-control select2" id="userid" name="userid[]" multiple>
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
                            <input type="hidden" name="page" value="teams">
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
    <!-- <form action="/admin/daily/export" method="POST" enctype="multipart/form-data">
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
    </form> -->

    <!-- Modal Add Task -->
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

    <!-- Modal Send Task -->
    <form action="/teams/sendDaily" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="sendDaily" tabindex="-1" aria-labelledby="sendDailytLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sendDailytLabel">Send Daily</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12 ml-4">
                            <input type="checkbox" class="form-check-input" id="extraTaskDaily" name="isplan">
                            <label class="form-check-label" for="extraTaskDaily">Extra Task</label>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">User</label>
                                <br>
                                <select class="custom-select col-lg-12 select2" name="user_id[]" multiple>
                                    <option value="">-- Choose --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="mb-3 col-lg-12" id="sendDailyTime">
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
                        <button type="submit" class="btn btn-primary">+ Send</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
