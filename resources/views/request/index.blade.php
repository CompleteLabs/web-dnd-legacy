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
                                    <h3 class="card-title"><strong>Change Task</strong></h3>
                                </div>
                                <div class="card-tools d-flex">
                                    <div class="input-group input-group-sm">
                                        <a href="/request/create"><button
                                                class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Add Request"><i class="fas fa-plus"></i></button>
                                        </a>
                                    </div>
                                </div>
                                {{-- <div class="card-tools">
                                    <div class="input-group input-group-sm" style="width: 150px;">
                                        <form action="/user" class="d-inline-flex">
                                            <input type="text" name="search" class="form-control float-right"
                                                placeholder="Cari">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                        </form>
                                    </div>
                                </div> --}}
                            {{-- </div> --}}
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
                                        <th>Request Type</th>
                                        <th>Date Request</th>
                                        <th>Status</th>
                                        <th>Task Existing</th>
                                        <th>Task Replace</th>
                                        <th>Approval Line</th>
                                        <th>Action By</th>
                                        <th>Action At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requests as $rs)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $rs->jenistodo }}</td>
                                            <td>{{ date('d M Y', $rs->created_at / 1000) }}</td>
                                            <td>{{ $rs->status }}</td>
                                            <td><a target="_blank" href="/req/exist?id={{ $rs->todo_request }}&jenistodo={{ $rs->jenistodo }}">Lihat Task</a></td>
                                            <td><a target="_blank" href="/req/replace?id={{ $rs->todo_replace }}&jenistodo={{ $rs->jenistodo }}">Lihat Task</a></td>
                                            <td>{{ $rs->approveId->nama_lengkap }}</td>
                                            <td>{{ $rs->approved_by ? $rs->approvedBy->nama_lengkap : '-' }}</td>
                                            <td>{{ $rs->approved_at ? date('d M Y', $rs->approved_at / 1000) : '-' }}
                                            </td>
                                            <td>
                                                @if ($rs->status == 'PENDING')
                                                    <a href="/request/cancel/{{ $rs->id }}" class="badge bg-danger"
                                                        onclick="return confirm('Apalah anda yakin membatalkan request ini ?')"><span><i
                                                                class="far fa-times-circle"></i></span></a>
                                                @else
                                                    -
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
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- /.content -->
@endsection
