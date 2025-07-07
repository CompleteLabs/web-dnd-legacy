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
                                    <h3 class="card-title"><strong>User</strong></h3>
                                </div>
                                <div class="card-tools d-flex">
                                    <div class="input-group input-group-sm" style="width: 220px;">
                                        <form action="/user" class="d-inline-flex">
                                            <input type="text" name="search" class="form-control float-right"
                                                placeholder="Cari">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default ml-1">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="input-group input-group-sm sm mr-3" style="width: 230px;">
                                        <div class="input-group-append ml-5">
                                            <a href="/user/create"><button class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Add User"><i class="fas fa-plus"></i></button>
                                            </a>
                                        </div>
                                        <div class="input-group-append ml-1">
                                            <a href="#"><button class="btn btn-success" data-toggle="modal" data-target="#imporUser" data-toggle="tooltip" data-placement="top" title="Import User"><i class="fa fa-upload" style="color: white"></i></button>
                                            </a>
                                        </div>
                                        <div class="input-group-append ml-1">
                                            <a href="/user/template"><button class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Download Template"><i class="fas fa-file-alt" style="color: white"></i></button>
                                            </a>
                                        </div>
                                        <div class="input-group-append ml-1">
                                            <a href="/user/export"><button class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Export User"><i class="fa fa-download" style="color: white"></i></button>
                                            </a>
                                        </div>
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
                            <div class="card-body table-responsive p-0" style="height: 500px;">
                                <table class="table table-hover table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Lengkap</th>
                                            <th>User Name</th>
                                            <th>Job Position</th>
                                            <th>Role</th>
                                            <th>Area</th>
                                            <th>Divisi</th>
                                            <th>D</th>
                                            <th>DR</th>
                                            <th>WN</th>
                                            <th>WR</th>
                                            <th>MN</th>
                                            <th>MR</th>
                                            <th>Approval</th>
                                            <th>Status</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr onclick="window.location='/user/{{ $user->id }}';" style="cursor: pointer;">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $user->nama_lengkap }}</td>
                                                <td>{{ $user->username }}</td>
                                                <td>{{ $user->position->name ?? '-'}}</td>
                                                <td>{{ $user->role->name }}</td>
                                                <td>{{ $user->area->name }}</td>
                                                <td>{{ $user->divisi->name }}</td>
                                                <td>
                                                    @if ($user->d)
                                                        <i class="far fa-check-circle" style="color: green;"></i>
                                                    @else
                                                        <i class="far fa-times-circle" style="color: red;"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($user->dr)
                                                        <i class="far fa-check-circle" style="color: green;"></i>
                                                    @else
                                                        <i class="far fa-times-circle" style="color: red;"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($user->wn)
                                                        <i class="far fa-check-circle" style="color: green;"></i>
                                                    @else
                                                        <i class="far fa-times-circle" style="color: red;"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($user->wr)
                                                        <i class="far fa-check-circle" style="color: green;"></i>
                                                    @else
                                                        <i class="far fa-times-circle" style="color: red;"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($user->mn)
                                                        <i class="far fa-check-circle" style="color: green;"></i>
                                                    @else
                                                        <i class="far fa-times-circle" style="color: red;"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($user->mr)
                                                        <i class="far fa-check-circle" style="color: green;"></i>
                                                    @else
                                                        <i class="far fa-times-circle" style="color: red;"></i>
                                                    @endif
                                                </td>
                                                <td>{{ $user->approval->nama_lengkap ?? 'KOSONG' }}</td>
                                                {{-- <td>
                                                    @if ($user->deleted_at)
                                                        NONAKTIF
                                                    @else
                                                        AKTIF
                                                    @endif
                                                </td> --}}
                                                <td>
                                                    {{-- <a href="/user/{{ $user->id }}" class="badge bg-warning"><span><i
                                                                class="fas fa-edit"></i></span></a> --}}
                                                    @if ($user->deleted_at)
                                                        <a href="/user/active/{{ $user->id }}" class="badge bg-success"
                                                            onclick="return confirm('Mengaktifkan kembali user {{ $user->nama_lengkap }}?')"><span><i
                                                                    class="far fa-check-circle"></i></span></a>
                                                    @else
                                                        <a href="/user/delete/{{ $user->id }}" class="badge bg-danger"
                                                            onclick="return confirm('Apalah anda yakin menonaktifkan user {{ $user->nama_lengkap }}?')"><span><i
                                                                    class="far fa-times-circle"></i></span></a>
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
                    <div class="d-flex justify-content-center">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </section>
    </section>

    <!-- Modal -->
    <form action="/user/import" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="imporUser" tabindex="-1" aria-labelledby="imporUserLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imporUserLabel">Import User</h5>
                    </div>
                    <div class="modal-body">
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
    <!-- /.content -->
@endsection
