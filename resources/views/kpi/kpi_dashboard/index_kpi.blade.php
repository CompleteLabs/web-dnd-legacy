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
                        <div class="card-header p-2">
                            <div class="row d-inline-flex ml-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="/dash-kpi" style="background-color: #917FB3;">Dashboard</a></li>
                                    <!-- <li class="nav-item"><a class="nav-link" href="/dash-daily">Daily</a></li>
                                    <li class="nav-item"><a class="nav-link" href="/dash-weekly">Weekly</a></li> -->
                                    <li class="nav-item"><a class="nav-link" href="/dash-monthly" style="color: #917FB3;">KPI</a></li>
                                    <li class="nav-item"><a class="nav-link" href="/leaderboard" style="color: #917FB3;">Leaderboard</a></li>
                                </ul>
                            </div>
                            <div class="card-tools">
                                <div class="input-group input-group-sm mr-3 mt-1" style="width: 220px;">
                                    <!-- <form action="/dash-weekly" class="d-inline-flex">
                                        <input type="text" id="monthpicker" name="month" class="form-control" placeholder="Choose Month">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form> -->
                                </div>
                            </div>
                        </div>
                        <form action="/dash-kpi" id="inputFilterChart" class="form-inline">
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="row">
                                        <!-- CHART KPI YEARLY -->
                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline" style="border-color: #917FB3;">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="far fa-chart-bar"></i>
                                                        KPI Division Yearly
                                                    </h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <!-- CHART -->
                                                    <div class="col-md-12">
                                                        <div class="input-group">
                                                            @if (auth()->user()->role_id == 1)
                                                                <select class="custom-select col-lg-2 mb-3" name="divisi_yearly" style="width: 180px;"
                                                                required>
                                                                    <option value="">-- Choose Division --</option>
                                                                    @foreach ($divisions as $division)
                                                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                            <input type="text" class="form-control col-lg-2" name="dateChartKpiYearly"
                                                                id="yearpicker" placeholder="Choose Year .." required>
                                                            <a href="javascript:{}" onclick="document.getElementById('inputFilterChart').submit();" class="btn btn-default mb-3" ><i class="fas fa-search"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div id="chartKpiYearly" style="height: 300px; padding: 0px; position: relative;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- CHART USERS KPI YEARLY -->
                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline" style="border-color: #917FB3;">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="far fa-chart-bar"></i>
                                                        Users's KPI Yearly
                                                    </h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <!-- CHART -->
                                                    <div class="col-md-12">
                                                        <div class="input-group">
                                                            @if (auth()->user()->role_id != 2 && auth()->user()->role_id != 3)
                                                                <select class="custom-select col-lg-2 mb-3" name="user_yearly" style="width: 180px;"
                                                                required>
                                                                    <option value="">-- Choose User --</option>
                                                                    @foreach ($users as $user)
                                                                        <option value="{{ $user->id }}">{{ $user->nama_lengkap }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                            <input type="text" class="form-control col-lg-2" name="dateChartUsersKpiYearly"
                                                                id="usersyearpicker" placeholder="Choose Year .." required>
                                                            <a href="javascript:{}" onclick="document.getElementById('inputFilterChart').submit();" class="btn btn-default mb-3" ><i class="fas fa-search"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div id="chartUsersKpiYearly" style="height: 300px; padding: 0px; position: relative;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- CHART KPI WEEKLY -->
                                        <!-- <div class="col-md-12">
                                            <div class="card card-primary card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="far fa-chart-bar"></i>
                                                        KPI Weekly
                                                    </h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-md-12">
                                                        <div class="input-group">
                                                            @if (auth()->user()->role_id == 1)
                                                                <select class="custom-select col-lg-2 mb-3" name="divisi_weekly" style="width: 180px;"
                                                                required>
                                                                    <option value="">-- Choose Division --</option>
                                                                    @foreach ($divisions as $division)
                                                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                            <input type="text" class="form-control col-lg-2" name="dateChartHighestKpiWeekly"
                                                                id="monthpicker" placeholder="Choose Month .." required>
                                                            <a href="javascript:{}" onclick="document.getElementById('inputFilterChart').submit();" class="btn btn-default mb-3" ><i class="fas fa-search"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div id="chartHighestKpiWeekly" style="height: 300px; padding: 0px; position: relative;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                        <!-- CHART KPI MONTHLY -->
                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline" style="border-color: #917FB3;">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="far fa-chart-bar"></i>
                                                        KPI Monthly
                                                    </h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <!-- CHART -->
                                                    <div class="col-md-12">
                                                        <div class="input-group">
                                                            @if (auth()->user()->role_id == 1)
                                                                <select class="custom-select col-lg-2 mb-3" name="divisi_monthly" style="width: 180px;"
                                                                required>
                                                                    <option value="">-- Choose Division --</option>
                                                                    @foreach ($divisions as $division)
                                                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                            <input type="text" class="form-control col-lg-2" name="dateChartHighestKpiMonthly"
                                                                id="monthpickermonthly" placeholder="Choose Month .." required>
                                                            <a href="javascript:{}" onclick="document.getElementById('inputFilterChart').submit();" class="btn btn-default mb-3" ><i class="fas fa-search"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div id="chartHighestKpiMonthly" style="height: 300px; padding: 0px; position: relative;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
    <!-- /.content-wrapper -->
@endsection
@section('footer')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    const yearlyData = [];
    @foreach($monthYear as $index => $month)
        yearlyData.push({
            name: {!! json_encode($month) !!},
            data: [{!! json_encode($averageYearlyKpi[$index]) !!}],
        });
    @endforeach

    Highcharts.chart('chartKpiYearly', {
            chart: {
                type: 'column',
            },
            title: {
                text: 'Yearly KPI - ' + {!! json_encode($divisiYearly) !!}
            },
            subtitle : {
                text: 'Period ' + {!! json_encode($dateChartKpiYearly) !!}
            },
            xAxis: {
                categories: [
                    '',
                ],
                crosshair: true,
                title: {
                    text: 'Month'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Average KPI'
                },
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{y:.2f}%'
                    }
                }
            },
            series: yearlyData,
            tooltip: {
                pointFormatter: function () {
                    return '<span style="color:' + this.color + '">\u25CF</span> ' + this.series.name + ': <b>' + Highcharts.numberFormat(this.y, 2) + '%</b><br/>'
                }
            },
        });

    const usersYearlyData = [];
    @foreach($usersMonthYear as $index => $month)
        usersYearlyData.push({
            name: {!! json_encode($month) !!},
            data: [{!! json_encode($usersAverageYearlyKpi[$index]) !!}],
        });
    @endforeach

    Highcharts.chart('chartUsersKpiYearly', {
            chart: {
                type: 'column',
            },
            title: {
                text: 'Yearly KPI - ' + {!! json_encode($userYearly) !!}
            },
            subtitle : {
                text: 'Period '+ {!! json_encode($dateChartUsersKpiYearly) !!}
            },
            xAxis: {
                categories: [
                    '',
                ],
                crosshair: true,
                title: {
                    text: 'Month'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Average KPI'
                },
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{y:.2f}%'
                    }
                }
            },
            series: usersYearlyData,
            tooltip: {
                pointFormatter: function () {
                    return '<span style="color:' + this.color + '">\u25CF</span> ' + this.series.name + ': <b>' + Highcharts.numberFormat(this.y, 2) + '%</b><br/>'
                }
            },
        });

    // const highestKpiWeeklyColumnData = [];
    // const highestKpiWeeklyLineData = [];
    // @foreach($highestKpiWeeklyUser as $index => $user)
    // highestKpiWeeklyColumnData.push({
    //     name: {!! json_encode($user) !!},
    //     y: {!! json_encode($highestKpiWeeklyUnit[$index]) !!},
    // });

    // highestKpiWeeklyLineData.push({
    //     name: {!! json_encode($user) !!},
    //     y: {!! json_encode($highestKpiWeeklyUnit[$index]) !!},
    // });
    // console.log(highestKpiWeeklyColumnData);
    // @endforeach

    // Highcharts.chart('chartHighestKpiWeekly', {
    //         title: {
    //             text: 'Highest KPI'
    //         },
    //         subtitle : {
    //             text: 'Period '
    //         },
    //         xAxis: {
    //             categories: [
    //                 @foreach($highestKpiWeeklyUser as $user)
    //                     {!! json_encode($user) !!},
    //                 @endforeach
    //             ],
    //             crosshair: true,
    //             title: {
    //                 text: 'User'
    //             }
    //         },
    //         yAxis: [
    //             {
    //                 min: 0,
    //                 title: {
    //                     text: 'Percentage'
    //                 },
    //                 labels: {
    //                     formatter: function () {
    //                         return this.value + '%';
    //                     }
    //                 }
    //             },
    //             {
    //                 min: 0,
    //                 title: {
    //                     text: ''
    //                 },
    //                 opposite: true, // Display the secondary y-axis on the opposite side
    //                 labels: {
    //                     enabled: false,
    //                 }
    //             }
    //         ],
    //         plotOptions: {
    //             column: {
    //                 pointPadding: 0.2,
    //                 borderWidth: 0,
    //                 dataLabels: {
    //                     enabled: true,
    //                     format: '{y:.0f}%',
    //                 }
    //             }
    //         },
    //         series: [
    //             {
    //                 type: 'column',
    //                 name: 'Score',
    //                 data: highestKpiWeeklyColumnData,
    //                 yAxis: 0,
    //             },
    //             {
    //                 type: 'spline',
    //                 name: 'Score',
    //                 data: highestKpiWeeklyLineData,
    //                 yAxis: 1,
    //             }
    //         ],
    //         tooltip: {
    //             shared: false,
    //             pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y:.0f}%</b><br/>',
    //         },
    //     });

    const highestKpiMonthlyColumnData = [];
    const highestKpiMonthlyLineData = [];
    @foreach($highestKpiMonthlyUser as $index => $user)
    highestKpiMonthlyColumnData.push({
        name: {!! json_encode($user) !!},
        y: {!! json_encode($highestKpiMonthlyUnit[$index]) !!},
    });

    highestKpiMonthlyLineData.push({
        name: {!! json_encode($user) !!},
        y: {!! json_encode($highestKpiMonthlyUnit[$index]) !!},
    });
    console.log(highestKpiMonthlyColumnData);
    @endforeach

    Highcharts.chart('chartHighestKpiMonthly', {
            title: {
                text: 'Monthly KPI - ' + {!! json_encode($divisiMonthly) !!}
            },
            subtitle : {
                text: 'Period ' + {!! json_encode($dateChartHighestKpiMonthly) !!}
            },
            xAxis: {
                categories: [
                    @foreach($highestKpiMonthlyUser as $user)
                        {!! json_encode($user) !!},
                    @endforeach
                ],
                crosshair: true,
                title: {
                    text: 'User'
                }
            },
            yAxis: [
                {
                    min: 0,
                    title: {
                        text: 'Percentage'
                    },
                    labels: {
                        formatter: function () {
                            return this.value + '%';
                        }
                    }
                },
                {
                    min: 0,
                    title: {
                        text: ''
                    },
                    opposite: true, // Display the secondary y-axis on the opposite side
                    labels: {
                        enabled: false,
                    }
                }
            ],
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{y:.0f}%',
                    },
                    color: '#b2a5ca',
                },
                spline: {
                    color: '#574c6b'
                }
            },
            series: [
                {
                    type: 'column',
                    name: 'Score',
                    data: highestKpiMonthlyColumnData,
                    yAxis: 0,
                },
                {
                    type: 'spline',
                    name: 'Score',
                    data: highestKpiMonthlyLineData,
                    yAxis: 1,
                }
            ],
            tooltip: {
                shared: false,
                pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y:.0f}%</b><br/>',
            },
        });
</script>
@endsection
