<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RisingStar;
use App\Models\RisingStarType;
use App\Models\Hsi;
use App\Models\User;
use App\Models\Telda;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Admin2Controller extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    // ══════════════════════════════════════════════════════
    // Helper: upsert RisingStar per (user_id, type_id, periode)
    // Kolom yang tidak dikirim tidak ditimpa.
    // ══════════════════════════════════════════════════════
    private function upsertRisingStar(int $typeId, int $userId, array $values, string $periode = null): RisingStar {

        $periode = $periode ?? Carbon::now()->format('Y-m-01');

        $record = RisingStar::firstOrNew([
            'user_id' => $userId,
            'type_id' => $typeId,
            'periode' => $periode,
        ]);

        // Selalu update user_id
        $record->user_id = $userId;

        // Hanya update kolom yang dikirim
        foreach ($values as $col => $val) {
            if ($val !== null) {   // ⬅️ KUNCI UTAMA DI SINI
                $record->{$col} = $val;
            }
        }

        // Jika record baru dan belum ada status
        if (! $record->exists) {
            $record->status = $values['status'] ?? 'active';
        }

        $record->save();

        return $record;
    }

    // ══ Rising Star Tables ══

    public function risingStar1Table(Request $request)
{
    $types = RisingStarType::where('type', '1')->get();

    $query = RisingStar::with(['type', 'user'])
        ->mainType(1)
        ->orderBy('created_at', 'desc');

    if ($request->filled('bulan')) {
        $query->whereMonth('periode', $request->bulan);
    }
    if ($request->filled('tahun')) {
        $query->whereYear('periode', $request->tahun);
    }
    if ($request->filled('type_id')) {
        $query->where('type_id', $request->type_id);
    }

    $rstars = $query->paginate(15)->withQueryString();

    $tahuns = RisingStar::mainType(1)
        ->selectRaw('YEAR(periode) as tahun')
        ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

    $users         = User::all();
    $selectedBulan = $request->bulan;
    $selectedTahun = $request->tahun;
    $selectedType  = $request->type_id;

    return view('admin.risingstar.risingStar1', compact(
        'rstars', 'types', 'users', 'tahuns',
        'selectedBulan', 'selectedTahun', 'selectedType'
    ));
}

    public function risingStar2Table(Request $request)
{
    $types = RisingStarType::where('type', '2')->get();

    $query = RisingStar::with(['type', 'user'])
        ->mainType(2)
        ->orderBy('created_at', 'desc');

    if ($request->filled('bulan')) {
        $query->whereMonth('periode', $request->bulan);
    }
    if ($request->filled('tahun')) {
        $query->whereYear('periode', $request->tahun);
    }
    if ($request->filled('type_id')) {
        $query->where('type_id', $request->type_id);
    }

    $rstars = $query->paginate(15)->withQueryString();

    $tahuns = RisingStar::mainType(2)
        ->selectRaw('YEAR(periode) as tahun')
        ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

    $users         = User::all();
    $selectedBulan = $request->bulan;
    $selectedTahun = $request->tahun;
    $selectedType  = $request->type_id;

    return view('admin.risingstar.risingStar2', compact(
        'rstars', 'types', 'users', 'tahuns',
        'selectedBulan', 'selectedTahun', 'selectedType'
    ));
}

    public function risingStar3Table(Request $request)
{
    $types = RisingStarType::where('type', '3')->get();

    $query = RisingStar::with(['type', 'user'])
        ->mainType(3)
        ->orderBy('created_at', 'desc');

    if ($request->filled('bulan')) {
        $query->whereMonth('periode', $request->bulan);
    }
    if ($request->filled('tahun')) {
        $query->whereYear('periode', $request->tahun);
    }
    if ($request->filled('type_id')) {
        $query->where('type_id', $request->type_id);
    }

    $rstars = $query->paginate(15)->withQueryString();

    $tahuns = RisingStar::mainType(3)
        ->selectRaw('YEAR(periode) as tahun')
        ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

    $users         = User::all();
    $selectedBulan = $request->bulan;
    $selectedTahun = $request->tahun;
    $selectedType  = $request->type_id;

    return view('admin.risingstar.risingStar3', compact(
        'rstars', 'types', 'users', 'tahuns',
        'selectedBulan', 'selectedTahun', 'selectedType'
    ));
}

