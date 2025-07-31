@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Review Karyawan</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Nama Karyawan</th>
                                    <td>{{ $employeeReview->user->nama_lengkap }}</td>
                                </tr>
                                <tr>
                                    <th>Periode</th>
                                    <td>{{ $employeeReview->periode }}</td>
                                </tr>
                                <tr>
                                    <th>Responsiveness</th>
                                    <td>{{ $employeeReview->responsiveness }}</td>
                                </tr>
                                <tr>
                                    <th>Problem Solver</th>
                                    <td>{{ $employeeReview->problem_solver }}</td>
                                </tr>
                                <tr>
                                    <th>Helpfulness</th>
                                    <td>{{ $employeeReview->helpfulness }}</td>
                                </tr>
                                <tr>
                                    <th>Initiative</th>
                                    <td>{{ $employeeReview->initiative }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('employee_reviews.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
