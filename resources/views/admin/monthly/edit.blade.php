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
                                <h3 class="card-title"><strong>EDIT &raquo;</strong> {{ $monthly->task }}</h3>
                            </div>
                            @if ($message = Session::get('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @endif
                            <div class="card-body">
                                <form action="/monthly/update/{{ $monthly->id }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $monthly->id }}">
                                    <div class="row">
                                        <div class="mb-3 col-lg-3">
                                            <label for="task" class="form-label">Task</label>
                                            <input type="text" class="form-control" id="task" name="task"
                                                value="{{ $monthly->task }}" autocomplete="off" required>
                                        </div>
                                        <div class="mb-3 col-lg-3">
                                            <label for="tipe" class="form-label">Tipe</label>
                                            <input type="text" class="form-control" id="tipe" name="tipe"
                                                value="{{ $monthly->tipe }}" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-lg-2">
                                            <label for="date" class="form-label">Month</label>
                                            <input type="month" class="form-control" id="date" name="date"
                                                value={{ date('Y-m',$monthly->date / 1000) }} min="1" required>
                                        </div>
                                        @if ($monthly->tipe == 'RESULT')
                                            <div class="mb-3 col-lg-2">
                                                <label for="valueplan" class="form-label">Value Plan</label>
                                                <input type="number" class="form-control" id="valueplan" name="value_plan"
                                                    value="{{ $monthly->value_plan }}" required>
                                                <span class="ml-2"
                                                    id="nominal">Value : {{ number_format($monthly->value_plan, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-success mt-3">Update</button>
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