public function risingStar4Table(Request $request)
{
    $types = RisingStarType::where('type', '4')->get();
    $query = RisingStar::with(['type', 'user'])
        ->mainType(4)
        ->orderBy('created_at', 'desc');

    if ($request->filled('bulan'))   $query->whereMonth('periode', $request->bulan);
    if ($request->filled('tahun'))   $query->whereYear('periode', $request->tahun);
    if ($request->filled('type_id')) $query->where('type_id', $request->type_id);

    $rstars = $query->paginate(15)->withQueryString();

    // ← Tambahkan ini
    $ringkasanAll = RisingStar::with(['type', 'user'])
        ->mainType(4)
        ->where('is_latest', true)
        ->orderByDesc('periode')
        ->orderBy('type_id')
        ->get();
        // dd($ringkasanAll);

    $tahuns = RisingStar::mainType(4)
        ->selectRaw('YEAR(periode) as tahun')
        ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

    $users         = User::all();
    $selectedBulan = $request->bulan;
    $selectedTahun = $request->tahun;
    $selectedType  = $request->type_id;

    return view('admin.risingstar.risingStar4', compact(
        'rstars', 'ringkasanAll', 'types', 'users', 'tahuns',
        'selectedBulan', 'selectedTahun', 'selectedType'
    ));
}

public function toggleRisingStarStatus($id)
{
    $item = \App\Models\RisingStar::findOrFail($id);
    abort_if(!$item->is_latest, 403, 'Hanya record terbaru yang bisa diubah statusnya.');
    $item->status = $item->status === 'active' ? 'inactive' : 'active';
    $item->save();
    return back()->with('success', 'Status berhasil diubah');
}

    // ══ Rising Star Store ══
    // Upsert per (user_id, type_id, periode) — tidak buat baris baru jika periode sama.
    public function risingStarStore(Request $request)
    {
        $request->validate([
            'status'     => 'required|in:active,inactive',
            'type_id'    => 'required|exists:rising_star_types,id',
            'periode'    => 'nullable|string',
            'commitment' => 'nullable|numeric|min:0',
            'real_ratio' => 'nullable|numeric|min:0',
            'segment'    => 'nullable|in:gov,sme',
            'user_id'    => 'nullable|exists:users,id',
        ]);

        // Tentukan user
        if ($request->filled('segment')) {
            $segmentUserMap = [
                'gov' => 2,
                'sme' => 4,
            ];
            $userId = $segmentUserMap[$request->segment] ?? Auth::id();
        } else {
            $userId = $request->filled('user_id')
                ? (int) $request->user_id
                : Auth::id();
        }

        $periode = $request->filled('periode')
            ? Carbon::parse($request->periode . '-01')->format('Y-m-d')
            : Carbon::now()->format('Y-m-01');

        // Bangun values secara dinamis
        $values = [
            'status'     => $request->status,
            'commitment' => $request->commitment,
        ];

        // Hanya masukkan real_ratio jika diinput
        if ($request->filled('real_ratio')) {
            $values['real_ratio'] = $request->real_ratio;
        }

        // Scope is_latest per user_id + type_id + periode
        $existing = RisingStar::where('user_id', $userId)
            ->where('type_id', (int) $request->type_id)
            ->where('periode', $periode)
            ->where('is_latest', true)
            ->first();

        $commitment = isset($values['commitment']) && $values['commitment'] !== null
            ? $values['commitment']
            : ($existing->commitment ?? null);

        $lastRealRow = RisingStar::where('user_id', $userId)
            ->where('type_id', (int) $request->type_id)
            ->where('periode', $periode)
            ->where('is_latest', true)
            ->first();

        $realRatio     = $request->filled('real_ratio') ? $request->real_ratio : ($lastRealRow->real_ratio ?? null);
        $realUpdatedAt = $request->filled('real_ratio') ? now() : ($lastRealRow->real_updated_at ?? null);

        DB::transaction(function () use ($userId, $request, $periode, $commitment, $realRatio, $realUpdatedAt, $values) {
            // Set is_latest false untuk kombinasi user + type + periode yang sama
            RisingStar::where('user_id', $userId)
                ->where('type_id', (int) $request->type_id)
                ->where('periode', $periode)
                ->update(['is_latest' => false]);

            RisingStar::create([
                'user_id'         => $userId,
                'type_id'         => (int) $request->type_id,
                'periode'         => $periode,
                'status'          => $values['status'] ?? 'active',
                'is_latest'       => true,
                'commitment'      => $commitment,
                'real_ratio'      => $realRatio,
                'real_updated_at' => $realUpdatedAt,
            ]);
        });

        return back()->with(
            'success',
            "Data Rising Star periode " .
            Carbon::parse($periode)->format('F Y') .
            " berhasil disimpan."
        );
    }

    // ══ HSI ══

    public function hsiTable(Request $request)
{
    $selectedPeriode = $request->get('periode', Carbon::now()->format('Y-m'));
    $currentPeriode = $selectedPeriode . '-01';

    $existing = Hsi::where('type', 'Sales HSI Non AM Non Telda')
        ->where('periode', $currentPeriode)
        ->orderBy('updated_at', 'desc')
        ->first();

    $query = Hsi::with('user')
        ->where('type', 'Sales HSI Non AM Non Telda')
        ->orderBy('periode', 'desc')
        ->orderBy('updated_at', 'desc');

    if ($request->filled('bulan')) {
        $query->whereMonth('periode', $request->bulan);
    }
    if ($request->filled('tahun')) {
        $query->whereYear('periode', $request->tahun);
    }

    $hsi = $query->paginate(10)->withQueryString();

    $tahuns = Hsi::where('type', 'Sales HSI Non AM Non Telda')
        ->selectRaw('YEAR(periode) as tahun')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    $users         = User::all();
    $selectedBulan = $request->bulan;
    $selectedTahun = $request->tahun;

    return view('admin.hsiagency.hsi', compact(
        'existing', 'hsi', 'users', 'tahuns', 'selectedBulan', 'selectedTahun'
    ));
}

