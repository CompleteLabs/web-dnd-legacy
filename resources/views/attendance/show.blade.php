@extends('layout.main_tamplate')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Absensi</h3>
        <a href="{{ route('attendance.index') }}" class="btn btn-primary float-right">Kembali</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Nama Karyawan</th>
                <td>{{ $attendance->user->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <th>Periode</th>
                <td>{{ $attendance->periode }}</td>
            </tr>
            <tr>
                <th>Hari Kerja</th>
                <td>{{ $attendance->work_days }}</td>
            </tr>
            <tr>
                <th>Keterlambatan < 30 Menit</th>
                <td>{{ $attendance->late_less_30 }}</td>
            </tr>
            <tr>
                <th>Keterlambatan > 30 Menit</th>
                <td>{{ $attendance->late_more_30 }}</td>
            </tr>
            <tr>
                <th>Hari Sakit/Izin</th>
                <td>{{ $attendance->sick_days }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
