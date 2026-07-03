<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Ctc;
use App\Models\Ct0;
use App\Models\User;
use App\Models\Psak;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.index');
    }

    public function collectionRatioTable(Request $request)
    {
        $query = Collection::with('user')
            ->where('type', 'Collection Ratio')
            ->orderBy('created_at', 'desc');

        if ($request->filled('segment')) $query->where('segment', $request->segment);
        if ($request->filled('bulan'))   $query->whereMonth('periode', $request->bulan);
        if ($request->filled('tahun'))   $query->whereYear('periode', $request->tahun);
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('real_ratio', 'like', '%'.$request->cari.'%')
                ->orWhere('commitment', 'like', '%'.$request->cari.'%')
                ->orWhere('segment', 'like', '%'.$request->cari.'%');
            });
        }

        $collections = $query->paginate(20)->withQueryString();

       
        $ringkasanAll = Collection::where('type', 'Collection Ratio')
            ->where('is_latest', true)
            ->orderByDesc('periode')
            ->orderBy('segment')
            ->get();

        $tahuns = Collection::where('type', 'Collection Ratio')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $segments = Collection::where('type', 'Collection Ratio')
            ->whereNotNull('segment')->distinct()->orderBy('segment')->pluck('segment');

        $users = Collection::with('user')
            ->where('type', 'Collection Ratio')->get()
            ->pluck('user')->filter()->unique('id')->values();

        $selectedSegment = $request->segment;
        $selectedBulan   = $request->bulan;
        $selectedTahun   = $request->tahun;
        $selectedCari    = $request->cari;

        return view('admin.collection.collectionRatio', compact(
            'collections', 'ringkasanAll', 'users', 'tahuns', 'segments',
            'selectedSegment', 'selectedBulan', 'selectedTahun', 'selectedCari'
        ));
    }

    public function collectionRatioStore(Request $request)
    {
        $request->validate([
            'status'     => 'required|in:active,inactive',
            'periode'    => 'required|date_format:Y-m',
            'segment'    => 'required|string',   
            'commitment' => 'nullable|string',
            'real_ratio' => 'nullable|string',
        ]);

        $periodeDate = $request->periode . '-01';

        $lastCommitment = $request->filled('commitment')
            ? $request->commitment
            : Collection::where('type', 'Collection Ratio')
                ->where('segment', $request->segment)
                ->where('periode', $periodeDate)
                ->where('is_latest', true)       // ← ganti orderBy+first
                ->value('commitment');

        $lastRealRow = Collection::where('type', 'Collection Ratio')
            ->where('segment', $request->segment)  // ← tambah scope segment
            ->where('periode', $periodeDate)
            ->where('is_latest', true)
            ->first();

        $lastReal          = $request->filled('real_ratio') ? $request->real_ratio : ($lastRealRow->real_ratio ?? null);
        $lastRealUpdatedAt = $request->filled('real_ratio') ? now() : ($lastRealRow->real_updated_at ?? null);

        DB::transaction(function () use ($request, $periodeDate, $lastCommitment, $lastReal, $lastRealUpdatedAt) {
           
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
                'real_ratio'      => $lastReal,
                'real_updated_at' => $lastRealUpdatedAt,
            ]);
        });

        return back()->with('success', 'Data berhasil disimpan');
    }

    public function cycTable(Request $request)
{
    $query = Collection::with('user')
        ->where('type', 'CYC')
        ->orderBy('created_at', 'desc');

    if ($request->filled('segment')) $query->where('segment', $request->segment);
    if ($request->filled('bulan'))   $query->whereMonth('periode', $request->bulan);
    if ($request->filled('tahun'))   $query->whereYear('periode', $request->tahun);
    if ($request->filled('cari')) {
        $query->where(function($q) use ($request) {
            $q->where('real_ratio', 'like', '%'.$request->cari.'%')
              ->orWhere('commitment', 'like', '%'.$request->cari.'%')
              ->orWhere('segment', 'like', '%'.$request->cari.'%');
        });
    }

    $collections = $query->paginate(20)->withQueryString();

    $ringkasanAll = Collection::where('type', 'CYC')
        ->where('is_latest', true)
        ->orderByDesc('periode')
        ->orderBy('segment')
        ->get();

    $tahuns = Collection::where('type', 'CYC')
        ->selectRaw('YEAR(periode) as tahun')
        ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

    $segments = Collection::where('type', 'CYC')
        ->whereNotNull('segment')->distinct()->orderBy('segment')->pluck('segment');

    $users = Collection::with('user')
        ->where('type', 'CYC')->get()
        ->pluck('user')->filter()->unique('id')->values();

    $selectedSegment = $request->segment;
    $selectedBulan   = $request->bulan;
    $selectedTahun   = $request->tahun;
    $selectedCari    = $request->cari;

    return view('admin.collection.cyc', compact(
        'collections', 'ringkasanAll', 'users', 'tahuns', 'segments',
        'selectedSegment', 'selectedBulan', 'selectedTahun', 'selectedCari'
    ));
}

    public function cycStore(Request $request)
    {
        $request->validate([
            'status'     => 'required|in:active,inactive',
            'periode'    => 'required|date_format:Y-m',
            'segment'    => 'required|string',
            'commitment' => 'nullable|string',
            'real_ratio' => 'nullable|string',
        ]);

        $periodeDate = $request->periode . '-01';

        $lastCommitment = $request->filled('commitment')
            ? $request->commitment
            : Collection::where('type', 'CYC')
                ->where('segment', $request->segment)
                ->where('periode', $periodeDate)
                ->where('is_latest', true)
                ->value('commitment');

        $lastRealRow = Collection::where('type', 'CYC')
            ->where('segment', $request->segment)
            ->where('periode', $periodeDate)
            ->where('is_latest', true)
            ->first();

        $lastReal          = $request->filled('real_ratio') ? $request->real_ratio : ($lastRealRow->real_ratio ?? null);
        $lastRealUpdatedAt = $request->filled('real_ratio') ? now() : ($lastRealRow->real_updated_at ?? null);

        DB::transaction(function () use ($request, $periodeDate, $lastCommitment, $lastReal, $lastRealUpdatedAt) {
            Collection::where('type', 'CYC')
                ->where('segment', $request->segment)
                ->where('periode', $periodeDate)
                ->update(['is_latest' => false]);

            Collection::create([
                'user_id'         => Auth::id(),
                'type'            => 'CYC',
                'segment'         => $request->segment,
                'periode'         => $periodeDate,
                'status'          => $request->status,
                'is_latest'       => true,
                'commitment'      => $lastCommitment,
                'real_ratio'      => $lastReal,
                'real_updated_at' => $lastRealUpdatedAt,
            ]);
        });

        return back()->with('success', 'Data CYC berhasil disimpan');
    }

    public function c3mrTable(Request $request)
    {
        $query = Collection::with('user')
            ->where('type', 'C3MR')
            ->orderBy('created_at', 'desc');

        if ($request->filled('bulan')) $query->whereMonth('periode', $request->bulan);
        if ($request->filled('tahun')) $query->whereYear('periode', $request->tahun);
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('real_ratio', 'like', '%'.$request->cari.'%')
                ->orWhere('commitment', 'like', '%'.$request->cari.'%');
            });
        }

        $collections = $query->paginate(20)->withQueryString();

        
        $ringkasanAll = Collection::where('type', 'C3MR')
            ->where('is_latest', true)
            ->orderByDesc('periode')
            ->get();

        

        $tahuns = Collection::where('type', 'C3MR')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $users = Collection::with('user')
            ->where('type', 'C3MR')->get()
            ->pluck('user')->filter()->unique('id')->values();

        $selectedBulan = $request->bulan;
        $selectedTahun = $request->tahun;
        $selectedCari  = $request->cari;

        return view('admin.collection.c3mr', compact(
            'collections', 'ringkasanAll', 'users', 'tahuns',
            'selectedBulan', 'selectedTahun', 'selectedCari'
        ));
    }

    public function c3mrStore(Request $request)
    {
        $request->validate([
            'status'     => 'required|in:active,inactive',
            'periode'    => 'required|date_format:Y-m',
            'commitment' => 'nullable|string',
            'real_ratio' => 'nullable|string',
        ]);

        $periodeDate = $request->periode . '-01';

        $lastCommitment = $request->filled('commitment')
            ? $request->commitment
            : Collection::where('type', 'C3MR')
                ->where('periode', $periodeDate)
                ->whereNotNull('commitment')
                ->orderBy('created_at', 'desc')
                ->value('commitment');

        $lastRealRow = Collection::where('type', 'C3MR')
            ->where('periode', $periodeDate)
            ->whereNotNull('real_ratio')
            ->orderBy('created_at', 'desc')
            ->first();

        $lastReal          = $request->filled('real_ratio') ? $request->real_ratio : ($lastRealRow->real_ratio ?? null);
        $lastRealUpdatedAt = $request->filled('real_ratio') ? now() : ($lastRealRow->real_updated_at ?? null);

        DB::transaction(function () use ($request, $periodeDate, $lastCommitment, $lastReal, $lastRealUpdatedAt) {
          
            Collection::where('type', 'C3MR')
                ->where('periode', $periodeDate)
                ->update(['is_latest' => false]);

            Collection::create([
                'user_id'         => Auth::id(),
                'type'            => 'C3MR',
                'periode'         => $periodeDate,
                'status'          => $request->status,
                'is_latest'       => true,
                'commitment'      => $lastCommitment,
                'real_ratio'      => $lastReal,
                'real_updated_at' => $lastRealUpdatedAt,
            ]);
        });

        return back()->with('success', 'Data berhasil disimpan');
    }

    /**
     * Flip active/inactive status for a given collection record.
     */
    public function toggleCollectionStatus($id)
    {
        $collection = Collection::findOrFail($id);

      
        abort_if(!$collection->is_latest, 403, 'Hanya record terbaru yang bisa diubah statusnya.');

        $collection->status = $collection->status === 'active' ? 'inactive' : 'active';
        $collection->save();

        return back()->with('success', 'Status berhasil diubah');
    }

    public function billingTable(Request $request)
    {
        $query = Collection::with('user')
            ->where('type', 'Billing Perdana')
            ->orderBy('created_at', 'desc');

        if ($request->filled('bulan')) $query->whereMonth('periode', $request->bulan);
      
        if ($request->filled('tahun')) $query->whereYear('periode', $request->tahun);
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('real_ratio', 'like', '%'.$request->cari.'%')
                ->orWhere('commitment', 'like', '%'.$request->cari.'%');
            });
        }

        $collections = $query->paginate(20)->withQueryString();

        $ringkasanAll = Collection::where('type', 'Billing Perdana')
            ->where('is_latest', true)
            ->orderByDesc('periode')
            ->get();

        $tahuns = Collection::where('type', 'Billing Perdana')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $users = Collection::with('user')
            ->where('type', 'Billing Perdana')->get()
            ->pluck('user')->filter()->unique('id')->values();

        $selectedBulan = $request->bulan;
        $selectedTahun = $request->tahun;
        $selectedCari  = $request->cari;

        return view('admin.collection.billingPerdana', compact(
            'collections', 'ringkasanAll', 'users', 'tahuns',
            'selectedBulan', 'selectedTahun', 'selectedCari'
        ));
    }
    public function billingStore(Request $request)
    {
        $request->validate([
            'status'     => 'required|in:active,inactive',
            'periode'    => 'required|date_format:Y-m',
            'commitment' => 'nullable|string',
            'real_ratio' => 'nullable|string',
        ]);

        $periodeDate = $request->periode . '-01';

        $lastCommitment = $request->filled('commitment')
            ? $request->commitment
            : Collection::where('type', 'Billing Perdana')
                ->where('periode', $periodeDate)
                ->where('is_latest', true)
                ->value('commitment');

        $lastRealRow = Collection::where('type', 'Billing Perdana')
            ->where('periode', $periodeDate)
            ->where('is_latest', true)
            ->first();

        $lastReal          = $request->filled('real_ratio') ? $request->real_ratio : ($lastRealRow->real_ratio ?? null);
        $lastRealUpdatedAt = $request->filled('real_ratio') ? now() : ($lastRealRow->real_updated_at ?? null);

        DB::transaction(function () use ($request, $periodeDate, $lastCommitment, $lastReal, $lastRealUpdatedAt) {
            Collection::where('type', 'Billing Perdana')
                ->where('periode', $periodeDate)
                ->update(['is_latest' => false]);

            Collection::create([
                'user_id'         => Auth::id(),
                'type'            => 'Billing Perdana',
                'periode'         => $periodeDate,
                'status'          => $request->status,
                'is_latest'       => true,
                'commitment'      => $lastCommitment,
                'real_ratio'      => $lastReal,
                'real_updated_at' => $lastRealUpdatedAt,
            ]);
        });

        return back()->with('success', 'Data berhasil disimpan');
    }

    public function utipTable(Request $request)
    {
        // Ambil submit_token terbaru
        $query = Collection::with('user')
            ->where('type', 'like', '%UTIP%')
            ->orderBy('created_at', 'desc');

        // if ($request->filled('user'))  $query->where('user_id', $request->user);
        if ($request->filled('tipe'))  $query->where('type', $request->tipe);
        if ($request->filled('bulan')) $query->whereMonth('periode', $request->bulan); 
        if ($request->filled('tahun')) $query->whereYear('periode', $request->tahun);  
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('type', 'like', '%'.$request->cari.'%')
                ->orWhere('real_ratio', 'like', '%'.$request->cari.'%')
                ->orWhere('commitment', 'like', '%'.$request->cari.'%');
            });
        }

        $collections = $query->paginate(20)->withQueryString();

       
        $ringkasanAll = Collection::where('type', 'like', '%UTIP%')
            ->where('is_latest', true)
            ->orderByRaw("CASE WHEN type LIKE '%Corrective%' THEN 0 ELSE 1 END")
            ->orderBy('type')
            ->get();

        
        $tahuns = Collection::where('type', 'like', '%UTIP%')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $tipes = Collection::where('type', 'like', '%UTIP%')
            ->distinct()
            ->orderByRaw("CASE WHEN type LIKE '%Corrective%' THEN 0 ELSE 1 END")
            ->orderBy('type')->pluck('type');

        $users = Collection::with('user')
            ->where('type', 'like', '%UTIP%')->get()
            ->pluck('user')->filter()->unique('id')->values();

        $selectedTipe  = $request->tipe;
        $selectedBulan = $request->bulan;
        $selectedTahun = $request->tahun;
        $selectedCari  = $request->cari;

        return view('admin.collection.utip', compact(
            'collections', 'ringkasanAll', 'users', 'tahuns', 'tipes',
            'selectedTipe', 'selectedBulan', 'selectedTahun', 'selectedCari'
        ));
    }

    public function utipStore(Request $request)
    {
        $request->validate([
            'status'  => 'required|in:active,inactive',
            'periode' => 'required|date_format:Y-m',
            'type'    => 'required|string',
            'file'    => 'required|file|max:51200',

            
            'kondisi'   => 'required|array|size:7',
            'kondisi.*' => 'required|string',
            'plan'      => 'required|array|size:7',
            'plan.*'    => 'nullable|numeric',
            'real_ratio'=> 'required|array|size:7',
            'real_ratio.*' => 'nullable|numeric',
            'ol_fm'     => 'required|array|size:7',
            'ol_fm.*'   => 'nullable|numeric',
            ], [
    'file.max' => 'Ukuran file maksimal 50 MB.',
        ]);

        $periodeDate = $request->periode . '-01';

        $isUpdate = Collection::where('type', $request->type)
            ->where('periode', $periodeDate)
            ->where('is_latest', true)
            ->exists();

        // Upload file
        $submitToken = \Illuminate\Support\Str::uuid()->toString();
        $filePath = null;
        $fileName = null;
        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->store('utip_files', 'public');
        }

        // Simpan per kondisi
        foreach ($request->kondisi as $idx => $kondisiName) {
            $existing = Collection::where('type', $request->type)
                ->where('periode', $periodeDate)
                ->where('kondisi', $kondisiName)
                ->orderBy('created_at', 'desc')
                ->first();

            $plan      = ($request->plan[$idx] !== null && $request->plan[$idx] !== '')
                            ? $request->plan[$idx]
                            : ($existing->plan ?? null);
            $realRatio = ($request->real_ratio[$idx] !== null && $request->real_ratio[$idx] !== '')
                            ? $request->real_ratio[$idx]
                            : ($existing->real_ratio ?? null);
            $olFm      = ($request->ol_fm[$idx] !== null && $request->ol_fm[$idx] !== '')
                            ? $request->ol_fm[$idx]
                            : ($existing->ol_fm ?? null);

            $realUpdatedAt = ($request->real_ratio[$idx] !== null && $request->real_ratio[$idx] !== '')
                            ? now()
                            : ($existing->real_updated_at ?? null);

            // Skip kondisi yang tidak diisi sama sekali
            $planVal = ($request->plan[$idx] !== null && $request->plan[$idx] !== '') ? $request->plan[$idx] : null;
            $realVal = ($request->real_ratio[$idx] !== null && $request->real_ratio[$idx] !== '') ? $request->real_ratio[$idx] : null;
            $olFmVal = ($request->ol_fm[$idx] !== null && $request->ol_fm[$idx] !== '') ? $request->ol_fm[$idx] : null;

            // Kalau semua kosong, skip — tidak perlu simpan
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

    public function utipPreviewImport(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:51200'],
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'File harus berformat .xlsx atau .xls.',
        ]);
 
        try {
              $readFilter = new class implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
                public function readCell($column, $row, $worksheetName = ''): bool
                {
                    return $row <= 5000;
                }
            };
 
            $reader = IOFactory::createReaderForFile($request->file('file')->getRealPath());
            $reader->setReadFilter($readFilter);
            $spreadsheet = $reader->load($request->file('file')->getRealPath());
 
            // statusSums: 'DEPOSIT' => ['saldo'=>x, 'flag'=>y, 'sisa'=>z]
            $statusSums = [];
 
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $colMap = $this->utipMapHeaderColumns($sheet);
 
            
                if (!isset($colMap['STATUS'], $colMap['SALDO AWAL'])) {
                    continue;
                }
 
                $highestRow = $sheet->getHighestRow();
 
                for ($row = 2; $row <= $highestRow; $row++) {
                    $statusRaw = (string) $sheet->getCell([$colMap['STATUS'], $row])->getValue();
                    $status    = strtoupper(trim($statusRaw));
 
                  
                    if ($status === '') {
                        continue;
                    }
 
                    $saldo = $this->utipNumericCell($sheet, $colMap['SALDO AWAL'] ?? null, $row);
                    $flag  = $this->utipNumericCell($sheet, $colMap['FLAG'] ?? null, $row);
                    $sisa  = $this->utipNumericCell($sheet, $colMap['SISA_FLAG'] ?? null, $row);
 
                    if (!isset($statusSums[$status])) {
                        $statusSums[$status] = ['saldo' => 0, 'flag' => 0, 'sisa' => 0];
                    }
                    $statusSums[$status]['saldo'] += $saldo;
                    $statusSums[$status]['flag']  += $flag;
                    $statusSums[$status]['sisa']  += $sisa;
                }
            }
 
            $statusToKondisiIndex = [
                'DEPOSIT'                => 3, // Sudah BC, Deposit
                'BELUM BC'               => 5, // Belum BC, Late Input
                'BELUM TERIDENTIFIKASI'  => 6, // Belum teridentifikasi
                'PROSES FLAGGING'        => 0, // Sudah BC, Potensi Flag 
            ];
 
            $mapped = [];
            foreach ($statusToKondisiIndex as $statusKey => $idx) {
                if (isset($statusSums[$statusKey])) {
                    $mapped[$idx] = [
                        'plan'       => round($statusSums[$statusKey]['saldo']),
                        'real_ratio' => round($statusSums[$statusKey]['flag']),
                        'ol_fm'      => round($statusSums[$statusKey]['sisa']),
                    ];
                }
            }
 
            return response()->json([
                'success'    => true,
                'mapped'     => $mapped,      
                'raw_status' => $statusSums,    
            ]);
 
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file: ' . $e->getMessage(),
            ], 422);
        }
    }
 
    private function utipMapHeaderColumns(Worksheet $sheet): array
    {
        $colMap   = [];
        $maxCol   = $sheet->getHighestColumn();
        $maxColIx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);
 
        for ($col = 1; $col <= $maxColIx; $col++) {
            $raw        = (string) $sheet->getCell([$col, 1])->getValue();
            $normalized = preg_replace('/\s+/', ' ', strtoupper(trim($raw)));
            if ($normalized !== '') {
                $colMap[$normalized] = $col;
            }
        }
 
        return $colMap;
    }
 
    private function utipNumericCell(Worksheet $sheet, ?int $col, int $row): float
    {
        if ($col === null) {
            return 0;
        }
        $value = $sheet->getCell([$col, $row])->getValue();
 
        if (is_numeric($value)) {
            return (float) $value;
        }
 
        $clean = preg_replace('/[^\d,.\-]/', '', (string) $value);
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);
 
        return is_numeric($clean) ? (float) $clean : 0;
    }

    public function arTable(Request $request)
    {
        // Ambil submit_token terbaru
        $query = Collection::with('user')
            ->where('type', 'like', '%ar%')
            ->orderBy('created_at', 'desc');

        // if ($request->filled('user'))  $query->where('user_id', $request->user);
        if ($request->filled('tipe'))  $query->where('type', $request->tipe);
        if ($request->filled('bulan')) $query->whereMonth('periode', $request->bulan); // ← fix: periode bukan created_at
        if ($request->filled('tahun')) $query->whereYear('periode', $request->tahun);  // ← fix: periode bukan created_at
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('type', 'like', '%'.$request->cari.'%')
                ->orWhere('real_ratio', 'like', '%'.$request->cari.'%')
                ->orWhere('commitment', 'like', '%'.$request->cari.'%');
            });
        }

        $collections = $query->paginate(20)->withQueryString();


        $ringkasanAll = Collection::where('type', 'like', '%ar%')
            ->where('is_latest', true)
            ->orderByRaw("CASE WHEN type LIKE '%Corrective%' THEN 0 ELSE 1 END")
            ->orderBy('type')
            ->get();

    
        $tahuns = Collection::where('type', 'like', '%ar%')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $tipes = Collection::where('type', 'like', '%ar%')
            ->select('segment')
            ->distinct()
            ->orderBy('segment')->pluck('segment');

        $users = Collection::with('user')
            ->where('type', 'like', '%ar%')->get()
            ->pluck('user')->filter()->unique('id')->values();

        $selectedTipe  = $request->tipe;
        $selectedBulan = $request->bulan;
        $selectedTahun = $request->tahun;
        $selectedCari  = $request->cari;

        return view('admin.collection.ar', compact(
            'collections', 'ringkasanAll', 'users', 'tahuns', 'tipes',
            'selectedTipe', 'selectedBulan', 'selectedTahun', 'selectedCari'
        ));
    }

    public function arStore(Request $request)
    {
        $request->validate([
            'status'  => 'required|in:active,inactive',
            'periode' => 'required|date_format:Y-m',
            'segment' => 'required|string',
            'file'    => 'required|file|max:10240',

            'kondisi'   => 'required|array|size:8',
            'kondisi.*' => 'required|string',
            'real_ratio'=> 'required|array|size:8',
            'real_ratio.*' => 'nullable|numeric',
        ]);

        $periodeDate = $request->periode . '-01';

        $isUpdate = Collection::where('type', $request->segment)
            ->where('periode', $periodeDate)
            ->where('is_latest', true)
            ->exists();

        // Upload file
        $submitToken = \Illuminate\Support\Str::uuid()->toString();
        $filePath = null;
        $fileName = null;
        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->store('ar_files', 'public');
        }

    
        foreach ($request->kondisi as $idx => $kondisiName) {
            $existing = Collection::where('type', $request->segment)
                ->where('periode', $periodeDate)
                ->where('kondisi', $kondisiName)
                ->orderBy('created_at', 'desc')
                ->first();

            $realRatio = ($request->real_ratio[$idx] !== null && $request->real_ratio[$idx] !== '')
                            ? $request->real_ratio[$idx]
                            : ($existing->real_ratio ?? null);
            $realUpdatedAt = ($request->real_ratio[$idx] !== null && $request->real_ratio[$idx] !== '')
                            ? now()
                            : ($existing->real_updated_at ?? null);

            // Skip kondisi yang tidak diisi sama sekali
            $realVal = ($request->real_ratio[$idx] !== null && $request->real_ratio[$idx] !== '') ? $request->real_ratio[$idx] : null;

            
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

    public function ctcTable(Request $request)
    {
        $query = Ctc::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('bulan')) {
            $query->whereMonth('periode', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('periode', $request->tahun);
        }
        if ($request->filled('segment')) {
            $query->where('segment', $request->segment);
        }

        $collections = $query->paginate(15)->withQueryString();

        $tahuns = Ctc::selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $users           = User::all();
        $selectedBulan   = $request->bulan;
        $selectedTahun   = $request->tahun;
        $selectedSegment = $request->segment;

        return view('admin.ctc.ctc', compact(
            'collections', 'users', 'tahuns',
            'selectedBulan', 'selectedTahun', 'selectedSegment'
        ));
    }
    public function ctcStore(Request $request)
{
    $request->validate([
        'status'     => 'required|in:active,inactive',
        'segment'    => 'required|string',
        'periode'    => 'nullable|string',
        'commitment' => 'nullable|integer',
        'real_ratio' => 'nullable|integer',
    ]);

    $periode = $request->filled('periode')
        ? Carbon::parse($request->periode . '-01')->format('Y-m-d')
        : Carbon::now()->format('Y-m-01');

    $existing = Ctc::where('user_id', Auth::id())
        ->where('segment', $request->segment)
        ->where('periode', $periode)
        ->orderBy('updated_at', 'desc')
        ->first();

    $commitment = $request->filled('commitment')
        ? $request->commitment
        : ($existing->commitment ?? null);

    $lastRealRow = Ctc::where('segment', $request->segment)
        ->where('periode', $periode)
        ->whereNotNull('real_ratio')
        ->orderBy('created_at', 'desc')
        ->first();

    $realRatio     = $request->filled('real_ratio') ? $request->real_ratio : ($lastRealRow->real_ratio ?? null);
    $realUpdatedAt = $request->filled('real_ratio') ? now() : ($lastRealRow->real_updated_at ?? null);

    Ctc::create([
        'user_id'         => Auth::id(),
        'segment'         => $request->segment,
        'periode'         => $periode,
        'status'          => $request->status,
        'commitment'      => $commitment,
        'real_ratio'      => $realRatio,
        'real_updated_at' => $realUpdatedAt,
    ]);

    return back()->with('success', 'Data CTC periode ' . Carbon::parse($periode)->format('F Y') . ' berhasil disimpan.');
}

    public function ct0Table(Request $request)
{
    $query = Ct0::with('user')
        ->orderBy('created_at', 'desc');

    if ($request->filled('bulan')) {
        $query->whereMonth('periode', $request->bulan);
    }
    if ($request->filled('tahun')) {
        $query->whereYear('periode', $request->tahun);
    }
    if ($request->filled('region')) {
        $query->where('region', $request->region);
    }

    $collections = $query->paginate(15)->withQueryString();

    $tahuns = Ct0::selectRaw('YEAR(periode) as tahun')
        ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

    $users           = User::all();
    $selectedBulan   = $request->bulan;
    $selectedTahun   = $request->tahun;
    $selectedRegion  = $request->region;

    return view('admin.ctc.ct0', compact(
        'collections', 'users', 'tahuns',
        'selectedBulan', 'selectedTahun', 'selectedRegion'
    ));
}

    public function ct0Store(Request $request)
{
    $request->validate([
        'status'     => 'required|in:active,inactive',
        'region'     => 'required|string',
        'periode'    => 'nullable|string',
        'plan'       => 'nullable|numeric',
        'commitment' => 'nullable|numeric',
        'real_ratio' => 'nullable|numeric',
    ]);

    $periode = $request->filled('periode')
        ? Carbon::parse($request->periode . '-01')->format('Y-m-d')
        : Carbon::now()->format('Y-m-01');

    $existing = Ct0::where('user_id', Auth::id())
        ->where('region', $request->region)
        ->where('periode', $periode)
        ->orderBy('updated_at', 'desc')
        ->first();

    $plan = $request->filled('plan')
        ? $request->plan
        : ($existing->plan ?? null);

    $commitment = $request->filled('commitment')
        ? $request->commitment
        : ($existing->commitment ?? null);

    $lastRealRow = Ct0::where('region', $request->region)
        ->where('periode', $periode)
        ->whereNotNull('real_ratio')
        ->orderBy('created_at', 'desc')
        ->first();

    $realRatio     = $request->filled('real_ratio') ? $request->real_ratio : ($lastRealRow->real_ratio ?? null);
    $realUpdatedAt = $request->filled('real_ratio') ? now() : ($lastRealRow->real_updated_at ?? null);

    Ct0::create([
        'user_id'         => Auth::id(),
        'region'          => $request->region,
        'periode'         => $periode,
        'status'          => $request->status,
        'plan'            => $plan,
        'commitment'      => $commitment,
        'real_ratio'      => $realRatio,
        'real_updated_at' => $realUpdatedAt,
    ]);

    return back()->with('success', 'Data CT0 periode ' . Carbon::parse($periode)->format('F Y') . ' berhasil disimpan.');
}

    public function psakGov(Request $request)
    {
        $query = Psak::with('user')
            ->where('type', 'Government')
            ->orderBy('created_at', 'desc');

        if ($request->filled('segment')) {
            $query->where('segment', $request->segment);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('periode', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('periode', $request->tahun);
        }

        $govs = $query->paginate(20)->withQueryString();

        $tahuns = Psak::where('type', 'Government')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $users           = User::all();
        $selectedSegment = $request->segment;
        $selectedBulan   = $request->bulan;
        $selectedTahun   = $request->tahun;

        return view('admin.psak.gov', compact(
            'govs', 'users', 'tahuns',
            'selectedSegment', 'selectedBulan', 'selectedTahun'
        ));
    }

    public function psakGovStore(Request $request)
    {
        $request->validate([
            'periode'  => 'required|string',
            'segment'  => 'required|string',
            'comm_ssl' => 'nullable|numeric',
            'comm_rp'  => 'nullable|numeric',
            'real_ssl' => 'nullable|numeric',
            'real_rp'  => 'nullable|numeric',
        ]);

        $periode = $request->periode . '-01';

        $existing = Psak::where('type', 'Government')
            ->where('segment', $request->segment)
            ->where('periode', $periode)
            ->orderBy('created_at', 'desc')
            ->first();

        $realFilled    = $request->filled('real_ssl') || $request->filled('real_rp');
        $realUpdatedAt = $realFilled ? now() : ($existing->real_updated_at ?? null);

        Psak::create([
            'user_id'         => Auth::id(),
            'type'            => 'Government',
            'segment'         => $request->segment,
            'periode'         => $periode,
            'comm_ssl'        => $request->filled('comm_ssl') ? $request->comm_ssl : ($existing->comm_ssl ?? null),
            'comm_rp'         => $request->filled('comm_rp')  ? $request->comm_rp  : ($existing->comm_rp  ?? null),
            'real_ssl'        => $request->filled('real_ssl') ? $request->real_ssl : ($existing->real_ssl ?? null),
            'real_rp'         => $request->filled('real_rp')  ? $request->real_rp  : ($existing->real_rp  ?? null),
            'real_updated_at' => $realUpdatedAt,
        ]);

        return back()->with('success', 'Data PSAK Government periode ' . Carbon::parse($periode)->translatedFormat('F Y') . ' berhasil disimpan.');
    }

    public function psakPrivate(Request $request)
    {
        $query = Psak::with('user')
            ->where('type', 'Private')
            ->orderBy('created_at', 'desc');

        if ($request->filled('segment')) {
            $query->where('segment', $request->segment);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('periode', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('periode', $request->tahun);
        }

        $pvts = $query->paginate(20)->withQueryString();

        $tahuns = Psak::where('type', 'Private')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $users           = User::all();
        $selectedSegment = $request->segment;
        $selectedBulan   = $request->bulan;
        $selectedTahun   = $request->tahun;

        return view('admin.psak.private', compact(
            'pvts', 'users', 'tahuns',
            'selectedSegment', 'selectedBulan', 'selectedTahun'
        ));
    }

    public function psakPrivateStore(Request $request)
    {
        $request->validate([
            'periode'  => 'required|string',
            'segment'  => 'required|string',
            'comm_ssl' => 'nullable|numeric',
            'comm_rp'  => 'nullable|numeric',
            'real_ssl' => 'nullable|numeric',
            'real_rp'  => 'nullable|numeric',
        ]);

        $periode = $request->periode . '-01';

        $existing = Psak::where('type', 'Private')
            ->where('segment', $request->segment)
            ->where('periode', $periode)
            ->orderBy('created_at', 'desc')
            ->first();

        $realFilled    = $request->filled('real_ssl') || $request->filled('real_rp');
        $realUpdatedAt = $realFilled ? now() : ($existing->real_updated_at ?? null);

        Psak::create([
            'user_id'         => Auth::id(),
            'type'            => 'Private',
            'segment'         => $request->segment,
            'periode'         => $periode,
            'comm_ssl'        => $request->filled('comm_ssl') ? $request->comm_ssl : ($existing->comm_ssl ?? null),
            'comm_rp'         => $request->filled('comm_rp')  ? $request->comm_rp  : ($existing->comm_rp  ?? null),
            'real_ssl'        => $request->filled('real_ssl') ? $request->real_ssl : ($existing->real_ssl ?? null),
            'real_rp'         => $request->filled('real_rp')  ? $request->real_rp  : ($existing->real_rp  ?? null),
            'real_updated_at' => $realUpdatedAt,
        ]);

        return back()->with('success', 'Data PSAK Private periode ' . Carbon::parse($periode)->translatedFormat('F Y') . ' berhasil disimpan.');
    }

    public function psakSoe(Request $request)
    {
        $query = Psak::with('user')
            ->where('type', 'SOE')
            ->orderBy('created_at', 'desc');

        if ($request->filled('segment')) {
            $query->where('segment', $request->segment);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('periode', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('periode', $request->tahun);
        }

        $soes = $query->paginate(20)->withQueryString();

        $tahuns = Psak::where('type', 'SOE')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $users           = User::all();
        $selectedSegment = $request->segment;
        $selectedBulan   = $request->bulan;
        $selectedTahun   = $request->tahun;

        return view('admin.psak.soe', compact(
            'soes', 'users', 'tahuns',
            'selectedSegment', 'selectedBulan', 'selectedTahun'
        ));
    }

    public function psakSoeStore(Request $request)
    {
        $request->validate([
            'periode'  => 'required|string',
            'segment'  => 'required|string',
            'comm_ssl' => 'nullable|numeric',
            'comm_rp'  => 'nullable|numeric',
            'real_ssl' => 'nullable|numeric',
            'real_rp'  => 'nullable|numeric',
        ]);

        $periode = $request->periode . '-01';

        $existing = Psak::where('type', 'SOE')
            ->where('segment', $request->segment)
            ->where('periode', $periode)
            ->orderBy('created_at', 'desc')
            ->first();

        $realFilled    = $request->filled('real_ssl') || $request->filled('real_rp');
        $realUpdatedAt = $realFilled ? now() : ($existing->real_updated_at ?? null);

        Psak::create([
            'user_id'         => Auth::id(),
            'type'            => 'SOE',
            'segment'         => $request->segment,
            'periode'         => $periode,
            'comm_ssl'        => $request->filled('comm_ssl') ? $request->comm_ssl : ($existing->comm_ssl ?? null),
            'comm_rp'         => $request->filled('comm_rp')  ? $request->comm_rp  : ($existing->comm_rp  ?? null),
            'real_ssl'        => $request->filled('real_ssl') ? $request->real_ssl : ($existing->real_ssl ?? null),
            'real_rp'         => $request->filled('real_rp')  ? $request->real_rp  : ($existing->real_rp  ?? null),
            'real_updated_at' => $realUpdatedAt,
        ]);

        return back()->with('success', 'Data PSAK SOE periode ' . Carbon::parse($periode)->translatedFormat('F Y') . ' berhasil disimpan.');
    }

    public function psakSme(Request $request)
    {
        $query = Psak::with('user')
            ->where('type', 'SME')
            ->orderBy('created_at', 'desc');

        if ($request->filled('segment')) {
            $query->where('segment', $request->segment);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('periode', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('periode', $request->tahun);
        }

        $smes = $query->paginate(20)->withQueryString();

        $tahuns = Psak::where('type', 'SME')
            ->selectRaw('YEAR(periode) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $users           = User::all();
        $selectedSegment = $request->segment;
        $selectedBulan   = $request->bulan;
        $selectedTahun   = $request->tahun;

        return view('admin.psak.sme', compact(
            'smes', 'users', 'tahuns',
            'selectedSegment', 'selectedBulan', 'selectedTahun'
        ));
    }

    public function psakSmeStore(Request $request)
    {
        $request->validate([
            'periode'  => 'required|string',
            'segment'  => 'required|string',
            'comm_ssl' => 'nullable|numeric',
            'comm_rp'  => 'nullable|numeric',
            'real_ssl' => 'nullable|numeric',
            'real_rp'  => 'nullable|numeric',
        ]);

        $periode = $request->periode . '-01';

        $existing = Psak::where('type', 'SME')
            ->where('segment', $request->segment)
            ->where('periode', $periode)
            ->orderBy('created_at', 'desc')
            ->first();

        $realFilled    = $request->filled('real_ssl') || $request->filled('real_rp');
        $realUpdatedAt = $realFilled ? now() : ($existing->real_updated_at ?? null);

        Psak::create([
            'user_id'         => Auth::id(),
            'type'            => 'SME',
            'segment'         => $request->segment,
            'periode'         => $periode,
            'comm_ssl'        => $request->filled('comm_ssl') ? $request->comm_ssl : ($existing->comm_ssl ?? null),
            'comm_rp'         => $request->filled('comm_rp')  ? $request->comm_rp  : ($existing->comm_rp  ?? null),
            'real_ssl'        => $request->filled('real_ssl') ? $request->real_ssl : ($existing->real_ssl ?? null),
            'real_rp'         => $request->filled('real_rp')  ? $request->real_rp  : ($existing->real_rp  ?? null),
            'real_updated_at' => $realUpdatedAt,
        ]);

        return back()->with('success', 'Data PSAK SME periode ' . Carbon::parse($periode)->translatedFormat('F Y') . ' berhasil disimpan.');
    }

    public function risingStar1Table()
    {
        $collections = Ctc::with('user')
        // ->where('type', 'like', '%UTIP%')
        ->orderBy('created_at', 'desc')
        ->paginate(15)
        ->withQueryString();
        $users = User::all();
        return view('admin.ctc.ctc', compact('collections', 'users'));
    }

    public function risingStar1Store(Request $request)
    {
        $request->validate([
            'status'     => 'required|in:active,inactive',
            'segment'    => 'nullable|string',
            'commitment' => 'nullable|string',
            'real_ratio' => 'nullable|string',
        ]);

        Ctc::create([
            'user_id'    => Auth::id(),
            'status'     => $request->status,
            'segment'    => $request->segment,
            'commitment' => $request->commitment,
            'real_ratio' => $request->real_ratio,
        ]);

        return back()->with('success', 'Data berhasil disimpan');
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
