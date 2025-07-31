@extends('layout.main_tamplate')


@section('content')
    <section class="content-header">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            @if (auth()->user()->role_id != 1)
                                <div class="card-header">
                                    <div class="row d-inline-flex">
                                        <h3 class="card-title"><strong>Dashboard Week {{ $week ?? now()->weekOfYear }} Year
                                            {{ $year ?? now()->year }}
                                            ({{ $monday? $monday->format('d M'): now()->startOfWeek()->format('d M') }} -
                                            {{ $monday? $monday->endOfWeek()->format('d M'): now()->endOfWeek()->format('d M') }})
                                        </strong></h3>
                                    </div>
                                    <div class="card-tools">
                                        <div class="input-group input-group-sm" style="width: 300px;">
                                            <form action="/result" class="d-inline-flex" method="GET">
                                                <input type="number" name="year" class="form-control float-right" placeholder="Tahun"
                                                    value={{ $year ?? now()->year }} min="2022" required>
                                                <input type="number" name="week" class="form-control float-right mx-2"
                                                    placeholder="Minggu" value={{ $week ?? now()->weekOfYear }} min="1" required>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-default">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3 col-sm-6">
                                        <div class="info-box">
                                            <span class="info-box-icon" style="background-color: #917FB3;"><img src="{{ asset('assets') }}/daily-white.png" width='25'
                                                    height='25' class='mr-1'></span>
                                            <a href="#">
                                                <div class="info-box-content">
                                                    <small class="info-box-text text-dark">Closed / Total Task Daily</small>
                                                    <span
                                                        class="info-box-number text-dark">{{ $data['closedTaskDaily'] }}/{{ $data['totalTaskDaily'] }}</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="info-box">
                                            <span class="info-box-icon" style="background-color: #917FB3;"><img src="{{ asset('assets') }}/daily-white.png" width='25'
                                                    height='25' class='mr-1'></span>
                                            <a href="#">
                                                <div class="info-box-content">
                                                    <small class="info-box-text text-dark">Submited Daily / Work Day</small>
                                                    <span class="info-box-number text-dark">{{ $data['submitedDaily'] }}/6</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    @if (auth()->user()->wr || auth()->user()->wn)
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box">
                                                <span class="info-box-icon" style="background-color: #917FB3;"><img src="{{ asset('assets') }}/week-white.png" width='25'
                                                        height='25' class='mr-1'></span>
                                                <a href="#">
                                                    <div class="info-box-content">
                                                        <small class="info-box-text text-dark">Closed / Total Task Weekly</small>
                                                        <span class="info-box-number text-dark">{{ $data['closedTaskWeekly'] }} /
                                                            {{ $data['totalTaskWeekly'] }}</span>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-md-3 col-sm-6">
                                        <div class="info-box">
                                            <span class="info-box-icon" style="background-color: #917FB3;"><img src="{{ asset('assets') }}/daily-white.png" width='25'
                                                    height='25' class='mr-1'></span>
                                            <a href="#">
                                                <div class="info-box-content">
                                                    <small class="info-box-text text-dark">Point Daily</small>
                                                    <span
                                                        class="info-box-number text-dark">{{ number_format($data['pointDaily'], 1, ',', ' ') }}</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="info-box">
                                            <span class="info-box-icon" style="background-color: #917FB3;"><img src="{{ asset('assets') }}/daily-white.png" width='25'
                                                    height='25' class='mr-1'></span>
                                            <a href="#">
                                                <div class="info-box-content">
                                                    <small class="info-box-text text-dark">Ontime Point Daily</small>
                                                    <span
                                                        class="info-box-number text-dark">{{ number_format($data['pointOntime'], 1, ',', ' ') }}</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    @if (auth()->user()->wr || auth()->user()->wn)
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box">
                                                <span class="info-box-icon" style="background-color: #917FB3;"><img src="{{ asset('assets') }}/week-white.png" width='25'
                                                        height='25' class='mr-1'></span>
                                                <a href="#">
                                                    <div class="info-box-content">
                                                        <small class="info-box-text text-dark">Point Weekly</small>
                                                        <span
                                                            class="info-box-number text-dark">{{ number_format($data['pointWeekly'], 1, ',', ' ') }}</span>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-3 col-sm-6">
                                        <div class="info-box">
                                            <span class="info-box-icon" style="background-color: #917FB3;"><i class="fab fa-slack-hash" style="color: white"></i></span>
                                            <a href="#">
                                                <div class="info-box-content">
                                                    <small class="info-box-text text-dark">Total Point Week
                                                        {{ $week ?? now()->weekOfYear }}</small>
                                                    <span
                                                        class="info-box-number text-dark">{{ number_format($data['totalKpi'], 1, ',', ' ') }}</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @if (auth()->user()->mn || auth()->user()->mr)
                                    <div class="row">
                                        <h5 class="my-2 ml-3"><strong>Monthly - Period {{ $monday ? $monday->format('M') : now()->format('M') }}</strong></h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box">
                                                <span class="info-box-icon" style="background-color: #917FB3;"><img src="{{ asset('assets') }}/monthly-white.png"
                                                        width='25' height='25' class='mr-1'></span>
                                                <a href="#">
                                                    <div class="info-box-content">
                                                        <small class="info-box-text text-dark">Closed / Total Task Monthly</small>
                                                        <span class="info-box-number text-dark">{{ $data['closedTaskMonthly'] }} /
                                                            {{ $data['totalTaskMonthly'] }}</span>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box">
                                                <span class="info-box-icon" style="background-color: #917FB3;"><img src="{{ asset('assets') }}/monthly-white.png"
                                                        width='25' height='25' class='mr-1'></span>
                                                <a href="#">
                                                    <div class="info-box-content">
                                                        <small class="info-box-text text-dark">Point Monthly</small>
                                                        <span
                                                            class="info-box-number text-dark">{{ number_format($data['pointMonthly'], 1, ',', ' ') }}</span>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection
