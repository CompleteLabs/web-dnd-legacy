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
                                <h3 class="card-title"><strong>REPORT ACTUAL &raquo;</strong> {{ $weekly->task }}</h3>
                            </div>
                            @if ($message = Session::get('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @endif
                            <div class="card-body">
                                <form action="/weekly/change" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $weekly->id }}">
                                    <input type="hidden" name="page" value="teams">
                                    <div class="row">
                                        <div class="mb-3 col-lg-3">
                                            <label for="task" class="form-label">Task</label>
                                            <input type="text" class="form-control" id="task" name="task"
                                                value="{{ $weekly->task }}" disabled>
                                        </div>
                                        <div class="mb-3 col-lg-3">
                                            <label for="tipe" class="form-label">Tipe</label>
                                            <input type="text" class="form-control" id="tipe" name="tipe"
                                                value="{{ $weekly->tipe }}" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-lg-2">
                                            <label for="week" class="form-label">Week</label>
                                            <input type="number" class="form-control" id="week" name="week"
                                                value="{{ $weekly->week }}" min="1" max="52" disabled>
                                        </div>
                                        <div class="mb-3 col-lg-2">
                                            <label for="year" class="form-label">Year</label>
                                            <input type="number" class="form-control" id="year" name="year"
                                                value="{{ $weekly->year }}" min="2022" disabled>
                                        </div>
                                        <div class="mb-3 col-lg-2">
                                            <label for="valueplan" class="form-label">Value Plan</label>
                                            <input type="text" class="form-control" id="valueplan" name="value_plan"
                                                value="{{ number_format($weekly->value_plan, 0, ',', '.') }}" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-lg-2">
                                            <label for="valueactual" class="form-label">Value Actual</label>
                                            <input type="number" class="form-control" id="valueactual" name="value_actual"
                                                value="0" required>
                                            <span id="nominal"></span>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success mt-3">Submit</button>
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
