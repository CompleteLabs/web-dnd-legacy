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
                        <div class="card-header">
                            <h3><strong>Add KPI</strong></h3>
                        </div>
                        <div class="card-body">
                            <p>Untuk KPI yang memiliki <strong>tenggat waktu</strong>, dapat mengisikan Start Date & End Date. Jika tidak memiliki tenggat waktu, Start Date dan End Date dapat dikosongkan.</p>
                            <p>Untuk KPI yang memiliki <strong>target angka/nominal</strong>, dapat memilih Count Type = RESULT dan isikan angka target / Value Plannya. Jika tidak memiliki angka target, dapat memilih Count Type = NON dan kosongkan Value Plannya.</p>
                        </div>
                    </div>
                    <form action="/kpi" method="POST" enctype="multipart/form-data">
                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0" style="height: 750px;">
                                <div class="col-md-12 mt-3">
                                    <div class="" id="formaddkpi" >
                                        {{csrf_field()}}
                                        <div class="mb-3 col-lg-12">
                                            <input type="hidden" name="kpi_type_id" value="3">
                                        </div>
                                        <div class="mb-3 col-lg-12">
                                            <label for="position_id" class="form-label">Job Position</label>
                                            <select class="form-control" id="position_id" name="position_id" required onchange="toggleOption()" style="width: 100%; overflow-x: auto;">
                                                <option selected disabled>-- Choose Job Position --</option>
                                                @foreach ($positions as $post)
                                                    <option value="{{ $post->id }}">
                                                        {{ $post->name }} -
                                                        @foreach ($post->user as $user)
                                                            {{ $user->nama_lengkap }}
                                                            @if (!$loop->last)
                                                                , <!-- Add a comma if it's not the last user -->
                                                            @endif
                                                        @endforeach
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3 col-lg-12">
                                            <label for="date" class="form-label">Month</label>
                                            <input type="text" data-format="mm/yyyy" class="form-control" id="monthpicker" name="date" required>
                                        </div>
                                        <div class="mb-3 col-lg-12">
                                            <label for="kpi_category_id" class="form-label">KPI Category</label>
                                            <input type="text" class="form-control" placeholder="MAIN JOB" readonly>
                                            <input type="hidden" name="kpi_category_id" value="3">
                                        </div>
                                        <div class="mb-3 col-lg-12">
                                            <label for="percentage" class="form-label">Percentage %</label>
                                            <input type="number" class="form-control" name="percentageMain" required>
                                        </div>
                                        <br>
                                        <div>
                                            <div class="form-group" >
                                                <div class="card-body table-responsive p-0" style="min-height: 300px;">
                                                    <table class="table table-head-fixed text-nowrap">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:50%;">KPI Desc</th>
                                                                <th style="width:10%;">Start Date</th>
                                                                <th style="width:10%;">End Date</th>
                                                                <th style="width:10%;">Count Type</th>
                                                                <th style="width:10%;">Value Plan</th>
                                                                <th style="width:10%;">
                                                                    <a href="#addkpiMain" class="badge bg-success" id="addKpiMain">Add <span class="lnr lnr-plus-circle"></span></a>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tablekpiMain" class="tablekpi">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0" style="height: 750px;">
                                <div class="col-md-12 mt-3">
                                    <div class="" id="formaddkpi" >
                                        {{csrf_field()}}
                                        <div class="mb-3 col-lg-12">
                                            <input type="hidden" name="kpi_type_id" value="3">
                                        </div>
                                        <div class="mb-3 col-lg-12">
                                            <label for="kpi_category_id" class="form-label">KPI Category</label>
                                            <input type="text" class="form-control" name="kpi_category_id" placeholder="ADMINISTRATION" readonly>
                                            <input type="hidden" name="kpi_category_id" value="1">
                                        </div>
                                        <div class="mb-3 col-lg-12">
                                            <label for="percentage" class="form-label">Percentage %</label>
                                            <input type="number" class="form-control" name="percentageAdm" required>
                                        </div>
                                        <br>
                                        <div>
                                            <div class="form-group" >
                                                <div class="card-body table-responsive p-0" style="min-height: 300px;">
                                                    <table class="table table-head-fixed text-nowrap">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:50%;">KPI Desc</th>
                                                                <th style="width:10%;">Start Date</th>
                                                                <th style="width:10%;">End Date</th>
                                                                <th style="width:10%;">Count Type</th>
                                                                <th style="width:10%;">Value Plan</th>
                                                                <th style="width:10%;">
                                                                    <a href="#addkpi" class="badge bg-success" id="addKpi">Add <span class="lnr lnr-plus-circle"></span></a>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tablekpi" class="tablekpi">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0" style="height: 750px;">
                                <div class="col-md-12 mt-3">
                                    <div class="" id="formaddkpi" >
                                        {{csrf_field()}}
                                        <div class="mb-3 col-lg-12">
                                            <input type="hidden" name="kpi_type_id" value="3">
                                        </div>
                                        <div class="mb-3 col-lg-12">
                                            <label for="kpi_category_id" class="form-label">KPI Category</label>
                                            <input type="text" class="form-control" placeholder="REPORTING" readonly>
                                            <input type="hidden" name="kpi_category_id" value="2">
                                        </div>
                                        <div class="mb-3 col-lg-12">
                                            <label for="percentage" class="form-label">Percentage %</label>
                                            <input type="number" class="form-control" name="percentageRep" required>
                                        </div>
                                        <br>
                                        <div>
                                            <div class="form-group" >
                                                <div class="card-body table-responsive p-0" style="min-height: 300px;">
                                                    <table class="table table-head-fixed text-nowrap">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:50%;">KPI Desc</th>
                                                                <th style="width:10%;">Start Date</th>
                                                                <th style="width:10%;">End Date</th>
                                                                <th style="width:10%;">Count Type</th>
                                                                <th style="width:10%;">Value Plan</th>
                                                                <th style="width:10%;">
                                                                    <a href="#addkpiRep" class="badge bg-success" id="addKpiRep">Add <span class="lnr lnr-plus-circle"></span></a>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tablekpiRep" class="tablekpi">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="col-lg-12" style="text-align: right;">
                        <button type="submit" class="btn btn-md btn-info" style="width: 100%; background-color: #917FB3; border-color: #917FB3;" onclick="return confirm('Are you sure want to submit ?')">SUBMIT</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </section>
<script>
function toggleOption() {
    var kpi_type_id = $('#kpi_type_id').val();
    var kpi_category_id = $('#kpi_category_id').val();
    var position_id = $('#position_id').val();
}
</script>
@endsection
