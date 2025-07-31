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
                            <h3 class="card-title"><strong>KPI</strong></h3>
                            <div class="card-tools d-flex align-items-center">
                                <div class="input-group input-group-sm mr-3" style="width: 430px;">
                                    <form action="kpi" class="d-inline-flex">
                                        <select class="custom-select col-lg-12 mx-2" name="position_id" id="position_id" style="width: 300px;">
                                            <option value="">--Choose Position--</option>
                                            @foreach ($positions as $post)
                                                <option value="{{ $post->id }}">{{ $post->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <!-- KPI GABISA IMPORT DONG -->
                                <!-- <div class="input-group input-group-sm mr-3" style="width: 30px;">
                                    <a href="" data-toggle="modal" data-target="#importKpi" data-toggle="tooltip" data-placement="top" title="Upload KPI" class="btn btn-tool btn-sm">
                                        <i class="fa fa-upload"></i>
                                    </a>
                                </div> -->
                                <div class="input-group input-group-sm mr-3 ml-3" style="width: 30px;">
                                    <a href="" data-toggle="modal" data-target="#copyKpi" data-toggle="tooltip" data-placement="top" title="Copy KPI" class="btn btn-tool btn-sm">
                                        <i class="fa fa-copy"></i>
                                    </a>
                                </div>
                                <div class="input-group input-group-sm mr-3" style="width: 30px;">
                                    <a href="" data-toggle="modal" data-target="#exportKpi" data-toggle="tooltip" data-placement="top" title="Export KPI" class="btn btn-tool btn-sm">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                                <div class="input-group input-group-sm" style="width: 30px;">
                                    <a href="/kpi/create" data-toggle="tooltip" data-placement="top" title="Add KPI" class="btn btn-tool btn-sm">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0" style="height: 500px;">
                            <table class="table table-striped table-hover table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Position</th>
                                        <th>User</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Percentage</th>
                                        <th style="text-align: right;">More</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kpis as $kpi)
                                        <tr onclick="window.location='/kpi/{{ $kpi->id }}/show';" style="cursor: pointer;">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $kpi->user->position->name ?? '-' }}</td>
                                            <td>{{ $kpi->user->nama_lengkap ?? '-' }}</td>
                                            <td>{{ $kpi->kpi_category->name ?? '-' }}</td>
                                            <td>{{ $kpi->kpi_type->name }}</td>
                                            <td>
                                                @if ($kpi->kpi_type->name === 'DAILY')
                                                    {{ Carbon\Carbon::parse($kpi->date)->format('d M Y') }}
                                                @elseif ($kpi->kpi_type->name === 'WEEKLY')
                                                    {{-- Format date as yearly week format (e.g., 2023-W26) --}}
                                                    {{ Carbon\Carbon::parse($kpi->date)->format('\WW Y') }}
                                                @elseif ($kpi->kpi_type->name === 'MONTHLY')
                                                    {{ Carbon\Carbon::parse($kpi->date)->format('F Y') }}
                                                @else
                                                    {{-- Default date format --}}
                                                    {{ Carbon\Carbon::parse($kpi->date)->format('d M Y') }}
                                                @endif
                                            </td>
                                            <td>{{ $kpi->percentage }}%</td>
                                            <td style="text-align: right;">
                                                <!-- <a href="/kpi/{{ $kpi->id }}/edit" style="color: orange;">
                                                    <span><i class="fas fa-edit"></i></span>
                                                </a> -->
                                                {{-- @if (auth()->user()->role_id == 1) --}}
                                                    <a href="/kpi/{{ $kpi->id }}/delete" style="color: red;" onclick="return confirm('Sure to delete data ?')">
                                                        <span><i class="fas fa-trash"></i></span>
                                                    </a>
                                                {{-- @endif --}}
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
        @if (count($kpis) == 100)
            <div class="d-flex justify-content-center">
                {{ $kpis->links() }}
            </div>
        @endif
    </section>
    <!-- /.content -->
    <!-- /.content-wrapper -->

    <!-- Modal Copy -->
    <div class="modal fade" id="copyKpi" tabindex="-1" role="dialog" aria-labelledby="copyKpiLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {{-- <form id="copyForm" method="POST" action="/kpi/copy" enctype="multipart/form-data"> --}}
                <form method="POST" action="/kpi/copy" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="copyKpiLabel">Copy KPI</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12">
                            <label for="copy_position_id" class="form-label">Job Position</label>
                            <select class="form-control" id="copy_position_id" name="copy_position_id" required style="width: 100%; overflow-x: auto;" onchange="toggleOptions()">
                                <option selected disabled>-- Choose Job Position --</option>
                                @foreach ($positionsToCopy as $post)
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
                            <label for="fromDate" class="form-label">From Month</label>
                            <input type="text" data-format="mm/yyyy" class="form-control" id="monthpicker" name="fromDate" required>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="toDate" class="form-label">To Month</label>
                            <input type="text" data-format="mm/yyyy" class="form-control" id="toMonthpicker" name="toDate" required>
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info">Copy</button>
                        {{-- <button type="submit" class="btn btn-info" id="copyBtn">
                            <span id="copyBtnText">Copy</span>

                            <div class="spinner-border spinner-border-sm" role="status" id="copyBtnSpinner" style="display: none;">
                                <span class="sr-only">Loading...</span>
                            </div>

                            <span id="copyBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Upload -->
    <div class="modal fade" id="importKpi" tabindex="-1" role="dialog" aria-labelledby="importKpiLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="/kpi/import" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importKpiLabel">Import KPI</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12">
                            <label for="kpi_type_id" class="form-label">KPI Type</label>
                            <select class="form-control" id="kpi_type_id" name="kpi_type_id" required onchange="toggleOption()">
                                <option selected disabled>-- Choose Type --</option>
                                @foreach ($kpitypes as $kpit)
                                    <option value="{{ $kpit->id }}">
                                        {{ $kpit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="kpi_category_id" class="form-label">KPI Category</label>
                            <select class="form-control" id="kpi_category_id" name="kpi_category_id" required onchange="toggleOption()">
                                <option selected disabled>-- Choose Category --</option>
                                @foreach ($kpicategories as $kpic)
                                    <option value="{{ $kpic->id }}">
                                        {{ $kpic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="position_id" class="form-label">Job Position</label>
                            <select class="form-control" id="position_id" name="position_id" required onchange="toggleOption()">
                                <option selected disabled>-- Choose Job Position --</option>
                                @foreach ($positions as $post)
                                    <option value="{{ $post->id }}">
                                        {{ $post->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="percentage" class="form-label">Percentage %</label>
                            <input type="percentage" class="form-control" name="percentage" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="formFile" class="form-label">Pilih File</label>
                            <input class="form-control" type="file" id="formFile" name="file">
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Export -->
    <div class="modal fade" id="exportKpi" tabindex="-1" role="dialog" aria-labelledby="exportKpiLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="/kpi/export" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportKpiLabel">Export KPI</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12">
                            <label for="divisi_id" class="form-label">Divisi</label>
                            <select class="custom-select col-lg-12" name="divisi_id" id="divisi_id" required>
                                @foreach ($divisis as $divisi)
                                    <option value="{{ $divisi->id }}">{{ $divisi->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="month" class="form-label">Month</label>
                            <input type="month" class="form-control" id="month" name="month" required>
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
    document.getElementById('copyForm').addEventListener('submit', function (event) {
        event.preventDefault();

        // Get references to button elements
        var copyBtn = document.getElementById('copyBtn');
        var copyBtnText = document.getElementById('copyBtnText');
        var copyBtnSpinner = document.getElementById('copyBtnSpinner');

        // Disable the button and show the spinner
        copyBtnText.style.display = 'none';
        copyBtnSpinner.style.display = 'inline-block';
        copyBtn.disabled = true;

        // Perform the AJAX request to copy the KPIs
        axios.post('/kpi/copy', {
            fromDate: document.getElementById('monthpicker').value,
            toDate: document.getElementById('toMonthpicker').value,
            position_id: document.getElementById('copy_position_id').value,
        })
        .then(function (response) {
            // Re-enable the button and hide the spinner
            copyBtnText.style.display = 'inline-block';
            copyBtnSpinner.style.display = 'none';
            copyBtn.disabled = false;

            // Redirect to the kpi page
            window.location.href = '/kpi';

            // Show success message if needed
            alert('Copying success!');
        })
        .catch(function (error) {
            // Handle errors, show error message if needed
            console.error(error);

            // Re-enable the button and hide the spinner on error
            copyBtnText.style.display = 'inline-block';
            copyBtnSpinner.style.display = 'none';
            copyBtn.disabled = false;

            // Show an error message
            alert('Copying failed. Please try again.');
        });
    });
</script>
@endsection

