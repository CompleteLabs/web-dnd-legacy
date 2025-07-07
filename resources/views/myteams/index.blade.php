@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
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
                                <h3 class="card-title mt-2"><strong>{{ $title }}</strong></h3>
                                <div class="card-tools">
                                    <!-- Import Data Button -->
                                    <button class="btn btn-success mr-1" data-toggle="modal" data-target="#importModal">
                                        <i class="fas fa-upload"></i> Import Penilaian
                                    </button>
                                    <a href="{{ url('employee_reviews/download') }}" class="btn btn-warning"
                                        data-toggle="tooltip" data-placement="top" title="Download Template">
                                        <i class="fas fa-file-alt" style="color: white"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-0" style="height: 500px;">
                                @if ($employees->isNotEmpty())
                                    <table class="table table-hover table-head-fixed text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Nama Lengkap</th>
                                                <th>Posisi</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($employees as $employee)
                                                <tr>
                                                    <td>{{ $employee->nama_lengkap }}</td>
                                                    <td>{{ $employee->position->name ?? '-' }}</td>
                                                    <td>
                                                        <!-- Add Review Button -->
                                                        @if (!in_array($employee->id, $existingReviews))
                                                            <button class="btn btn-primary" data-toggle="modal"
                                                                data-target="#addReviewModal"
                                                                data-user="{{ $employee->id }}"
                                                                data-name="{{ $employee->nama_lengkap }}">
                                                                <i class="fas fa-plus"></i> Tambah Review
                                                            </button>
                                                        @else
                                                            <span class="badge badge-secondary">Review sudah ada periode {{ \Carbon\Carbon::now()->day <= 5 ? \Carbon\Carbon::now()->subMonth()->format('Y-m') : \Carbon\Carbon::now()->format('Y-m') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-center">Anda saat ini tidak memiliki anggota tim.</p>
                                @endif
                            </div>
                        </div>
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
                    <h5 class="modal-title" id="importModalLabel">Import Penilaian Tim</h5>
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

    <!-- Modal Add Review -->
    <div class="modal fade" id="addReviewModal" tabindex="-1" role="dialog" aria-labelledby="addReviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addReviewModalLabel">Penilaian untuk <span id="employeeName"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('employee_reviews.store') }}" method="POST">
                        @csrf
                        <input type="hidden" class="form-control" name="user_id" id="reviewUserId">

                        <!-- Periode Field -->
                        <div class="form-group">
                            <label for="periode">Periode (YYYY-MM)</label>
                            <input type="text" name="periode" class="form-control"
                            value="{{ \Carbon\Carbon::now()->day <= 5 ? \Carbon\Carbon::now()->subMonth()->format('Y-m') : \Carbon\Carbon::now()->format('Y-m') }}" readonly>
                            <small class="text-danger">*Jika tanggal hari ini sebelum tanggal 5, periode akan mengacu pada bulan sebelumnya.</small>
                        </div>

                        <!-- Responsiveness Slider -->
                        <div class="form-group">
                            <label for="responsiveness">Responsivitas: <span id="responsivenessValue">3</span></label>
                            <input type="range" name="responsiveness" class="custom-range" id="responsiveness"
                                min="0" max="5" value="3"
                                oninput="document.getElementById('responsivenessValue').innerText = this.value">
                        </div>

                        <!-- Problem Solver Slider -->
                        <div class="form-group">
                            <label for="problem_solver">Pemecahan Masalah: <span id="problemSolverValue">3</span></label>
                            <input type="range" name="problem_solver" class="custom-range" id="problem_solver"
                                min="0" max="5" value="3"
                                oninput="document.getElementById('problemSolverValue').innerText = this.value">
                        </div>

                        <!-- Helpfulness Slider -->
                        <div class="form-group">
                            <label for="helpfulness">Kesediaan Membantu: <span id="helpfulnessValue">3</span></label>
                            <input type="range" name="helpfulness" class="custom-range" id="helpfulness"
                                min="0" max="5" value="3"
                                oninput="document.getElementById('helpfulnessValue').innerText = this.value">
                        </div>

                        <!-- Initiative Slider -->
                        <div class="form-group">
                            <label for="initiative">Inisiatif: <span id="initiativeValue">3</span></label>
                            <input type="range" name="initiative" class="custom-range" id="initiative" min="0"
                                max="5" value="3"
                                oninput="document.getElementById('initiativeValue').innerText = this.value">
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        // Display employee data in the modal and set initial slider values
        $('#addReviewModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var userId = button.data('user'); // Extract user ID
            var userName = button.data('name'); // Extract user name

            var modal = $(this);
            modal.find('#reviewUserId').val(userId); // Set hidden input value for user_id
            modal.find('#employeeName').text(userName); // Set employee name in modal title

            // Initialize slider display values
            document.getElementById('responsivenessValue').innerText = document.getElementById('responsiveness')
                .value;
        });
    </script>
@endsection
