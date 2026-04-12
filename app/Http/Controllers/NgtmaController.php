<?php
namespace App\Http\Controllers;

use App\Models\Ngtma;
use App\Models\ScallingImport;
use App\Models\FunnelTracking;
use App\Models\TaskProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NgtmaController extends Controller
{
    private const HEADER_ROW     = 3;
    private const DATA_START_ROW = 7;
    private const MAX_COL        = 9;

    private array $columnMap = [
        'PROJECT'      => 'project',
        'ID LOP'       => 'id_lop',
        'CC'           => 'cc',
        'NIPNAS'       => 'nipnas',
        'AM'           => 'am',
        'MITRA'        => 'mitra',
        'EST BULAN BC' => 'plan_bulan_billcomp_2025',
        'EST NILAI BC' => 'est_nilai_bc',
    ];

    // ── Mapping segment param → DB value ──────────────────────────────
    private array $segmentMap = [
        'gov'     => 'government',
        'private' => 'private',
        'soe'     => 'soe',
        'sme'     => 'sme',
    ];

    private array $segmentLabel = [
        'gov'     => 'Government',
        'private' => 'Private',
        'soe'     => 'SOE',
        'sme'     => 'SME',
    ];

    public function index(string $segment)
    {
        abort_if(!isset($this->segmentMap[$segment]), 404);

        $segmentDb = $this->segmentMap[$segment];

        $logs = ScallingImport::where('type', 'ngtma')
            ->where('segment', $segmentDb)
            ->latest()
            ->paginate(10);

        $currentPeriode = request()->get('periode', date('Y-m'));

        $projects = Ngtma::with('scallingImport')->latest()->paginate(20);

        return view('admin.ngtma.ngtma', compact(
            'logs', 'segment', 'currentPeriode', 'projects'
        ))->with('segmentLabel', $this->segmentLabel[$segment]);
    }

    public function import(Request $request, string $segment)
    {
        abort_if(!isset($this->segmentMap[$segment]), 404);

        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [
            'excel_file.required' => 'File Excel wajib diunggah.',
            'excel_file.mimes'    => 'File harus berformat .xlsx, .xls, atau .csv.',
            'excel_file.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        $request->validate([
            'periode' => 'required|date_format:Y-m',
        ]);

        $segmentDb        = $this->segmentMap[$segment];
        $periodeDate      = $request->periode . '-01';
        $file             = $request->file('excel_file');
        $originalFilename = $file->getClientOriginalName();
        $tempPath         = $file->getRealPath();

        try {
            $spreadsheet = IOFactory::load($tempPath);
            $sheet       = $spreadsheet->getActiveSheet();
            $headerMap   = $this->readAndValidateHeaders($sheet);
            $rows        = [];
            $highestRow  = $sheet->getHighestRow();

            for ($rowIndex = self::DATA_START_ROW; $rowIndex <= $highestRow; $rowIndex++) {
                if ($this->isTotalRow($sheet, $rowIndex)) break;
                $rowData = $this->readRow($sheet, $rowIndex, $headerMap);
                if ($this->isRowEmpty($rowData)) continue;
                $rows[] = $rowData;
            }

            if (empty($rows)) {
                throw new \Exception('Tidak ada data valid yang ditemukan di dalam file Excel.');
            }

            DB::transaction(function () use ($rows, $originalFilename, $request, $periodeDate, $segmentDb) {
                $log = ScallingImport::where('periode', $periodeDate)
                    ->where('type', 'ngtma')
                    ->where('segment', $segmentDb)
                    ->first();

                if ($log) {
                    $log->update([
                        'original_filename'   => $originalFilename,
                        'status'              => 'active',
                        'total_rows_imported' => count($rows),
                        'uploaded_by'         => auth()->user()->name ?? $request->ip(),
                    ]);

                    $importedIds = Ngtma::where('imports_log_id', $log->id)
                        ->where('is_manual', false)
                        ->pluck('id');

                    if ($importedIds->isNotEmpty()) {
                        Ngtma::whereIn('id', $importedIds)->delete();
                    }
                } else {
                    $log = ScallingImport::create([
                        'original_filename'   => $originalFilename,
                        'status'              => 'active',
                        'type'                => 'ngtma',
                        'segment'             => $segmentDb,
                        'periode'             => $periodeDate,
                        'total_rows_imported' => count($rows),
                        'uploaded_by'         => auth()->user()->name ?? $request->ip(),
                    ]);
                }

                $timestamp  = now();
                $insertRows = array_map(fn($row) => array_merge($row, [
                    'imports_log_id' => $log->id,
                    'is_manual'      => false,
                    'created_at'     => $timestamp,
                    'updated_at'     => $timestamp,
                ]), $rows);

                foreach (array_chunk($insertRows, 500) as $chunk) {
                    Ngtma::insert($chunk);
                }

                $log->update([
                    'total_rows_imported' => Ngtma::where('imports_log_id', $log->id)->count(),
                ]);

                $allRows = Ngtma::where('imports_log_id', $log->id)
                    ->orderBy('is_manual', 'desc')
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($allRows as $index => $row) {
                    $row->update(['no' => $index + 1]);
                }
            });

            @unlink($tempPath);
            return redirect()->back()
                ->with('success', "Import berhasil! " . count($rows) . " baris diimpor dari \"{$originalFilename}\".");

        } catch (\Throwable $e) {
            @unlink($tempPath);
            return redirect()->back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function storeData(Request $request, string $segment)
    {
        abort_if(!isset($this->segmentMap[$segment]), 404);

        $request->validate([
            'periode'                  => 'required|date_format:Y-m',
            'project'                  => 'required|string|max:255',
            'cc'                       => 'required|string|max:100',
            'am'                       => 'required|string|max:100',
            'plan_bulan_billcomp_2025' => 'required|integer|min:1|max:12',
            'est_nilai_bc'             => 'required|numeric|min:0',
        ]);

        $segmentDb   = $this->segmentMap[$segment];
        $periodeDate = $request->periode . '-01';

        $log = ScallingImport::where('periode', $periodeDate)
            ->where('type', 'ngtma')
            ->where('segment', $segmentDb)
            ->first();

        if (!$log) {
            $log = ScallingImport::create([
                'original_filename'   => 'manual-input',
                'status'              => 'active',
                'type'                => 'ngtma',
                'segment'             => $segmentDb,
                'periode'             => $periodeDate,
                'total_rows_imported' => 0,
                'uploaded_by'         => auth()->user()->name ?? $request->ip(),
            ]);
        }

        $data = Ngtma::create([
            'imports_log_id'           => $log->id,
            'is_manual'                => true,
            'no'                       => $log->ngtma()->count() + 1,
            'project'                  => $request->project,
            'id_lop'                   => $request->id_lop,
            'cc'                       => $request->cc,
            'nipnas'                   => $request->nipnas,
            'am'                       => $request->am,
            'mitra'                    => $request->mitra,
            'plan_bulan_billcomp_2025' => $request->plan_bulan_billcomp_2025,
            'est_nilai_bc'             => $request->est_nilai_bc,
        ]);

        return redirect()->back()
            ->with('success', "Data untuk project \"{$data->project}\" berhasil disimpan.");
    }

    public function toggleStatus($id)
    {
        $log         = ScallingImport::findOrFail($id);
        $log->status = $log->status === 'active' ? 'inactive' : 'active';
        $log->save();
        return back()->with('success', 'Status berhasil diubah');
    }

    // ── PRIVATE HELPERS ───────────────────────────────────────────────
    private function readAndValidateHeaders(Worksheet $sheet): array
    {
        $headerMap    = [];
        $foundHeaders = [];

        for ($col = 1; $col <= self::MAX_COL; $col++) {
            $coordinate = Coordinate::stringFromColumnIndex($col) . self::HEADER_ROW;
            $normalized = preg_replace('/\s+/', ' ', strtoupper(trim((string) $sheet->getCell($coordinate)->getValue())));
            if (isset($this->columnMap[$normalized])) {
                $headerMap[$col] = $this->columnMap[$normalized];
                $foundHeaders[]  = $normalized;
            }
        }

        if (!in_array('PROJECT', $foundHeaders)) {
            throw new \Exception(
                "Header tidak ditemukan di baris ke-" . self::HEADER_ROW . ". " .
                "Kolom terbaca: " . (implode(', ', $foundHeaders) ?: '(tidak ada)') . "."
            );
        }

        return $headerMap;
    }

    private function isTotalRow(Worksheet $sheet, int $rowIndex): bool
    {
        for ($col = 1; $col <= self::MAX_COL; $col++) {
            $value = strtoupper(trim((string) $sheet->getCell(
                Coordinate::stringFromColumnIndex($col) . $rowIndex
            )->getValue()));
            if (str_contains($value, 'TOTAL')) return true;
        }
        return false;
    }

    private function readRow(Worksheet $sheet, int $rowIndex, array $headerMap): array
    {
        $rowData = [];
        for ($col = 1; $col <= self::MAX_COL; $col++) {
            if (!isset($headerMap[$col])) continue;
            $dbColumn  = $headerMap[$col];
            $raw       = $sheet->getCell(Coordinate::stringFromColumnIndex($col) . $rowIndex)->getValue();
            $cellValue = trim((string) $raw);

            if (in_array($dbColumn, ['no', 'plan_bulan_billcomp_2025'])) {
                $cellValue = is_numeric($cellValue) ? (int) $cellValue : null;
            } elseif ($dbColumn === 'est_nilai_bc') {
                if (is_numeric($raw)) {
                    $cellValue = (float) $raw;
                } else {
                    $clean     = str_replace(',', '.', preg_replace('/[^\d,.]/', '', $cellValue));
                    $cellValue = is_numeric($clean) ? (float) $clean : null;
                }
            } else {
                $cellValue = ($cellValue === '') ? null : $cellValue;
            }

            $rowData[$dbColumn] = $cellValue;
        }
        return $rowData;
    }

    private function isRowEmpty(array $rowData): bool
    {
        foreach ($rowData as $value) {
            if ($value !== null && $value !== '') return false;
        }
        return true;
    }

    public function progress(Request $request, string $segment)
    {
        $segmentMap = [
            'gov'     => ['label' => 'Government', 'db' => 'government'],
            'private' => ['label' => 'Private',    'db' => 'private'],
            'soe'     => ['label' => 'SOE',        'db' => 'soe'],
            'sme'     => ['label' => 'SME',        'db' => 'sme'],
        ];

        $type = 'ngtma';

        abort_if(!isset($segmentMap[$segment]), 404);
        abort_if(empty($type), 404);

        $segmentLabel = $segmentMap[$segment]['label'];
        $segmentDb    = $segmentMap[$segment]['db'];
        $typeLabel    = $type;

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

        if ($import) {
            $dataRows = \App\Models\Ngtma::where('imports_log_id', $import->id)
                ->with(['funnel.todayProgress'])
                ->get()
                ->filter(fn($r) => strtoupper(trim($r->no ?? '')) !== 'TOTAL')
                ->values();

            $dataIds = $dataRows->pluck('id');

            $allFunnels = \App\Models\FunnelTracking::with('todayProgress')
                ->whereIn('ngtma_id', $dataIds)
                ->get();

            $funnelMap = $dataIds->mapWithKeys(function ($dataId) use ($allFunnels) {
                $latest = $allFunnels->where('ngtma_id', $dataId)
                    ->sortByDesc('updated_at')
                    ->first();
                return [$dataId => $latest];
            });
        }

        return view('admin.ngtma.progress', compact(
            'segment', 'type',
            'segmentLabel', 'typeLabel',
            'periodeLabel', 'currentPeriode',
            'periodOptions',
            'import', 'dataRows', 'funnelMap'
        ));
    }

    public function updateFunnelCheckbox(Request $request, string $segment)
    {
        $segmentMap = [
            'gov' => 'government',
            'private' => 'private',
            'soe' => 'soe',
            'sme' => 'sme',
        ];

        abort_if(!isset($segmentMap[$segment]), 404);
        $request->validate([
            'data_type'    => 'required|in:ngtma',
            'ngtma_id'     => 'required|exists:ngtmas,id',
            'field'        => 'required|string',
            'value'        => 'required',
            'est_nilai_bc' => 'nullable',
        ]);

        if (auth()->user()->role !== 'admin') {
            $ngtma = \App\Models\Ngtma::find($request->ngtma_id);
            if ($ngtma) {
               $import = $ngtma->scallingImport;
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
            'ngtma_id'   => $request->ngtma_id,
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

        $dataId   = $request->ngtma_id;
        $funnel   = \App\Models\FunnelTracking::where('ngtma_id', $dataId)->first();
        $periodeImport = \App\Models\Ngtma::find($dataId)?->scallingImport;

        $dataIdsInPeriode = collect();
        if ($periodeImport) {
            $dataIdsInPeriode = \App\Models\Ngtma::where('imports_log_id', $periodeImport->id)
                ->pluck('id');
        }

        $total = \App\Models\FunnelTracking::whereIn('ngtma_id', $dataIdsInPeriode)
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
}
