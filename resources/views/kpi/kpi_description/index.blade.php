@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
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
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title"><strong>KPI Description</strong></h3>
                            <div class="card-tools">
                                <a data-toggle="modal" data-target="#addKPIDescription" class="btn btn-tool btn-sm">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0" style="height: 500px;">
                            <table class="table table-striped table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>KPI Category</th>
                                        <th>Description</th>
                                        <th style="text-align: right;">More</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kpiDescriptions as $kpid)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $kpid->kpi_category->name }}</td>
                                            <td>{{ $kpid->description }}</td>
                                            <td style="text-align: right;">
                                                <a href="/kpidescription/{{ $kpid->id }}/edit" style="color: orange;">
                                                    <span><i class="fas fa-edit"></i></span>
                                                </a>
                                                <a href="/kpidescription/{{ $kpid->id }}/delete" style="color: red;" onclick="return confirm('Sure to delete data ?')">
                                                    <span><i class="fas fa-trash"></i></span>
                                                </a>
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
        </div>
    </section>
    <!-- /.content -->
    <!-- /.content-wrapper -->
    <!-- Modal Add -->
    <div class="modal fade" id="addKPIDescription" tabindex="-1" role="dialog" aria-labelledby="addKPIDescriptionLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="/kpidescription">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addKPIDescriptionLabel">Add KPI Description</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12">
                            <label for="kpi_category_id" class="form-label">KPI Category</label>
                            <select class="form-control" id="kpi_category_id" name="kpi_category_id" required>
                                <option selected disabled>-- Choose Category --</option>
                                    @foreach ($kpiCategories as $kpiCategory)
                                        <option value="{{ $kpiCategory->id }}">
                                            {{ $kpiCategory->name }}</option>
                                    @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" name="description" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
