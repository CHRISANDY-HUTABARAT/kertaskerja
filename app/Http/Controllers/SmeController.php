<?php

namespace App\Http\Controllers;
use App\Models\ScallingImport;
use App\Models\ScallingData;
use App\Models\RisingStar;
use App\Models\Hsi;
use App\Models\TaskProgress;
use App\Models\FunnelTracking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SmeController extends Controller
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
        //
    }

    public function initiate()
    {
        $logs = ScallingImport::where('type', 'initiate')->where('segment', 'sme')->latest()->paginate(10);
        $projects       = ScallingData::with('scallingImport')
            ->whereHas('scallingImport', function ($query) {
                $query->where('type', 'initiate')->where('segment', 'sme');
            })
            ->latest()
            ->paginate(10);
        return view('dashboard.sme.initiate', compact('logs', 'projects'));
    }

    public function lopOnHand()
    {
        // gunakan parameter periode (format YYYY-MM) daripada month/year terpisah
        $currentPeriode = request()->get('periode', date('Y-m'));
        // kolom periode di database disimpan sebagai DATE (YYYY-MM-01), jadi tambahkan '-01'
        $currentPeriodeDate = $currentPeriode . '-01';

        // produce list of available periods (YYYY-MM) from past imports
        $periodOptions = ScallingImport::where('type', 'on-hand')
            ->where('segment', 'sme')
            ->orderBy('periode', 'desc')
            ->get()
            ->pluck('periode')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m'))
            ->unique()
            ->values();
            // dd($periodOptions);

        $latestImport = ScallingImport::with(['data' => function($query) {
            $query->orderBy('am', 'asc')
                ->orderByRaw('CAST(no AS UNSIGNED) asc');
        }, 'data.funnel.todayProgress'])
        ->where('type', 'on-hand')
        ->where('segment', 'sme')
        ->where('periode', $currentPeriodeDate)
        ->latest()
        ->first();

        if (!$latestImport) {
            $latestImport = ScallingImport::with(['data' => function($query) {
                $query->orderBy('am', 'asc')
                    ->orderByRaw('CAST(no AS UNSIGNED) asc');
            }, 'data.funnel.todayProgress'])
            ->where('type', 'on-hand')
            ->where('segment', 'sme')
            ->where('periode', '<', $currentPeriodeDate)
            ->orderBy('periode', 'desc')
            ->first();

            if ($latestImport) {
                $currentPeriode = Carbon::parse($latestImport->periode)->format('Y-m');
            }
        }
        // Get admin note

        return view('dashboard.sme.lop-on-hand', compact('latestImport', 'currentPeriode', 'periodOptions'));
    }

    public function lopKoreksi()
    {
        // gunakan parameter periode (format YYYY-MM) daripada month/year terpisah
        $currentPeriode = request()->get('periode', date('Y-m'));
        // kolom periode di database disimpan sebagai DATE (YYYY-MM-01), jadi tambahkan '-01'
        $currentPeriodeDate = $currentPeriode . '-01';

        // produce list of available periods (YYYY-MM) from past imports
        $periodOptions = ScallingImport::where('type', 'koreksi')
            ->where('segment', 'sme')
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
        ->where('segment', 'sme')
        ->where('periode', $currentPeriodeDate)
        ->latest()
        ->first();

        $rows = $latestImport
        ? \App\Models\Koreksi::where('imports_log_id', $latestImport->id)->get()
        : collect();

        return view('dashboard.sme.lop-koreksi', compact('latestImport', 'rows', 'currentPeriode', 'periodOptions'));
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
            ->where('segment', 'sme')
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
        ->where('segment', 'sme')
        ->where('periode', $currentPeriodeDate)
        ->latest()
        ->first();

        if (!$latestImport) {
            $latestImport = ScallingImport::with(['data' => function($query) {
                $query->orderBy('am', 'asc')
                    ->orderByRaw('CAST(no AS UNSIGNED) asc');
            }, 'data.funnel.todayProgress'])
            ->where('type', 'qualified')
            ->where('segment', 'sme')
            ->where('periode', '<', $currentPeriodeDate)
            ->orderBy('periode', 'desc')
            ->first();

            if ($latestImport) {
                $currentPeriode = Carbon::parse($latestImport->periode)->format('Y-m');
            }
        }

        // Get admin note

        return view('dashboard.sme.lop-qualified', compact('latestImport', 'currentPeriode', 'periodOptions'));
    }
    public function lopInitiate()
    {
        $currentPeriode = request()->get('periode', date('Y-m'));
        $currentPeriodeDate = $currentPeriode . '-01';

        $periodOptions = ScallingImport::where('type', 'initiate')
            ->where('segment', 'sme')
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
        ->where('type', 'initiate')
        ->where('segment', 'sme')
        ->where('periode', $currentPeriodeDate)
        ->latest()
        ->first();

        if (!$latestImport) {
            $latestImport = ScallingImport::with(['data' => function($query) {
                $query->orderBy('am', 'asc')
                    ->orderByRaw('CAST(no AS UNSIGNED) asc');
            }, 'data.funnel.todayProgress'])
            ->where('type', 'initiate')
            ->where('segment', 'sme')
            ->where('periode', '<', $currentPeriodeDate)
            ->orderBy('periode', 'desc')
            ->first();

            if ($latestImport) {
                $currentPeriode = Carbon::parse($latestImport->periode)->format('Y-m');
                $currentPeriodeDate = $latestImport->periode;
            }
        }

        $rows = collect();
        if ($latestImport) {
            $rows = $latestImport->data
                ->filter(fn($item) => strtoupper(trim($item->no ?? '')) !== 'TOTAL');
        }

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

        return view('dashboard.sme.lop-initiate', compact(
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
        'auto_value'     => $value,
    ]);
    }

    // ══════════════════════════════════════════════════════
    // Helper: upsert RisingStar per (user_id, type_id, periode)
    // Hanya kolom yang dikirim yang diupdate — tidak overwrite kolom lain.
    // ══════════════════════════════════════════════════════
    private function upsertRisingStar(int $typeId, array $values): RisingStar
{
    // ← Gunakan periode dari $values jika ada, fallback ke bulan berjalan
    $periode = isset($values['periode']) ? $values['periode'] : now()->format('Y-m-01');

    $last = RisingStar::where('user_id', auth()->id())
        ->where('type_id', $typeId)
        ->where('periode', $periode)
        ->where('is_latest', true)
        ->first();

    $realRatio     = isset($values['real_ratio']) ? $values['real_ratio'] : ($last?->real_ratio);
    $realUpdatedAt = isset($values['real_ratio']) ? now() : ($last?->real_updated_at);

    $result = DB::transaction(function () use ($typeId, $periode, $values, $last, $realRatio, $realUpdatedAt) {
        RisingStar::where('user_id', auth()->id())
            ->where('type_id', $typeId)
            ->where('periode', $periode)
            ->update(['is_latest' => false]);

        return RisingStar::create([
            'user_id'         => auth()->id(),
            'type_id'         => $typeId,
            'periode'         => $periode,
            'status'          => 'active',
            'is_latest'       => true,
            'commitment'      => $values['commitment'] ?? ($last?->commitment),
            'real_ratio'      => $realRatio,
            'real_updated_at' => $realUpdatedAt,
        ]);
    });

    return $result;
}

    // ══ AOSODOMORO 0-3 Bulan (type_id: 7) ══

    public function aosodomoro03Bulan(Request $request)
    {
        $periodeOptions = RisingStar::where('user_id', auth()->id())
            ->where('type_id', 11)
            ->selectRaw('DATE_FORMAT(periode, "%Y-%m") as periode_ym, MAX(periode) as max_periode')
            ->where('periode', '<=', Carbon::now()->endOfMonth()->format('Y-m-d'))
            ->groupBy('periode_ym')
            ->orderByDesc('max_periode')
            ->pluck('periode_ym');

        $selectedPeriode = $request->filled('selected_periode')
            ? $request->selected_periode
            : Carbon::now()->format('Y-m');

        $periodeDate = $selectedPeriode . '-01';

        $existing = RisingStar::where('user_id', auth()->id())
            ->where('type_id', 11)
            ->where('periode', $periodeDate)
            ->where('is_latest', true)
            ->first();

        $isLocked = $existing && $existing->status === 'inactive';

        $query = RisingStar::with(['user', 'type'])
            ->where('user_id', auth()->id())
            ->where('type_id', 11)
            ->orderBy('created_at', 'desc');

        if ($request->filled('bulan')) $query->whereMonth('periode', $request->bulan);
        if ($request->filled('tahun')) $query->whereYear('periode', $request->tahun);
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('commitment', 'like', '%'.$request->cari.'%')
                ->orWhere('real_ratio', 'like', '%'.$request->cari.'%');
            });
        }

        $history = $query->paginate(20)->withQueryString();

        $tahuns = RisingStar::where('user_id', auth()->id())
            ->where('type_id', 11)
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $selectedBulan = $request->bulan;
        $selectedTahun = $request->tahun;
        $selectedCari  = $request->cari;

        return view('dashboard.sme.aosodomoro-0-3-bulan', compact(
            'history', 'existing', 'isLocked',
            'periodeOptions', 'selectedPeriode',
            'tahuns', 'selectedBulan', 'selectedTahun', 'selectedCari'
        ));
    }

    public function storeAosodomoro03Bulan(Request $request)
    {
        $request->validate([
            'real_ratio' => 'required|numeric|min:0',
            'periode'    => 'required|date_format:Y-m-d',
        ]);

        $latest = RisingStar::where('user_id', auth()->id())
            ->where('type_id', 11)
            ->where('periode', $request->periode)
            ->where('is_latest', true)
            ->first();

        if ($latest && $latest->status === 'inactive') {
            return redirect()->back()
                ->with('error', 'Input tidak dapat dilakukan. Periode ini sudah dinonaktifkan oleh admin.');
        }

        $this->upsertRisingStar(11, [
            'real_ratio' => $request->real_ratio,
            'periode'    => $request->periode,
        ]);

        return redirect()->back()
            ->with('success', 'Data realisasi Aosodomoro 0-3 Bulan berhasil disimpan.');
    }

    // ══ AOSODOMORO > 3 Bulan (type_id: 12) ══

    public function aosodomoroAbove3Bulan(Request $request)
    {
        $periodeOptions = RisingStar::where('user_id', auth()->id())
            ->where('type_id', 12)
            ->selectRaw('DATE_FORMAT(periode, "%Y-%m") as periode_ym, MAX(periode) as max_periode')
            ->where('periode', '<=', Carbon::now()->endOfMonth()->format('Y-m-d'))
            ->groupBy('periode_ym')
            ->orderByDesc('max_periode')
            ->pluck('periode_ym');

        $selectedPeriode = $request->filled('selected_periode')
            ? $request->selected_periode
            : Carbon::now()->format('Y-m');

        $periodeDate = $selectedPeriode . '-01';

        $existing = RisingStar::where('user_id', auth()->id())
            ->where('type_id', 12)
            ->where('periode', $periodeDate)
            ->where('is_latest', true)
            ->first();

        $isLocked = $existing && $existing->status === 'inactive';

        $query = RisingStar::with(['user', 'type'])
            ->where('user_id', auth()->id())
            ->where('type_id', 12)
            ->orderBy('created_at', 'desc');

        if ($request->filled('bulan')) $query->whereMonth('periode', $request->bulan);
        if ($request->filled('tahun')) $query->whereYear('periode', $request->tahun);
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('commitment', 'like', '%'.$request->cari.'%')
                ->orWhere('real_ratio', 'like', '%'.$request->cari.'%');
            });
        }

        $history = $query->paginate(20)->withQueryString();

        $tahuns = RisingStar::where('user_id', auth()->id())
            ->where('type_id', 12)
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $selectedBulan = $request->bulan;
        $selectedTahun = $request->tahun;
        $selectedCari  = $request->cari;

        return view('dashboard.sme.aosodomoro-above-3-bulan', compact(
            'history', 'existing', 'isLocked',
            'periodeOptions', 'selectedPeriode',
            'tahuns', 'selectedBulan', 'selectedTahun', 'selectedCari'
        ));
    }

    public function storeAosodomoroAbove3Bulan(Request $request)
    {
        $request->validate([
            'real_ratio' => 'required|numeric|min:0',
            'periode'    => 'required|date_format:Y-m-d',
        ]);

        $latest = RisingStar::where('user_id', auth()->id())
            ->where('type_id', 12)
            ->where('periode', $request->periode)
            ->where('is_latest', true)
            ->first();

        if ($latest && $latest->status === 'inactive') {
            return redirect()->back()
                ->with('error', 'Input tidak dapat dilakukan. Periode ini sudah dinonaktifkan oleh admin.');
        }

        $this->upsertRisingStar(12, [
            'real_ratio' => $request->real_ratio,
            'periode'    => $request->periode,
        ]);

        return redirect()->back()
            ->with('success', 'Data realisasi Aosodomoro Above-3 Bulan berhasil disimpan.');
    }

