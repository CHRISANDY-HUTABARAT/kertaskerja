<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function c3mr(Request $request)
    {
        $currentDate = Carbon::now();

        // ← Ambil semua periode yang tersedia, urutkan terbaru
        $periodeOptions = Collection::where('type', 'C3MR')
        ->selectRaw('DATE_FORMAT(periode, "%Y-%m") as periode_ym, MAX(periode) as max_periode')
        ->where('periode', '<=', Carbon::now()->endOfMonth()->format('Y-m-d')) // ← tambahkan ini
        ->groupBy('periode_ym')
        ->orderByDesc('max_periode')
        ->pluck('periode_ym');

        // ← Periode yang dipilih, default ke bulan berjalan
        $selectedPeriode = $request->filled('selected_periode')
            ? $request->selected_periode
            : $currentDate->format('Y-m');

        // ← Parse periode yang dipilih
        [$selYear, $selMonth] = explode('-', $selectedPeriode);

        // ← $comm mengikuti periode yang dipilih
        $comm = Collection::where('type', 'C3MR')
            ->whereYear('periode', $selYear)
            ->whereMonth('periode', $selMonth)
            ->where('is_latest', true)
            ->first();

        $periode = Collection::where('type', 'C3MR')
            ->whereYear('periode', $selYear)
            ->whereMonth('periode', $selMonth)
            ->orderBy('updated_at', 'asc')
            ->first();

        $query = Collection::where('type', 'C3MR')
            ->orderBy('created_at', 'desc');

        if ($request->filled('bulan')) $query->whereMonth('periode', $request->bulan);
        if ($request->filled('tahun')) $query->whereYear('periode', $request->tahun);
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('real_ratio', 'like', '%'.$request->cari.'%')
                ->orWhere('commitment', 'like', '%'.$request->cari.'%');
            });
        }

        $activities = $query->paginate(10)->withQueryString();

        $tahuns = Collection::where('type', 'C3MR')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $selectedBulan   = $request->bulan;
        $selectedTahun   = $request->tahun;
        $selectedCari    = $request->cari;

        return view('dashboard.collection.c3mr', compact(
            'activities', 'comm', 'periode',
            'periodeOptions', 'selectedPeriode',
            'tahuns', 'selectedBulan', 'selectedTahun', 'selectedCari'
        ));
    }

    public function storeC3mrRealisasi(Request $request)
    {
        $request->validate([
            'periode'      => 'required|string',
            'ratio_aktual' => 'required|numeric',
        ]);

        // ← Cek status periode yang akan diisi
        $current = Collection::where('type', 'C3MR')
            ->where('periode', $request->periode)
            ->where('is_latest', true)
            ->first();

        if ($current && $current->status === 'inactive') {
            return back()->with('error', 'Periode ini sudah dinonaktifkan. Realisasi tidak dapat disimpan.');
        }

        $lastCommitment = $current?->commitment;

        DB::transaction(function () use ($request, $lastCommitment) {
            Collection::where('type', 'C3MR')
                ->where('periode', $request->periode)
                ->update(['is_latest' => false]);

            Collection::create([
                'user_id'         => Auth::id(),
                'type'            => 'C3MR',
                'periode'         => $request->periode,
                'is_latest'       => true,
                'commitment'      => $lastCommitment,
                'real_ratio'      => $request->ratio_aktual,
                'real_updated_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', 'C3MR Realisasi berhasil disimpan');
    }

    public function billing(Request $request)
    {
        $currentDate = Carbon::now();

        $periodeOptions = Collection::where('type', 'Billing Perdana')
            ->selectRaw('DATE_FORMAT(periode, "%Y-%m") as periode_ym, MAX(periode) as max_periode')
            ->where('periode', '<=', Carbon::now()->endOfMonth()->format('Y-m-d'))
            ->groupBy('periode_ym')
            ->orderByDesc('max_periode')
            ->pluck('periode_ym');

        $selectedPeriode = $request->filled('selected_periode')
            ? $request->selected_periode
            : $currentDate->format('Y-m');

        [$selYear, $selMonth] = explode('-', $selectedPeriode);

        $bill = Collection::where('type', 'Billing Perdana')
            ->whereYear('periode', $selYear)
            ->whereMonth('periode', $selMonth)
            ->where('is_latest', true)
            ->first();

        $periode = Collection::where('type', 'Billing Perdana')
            ->whereYear('periode', $selYear)
            ->whereMonth('periode', $selMonth)
            ->orderBy('updated_at', 'asc')
            ->first();

        $query = Collection::where('type', 'Billing Perdana')
            ->orderBy('created_at', 'desc');

        if ($request->filled('bulan')) $query->whereMonth('periode', $request->bulan);
        if ($request->filled('tahun')) $query->whereYear('periode', $request->tahun);
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('real_ratio', 'like', '%'.$request->cari.'%')
                ->orWhere('commitment', 'like', '%'.$request->cari.'%');
            });
        }

        $activities = $query->paginate(10)->withQueryString();

        $tahuns = Collection::where('type', 'Billing Perdana')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $selectedBulan = $request->bulan;
        $selectedTahun = $request->tahun;
        $selectedCari  = $request->cari;

        return view('dashboard.collection.billing', compact(
            'activities', 'bill', 'periode',
            'periodeOptions', 'selectedPeriode',
            'tahuns', 'selectedBulan', 'selectedTahun', 'selectedCari'
        ));
    }

    public function storeBillingRealisasi(Request $request)
    {
        $request->validate([
            'periode'      => 'required|string',
            'ratio_aktual' => 'required|numeric',
        ]);

        $current = Collection::where('type', 'Billing Perdana')
            ->where('periode', $request->periode)
            ->where('is_latest', true)
            ->first();

        if ($current && $current->status === 'inactive') {
            return back()->with('error', 'Periode ini sudah dinonaktifkan. Realisasi tidak dapat disimpan.');
        }

        $lastCommitment = $current?->commitment;

        DB::transaction(function () use ($request, $lastCommitment) {
            Collection::where('type', 'Billing Perdana')
                ->where('periode', $request->periode)
                ->update(['is_latest' => false]);

            Collection::create([
                'user_id'         => Auth::id(),
                'type'            => 'Billing Perdana',
                'periode'         => $request->periode,
                'is_latest'       => true,
                'commitment'      => $lastCommitment,
                'real_ratio'      => $request->ratio_aktual,
                'real_updated_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Billing Perdana Realisasi berhasil disimpan');
    }

    public function cr(Request $request)
    {
        $currentDate = Carbon::now();

        // ← Ambil semua periode yang tersedia, filter bulan depan ke atas
        $periodeOptions = Collection::where('type', 'Collection Ratio')
            ->selectRaw('DATE_FORMAT(periode, "%Y-%m") as periode_ym, MAX(periode) as max_periode')
            ->where('periode', '<=', Carbon::now()->endOfMonth()->format('Y-m-d'))
            ->groupBy('periode_ym')
            ->orderByDesc('max_periode')
            ->pluck('periode_ym');

        $selectedPeriode = $request->filled('selected_periode')
            ? $request->selected_periode
            : $currentDate->format('Y-m');

        [$selYear, $selMonth] = explode('-', $selectedPeriode);

        // ← latestSeg dan lockedSegments mengikuti periode yang dipilih
        $latestSeg = Collection::where('type', 'Collection Ratio')
            ->where('is_latest', true)
            ->whereYear('periode', $selYear)
            ->whereMonth('periode', $selMonth)
            ->get();

        $lockedSegments = Collection::where('type', 'Collection Ratio')
            ->where('is_latest', true)
            ->where('status', 'inactive')
            ->whereYear('periode', $selYear)
            ->whereMonth('periode', $selMonth)
            ->pluck('segment')
            ->toArray();

        $query = Collection::where('type', 'Collection Ratio')
            ->orderBy('created_at', 'desc');

        if ($request->filled('segment')) $query->where('segment', $request->segment);
        if ($request->filled('bulan'))   $query->whereMonth('periode', $request->bulan);
        if ($request->filled('tahun'))   $query->whereYear('periode', $request->tahun);
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('segment', 'like', '%'.$request->cari.'%')
                ->orWhere('real_ratio', 'like', '%'.$request->cari.'%')
                ->orWhere('commitment', 'like', '%'.$request->cari.'%');
            });
        }

        $collections = $query->paginate(10)->withQueryString();

        $segments = Collection::where('type', 'Collection Ratio')
            ->whereNotNull('segment')->distinct()->orderBy('segment')->pluck('segment');

        $tahuns = Collection::where('type', 'Collection Ratio')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $selectedSegment = $request->segment;
        $selectedBulan   = $request->bulan;
        $selectedTahun   = $request->tahun;
        $selectedCari    = $request->cari;

        return view('dashboard.collection.collectionRatio', compact(
            'collections', 'segments', 'tahuns', 'latestSeg', 'lockedSegments',
            'periodeOptions', 'selectedPeriode',
            'selectedSegment', 'selectedBulan', 'selectedTahun', 'selectedCari'
        ));
    }

    public function storeCrRealisasi(Request $request)
    {
        $request->validate([
            'status'     => 'required|in:active,inactive',
            'periode'    => 'required|date_format:Y-m',
            'segment'    => 'required|string',
            'real_ratio' => 'nullable|string',
        ]);

        $periodeDate = $request->periode . '-01';

         // ← Cek apakah segment ini inactive untuk periode yang diminta
        $isLocked = Collection::where('type', 'Collection Ratio')
            ->where('segment', $request->segment)
            ->where('periode', $periodeDate)
            ->where('is_latest', true)
            ->where('status', 'inactive')
            ->exists();

        if ($isLocked) {
            return back()
                ->withInput()
                ->with('error', 'Segment ' . $request->segment . ' sudah dinonaktifkan untuk periode ' . \Carbon\Carbon::parse($periodeDate)->translatedFormat('F Y') . '.');
        }

        // ← Ambil commitment dari is_latest, scope per segment
        $lastCommitment = Collection::where('type', 'Collection Ratio')
            ->where('segment', $request->segment)
            ->where('periode', $periodeDate)
            ->where('is_latest', true)
            ->value('commitment');

        DB::transaction(function () use ($request, $periodeDate, $lastCommitment) {
            Collection::where('type', 'Collection Ratio')
                ->where('segment', $request->segment)
                ->where('periode', $periodeDate)
                ->update(['is_latest' => false]);

            Collection::create([
                'user_id'         => Auth::id(),
                'type'            => 'Collection Ratio',
                'segment'         => $request->segment,
                'periode'         => $periodeDate,
                'status'          => $request->status,
                'is_latest'       => true,
                'commitment'      => $lastCommitment,
                'real_ratio'      => $request->real_ratio,
                'real_updated_at' => now(),
            ]);
        });

        return back()->with('success', 'Data berhasil disimpan');
    }

    public function utip(Request $request)
    {
        $currentDate = Carbon::now();

        $utips = Collection::where('type', 'like', '%UTIP%')
            ->where('is_latest', true)
            ->where('status', 'active')
            ->whereYear('periode', $currentDate->year)
            ->whereMonth('periode', $currentDate->month)
            ->orderByRaw("CASE WHEN type LIKE '%Corrective%' THEN 0 ELSE 1 END")
            ->orderBy('type')
            ->get();

        $lockedTypes = Collection::where('type', 'like', '%UTIP%')
            ->where('is_latest', true)
            ->where('status', 'inactive')
            ->pluck('type')
            ->toArray();

        $query = Collection::where('type', 'like', '%UTIP%')
            ->orderBy('created_at', 'desc');

        if ($request->filled('tipe'))  $query->where('type', $request->tipe);
        if ($request->filled('bulan')) $query->whereMonth('periode', $request->bulan); // ← fix: periode
        if ($request->filled('tahun')) $query->whereYear('periode', $request->tahun);  // ← fix: periode
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('type', 'like', '%'.$request->cari.'%')
                ->orWhere('real_ratio', 'like', '%'.$request->cari.'%')
                ->orWhere('commitment', 'like', '%'.$request->cari.'%');
            });
        }

        $activities = $query->paginate(10)->withQueryString();

        $tahuns = Collection::where('type', 'like', '%UTIP%')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $tipes = Collection::where('type', 'like', '%UTIP%')
            ->distinct()->orderBy('type')->pluck('type');

        $selectedTipe  = $request->tipe;
        $selectedBulan = $request->bulan;
        $selectedTahun = $request->tahun;
        $selectedCari  = $request->cari;

        return view('dashboard.collection.utip', compact(
            'activities', 'utips', 'lockedTypes',
            'tahuns', 'tipes', 'selectedTipe', 'selectedBulan', 'selectedTahun', 'selectedCari'
        ));
    }

    public function storeUtipRealisasi(Request $request)
    {
        $request->validate([
            'status'       => 'required|in:active,inactive',
            'periode'      => 'required|date_format:Y-m',
            'type'         => 'required|string',
            'file'         => 'required|file|max:10240',
            'kondisi'      => 'required|array|size:7',
            'kondisi.*'    => 'required|string',
            'plan'         => 'required|array|size:7',
            'plan.*'       => 'nullable|numeric',
            'real_ratio'   => 'required|array|size:7',
            'real_ratio.*' => 'nullable|numeric',
            'ol_fm'        => 'required|array|size:7',
            'ol_fm.*'      => 'nullable|numeric',
        ]);

        $periodeDate = $request->periode . '-01';

        $isUpdate = Collection::where('type', $request->type)
            ->where('periode', $periodeDate)
            ->where('is_latest', true)
            ->exists();

        // Cek status locked
        $latest = Collection::where('type', $request->type)
            ->where('is_latest', true)
            ->first();

        if ($latest && $latest->status === 'inactive') {
            return back()->with('error', 'Tipe UTIP ini sudah dinonaktifkan. Realisasi tidak dapat disimpan.');
        }

        $submitToken = \Illuminate\Support\Str::uuid()->toString();
        $filePath = null;
        $fileName = null;
        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->store('utip_files', 'public');
        }

        foreach ($request->kondisi as $idx => $kondisiName) {
            $existing = Collection::where('type', $request->type)
                ->where('periode', $periodeDate)
                ->where('kondisi', $kondisiName)
                ->orderBy('created_at', 'desc')
                ->first();

            $planVal   = ($request->plan[$idx] !== null && $request->plan[$idx] !== '') ? $request->plan[$idx] : null;
            $realVal   = ($request->real_ratio[$idx] !== null && $request->real_ratio[$idx] !== '') ? $request->real_ratio[$idx] : null;
            $olFmVal   = ($request->ol_fm[$idx] !== null && $request->ol_fm[$idx] !== '') ? $request->ol_fm[$idx] : null;

            if (is_null($planVal) && is_null($realVal) && is_null($olFmVal)) {
                continue;
            }

            Collection::create([
                'user_id'         => Auth::id(),
                'type'            => $request->type,
                'kondisi'         => $kondisiName,
                'periode'         => $periodeDate,
                'status'          => $request->status,
                'is_latest'       => true,
                'plan'            => $planVal ?? ($existing->plan ?? null),
                'ol_fm'           => $olFmVal ?? ($existing->ol_fm ?? null),
                'real_ratio'      => $realVal ?? ($existing->real_ratio ?? null),
                'real_updated_at' => !is_null($realVal) ? now() : ($existing->real_updated_at ?? null),
                'file_path'       => $filePath,
                'file_name'       => $fileName,
                'submit_token'    => $submitToken,
            ]);
        }

        return back()->with('success', 'Data UTIP berhasil disimpan');
    }

    public function ar(Request $request)
    {
        $currentDate = Carbon::now();

        $ars = Collection::where('type', 'like', '%ar%')
            ->where('is_latest', true)
            ->where('status', 'active')
            ->whereYear('periode', $currentDate->year)
            ->whereMonth('periode', $currentDate->month)
            ->orderBy('type')
            ->get();

        $lockedTypes = Collection::where('type', 'like', '%ar%')
            ->where('is_latest', true)
            ->where('status', 'inactive')
            ->pluck('type')
            ->toArray();

        $query = Collection::where('type', 'like', '%ar%')
            ->orderBy('created_at', 'desc');

        if ($request->filled('tipe'))  $query->where('type', $request->tipe);
        if ($request->filled('bulan')) $query->whereMonth('periode', $request->bulan); // ← fix: periode
        if ($request->filled('tahun')) $query->whereYear('periode', $request->tahun);  // ← fix: periode
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('type', 'like', '%'.$request->cari.'%')
                ->orWhere('real_ratio', 'like', '%'.$request->cari.'%')
                ->orWhere('commitment', 'like', '%'.$request->cari.'%');
            });
        }

        $activities = $query->paginate(10)->withQueryString();

        $tahuns = Collection::where('type', 'like', '%ar%')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $tipes = Collection::where('type', 'like', '%ar%')
            ->distinct()->orderBy('type')->pluck('type');

        $selectedTipe  = $request->tipe;
        $selectedBulan = $request->bulan;
        $selectedTahun = $request->tahun;
        $selectedCari  = $request->cari;

        return view('dashboard.collection.ar', compact(
            'activities', 'ars', 'lockedTypes',
            'tahuns', 'tipes', 'selectedTipe', 'selectedBulan', 'selectedTahun', 'selectedCari'
        ));
    }

    public function storeArRealisasi(Request $request)
    {
        $request->validate([
            'status'       => 'required|in:active,inactive',
            'periode'      => 'required|date_format:Y-m',
            'type'         => 'required|string',
            'file'         => 'required|file|max:10240',
            'kondisi'      => 'required|array|size:8',
            'kondisi.*'    => 'required|string',
            'real_ratio'   => 'required|array|size:8',
            'real_ratio.*' => 'nullable|numeric',
        ]);

        $periodeDate = $request->periode . '-01';

        $isUpdate = Collection::where('type', $request->type)
            ->where('periode', $periodeDate)
            ->where('is_latest', true)
            ->exists();

        // Cek status locked
        $latest = Collection::where('type', $request->type)
            ->where('is_latest', true)
            ->first();

        if ($latest && $latest->status === 'inactive') {
            return back()->with('error', 'Tipe AR ini sudah dinonaktifkan. Realisasi tidak dapat disimpan.');
        }

        $submitToken = \Illuminate\Support\Str::uuid()->toString();
        $filePath = null;
        $fileName = null;
        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->store('ar_files', 'public');
        }

        foreach ($request->kondisi as $idx => $kondisiName) {
            $existing = Collection::where('type', $request->type)
                ->where('periode', $periodeDate)
                ->where('kondisi', $kondisiName)
                ->orderBy('created_at', 'desc')
                ->first();

            $realVal   = ($request->real_ratio[$idx] !== null && $request->real_ratio[$idx] !== '') ? $request->real_ratio[$idx] : null;

            if (is_null($realVal)) {
                continue;
            }

            Collection::create([
                'user_id'         => Auth::id(),
                'type'            => $request->type,
                'segment'         => $request->segment,
                'kondisi'         => $kondisiName,
                'periode'         => $periodeDate,
                'status'          => $request->status,
                'is_latest'       => true,
                'real_ratio'      => $realVal ?? ($existing->real_ratio ?? null),
                'real_updated_at' => !is_null($realVal) ? now() : ($existing->real_updated_at ?? null),
                'file_path'       => $filePath,
                'file_name'       => $fileName,
                'submit_token'    => $submitToken,
            ]);
        }

        return back()->with('success', 'Data AR berhasil disimpan');
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
