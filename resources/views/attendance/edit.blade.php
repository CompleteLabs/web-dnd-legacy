@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <!-- Main content -->
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

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Edit Data Absensi</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <!-- Dropdown for User ID -->
                                    <div class="form-group">
                                        <label for="user_id">Nama Karyawan</label>
                                        <select name="user_id" id="user_id" class="form-control" required>
                                            <option value="" disabled>Pilih Karyawan</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ $attendance->user_id == $user->id ? 'selected' : '' }}>
                                                    {{ $user->nama_lengkap ?? '-' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Periode Field -->
                                    <div class="form-group">
                                        <label for="periode">Periode (YYYY-MM)</label>
                                        <input type="text" class="form-control" name="periode"
                                            value="{{ $attendance->periode }}" readonly required>
                                    </div>

                                    <!-- Late Less Than 30 Minutes -->
                                    <div class="form-group">
                                        <label for="late_less_30">Hari Efektif Kerja</label>
                                        <input type="number" class="form-control" name="work_days"
                                            value="{{ $attendance->work_days }}" min="0" placeholder="0" required>
                                    </div>

                                    <!-- Late Less Than 30 Minutes -->
                                    <div class="form-group">
                                        <label for="late_less_30">Keterlambatan < 30 Menit</label>
                                                <input type="number" class="form-control" name="late_less_30"
                                                    value="{{ $attendance->late_less_30 }}" min="0" placeholder="0"
                                                    required>
                                    </div>

                                    <!-- Late More Than 30 Minutes -->
                                    <div class="form-group">
                                        <label for="late_more_30">Keterlambatan > 30 Menit</label>
                                        <input type="number" class="form-control" name="late_more_30"
                                            value="{{ $attendance->late_more_30 }}" min="0" placeholder="0" required>
                                    </div>

                                    <!-- Sick Days -->
                                    <div class="form-group">
                                        <label for="sick_days">Hari Sakit/Izin</label>
                                        <input type="number" class="form-control" name="sick_days"
                                            value="{{ $attendance->sick_days }}" min="0" placeholder="0" required>
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
