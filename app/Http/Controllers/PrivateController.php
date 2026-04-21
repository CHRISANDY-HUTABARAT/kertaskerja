<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScallingImport;
use App\Models\ScallingData;


class PrivateController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private const FUNNEL_ORDER = [
        'f0_inisiasi_solusi',
        'f1_tech_budget',
        'f2_p0_p1',
        'f2_p2',
        'f2_p3',
        'f2_p4',
        'f2_offering',
        'f2_p5',
        'f2_proposal',
        'f3_p6',
        'f3_p7',
        'f3_submit',
        'f4_negosiasi',
        'f5_sk_mitra',
        'f5_ttd_kontrak',
        'f5_p8',
        'delivery_kontrak',
        'delivery_baut_bast',
        'delivery_baso',
        'delivery_billing_complete',
    ];
    public function index()
    {
        return view('dashboard.private.index');
    }

    public function scalling()
    {
        // Scalling page acts as parent menu for LOP categories
        return view('dashboard.private.scalling');
    }

    public function initiate()
    {
        $logs = ScallingImport::where('type', 'initiate')->where('segment', 'private')->latest()->paginate(10);
        $projects       = ScallingData::with('scallingImport')
            ->whereHas('scallingImport', function ($query) {
                $query->where('type', 'initiate')->where('segment', 'private');
            })
            ->latest()
            ->paginate(10);
        return view('dashboard.private.initiate', compact('logs', 'projects'));
    }

    public function lopOnHand()
    {
        // gunakan parameter periode (format YYYY-MM) daripada month/year terpisah
        $currentPeriode = request()->get('periode', date('Y-m'));
        // kolom periode di database disimpan sebagai DATE (YYYY-MM-01), jadi tambahkan '-01'
        $currentPeriodeDate = $currentPeriode . '-01';

        // produce list of available periods (YYYY-MM) from past imports
        $periodOptions = ScallingImport::where('type', 'on-hand')
            ->where('segment', 'private')
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m'))
            ->unique()
            ->values();

        $latestImport = ScallingImport::with(['data' => function($query) {
            $query->orderBy('am', 'asc')
                ->orderByRaw('CAST(no AS UNSIGNED) asc');
        }, 'data.funnel.todayProgress'])
        ->where('type', 'on-hand')
        ->where('segment', 'private')
        ->where('periode', $currentPeriodeDate)
        ->latest()
        ->first();

        // Get admin note

        return view('dashboard.private.lop-on-hand', compact('latestImport', 'currentPeriode', 'periodOptions'));
    }

    public function lopKoreksi()
    {
        // gunakan parameter periode (format YYYY-MM) daripada month/year terpisah
        $currentPeriode = request()->get('periode', date('Y-m'));
        // kolom periode di database disimpan sebagai DATE (YYYY-MM-01), jadi tambahkan '-01'
        $currentPeriodeDate = $currentPeriode . '-01';

        // produce list of available periods (YYYY-MM) from past imports
        $periodOptions = ScallingImport::where('type', 'koreksi')
            ->where('segment', 'private')
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m'))
            ->unique()
            ->values();

        $latestImport = ScallingImport::with(['data' => function($query) {
            $query->orderBy('am', 'asc')
                ->orderByRaw('CAST(no AS UNSIGNED) asc');
        }, 'data.funnel.todayProgress'])
        ->where('type', 'koreksi')
        ->where('segment', 'private')
        ->where('periode', $currentPeriodeDate)
        ->latest()
        ->first();

        $rows = $latestImport
        ? \App\Models\Koreksi::where('imports_log_id', $latestImport->id)->get()
        : collect();

        return view('dashboard.private.lop-koreksi', compact('latestImport', 'rows', 'currentPeriode', 'periodOptions'));
    }

    public function updateRealisasiKoreksi(Request $request)
    {
        $request->validate([
            'id'        => 'required|integer|exists:koreksis,id',
            'realisasi' => 'required|numeric|min:0',
        ]);

        $koreksi = \App\Models\Koreksi::findOrFail($request->id);
        $koreksi->update(['realisasi' => $request->realisasi]);

        return response()->json(['success' => true]);
    }

    public function lopQualified()
    {
        // gunakan parameter periode (format YYYY-MM) daripada month/year terpisah
        $currentPeriode = request()->get('periode', date('Y-m'));
        // kolom periode di database disimpan sebagai DATE (YYYY-MM-01), jadi tambahkan '-01'
        $currentPeriodeDate = $currentPeriode . '-01';

        // produce list of available periods (YYYY-MM) from past imports
        $periodOptions = ScallingImport::where('type', 'qualified')
            ->where('segment', 'private')
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m'))
            ->unique()
            ->values();

        $latestImport = ScallingImport::with(['data' => function($query) {
            $query->orderBy('am', 'asc')
                ->orderByRaw('CAST(no AS UNSIGNED) asc');
        }, 'data.funnel.todayProgress'])
        ->where('type', 'qualified')
        ->where('segment', 'private')
        ->where('periode', $currentPeriodeDate)
        ->latest()
        ->first();

        // Get admin note

        return view('dashboard.private.lop-qualified', compact('latestImport', 'currentPeriode', 'periodOptions'));
    }

    public function lopInitiate()
    {
        $currentPeriode = request()->get('periode', date('Y-m'));
        $currentPeriodeDate = $currentPeriode . '-01';

        $periodOptions = ScallingImport::where('type', 'initiate')
            ->where('segment', 'private')
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m'))
            ->unique()
            ->values();

        $rows = ScallingData::with(['funnel.todayProgress'])
            ->whereHas('scallingImport', function ($query) use ($currentPeriodeDate) {
                $query->where('type', 'initiate')
                    ->where('segment', 'private')
                    ->where('periode', $currentPeriodeDate);
            })
            ->orderBy('am', 'asc')
            ->get()
            ->filter(fn($item) => strtoupper(trim($item->no ?? '')) !== 'TOTAL');

        $latestImport = ScallingImport::with(['data' => function($query) {
            $query->orderBy('am', 'asc')
                ->orderByRaw('CAST(no AS UNSIGNED) asc');
        }, 'data.funnel.todayProgress'])
        ->where('type', 'initiate')
        ->where('segment', 'private')
        ->where('periode', $currentPeriodeDate)
        ->latest()
        ->first();

        // Hitung total langsung dari $rows yang sudah difilter
        $totalEstNilai = $rows->sum(fn($item) => floatval($item->est_nilai_bc ?? 0));

        $totalBillComp = $rows->sum(function ($item) {
            $funnel        = $item->funnel;
            $master        = $funnel;
            $todayProgress = $funnel?->todayProgress;

            $masterChecked = $master && $master->delivery_billing_complete;
            $todayChecked  = $todayProgress && $todayProgress->delivery_billing_complete;

            if ($todayChecked || $masterChecked) {
                $nilai = $todayProgress->delivery_nilai_billcomp
                    ?? ($masterChecked ? $master->delivery_nilai_billcomp : null);

                if (!$nilai) {
                    $cleanValue = str_replace(['.', ','], '', $item->est_nilai_bc ?? '0');
                    $nilai = (float) $cleanValue;
                }
                return (float) $nilai;
            }
            return 0;
        });
        // dd($totalBillComp);

        return view('dashboard.private.lop-initiate', compact(
            'latestImport', 'currentPeriode', 'periodOptions', 'rows', 'totalEstNilai', 'totalBillComp'
        ));
    }

    public function storeData(Request $request)
    {
        $request->validate([
            'status'                   => 'required|in:active,inactive',
            'periode'                  => 'required|date_format:Y-m',
            'project'                  => 'required|string|max:255',
            'cc'                       => 'required|string|max:100',
            'am'                       => 'required|string|max:100',
            'plan_bulan_billcomp_2025' => 'required|integer|min:1|max:12',
            'est_nilai_bc'             => 'required|numeric|min:0',
        ], [
            'status.required'                   => 'Status wajib diisi',
            'status.in'                         => 'Status harus berupa "active" atau "inactive"',
            'periode.required'                  => 'Periode wajib diisi',
            'periode.date_format'               => 'Format periode harus berupa bulan dan tahun (contoh: 2025-03)',
            'project.required'                  => 'Nama project wajib diisi',
            'project.max'                       => 'Nama project maksimal 255 karakter',
            'cc.required'                       => 'CC wajib diisi',
            'cc.max'                            => 'CC maksimal 100 karakter',
            'am.required'                       => 'Nama AM wajib diisi',
            'am.max'                            => 'Nama AM maksimal 100 karakter',
            'plan_bulan_billcomp_2025.required' => 'Plan bulan wajib diisi',
            'plan_bulan_billcomp_2025.integer'  => 'Plan harus berupa angka (contoh: 10)',
            'plan_bulan_billcomp_2025.min'      => 'Plan bulan minimal 1',
            'plan_bulan_billcomp_2025.max'      => 'Plan bulan maksimal 12',
            'est_nilai_bc.required'             => 'Estimasi nilai BC wajib diisi',
            'est_nilai_bc.numeric'              => 'Estimasi nilai BC harus berupa angka (contoh: 10000)',
            'est_nilai_bc.min'                  => 'Estimasi nilai BC tidak boleh negatif',
        ]);

        $periodeDate = $request->periode . '-01';

        $log = ScallingImport::where('periode', $periodeDate)
            ->where('type', $request->type)
            ->where('segment', $request->segment)
            ->first();

        if($log) {
            $data = ScallingData::create([
            'no'                      => $log->data()->count() + 1, // Auto-increment berdasarkan jumlah data yang sudah ada untuk log ini
            'imports_log_id'           => $log->id,
            'is_manual'                => true,
            'project'                  => $request->project,
            'id_lop'                   => $request->id_lop,
            'cc'                       => $request->cc,
            'nipnas'                   => $request->nipnas,
            'am'                       => $request->am,
            'mitra'                    => $request->mitra,
            'plan_bulan_billcomp_2025' => $request->plan_bulan_billcomp_2025,
            'est_nilai_bc'             => $request->est_nilai_bc,
        ]);
        } else {
            $import = ScallingImport::create([
                'original_filename'   => 'manual-input',
                'status'              => $request->status,
                'type'                => $request->type,
                'segment'             => $request->segment,
                'periode'             => $periodeDate,
                'total_rows_imported' => 0,
                'uploaded_by'         => auth()->user()->name ?? $request->ip(),
            ]);

            $data = ScallingData::create([
            'imports_log_id'           => $import->id,
            'is_manual'                => true,
            'project'                  => $request->project,
            'id_lop'                   => $request->id_lop,
            'cc'                       => $request->cc,
            'nipnas'                   => $request->nipnas,
            'am'                       => $request->am,
            'mitra'                    => $request->mitra,
            'plan_bulan_billcomp_2025' => $request->plan_bulan_billcomp_2025,
            'est_nilai_bc'             => $request->est_nilai_bc,
        ]);
        }



        return redirect()->back()->with('success', "Data untuk project \"{$data->project}\" berhasil disimpan.");
    }

    public function updateFunnelCheckbox(Request $request)
    {
        $request->validate([
            'data_type'    => 'required|in:on-hand,qualified,koreksi,initiate',
            'data_id'      => 'required|integer',
            'field'        => 'required|string',
            'value'        => 'required',
            'est_nilai_bc' => 'nullable',
        ]);

        if (auth()->user()->role !== 'admin') {
            $scallingData = \App\Models\ScallingData::find($request->data_id);
            if ($scallingData) {
               $import = $scallingData->scallingImport;
                if ($import && ($import->status ?? 'active') !== 'active') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data ini sedang dikunci oleh admin dan tidak dapat diubah.',
                    ], 403);
                }
            }
        }

        $value  = filter_var($request->value, FILTER_VALIDATE_BOOLEAN);
        $rawEst = $request->est_nilai_bc ?? null;

        $funnel = \App\Models\FunnelTracking::firstOrCreate([
            'data_type' => $request->data_type,
            'data_id'   => $request->data_id,
            'ngtma_id'  => $request->ngtma_id,
        ]);

        $autoFields = [];
        $currentField = $request->field;

        // Temukan posisi field yang diubah di FUNNEL_ORDER
        $currentIndex = array_search($currentField, self::FUNNEL_ORDER);

        if ($currentIndex !== false) {
            if ($value === true) {
                // Centang semua field SEBELUM field ini (yang belum tercentang)
                $autoFields = array_slice(self::FUNNEL_ORDER, 0, $currentIndex);
                foreach ($autoFields as $fld) {
                    $funnel->{$fld} = true;
                }
            } else {
                // Uncentang semua field SETELAH field ini
                $autoFields = array_slice(self::FUNNEL_ORDER, $currentIndex + 1);
                foreach ($autoFields as $fld) {
                    $funnel->{$fld} = false;
                }
                // Jika uncentang billing complete, reset nilai juga
                if (in_array('delivery_billing_complete', $autoFields) || $currentField === 'delivery_billing_complete') {
                    $funnel->delivery_nilai_billcomp = null;
                }
            }
        }

        // Set field yang diubah
        $funnel->{$currentField} = $value;

        // Handle billing complete khusus untuk nilai
        if ($currentField === 'delivery_billing_complete') {
            $funnel->delivery_nilai_billcomp = $value && is_numeric($rawEst) ? (float) $rawEst : null;
        }

        // Handle cancel
        if ($currentField === 'cancel' && $value === true) {
            $funnel->delivery_billing_complete = false;
            $funnel->delivery_nilai_billcomp   = null;
        }

        $funnel->save();

        // TaskProgress — logika sama
        $taskProgress = \App\Models\TaskProgress::firstOrCreate([
            'task_id' => $funnel->id,
            'user_id' => auth()->id(),
            'tanggal' => today(),
        ]);

        $taskProgress->{$currentField} = $value;

        foreach ($autoFields as $fld) {
            $taskProgress->{$fld} = $value === true ? true : false;
        }

        if ($currentField === 'delivery_billing_complete') {
            $taskProgress->delivery_nilai_billcomp = $value && is_numeric($rawEst) ? (float) $rawEst : null;
        }

        if ($currentField === 'cancel' && $value === true) {
            $taskProgress->delivery_billing_complete = false;
            $taskProgress->delivery_nilai_billcomp   = null;
        }

        $taskProgress->save();

        // Hitung total billcomp — tetap sama
        $dataId          = $request->data_id;
        $periodeImport   = \App\Models\ScallingData::find($dataId)?->scallingImport;
        $dataIdsInPeriode = collect();

        if ($periodeImport) {
            $dataIdsInPeriode = \App\Models\ScallingData::where('imports_log_id', $periodeImport->id)->pluck('id');
        }

        $total = \App\Models\FunnelTracking::whereIn('data_id', $dataIdsInPeriode)
            ->where('delivery_billing_complete', true)
            ->where('cancel', false)
            ->whereNotNull('delivery_nilai_billcomp')
            ->sum('delivery_nilai_billcomp');

        return response()->json([
            'success'        => true,
            'nilai_billcomp' => $taskProgress->delivery_nilai_billcomp,
            'total'          => number_format((float) $total, 0, ',', '.'),
            'auto_fields'    => $autoFields,
            'auto_value'     => $value,  // ← sekarang kirim value asli bukan hanya untuk billcomp
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