public function upselling(Request $request)
{
    $selectedPeriode = $request->filled('selected_periode')
        ? $request->selected_periode
        : now()->format('Y-m');

    $periode = $selectedPeriode . '-01';

    // Ambil periode yang statusnya active dari admin
    $periodeOptions = Hsi::where('type', 'Next Level HSI')
    ->whereRaw("(status IS NULL OR status = 'active')")
    ->selectRaw('DATE_FORMAT(periode, "%Y-%m") as periode_ym')
    ->groupBy('periode_ym')
    ->orderByDesc('periode_ym')
    ->pluck('periode_ym');

    // Pastikan periode sekarang selalu ada di opsi
    if (!$periodeOptions->contains(now()->format('Y-m'))) {
        $periodeOptions->prepend(now()->format('Y-m'));
    }

    $existing = Hsi::where('type', 'Next Level HSI')
    ->where('periode', $periode)
    ->orderBy('created_at', 'desc')
    ->first();

    $query = Hsi::with(['user'])
    ->where('user_id', auth()->id())
    ->where('type', 'Next Level HSI')
    ->orderBy('created_at', 'asc');

    if ($request->filled('bulan')) {
        $query->whereMonth('periode', $request->bulan);
    }
    if ($request->filled('tahun')) {
        $query->whereYear('periode', $request->tahun);
    }
    if ($request->filled('cari')) {
        $query->where(function($q) use ($request) {
            $q->where('commitment', 'like', '%'.$request->cari.'%')
              ->orWhere('real_ratio', 'like', '%'.$request->cari.'%');
        });
    }

    $history = $query->paginate(20)->withQueryString();

    $tahuns = Hsi::where('user_id', auth()->id())
        ->where('type', 'Next Level HSI')
        ->selectRaw('YEAR(periode) as tahun')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    $selectedBulan = $request->bulan;
    $selectedTahun = $request->tahun;
    $selectedCari  = $request->cari;

    return view('dashboard.sme.upselling', compact(
        'history', 'existing', 'tahuns',
        'selectedBulan', 'selectedTahun', 'selectedCari',
        'periodeOptions', 'selectedPeriode'
    ));
}

