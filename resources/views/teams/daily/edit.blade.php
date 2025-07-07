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
                                <h3 class="card-title"><strong>EDIT &raquo;</strong> {{ $daily->task }}</h3>
                            </div>
                            @if ($message = Session::get('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                            <div class="card-body">
                                <form action="{{ '/teams/daily/edit/'.$daily->id }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $daily->id }}">
                                    <div class="row">
                                        <div class="mb-6 col-lg-6">
                                            <label for="task" class="form-label">Task</label>
                                            <input type="text" class="form-control" id="task" name="task"
                                                value="{{ $daily->task }}" disabled>
                                        </div>
                                        <div class="mb-3 col-lg-3">
                                            <label for="task_categories" class="form-label">Task Category</label>
                                            <select class="custom-select col-lg-12" name="task_category_id" id="task_category" required>
                                                <option value="" selected>-- Pilih --</option>
                                                @foreach ($task_categories as $tc)
                                                    <option value="{{ $tc->id }}"
                                                        @if ($daily->task_category_id == $tc->id) selected @endif>
                                                        {{ $tc->task_category }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3 col-lg-3">
                                            <label for="task_status" class="form-label">Task Status</label>
                                            <select class="custom-select col-lg-12" name="task_status_id" id="task_status" required>
                                                <option value="" selected>-- Pilih --</option>
                                                @foreach ($task_status as $ts)
                                                    <option value="{{ $ts->id }}"
                                                        @if ($daily->task_status_id == $ts->id) selected @endif>
                                                        {{ $ts->task_status }}</option>
                                                @endforeach
                                            </select>
                                        </div>
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
