@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- Notifikasi Sukses/Error -->
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
                                <h3 class="card-title">Edit Review Karyawan</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('employee_reviews.update', $employeeReview->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <!-- Dropdown for User ID -->
                                    <div class="form-group">
                                        <label for="user_id">Karyawan</label>
                                        <select name="user_id" id="user_id" class="form-control" required>
                                            <option value="" disabled>Pilih Karyawan</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ $employeeReview->user_id == $user->id ? 'selected' : '' }}>
                                                    {{ $user->nama_lengkap }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Periode Field -->
                                    <div class="form-group">
                                        <label for="periode">Periode (YYYY-MM)</label>
                                        <input type="text" class="form-control" name="periode"
                                            value="{{ $employeeReview->periode }}" readonly required>
                                    </div>

                                    <!-- Responsiveness Slider -->
                                    <div class="form-group">
                                        <label for="responsiveness">Responsivitas: <span id="responsivenessValue">{{ $employeeReview->responsiveness }}</span></label>
                                        <input type="range" name="responsiveness" class="custom-range" id="responsiveness"
                                            min="0" max="5" value="{{ $employeeReview->responsiveness }}"
                                            oninput="document.getElementById('responsivenessValue').innerText = this.value">
                                    </div>

                                    <!-- Problem Solver Slider -->
                                    <div class="form-group">
                                        <label for="problem_solver">Pemecahan Masalah: <span id="problemSolverValue">{{ $employeeReview->problem_solver }}</span></label>
                                        <input type="range" name="problem_solver" class="custom-range" id="problem_solver"
                                            min="0" max="5" value="{{ $employeeReview->problem_solver }}"
                                            oninput="document.getElementById('problemSolverValue').innerText = this.value">
                                    </div>

                                    <!-- Helpfulness Slider -->
                                    <div class="form-group">
                                        <label for="helpfulness">Kesediaan Membantu: <span id="helpfulnessValue">{{ $employeeReview->helpfulness }}</span></label>
                                        <input type="range" name="helpfulness" class="custom-range" id="helpfulness"
                                            min="0" max="5" value="{{ $employeeReview->helpfulness }}"
                                            oninput="document.getElementById('helpfulnessValue').innerText = this.value">
                                    </div>

                                    <!-- Initiative Slider -->
                                    <div class="form-group">
                                        <label for="initiative">Inisiatif: <span id="initiativeValue">{{ $employeeReview->initiative }}</span></label>
                                        <input type="range" name="initiative" class="custom-range" id="initiative"
                                            min="0" max="5" value="{{ $employeeReview->initiative }}"
                                            oninput="document.getElementById('initiativeValue').innerText = this.value">
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection

@section('footer')
    <!-- Optional: Add JavaScript to show values dynamically on page load -->
    <script>
        // Set initial display values based on slider default positions
        document.getElementById('responsivenessValue').innerText = document.getElementById('responsiveness').value;
        document.getElementById('problemSolverValue').innerText = document.getElementById('problem_solver').value;
        document.getElementById('helpfulnessValue').innerText = document.getElementById('helpfulness').value;
        document.getElementById('initiativeValue').innerText = document.getElementById('initiative').value;
    </script>
@endsection
