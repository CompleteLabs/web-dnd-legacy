@extends('layout.main_tamplate')

@section('content')
<section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-sucess alert-dismissible fade show" role="alert">
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
                            <h3 class="card-title"><strong>Edit KPI</strong> &raquo; {{ $kpi->user->nama_lengkap }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0" style="height: 750px;">
                            <div class="col-md-12 mt-3">
                                <div class="" id="formaddkpi" >
                                    <form action="/kpi/{{ $kpi->id }}/update" method="POST" enctype="multipart/form-data">
                                    {{csrf_field()}}
                                    <div class="mb-3 col-lg-12">
                                        <!-- <label for="kpi_type_id" class="form-label">KPI Type</label>
                                        <select class="form-control" id="kpi_type_id" name="kpi_type_id" required onchange="toggleOption()">
                                            <option selected disabled>-- Choose Type --</option>
                                            @foreach ($kpitypes as $kpit)
                                                <option value="{{ $kpit->id }}"
                                                    {{ $kpit->id === $kpi->kpi_type->id ? 'selected' : '' }}>
                                                    {{ $kpit->name }}</option>
                                            @endforeach
                                        </select> -->
                                        <input type="hidden" name="kpi_type_id" value="{{ $kpi->kpi_type_id }}">
                                    </div>
                                    <div class="mb-3 col-lg-12">
                                        <label for="kpi_category_id" class="form-label">KPI Category</label>
                                        <select class="form-control" id="kpi_category_id" name="kpi_category_id" required onchange="toggleOption()">
                                            <option selected disabled>-- Choose Category --</option>
                                            @foreach ($kpicategories as $kpic)
                                                <option value="{{ $kpic->id }}"
                                                    {{ $kpic->id === $kpi->kpi_category->id ? 'selected' : '' }}>
                                                    {{ $kpic->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-lg-12">
                                        <label for="position_id" class="form-label">Job Position</label>
                                        <select class="form-control" id="position_id" name="position_id" required disabled>
                                            <option selected disabled>-- Choose Job Position --</option>
                                            @foreach ($positions as $post)
                                                <option value="{{ $post->id }}"
                                                    {{ $post->id === $kpi->user->position->id ? 'selected' : '' }}>
                                                    {{ $post->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-lg-12">
                                        <label for="date" class="form-label">Month</label>
                                        <input data-format="mm/yyyy" type="text" class="form-control" value="{{ Carbon\Carbon::parse($kpi->date)->format('m/Y') }}" id="monthpicker" name="date" required>
                                    </div>
                                    <div class="mb-3 col-lg-12">
                                        <label for="percentage" class="form-label">Percentage %</label>
                                        <input type="number" class="form-control" name="percentage" value="{{ $kpi->percentage }}" required>
                                    </div>
                                    <br>
                                    <div>
                                        <div class="form-group" >
                                            <div class="card-body table-responsive p-0" style="min-height: 300px;">
                                                <table class="table table-head-fixed text-nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:45%;">KPI Desc</th>
                                                            <th style="width:10%;">Start Date</th>
                                                            <th style="width:10%;">End Date</th>
                                                            <th style="width:10%;">Count Type</th>
                                                            <th style="width:10%;">Value Plan</th>
                                                            <th style="width:5%;">
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
                                    <br>
                                    <div class="col-lg-12 mb-3" style="text-align: right;">
                                        <button type="submit" class="btn btn-info" style="width: 100%; background-color: #917FB3; border-color: #917FB3;" onclick="return confirm('Are you sure want to update the data ?')">UPDATE</button>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
function toggleOption() {
    var kpi_type_id = $('#kpi_type_id').val();
    var kpi_category_id = $('#kpi_category_id').val();
    var position_id = $('#position_id').val();
}
</script>

<!-- Assuming the script is loaded in your edit.blade.php -->
<script>
$(document).ready(function() {
    // Get the kpiId from the URL
    const url = window.location.pathname;
    const parts = url.split('/'); // Split the path into parts
    const kpiId = parts[2]; // The kpiId should be the third part of the URL
    var baseUrl = window.location.protocol + "//" + window.location.host;

    console.log(kpiId);

    // Make an AJAX request to fetch the kpiDetail data for the specific KPI
    $.ajax({
        type: "get",
        url: `${baseUrl}/kpi/${kpiId}/kpiDetail`,
        success: function (data) {
            // Clear the table before appending new data
            $("#tablekpi").empty();

            // Append each kpiDetail to the table
            $.each(data, function (index, value) {
                var kpiDetailId = value.id;
                var description = value.kpi_description.description;
                var kpiDescription = value.kpi_description.description;
                var kpiDescriptionId = value.kpi_description.id;

                var startDate = value.start;
                var formattedStartDate = "";
                if (startDate !== null) {
                    var startDateParts = startDate.split(' ')[0].split('-');
                    var formattedStartDate = startDateParts[2] + '/' + startDateParts[1] + '/' + startDateParts[0];
                }

                var endDate = value.end;
                var formattedEndDate = "";
                if (endDate !== null) {
                    var endDateParts = endDate.split(' ')[0].split('-');
                    var formattedEndDate = endDateParts[2] + '/' + endDateParts[1] + '/' + endDateParts[0];
                }

                var countTypeSelect =
                '<select class="form-control" name="count_type[]" required>' +
                '<option value="NON" ' +
                (value.count_type === "NON" ? "selected" : "") +
                '>NON</option>' +
                '<option value="RESULT" ' +
                (value.count_type === "RESULT" ? "selected" : "") +
                '>RESULT</option>' +
                '</select>';

                var valuePlanInput =
                '<input type="number" placeholder="value_plan" class="form-control" name="value_plan[]" min="1" style="width: 110px;" value="' +
                value.value_plan +
                '">';

                if (formattedStartDate !== "") {
                    startDate =
                        '<input data-format="dd/mm/yyyy" type="text" class="form-control start-date" name="start[]" id="start_' + kpiDetailId + '" value="' + formattedStartDate + '">';
                } else {
                    startDate =
                        '<input data-format="dd/mm/yyyy" type="text" class="form-control start-date" name="start[]" id="start_' + kpiDetailId + '">';
                }

                if (formattedEndDate !== "") {
                    endDate =
                        '<input data-format="dd/mm/yyyy" type="text" class="form-control end-date" name="end[]" id="end_' + kpiDetailId + '" value="' + formattedEndDate + '">';
                } else {
                    endDate =
                        '<input data-format="dd/mm/yyyy" type="text" class="form-control end-date" name="end[]" id="end_' + kpiDetailId + '">';
                }

                $("#tablekpi").append(
                    '<tr class="kpi-row"><td><input type="text" placeholder="KPI description .." class="form-control" name="kpis[]" data-kpi-description-id="' + kpiDescriptionId + '" style="width: 100%;" value="' +
                    description +
                     '"><input type="hidden" name="kpi_description_id[]" value="' + kpiDescriptionId + '"></td><td>' +
                    startDate +
                    '</td><td>' +
                    endDate +
                    '</td><td>' +
                    countTypeSelect +
                    '</td><td>' +
                    valuePlanInput +
                    '</td><td><a href="#formreplacekpi" class="badge bg-danger btn_remove" data-kpi-description-id="' + kpiDescriptionId + '" id="kpi' +
                    index +
                    '"><span class="fas fa-minus"></span></a></td></tr>'
                );

                $("#start_" + kpiDetailId).datepicker({
                    dateFormat: "yy-mm-dd",
                    format: "dd/mm/yyyy",
                });

                $("#end_" + kpiDetailId).datepicker({
                    dateFormat: "yy-mm-dd",
                    format: "dd/mm/yyyy",
                });
            });
        },
        error: function (error) {
            console.log("Error fetching kpiDetail data:", error);
        },
    });

    $("#tablekpi").on("click", ".btn_remove", function (e) {
        e.preventDefault();
        var kpiDescriptionId = $(this).closest("tr").find('input[name="kpis[]"]').data('kpi-description-id');
        $(this).closest(".kpi-row").remove();
    });
});
</script>

@endsection
