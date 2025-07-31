@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-header">
                                <h3 class="card-title"><strong>REQUEST</strong></h3>
                            </div>
                            <div class="card-body">
                                <form action="/request" method="POST">
                                    @csrf
                                    <input type="hidden" id="idrequest" value={{ auth()->id() }}>
                                    <div class="row">
                                        <div class="col-lg-2 mb-3">
                                            <label for="jenistodo" class="form-label">Task Type</label>
                                            <select class="custom-select jenistodoselect" name="jenistodo" id="jenistodo"
                                                required>
                                                <option value="">--Choose Type--</option>
                                                <option value="Daily">Daily</option>
                                                @if (auth()->user()->wr || auth()->user()->wn)
                                                    <option value="Weekly">Weekly</option>
                                                @endif
                                                @if (auth()->user()->mr || auth()->user()->mn)
                                                    <option value="Monthly">Monthly</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="mb-3 col-lg-2 daterequest">
                                            <label for="date" class="form-label">Date</label>
                                            <input type="date" class="form-control dateselectedrequest" id="date"
                                                name="date">
                                        </div>
                                        @if (auth()->user()->wr || auth()->user()->wn)
                                            <div class="row col-lg-3 weekrequest">
                                                <div class="col-6">
                                                    <label for="year" class="form-label">Year</label>
                                                    <input type="number" class="form-control weekselectedrequest" id="year"
                                                        name="year" min="2022" max="2025" step="1"
                                                        value="{{ now()->year }}">
                                                </div>
                                                <div class="col-6">
                                                    <label for="week" class="form-label">Week</label>
                                                    <input type="number" class="form-control weekselectedrequest" id="week"
                                                        name="week" min="1" max="52" step="1" placeholder="Week">
                                                </div>
                                            </div>
                                        @endif
                                        @if (auth()->user()->mr || auth()->user()->mn)
                                            <div class="mb-3 col-lg-2 monthrequest">
                                                <label for="month" class="form-label">Month</label>
                                                <input type="month" class="form-control monthselectedrequest" id="month"
                                                    name="month">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <select id="duallistboxid" id="existingtask"
                                                    class="duallistbox existingtask" multiple="multiple"
                                                    name="selectedExisting[]">
                                                </select>
                                            </div>
                                            <!-- /.form-group -->
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    {{-- FORM REPLACE DAILY --}}
                                    <div class="form-group" id="formreplacedaily">
                                        <div class="row mt-5">
                                            <label class="form-label ml-2">Task Replace</label>
                                        </div>
                                        <div class="card-body table-responsive p-0" style="height: 300px;">
                                            <table class="table table-head-fixed text-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>Task</th>
                                                        <th style="width:20%;">Time</th>
                                                        <th style="width:10%;"><a href="#formreplacedaily"
                                                                class="badge bg-success" id="addDaily">Add <span><i
                                                                        class="fas fa-plus"></i></span></a></th>

                                                    </tr>
                                                </thead>
                                                <tbody id="tasksdaily">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    @if (auth()->user()->wr || auth()->user()->wn)
                                        {{-- FORM REPLACE WEEKLY --}}
                                        <div class="form-group" id="formreplaceweekly">
                                            <div class="row mt-5">
                                                <label class="form-label ml-2">Task Replace</label>
                                                <input type="hidden" value="{{ auth()->user()->wr }}" id="wr">
                                            </div>
                                            <div class="card-body table-responsive p-0" style="height: 300px;">
                                                <table class="table table-head-fixed text-nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th>Task</th>
                                                            @if (auth()->user()->wr)
                                                                <th style="width:20%;">Tipe</th>
                                                                <th style="width:20%;">Value</th>
                                                            @endif
                                                            <th style="width:10%;"><a href="#formreplaceweekly"
                                                                    class="badge bg-success" id="addWeekly">Add <span><i
                                                                            class="fas fa-plus"></i></span></a></th>

                                                        </tr>
                                                    </thead>
                                                    <tbody id="tasksweekly">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    @if (auth()->user()->mr || auth()->user()->mn)
                                        {{-- FORM REPLACE MONTHLY --}}
                                        <div class="form-group" id="formreplacemonthly">
                                            <div class="row mt-5">
                                                <label class="form-label ml-2">Task Replace</label>
                                                <input type="hidden" value="{{ auth()->user()->mr }}" id="mr">
                                            </div>
                                            <div class="card-body table-responsive p-0" style="height: 300px;">
                                                <table class="table table-head-fixed text-nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th>Task</th>
                                                            @if (auth()->user()->mr)
                                                                <th style="width:20%;">Tipe</th>
                                                                <th style="width:20%;">Value</th>
                                                            @endif
                                                            <th style="width:10%;"><a href="#formreplacemonthly"
                                                                    class="badge bg-success" id="addMonthly">Add <span><i
                                                                            class="fas fa-plus"></i></span></a></th>

                                                        </tr>
                                                    </thead>
                                                    <tbody id="tasksmonthly">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                    <!-- /.form-group -->
                                    <div class="col-lg-12" style="text-align: right;">
                                        <button type="submit" class="btn btn-success mt-3" style="width: 100%; background-color: #917FB3; border-color: #917FB3;">SUBMIT</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </section>
    </section>
    <!-- /.content -->
@endsection
