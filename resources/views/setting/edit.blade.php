@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-header">
                                @if ($title == 'Role')
                                    <h3 class="card-title"><strong>EDIT &raquo;</strong> {{ $role->name }}</h3>
                                @else
                                    <h3 class="card-title"><strong>EDIT &raquo;</strong> {{ $divisi->name }}</h3>
                                @endif
                            </div>
                            <div class="card-body">
                                @if ($title == 'Role')
                                    <form action="/setting/role/{{ $role->id }}" method="POST">
                                        @csrf
                                        <div class="mb-3 col-lg-3">
                                            <label for="name" class="form-label">Nama Role</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ $role->name }}" required>
                                        </div>
                                        <button type="submit" class="btn btn-success mt-3">Update</button>
                                    </form>
                                @else
                                    <form action="/setting/divisi/{{ $divisi->id }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="mb-3 col-lg-3">
                                                <label for="name" class="form-label">Nama Divisi</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    value="{{ $divisi->name }}" required>
                                            </div>
                                             <div class="mb-3 col-lg-3">
                                                <label for="area" class="form-label">Nama Area</label>
                                                <input type="text" class="form-control" id="area" name="area"
                                                    value="{{ $divisi->area->name }}" readonly>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-success mt-3">Update</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </section>
    </section>
    <!-- /.content -->
@endsection