public function hsiStore(Request $request)
{
    $request->validate([
        'type'       => 'required|string',
        'periode'    => 'required|string',
        'commitment' => 'nullable|numeric|min:0',
        'real_ratio' => 'nullable|numeric|min:0',
    ]);

    $periodeDate = $request->periode . '-01';

    $existing = Hsi::where('type', $request->type)
        ->where('periode', $periodeDate)
        ->orderBy('updated_at', 'desc')
        ->first();

    $commitment = $request->filled('commitment')
        ? $request->commitment
        : ($existing->commitment ?? null);

    $lastRealRow = Hsi::where('type', $request->type)
        ->where('periode', $periodeDate)
        ->whereNotNull('real_ratio')
        ->orderBy('created_at', 'desc')
        ->first();

    $realRatio     = $request->filled('real_ratio') ? $request->real_ratio : ($lastRealRow->real_ratio ?? null);
    $realUpdatedAt = $request->filled('real_ratio') ? now() : ($lastRealRow->real_updated_at ?? null);

    Hsi::create([
        'user_id'         => Auth::id(),
        'type'            => $request->type,
        'periode'         => $periodeDate,
        'commitment'      => $commitment,
        'real_ratio'      => $realRatio,
        'real_updated_at' => $realUpdatedAt,
    ]);

    return back()->with('success', 'Data HSI periode ' . Carbon::parse($periodeDate)->format('F Y') . ' berhasil disimpan.');
}

// ══ Upselling HSI ══

public function upsellingHsiIndex(Request $request)
{
    $ringkasanAll = Hsi::with('user')
    ->where('type', 'Next Level HSI')
    ->whereIn('id', function($q) {
        $q->selectRaw('MAX(id)')
          ->from('hsis')
          ->where('type', 'Next Level HSI')
          ->groupBy('periode');
    })
    ->orderBy('periode', 'asc')
    ->get();

    $query = Hsi::with('user')
    ->where('type', 'Next Level HSI')
    ->orderBy('created_at', 'asc');

    if ($request->filled('bulan'))   $query->whereMonth('periode', $request->bulan);
    if ($request->filled('tahun'))   $query->whereYear('periode', $request->tahun);
    if ($request->filled('user_id')) $query->where('user_id', $request->user_id);

    $history = $query->paginate(15)->withQueryString();

    $tahuns = Hsi::where('type', 'Next Level HSI')
        ->selectRaw('YEAR(periode) as tahun')
        ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

    $users         = User::all();
    $selectedBulan = $request->bulan;
    $selectedTahun = $request->tahun;
    $selectedUser  = $request->user_id;

    return view('admin.upsellinghsi.upselling', compact(
        'ringkasanAll', 'history', 'users', 'tahuns',
        'selectedBulan', 'selectedTahun', 'selectedUser'
    ));
}

public function upsellingHsiStore(Request $request)
{
    $request->validate([
        'periode'    => 'required|string',
        'commitment' => 'nullable|numeric|min:0',
        'real_ratio' => 'nullable|numeric|min:0',
    ]);

    $periodeDate = $request->periode . '-01';

    $userId = auth()->id();

    $existing = Hsi::where('user_id', $userId)
        ->where('type', 'Next Level HSI')
        ->where('periode', $periodeDate)
        ->orderBy('created_at', 'desc')
        ->first();

    $commitment = $request->filled('commitment') ? $request->commitment : ($existing->commitment ?? null);

    $lastReal = Hsi::where('user_id', $userId)
        ->where('type', 'Next Level HSI')
        ->where('periode', $periodeDate)
        ->whereNotNull('real_ratio')
        ->orderBy('created_at', 'desc')
        ->first();

    $real          = $request->filled('real_ratio') ? $request->real_ratio : ($lastReal->real_ratio ?? null);
    $realUpdatedAt = $request->filled('real_ratio') ? now() : ($lastReal->real_updated_at ?? null);

    // Sync status semua record periode ini
    Hsi::where('type', 'Next Level HSI')
        ->where('periode', $periodeDate)
        ->update(['status' => $request->status ?? 'active']);

    Hsi::create([
        'user_id'         => auth()->id(),
        'type'            => 'Next Level HSI',
        'periode'         => $periodeDate,
        'status'          => $request->status ?? 'active',
        'commitment'      => $commitment,
        'real_ratio'      => $real,
        'real_updated_at' => $realUpdatedAt,
    ]);

    return back()->with('success', 'Data Upselling HSI periode ' . \Carbon\Carbon::parse($periodeDate)->format('F Y') . ' berhasil disimpan.');
}

