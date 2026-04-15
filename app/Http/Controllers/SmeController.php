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
    public function index()
    {
        //
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
            $query->orderBy('am', 'asc')->orderBy('no', 'asc');
        }, 'data.funnel.todayProgress'])
        ->where('type', 'on-hand')
        ->where('segment', 'sme')
        ->where('periode', $currentPeriodeDate)
        ->latest()
        ->first();
            // dd($latestImport);

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
            $query->orderBy('am', 'asc')->orderBy('no', 'asc');
        }, 'data.funnel.todayProgress'])
        ->where('type', 'on-hand')
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
            $query->orderBy('am', 'asc')->orderBy('no', 'asc');
        }, 'data.funnel.todayProgress'])
        ->where('type', 'on-hand')
        ->where('segment', 'sme')
        ->where('periode', $currentPeriodeDate)
        ->latest()
        ->first();

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

        $rows = ScallingData::with(['funnel.todayProgress'])
            ->whereHas('scallingImport', function ($query) use ($currentPeriodeDate) {
                $query->where('type', 'initiate')
                    // ->where('status', 'active')
                    ->where('segment', 'sme')
                    ->where('periode', $currentPeriodeDate);
            })
            ->get()
            ->filter(fn($item) => strtoupper(trim($item->no ?? '')) !== 'TOTAL');

        $latestImport = ScallingImport::with(['data' => function($query) {
            $query->orderBy('am', 'asc')->orderBy('no', 'asc');
        }, 'data.funnel.todayProgress'])
        ->where('type', 'on-hand')
        ->where('segment', 'sme')
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
            'id_lop'                   => 'required|string|max:100',
            'cc'                       => 'required|string|max:100',
            'nipnas'                   => 'required|string|max:50',
            'am'                       => 'required|string|max:100',
            'mitra'                    => 'nullable|string|max:255',
            'plan_bulan_billcomp_2025' => 'required|integer|min:1|max:12',
            'est_nilai_bc'             => 'required|numeric|min:0',
        ], [
            'status.required'                   => 'Status wajib diisi',
            'status.in'                         => 'Status harus berupa "active" atau "inactive"',
            'periode.required'                  => 'Periode wajib diisi',
            'periode.date_format'               => 'Format periode harus berupa bulan dan tahun (contoh: 2025-03)',
            'project.required'                  => 'Nama project wajib diisi',
            'project.max'                       => 'Nama project maksimal 255 karakter',
            'id_lop.required'                   => 'ID LOP wajib diisi',
            'id_lop.max'                        => 'ID LOP maksimal 100 karakter',
            'cc.required'                       => 'CC wajib diisi',
            'cc.max'                            => 'CC maksimal 100 karakter',
            'nipnas.required'                   => 'NIPNAS wajib diisi',
            'nipnas.max'                        => 'NIPNAS maksimal 50 karakter',
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
        $funnel->{$request->field} = $value;

        if ($request->field === 'delivery_billing_complete') {
            $funnel->delivery_nilai_billcomp = $value && is_numeric($rawEst) ? (float) $rawEst : null;

            $autoFields = collect($funnel->getCasts())
                ->filter(fn($c, $k) => $c === 'boolean' && $k !== 'delivery_billing_complete' && $k !== 'cancel')
                ->keys()
                ->toArray();

            foreach ($autoFields as $fld) {
                $funnel->{$fld} = $value;
            }
        }

        if ($request->field === 'cancel' && $value === true) {
            $funnel->delivery_billing_complete = 0;
            $funnel->delivery_nilai_billcomp   = null;
        }

        $funnel->save();

        $taskProgress = \App\Models\TaskProgress::firstOrCreate([
            'task_id' => $funnel->id,
            'user_id' => auth()->id(),
            'tanggal' => today(),
        ]);

        $taskProgress->{$request->field} = $value;

        if ($request->field === 'delivery_billing_complete') {
            $taskProgress->delivery_nilai_billcomp = $value && is_numeric($rawEst) ? (float) $rawEst : null;

            foreach ($autoFields as $fld) {
                $taskProgress->{$fld} = $value;
            }
        }

        // ← TAMBAHAN: jika cancel di-set true, reset billing complete dan nilainya
        if ($request->field === 'cancel' && $value === true) {
            $taskProgress->delivery_billing_complete = 0;
            $taskProgress->delivery_nilai_billcomp   = null;
        }

        $taskProgress->save();

        $dataId   = $request->data_id;
        $funnel   = \App\Models\FunnelTracking::where('data_id', $dataId)->first();
        $periodeImport = \App\Models\ScallingData::find($dataId)?->scallingImport;

        $dataIdsInPeriode = collect();
        if ($periodeImport) {
            $dataIdsInPeriode = \App\Models\ScallingData::where('imports_log_id', $periodeImport->id)
                ->pluck('id');
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
            'auto_value'     => $request->field === 'delivery_billing_complete' ? $value : null,
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
    $periode = now()->format('Y-m-01');

    $existing = Hsi::where('user_id', auth()->id())
        ->where('type', 'Next Level HSI')
        ->where('periode', $periode)
        ->orderBy('created_at', 'desc')
        ->first();

    $query = Hsi::with(['user'])
        ->where('user_id', auth()->id())
        ->where('type', 'Next Level HSI')
        ->orderBy('created_at', 'desc');

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
        'selectedBulan', 'selectedTahun', 'selectedCari'
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

    $periode = now()->format('Y-m-01');

    $lastCommitment = Hsi::where('user_id', auth()->id())
        ->where('type', $request->type)
        ->where('periode', $periode)
        ->whereNotNull('commitment')
        ->orderBy('created_at', 'desc')
        ->value('commitment');

    $lastRealRow = Hsi::where('user_id', auth()->id())
        ->where('type', $request->type)
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
