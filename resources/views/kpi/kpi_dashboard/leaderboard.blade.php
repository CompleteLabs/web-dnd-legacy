@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                                <h3 class="card-title mb-2 mb-md-0">{{ $title }}</h3>
                                <form action="{{ url('leaderboard') }}" method="GET"
                                    class="d-flex flex-wrap align-items-center">
                                    <div class="form-group mr-2 mb-2 mb-md-0">
                                        <input type="month" name="month" class="form-control"
                                            value="{{ request('month', now()->format('Y-m')) }}" min="2020-01"
                                            max="{{ now()->format('Y-m') }}" />
                                    </div>

                                    <div class="form-group mr-2 mb-2 mb-md-0">
                                        <select name="area" id="area" class="form-control">
                                            <option value="">All Areas</option>
                                            @foreach ($areas as $area)
                                                <option value="{{ $area->id }}"
                                                    {{ request('area') == $area->id ? 'selected' : '' }}>
                                                    {{ $area->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mr-2 mb-2 mb-md-0">
                                        <select name="division" id="division" class="form-control">
                                            <option value="">All Divisions</option>
                                            @foreach ($divisions as $division)
                                                @if (request('area') == $division->area_id || !request('area'))
                                                    <option value="{{ $division->id }}"
                                                        {{ request('division') == $division->id ? 'selected' : '' }}>
                                                        {{ $division->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary mb-2 mb-md-0">Filter</button>
                                    <button type="button" class="ml-1 btn btn-success mb-2 mb-md-0" data-toggle="modal"
                                        data-target="#exportModal">Export</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered text-center">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Posisi</th>
                                            <th>Nama User</th>
                                            <th>Division</th>
                                            <th>Area</th>
                                            <th>KPI Score (40%)</th>
                                            <th>Attendance Score (40%)</th>
                                            <th>Activity Score (20%)</th>
                                            <th>Total Score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leaderboardData as $index => $data)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $data['user']->nama_lengkap }}</td>
                                                <td>{{ $data['user']->divisi->name ?? 'N/A' }}</td>
                                                <td>{{ $data['user']->area->name ?? 'N/A' }}</td>
                                                <td>{{ number_format($data['kpiScore'], 2) }}</td>
                                                <td>{{ number_format($data['attendanceScore'], 2) }}</td>
                                                <td>{{ number_format($data['activityScore'], 2) }}</td>
                                                <td>{{ number_format($data['totalScore'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <form action="{{ route('leaderboard.export') }}" method="GET" id="exportForm">
        <!-- Modal -->
        <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalLabel">Pilih Filter untuk Export</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Filter Form -->
                        <div class="form-group">
                            <label for="month">Bulan</label>
                            <input type="month" name="month" id="month-modal" class="form-control"
                                value="{{ request('month', now()->format('Y-m')) }}" min="2020-01" max="{{ now()->format('Y-m') }}">
                        </div>

                        <div class="form-group">
                            <label for="area-modal">Area</label>
                            <select name="area" id="area-modal" class="form-control">
                                <option value="">All Areas</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}" {{ request('area') == $area->id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="division-modal">Divisi</label>
                            <select name="division" id="division-modal" class="form-control">
                                <option value="">All Divisions</option>
                                @foreach ($divisions as $division)
                                    @if (request('area') == $division->area_id || !request('area'))
                                        <option value="{{ $division->id }}" {{ request('division') == $division->id ? 'selected' : '' }}>
                                            {{ $division->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Export Data</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@section('footer')
    <script>
        $(document).ready(function() {
            // For main form (area selection)
            $('#area').change(function() {
                var areaId = $(this).val();
                $.ajax({
                    url: '{{ url('divisi/get') }}/' + areaId,
                    type: 'GET',
                    success: function(response) {
                        $('#division').empty().append(
                        '<option value="">All Divisions</option>');
                        $.each(response, function(key, division) {
                            $('#division').append('<option value="' + division.id +
                                '">' + division.name + '</option>');
                        });
                    }
                });
            });

            // For modal form (area-modal selection)
            $('#area-modal').change(function() {
                var areaId = $(this).val();
                $.ajax({
                    url: '{{ url('divisi/get') }}/' + areaId,
                    type: 'GET',
                    success: function(response) {
                        $('#division-modal').empty().append(
                            '<option value="">All Divisions</option>');
                        $.each(response, function(key, division) {
                            $('#division-modal').append('<option value="' + division
                                .id + '">' + division.name + '</option>');
                        });
                    }
                });
            });
        });
    </script>
@endsection
