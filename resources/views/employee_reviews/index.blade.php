@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <!-- Konten Utama -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Notifikasi Sukses -->
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                        <!-- Notifikasi Error -->
                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif

                        @if (session('importMessage'))
                            <div class="alert alert-info">
                                {{ session('importMessage') }}
                            </div>
                        @endif

                        @if (session('skippedDetails') && count(session('skippedDetails')) > 0)
                            <div class="alert alert-warning">
                                <p>Data berikut dilewati karena sudah ada atau tidak valid:</p>
                                <ul>
                                    @foreach (session('skippedDetails') as $skipped)
                                        <li>{{ $skipped['nama_lengkap'] }} - Periode: {{ $skipped['periode'] }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card">
                            <div class="card-header">
                                <div class="row d-inline-flex">
                                    <h3 class="card-title mt-2"><strong>{{ $title }}</strong></h3>
                                </div>
                                <div class="card-tools">
                                    <a href="{{ route('employee_reviews.create') }}" class="btn btn-success mr-2"
                                        data-toggle="tooltip" data-placement="top" title="Tambah Review">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                    <button class="btn btn-success mr-1" data-toggle="modal" data-target="#importModal"
                                        data-toggle="tooltip" data-placement="top" title="Import User">
                                        <i class="fa fa-upload" style="color: white"></i>
                                    </button>
                                    <a href="{{ url('employee_reviews/download') }}" class="btn btn-warning"
                                        data-toggle="tooltip" data-placement="top" title="Download Template">
                                        <i class="fas fa-file-alt" style="color: white"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Tabel Review Karyawan -->
                            <div class="card-body table-responsive p-0" style="height: 500px;">
                                <table class="table table-hover table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Nama Lengkap</th>
                                            <th>Periode</th>
                                            <th>Responsivitas</th>
                                            <th>Pemecahan Masalah</th>
                                            <th>Kesediaan Membantu</th>
                                            <th>Inisiatif</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employeeReviews as $review)
                                            <tr>
                                                <td>{{ $review->user->nama_lengkap ?? "-" }}</td>
                                                <td>{{ $review->periode }}</td>
                                                <td>{{ $review->responsiveness }}</td>
                                                <td>{{ $review->problem_solver }}</td>
                                                <td>{{ $review->helpfulness }}</td>
                                                <td>{{ $review->initiative }}</td>
                                                <td>
                                                    <!-- Tombol Lihat -->
                                                    <a href="{{ route('employee_reviews.show', $review->id) }}"
                                                        class="btn btn-info">Lihat</a>
                                                    <!-- Tombol Edit -->
                                                    <a href="{{ route('employee_reviews.edit', $review->id) }}"
                                                        class="btn btn-warning">Edit</a>
                                                    <!-- Tombol Hapus -->
                                                    <form action="{{ route('employee_reviews.destroy', $review->id) }}"
                                                        method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                                    </form>
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
    </section>
    <!-- Modal Import Data -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data {{ $title }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('employee_reviews/import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Upload File Excel:</label>
                            <input type="file" class="form-control" name="file" required>
                        </div>
                        <button type="submit" class="btn btn-success">Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- @extends('layouts.pages.dashboard')

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">{{ $title }}</h2>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="text-end gap-2">
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadFile">Import</button>
                        <a href="{{ route('employee_reviews.create') }}" class="btn btn-outline-secondary">Tambah</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card table-card">
                <div class="card-body pt-4">
                    <div class="table-responsive">
                        <table class="table table-hover" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>Nama Lengkap</th>
                                    <th>Periode</th>
                                    <th>Responsivitas</th>
                                    <th>Pemecahan Masalah</th>
                                    <th>Kesediaan Membantu</th>
                                    <th>Inisiatif</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employeeReviews as $review)
                                    <tr>
                                        <td>{{ $review->user->nama_lengkap }}</td>
                                        <td>{{ $review->periode }}</td>
                                        <td>{{ $review->responsiveness }}</td>
                                        <td>{{ $review->problem_solver }}</td>
                                        <td>{{ $review->helpfulness }}</td>
                                        <td>{{ $review->initiative }}</td>
                                        <td>
                                            <!-- Tombol Lihat -->
                                            <a href="{{ route('employee_reviews.show', $review->id) }}"
                                                class="btn btn-info">Lihat</a>
                                            <!-- Tombol Edit -->
                                            <a href="{{ route('employee_reviews.edit', $review->id) }}"
                                                class="btn btn-warning">Edit</a>
                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('employee_reviews.destroy', $review->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadFile" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="uploadFileLabel">Upload Files</h1>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="pc-uppy-3">
                        <div class="for-DragDrop"></div>
                        <div class="for-ProgressBar"></div>
                        <div class="uploaded-files mt-3">
                            <h5>Uploaded files:</h5>
                            <ol></ol>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" data-bs-dismiss="modal">Add Files</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="{{ asset('assets/js/plugins/simple-datatables.js') }}"></script>
    <script>
        const dataTable = new simpleDatatables.DataTable('#pc-dt-simple', {
            sortable: false,
            perPage: 10
        });
    </script>

    <script src="{{ asset('assets/js/plugins/uppy.min.js') }}"></script>
    <script>
        const Tus = Uppy.Tus;
        const DragDrop = Uppy.DragDrop;
        const ProgressBar = Uppy.ProgressBar;

        const onUploadSuccess = (elForUploadedFiles) => (file, response) => {
            const url = response.uploadURL;
            const fileName = file.name;

            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = url;
            a.target = '_blank';
            a.appendChild(document.createTextNode(fileName));
            li.appendChild(a);

            document.querySelector(elForUploadedFiles).appendChild(li);
        };
        (function() {
            const pc_uppy_3 = new Uppy.Core({
                debug: true,
                autoProceed: true
            });
            pc_uppy_3
                .use(DragDrop, {
                    target: '.pc-uppy-3 .for-DragDrop'
                })
                .use(Tus, {
                    endpoint: 'https://tusd.tusdemo.net/files/'
                })
                .use(ProgressBar, {
                    target: '.pc-uppy-3 .for-ProgressBar',
                    hideAfterFinish: false
                })
                .on('upload-success', onUploadSuccess('.pc-uppy-3 .uploaded-files ol'));
        })();
        const offcanvasFileDesc = new bootstrap.Offcanvas('#offcanvasFileDesc');
        var FileDescAction = document.querySelectorAll('.file-card .form-check-label, .file-card td:nth-child(2)');
        for (var i = 0; i < FileDescAction.length; i++) {
            FileDescAction[i].addEventListener('click', function(event) {
                var targetElement = event.target;
                if (targetElement.tagName == 'LABEL') {
                    // if (targetElement.parentNode.children[0].checked == true) {
                    offcanvasFileDesc.show();
                    // }
                } else {
                    offcanvasFileDesc.show();
                }
            });
        }
    </script>
@endsection --}}
