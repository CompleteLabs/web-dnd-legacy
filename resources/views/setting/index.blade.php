@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row d-inline-flex">
                                @switch($title)
                                    @case('Role')
                                        <h3 class="card-title"><strong>Role Setting</strong></h3>
                                    @break

                                    @case('Divisi')
                                        <h3 class="card-title"><strong>Divisi Setting</strong></h3>
                                    @break

                                    @case('Task Category')
                                        <h3 class="card-title"><strong>Task Category Setting</strong></h3>
                                    @break

                                    @case('Task Status')
                                        <h3 class="card-title"><strong>Task Status Setting</strong></h3>
                                    @break

                                    @default
                                        <h3 class="card-title"><strong>Area Setting</strong></h3>
                                @endswitch
                            </div>
                            @switch($title)
                                @case('Role')
                                    <div class="card-tools d-flex">
                                        <div class="input-group input-group-sm" style="width: 40px;">
                                            <div class="input-group-append">
                                                <a href="#">
                                                    <button class="btn btn-success" data-toggle="modal" data-target="#addRole"
                                                    data-toggle="tooltip" data-placement="top" title="Add Role"><i class="fa fa-plus"></i></button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @break
                                @case('Divisi')
                                    <div class="card-tools d-flex">
                                        <div class="input-group input-group-sm" style="width: 40px;">
                                            <div class="input-group-append">
                                                <a href="#">
                                                    <button data-toggle="modal" data-target="#addDivisi"
                                                        class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Add Divisi"><i class="fa fa-plus"></i></button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @break
                                @case('Task Category')
                                    <div class="card-tools d-flex">
                                        <div class="input-group input-group-sm" style="width: 40px;">
                                            <div class="input-group-append">
                                                <a href="#">
                                                    <button data-toggle="modal" data-target="#addTaskCategory"
                                                        class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Add Task Category"><i class="fa fa-plus"></i></button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @break
                                @case('Task Status')
                                    <div class="card-tools d-flex">
                                        <div class="input-group input-group-sm" style="width: 40px;">
                                            <div class="input-group-append">
                                                <a href="#">
                                                    <button data-toggle="modal" data-target="#addTaskStatus"
                                                        class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Add Task Status"><i class="fa fa-plus"></i></button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @break
                                @default
                                    <div class="card-tools d-flex">
                                            <div class="input-group input-group-sm" style="width: 40px;">
                                                <div class="input-group-append">
                                                    <a href="#">
                                                        <button data-toggle="modal" data-target="#addArea"
                                                            class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Add Area"><i class="fa fa-plus"></i></button>
                                                    </a>
                                            </div>
                                        </div>
                                    </div>
                            @endswitch
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
                                        <th>Name</th>
                                        @if ($title == 'Divisi')
                                            <th>Area</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @switch($title)
                                        @case('Role')
                                            @foreach ($roles as $role)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $role->name }}</td>
                                                    {{-- <td>
                                                        <a href="/setting/role/{{ $role->id }}"
                                                            class="badge bg-warning"><span><i class="fas fa-edit"></i></span></a>
                                                    </td> --}}
                                                </tr>
                                            @endforeach
                                        @break

                                        @case('Divisi')
                                            @foreach ($divisis as $divisi)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $divisi->name }}</td>
                                                    <td>{{ $divisi->area->name }}</td>
                                                    {{-- <td>
                                                        <a href="/setting/divisi/{{ $divisi->id }}"
                                                            class="badge bg-warning"><span><i class="fas fa-edit"></i></span></a>
                                                    </td> --}}
                                                </tr>
                                            @endforeach
                                        @break

                                        @case('Task Category')
                                            @foreach ($taskcategories as $tc)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $tc->task_category }}</td>
                                                    {{-- <td>
                                                        <a href="/setting/taskcategory/{{ $tc->id }}"
                                                            class="badge bg-warning"><span><i class="fas fa-edit"></i></span></a>
                                                    </td> --}}
                                                </tr>
                                            @endforeach
                                        @break

                                        @case('Task Status')
                                            @foreach ($taskstatus as $ts)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $ts->task_status }}</td>
                                                    {{-- <td>
                                                        <a href="/setting/taskstatus/{{ $ts->id }}"
                                                            class="badge bg-warning"><span><i class="fas fa-edit"></i></span></a>
                                                    </td> --}}
                                                </tr>
                                            @endforeach
                                        @break

                                        @default
                                            @foreach ($areas as $area)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $area->name }}</td>
                                                    {{-- <td>
                                                        <a href="/setting/area/{{ $area->id }}"
                                                            class="badge bg-warning"><span><i class="fas fa-edit"></i></span></a>
                                                    </td> --}}
                                                </tr>
                                            @endforeach
                                    @endswitch
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
    <!-- /.content -->
    <!-- /.content-wrapper -->

    @switch($title)
        @case('Role')
            <!-- Modal Add Role-->
            <div class="modal fade" id="addRole" tabindex="-1" role="dialog" aria-labelledby="addRoleLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST" action="/setting/role">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addRoleLabel">Add Role</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3 col-lg-12">
                                    <label for="name" class="form-label">Role Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @break

        @case('Divisi')
            <!-- Modal Add Divisi-->
            <div class="modal fade" id="addDivisi" tabindex="-1" role="dialog" aria-labelledby="addDivisiLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST" action="/setting/divisi">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addDivisiLabel">Add Divisi</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3 col-lg-12">
                                    <label for="name" class="form-label">Divisi Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3 col-lg-12">
                                    <label for="area" class="form-label">Area</label>
                                    <select class="custom-select" id="area" name="area_id" required>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Add</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        @break

        @case('Task Category')
            <!-- Modal Task Category-->
            <div class="modal fade" id="addTaskCategory" tabindex="-1" role="dialog" aria-labelledby="addTaskCategoryLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST" action="/setting/taskcategory">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addTaskCategoryLabel">Task Category</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3 col-lg-12">
                                    <label for="task_category" class="form-label">Task Category</label>
                                    <input type="text" class="form-control" id="task_category" name="task_category" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @break

        @case('Task Status')
            <!-- Modal Task Status-->
            <div class="modal fade" id="addTaskStatus" tabindex="-1" role="dialog" aria-labelledby="addTaskStatusLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST" action="/setting/taskstatus">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addTaskStatusLabel">Task Status</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3 col-lg-12">
                                    <label for="task_status" class="form-label">Task Status</label>
                                    <input type="text" class="form-control" id="task_status" name="task_status" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @break

        @default
            <!-- Modal Add Area-->
            <div class="modal fade" id="addArea" tabindex="-1" role="dialog" aria-labelledby="addAreaLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST" action="/setting/area">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addAreaLabel">Add Area</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3 col-lg-12">
                                    <label for="name" class="form-label">Area Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Add</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
    @endswitch
@endsection