public function storeUpselling(Request $request)
{
    $request->validate([
        'type'       => 'required|string',
        'real_ratio' => 'nullable|numeric|min:0',
        'commitment' => 'nullable|numeric|min:0',
    ]);

    if (!$request->filled('real_ratio') && !$request->filled('commitment')) {
        return redirect()->back()
            ->withErrors(['real_ratio' => 'Komitmen atau Realisasi wajib diisi salah satu.'])
            ->withInput();
    }

    $periode = $request->filled('selected_periode')
    ? $request->selected_periode . '-01'
    : now()->format('Y-m-01');

    $lastCommitment = Hsi::where('type', $request->type)
        ->where('periode', $periode)
        ->whereNotNull('commitment')
        ->orderBy('created_at', 'desc')
        ->value('commitment');

    $lastRealRow = Hsi::where('type', $request->type)
        ->where('periode', $periode)
        ->whereNotNull('real_ratio')
        ->orderBy('created_at', 'desc')
        ->first();

    $commitment    = $request->filled('commitment') ? $request->commitment : $lastCommitment;
    $real          = $request->filled('real_ratio') ? $request->real_ratio : ($lastRealRow->real_ratio ?? null);
    $realUpdatedAt = $request->filled('real_ratio') ? now() : ($lastRealRow->real_updated_at ?? null);

    Hsi::create([
        'user_id'         => auth()->id(),
        'type'            => $request->type,
        'periode'         => $periode,
        'commitment'      => $commitment,
        'real_ratio'      => $real,
        'real_updated_at' => $realUpdatedAt,
    ]);

    return redirect()->route('dashboard.sme.upselling')
        ->with('success', 'Data Upselling berhasil disimpan.');
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
