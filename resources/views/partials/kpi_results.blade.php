<div>
    <div class="timeline-item">
        <h3 class="timeline-header"><strong>RESULTS:</strong></h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>KPI Category</th>
                        <th class="text-center">Percentage</th>
                        <th class="text-center">Actual</th>
                        <th class="text-center">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupedKpis as $yearMonth => $groupedKpisByCategory)
                        @foreach ($groupedKpisByCategory as $categoryName => $kpis)
                            @foreach ($kpis as $kpi)
                                <tr>
                                    <td>{{ $kpi->kpi_category->name }}</td>
                                    <td class="text-center">{{ number_format($kpi->percentage, 2) }}%</td>
                                    <td class="text-center">{{ number_format($kpi->actualCount * 100, 2) }}%</td>
                                    <td class="text-center">{{ number_format($kpi->score * 100, 2) }}%</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>FINAL SCORE</strong></td>
                        <td></td>
                        <td></td>
                        <td class="text-center"><strong>{{ number_format($totalScore * 100, 2) }}%</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
