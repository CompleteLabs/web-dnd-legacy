@extends('layout.main_tamplate')


@section('content')
    <section class="content-header">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card mt-1">
                    @if (auth()->user()->role_id != 1)
                        <div class="card-header">
                            <div class="row d-inline-flex">
                                <h3 class="card-title"><strong>Dashboard Week {{ now()->weekOfYear }} Year {{ now()->year }}
                                    ({{ now()->startOfWeek()->format('d M') }} - {{ now()->endOfWeek()->format('d M') }})
                                </strong></h3>
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
                                                {{ now()->weekOfYear }}</small>
                                            <span
                                                class="info-box-number text-dark">{{ number_format($data['totalKpi'], 1, ',', ' ') }}</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @if (auth()->user()->mn || auth()->user()->mr)
                            <div class="row">
                                <h5 class="my-2 ml-3"><strong>Monthly - Period {{ now()->format('M') }}</strong></h5>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="info-box">
                                        <span class="info-box-icon" style="background-color: #917FB3;"><img src="{{ asset('assets') }}/monthly-white.png"
                                                width='25' height='25' class='mr-1'></span>
                                        <a href="#">
                                            <div class="info-box-content">
                                                <small class="info-box-text text-dark">Closed / Total Task Monthly</small>
                                                <span class="info-box-number text-dark">{{ number_format($data['closedTaskMonthly'], 1, ',', ' ')  }} /
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
        </section>
    </section>
@endsection
