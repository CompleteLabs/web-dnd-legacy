@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-white">
                            <!-- /.card-header -->
                            <div class="card-header">
                                <h3 class="card-title">EDIT &raquo; {{ $position->name }}</h3>
                            </div>
                            <div class="card-body">
                                <form action="/position/{{ $position->id }}/update" method="POST">
                                    @csrf
                                    <div class="mb-3 col-lg-6">
                                        <label for="name" class="form-label">Position</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ $position->name }}" required>
                                    </div>
                                    <div class="col-lg-6">
                                        <button type="submit" class="btn btn-info mt-3">Update</button>
                                    </div>
                                </form>
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
