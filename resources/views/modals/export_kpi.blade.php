<div class="modal fade" id="exportKpi" tabindex="-1" role="dialog" aria-labelledby="exportKpiLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="GET" action="/kpi/exportMonthly">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exportKpiLabel">Download KPI</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if (auth()->user()->role_id == 1)
                        <div class="form-group">
                            <label for="divisi_id">Division</label>
                            <select class="custom-select" name="divisi_id" id="divisi_id">
                                <option value="">--Choose Division--</option>
                                @foreach ($divisions as $divisi)
                                    <option value="{{ $divisi->id }}">{{ $divisi->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="text" class="form-control" id="exportMonthly" name="date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">EXPORT</button>
                </div>
            </form>
        </div>
    </div>
</div>