public function upsellingHsiToggleStatus($id)
{
    $item = Hsi::findOrFail($id);
    $newStatus = ($item->status === 'active') ? 'inactive' : 'active';

    // Toggle semua record dalam periode yang sama
    Hsi::where('type', 'Next Level HSI')
        ->where('periode', $item->periode)
        ->update(['status' => $newStatus]);

    return back()->with('success', 'Status periode berhasil diubah.');
}


    // ══ Telda ══
public function teldaTable(Request $request)
{
    $teldas = [
        'lubukpakam'      => 'Lubuk Pakam',
        'binjai'          => 'Binjai',
        'siantar'         => 'Siantar',
        'kisaran'         => 'Kisaran',
        'kabanjahe'       => 'Kabanjahe',
        'rantauprapat'    => 'Rantau Prapat',
        'toba'            => 'Toba',
        'sibolga'         => 'Sibolga',
        'padangsidempuan' => 'Padang Sidempuan',
    ];

    $selectedPeriode  = $request->get('periode', Carbon::now()->format('Y-m'));
    $periodeDate      = $selectedPeriode . '-01';

    // Untuk form input: ambil record terbaru per region pada periode ini
    $existingByRegion = Telda::where('periode', $periodeDate)
        ->orderBy('updated_at', 'desc')
        ->get()
        ->groupBy('region')
        ->map(fn($rows) => $rows->first());

    // Untuk tabel riwayat: semua baris, dengan filter
    $query = Telda::with('user')->orderBy('created_at', 'desc');

    if ($request->filled('bulan')) {
        $query->whereMonth('periode', $request->bulan);
    }
    if ($request->filled('tahun')) {
        $query->whereYear('periode', $request->tahun);
    }
    if ($request->filled('region')) {
        $query->where('region', $request->region);
    }

    $history = $query->paginate(15)->withQueryString();

    $tahuns = Telda::selectRaw('YEAR(periode) as tahun')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    $users           = User::all();
    $selectedBulan   = $request->bulan;
    $selectedTahun   = $request->tahun;
    $selectedRegion  = $request->region;

    return view('admin.telda.telda', compact(
        'teldas', 'existingByRegion', 'history', 'users',
        'tahuns', 'selectedBulan', 'selectedTahun', 'selectedRegion', 'selectedPeriode'
    ));
}

public function teldaStore(Request $request)
{
    $request->validate([
        'periode'              => 'required|string',
        'regions'              => 'required|array',
        'regions.*.region'     => 'required|string',
        'regions.*.commitment' => 'nullable|numeric|min:0',
        'regions.*.real_ratio' => 'nullable|numeric|min:0',
    ]);

    $periode = $request->periode . '-01';

    foreach ($request->regions as $data) {
    $commitmentFilled = isset($data['commitment']) && $data['commitment'] !== '';
    $realFilled       = isset($data['real_ratio']) && $data['real_ratio'] !== '';

    // Skip region yang tidak diisi sama sekali
    if (!$commitmentFilled && !$realFilled) {
        continue;
    }

    $existing = Telda::where('periode', $periode)
        ->where('region', $data['region'])
        ->orderBy('updated_at', 'desc')
        ->first();

    $commitment = $commitmentFilled
        ? $data['commitment']
        : ($existing->commitment ?? null);

    $lastRealRow = Telda::where('periode', $periode)
        ->where('region', $data['region'])
        ->whereNotNull('real_ratio')
        ->orderBy('created_at', 'desc')
        ->first();

    $realRatio     = $realFilled ? $data['real_ratio'] : ($lastRealRow->real_ratio ?? null);
    $realUpdatedAt = $realFilled ? now() : ($lastRealRow->real_updated_at ?? null);

    Telda::create([
        'user_id'         => Auth::id(),
        'periode'         => $periode,
        'region'          => $data['region'],
        'status'          => $data['status'] ?? 'active',
        'commitment'      => $commitment,
        'real_ratio'      => $realRatio,
        'real_updated_at' => $realUpdatedAt,
    ]);

}

    return back()->with('success', 'Data Telda periode ' . Carbon::parse($periode)->format('F Y') . ' berhasil disimpan.');
}

    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}
