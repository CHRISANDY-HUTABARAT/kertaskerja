public function progress(Request $request, string $segment, string $type)
{
    $segmentMap = [
        'gov'     => ['label' => 'Government', 'db' => 'government'],
        'private' => ['label' => 'Private',    'db' => 'private'],
        'soe'     => ['label' => 'SOE',        'db' => 'soe'],
        'sme'     => ['label' => 'SME',        'db' => 'sme'],
    ];

    $typeMap = [
        'ngtma'   => 'NGTMA',
    ];

    abort_if(!isset($segmentMap[$segment]), 404);
    abort_if(!isset($typeMap[$type]), 404);

    $segmentLabel = $segmentMap[$segment]['label'];
    $segmentDb    = $segmentMap[$segment]['db'];
    $typeLabel    = $typeMap[$type];

    $availableImports = \App\Models\ScallingImport::where('segment', $segmentDb)
        ->where('type', $type)
        ->orderByDesc('periode')
        ->get();

    $periodOptions = $availableImports
        ->map(fn($i) => \Carbon\Carbon::parse($i->periode)->format('Y-m'))
        ->unique()
        ->values()
        ->toArray();

    if ($request->filled('periode')) {
        $currentPeriode = $request->input('periode');
    } elseif (count($periodOptions)) {
        $currentPeriode = $periodOptions[0];
    } else {
        $currentPeriode = \Carbon\Carbon::now()->format('Y-m');
    }

    [$periodeYear, $periodeMonth] = explode('-', $currentPeriode);
    $periodeLabel = \Carbon\Carbon::createFromDate((int)$periodeYear, (int)$periodeMonth, 1)->format('F Y');
    $periodeDate  = \Carbon\Carbon::createFromDate((int)$periodeYear, (int)$periodeMonth, 1)->format('Y-m-d');

    $import = \App\Models\ScallingImport::where('segment', $segmentDb)
        ->where('type', $type)
        ->where('periode', $periodeDate)
        ->first();

    $dataRows  = collect();
    $funnelMap = collect();

    $dataRows = \App\Models\Ngtma::where('imports_log_id', $import->id)
        ->with(['funnel.todayProgress'])
        ->get()
        ->filter(fn($r) => strtoupper(trim($r->no ?? '')) !== 'TOTAL')
        ->values();

    $dataIds = $dataRows->pluck('id');

    $allFunnels = \App\Models\FunnelTracking::with('todayProgress')
        ->whereIn('data_id', $dataIds)
        ->get();

    $funnelMap = $dataIds->mapWithKeys(function ($dataId) use ($allFunnels) {
        $latest = $allFunnels->where('data_id', $dataId)
            ->sortByDesc('updated_at')
            ->first();
        return [$dataId => $latest];
    });

    return view('report.progress', compact(
        'segment', 'type',
        'segmentLabel', 'typeLabel',
        'periodeLabel', 'currentPeriode',
        'periodOptions',
        'import', 'dataRows', 'funnelMap'
    ));
}