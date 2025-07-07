@if ($kpiDetail->count_type === 'NON')
    <form action="/dash/change" method="POST" id="nonCountForm">
        @csrf
        <input type="hidden" name="id" value="{{ $kpiDetail->id }}">
        <input type="hidden" name="type" value="monthly">
        <button type="submit" class="btn fas fa-check-circle"
            style="color: {{ $kpiDetail->value_result != null ? 'green' : 'red' }};"></button>
    </form>
@elseif ($kpiDetail->count_type === 'RESULT')
    <button type="button" class="btn fas fa-check-circle"
        style="color: {{ $kpiDetail->value_result != null ? 'green' : 'red' }};" data-toggle="modal"
        data-target="#changeStatus{{ $kpiDetail->id }}"></button>
    <!-- Modal Edit -->
    <div class="modal fade" id="changeStatus{{ $kpiDetail->id }}" tabindex="-1" role="dialog"
        aria-labelledby="changeStatusLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeStatusLabel">Update KPI</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/dash/change" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $kpiDetail->id }}">
                        <input type="hidden" name="type" value="monthly">
                        <div class="row">
                            <div class="mb-3 col-lg-6">
                                <label for="kpi_description_id" class="form-label">KPI Desc</label>
                                <input type="text" class="form-control" id="kpi_description_id"
                                    name="kpi_description_id" value="{{ $kpiDetail->kpi_description->description }}"
                                    disabled>
                            </div>
                            <div class="mb-3 col-lg-6">
                                <label for="count_type" class="form-label">Tipe</label>
                                <input type="text" class="form-control" id="count_type" name="count_type"
                                    value="{{ $kpiDetail->count_type }}" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-lg-6">
                                <label for="week" class="form-label">Month</label>
                                <input type="text" class="form-control" id="week" name="week"
                                    value="{{ Carbon\Carbon::parse($kpi->date)->format('F Y') }}" readonly>
                            </div>
                            <div class="mb-3 col-lg-6">
                                <label for="value_plan" class="form-label">Value Plan</label>
                                <input type="text" class="form-control" name="value_plan"
                                    value="{{ number_format($kpiDetail->value_plan, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-lg-6">
                                <label for="value_actual" class="form-label">Value Actual</label>
                                <input type="number" class="form-control" id="value_actual" name="value_actual"
                                    value="0" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success mt-3">Submit</button>
                    </form>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
@endif
