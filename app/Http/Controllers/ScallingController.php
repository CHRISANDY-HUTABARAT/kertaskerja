<?php

namespace App\Http\Controllers;

use App\Models\ScallingData;
use App\Models\ScallingImport;
use App\Models\FunnelTracking;
use App\Models\TaskProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScallingController extends Controller
{
    // ── LAYOUT EXCEL ─────────────────────────────────────────────────────────
    private const HEADER_ROW     = 3;  // Baris ke-3: header kolom (NO, PROJECT, dst.)
    private const DATA_START_ROW = 7;  // Baris ke-7: data pertama
    private const MAX_COL        = 9;  // Kolom A(1) s/d I(9)

    // ── MAPPING HEADER EXCEL → KOLOM DATABASE ────────────────────────────────
    private array $columnMap = [
        'PROJECT'                  => 'project',
        'ID LOP'                   => 'id_lop',
        'CC'                       => 'cc',
        'NIPNAS'                   => 'nipnas',
        'AM'                       => 'am',
        'MITRA'                    => 'mitra',
        'EST BULAN BC'             => 'plan_bulan_billcomp_2025',
        'EST NILAI BC'             => 'est_nilai_bc',
    ];

    // ── HALAMAN INDEX ─────────────────────────────────────────────────────────

    public function indexGov()
    {
        return view('admin.scalling.gov.gov', $this->sharedViewData());
    }

    public function indexSoe()
    {
        return view('admin.scalling.soe.soe', $this->sharedViewData());
    }

    public function indexSme()
    {
        return view('admin.scalling.sme.sme', $this->sharedViewData());
    }

    public function indexPrivate()
    {
        return view('admin.scalling.private.private', $this->sharedViewData());
    }

    public function indexInitiate()
    {
        return view('admin.scalling.initiate.initiate', $this->sharedViewData());
    }

    // ── GOVERNMENT ────────────────────────────────────────────────────────────

    public function onHandGov()
    {
        $logs     = ScallingImport::where('type', 'on-hand')->where('segment', 'government')->latest()->paginate(10);
        $projects = ScallingData::with('scallingImport')->latest()->paginate(20);
        // dd($projects);
        $currentPeriode = request()->get('periode', date('Y-m'));
        return view('admin.scalling.gov.onHand', compact('logs', 'projects', 'currentPeriode'));
    }

    // public function koreksiGov()
    // {
    //     $logs     = ScallingImport::where('type', 'koreksi')->where('segment', 'government')->latest()->paginate(10);
    //     $projects = ScallingData::with('scallingImport')->latest()->paginate(20);
    //     return view('admin.scalling.gov.koreksi', compact('logs', 'projects'));
    // }

    public function qualifiedGov()
    {
        $logs     = ScallingImport::where('type', 'qualified')->where('segment', 'government')->latest()->paginate(10);
        $projects = ScallingData::with('scallingImport')->latest()->paginate(20);
        $currentPeriode = request()->get('periode', date('Y-m'));

        return view('admin.scalling.gov.qualified', compact('logs', 'projects', 'currentPeriode'));
    }

    public function initiateGov()
    {
        $currentPeriode = now()->format('Y-m');
        $logs           = ScallingImport::where('type', 'initiate')->where('segment', 'government')->latest()->paginate(10);
        $projects       = ScallingData::with('scallingImport')
            ->whereHas('scallingImport', function ($query) {
                $query->where('type', 'initiate')->where('segment', 'government');
            })
            ->latest()
            ->paginate(10);
        $periodes = ScallingImport::whereHas('scallingData')
            ->selectRaw("DATE_FORMAT(periode, '%Y-%m') as periode")
            ->where('type', 'initiate')
            ->where('segment', 'government')
            ->distinct()
            ->orderBy('periode', 'asc')
            ->pluck('periode');

        return view('admin.scalling.gov.initiate', compact('logs', 'projects', 'periodes', 'currentPeriode'));
    }

    // ── SOE ───────────────────────────────────────────────────────────────────

    public function onHandSoe()
    {
        $logs     = ScallingImport::where('type', 'on-hand')->where('segment', 'soe')->latest()->paginate(10);
        $projects = ScallingData::with('scallingImport')->latest()->paginate(20);
        $currentPeriode = request()->get('periode', date('Y-m'));

        return view('admin.scalling.soe.onHand', compact('logs', 'projects', 'currentPeriode'));
    }

    // public function koreksiSoe()
    // {
    //     $logs     = ScallingImport::where('type', 'koreksi')->where('segment', 'soe')->latest()->paginate(10);
    //     $projects = ScallingData::with('scallingImport')->latest()->paginate(20);
    //     return view('admin.scalling.soe.koreksi', compact('logs', 'projects'));
    // }

    public function qualifiedSoe()
    {
        $logs     = ScallingImport::where('type', 'qualified')->where('segment', 'soe')->latest()->paginate(10);
        $projects = ScallingData::with('scallingImport')->latest()->paginate(20);
        $currentPeriode = request()->get('periode', date('Y-m'));
        return view('admin.scalling.soe.qualified', compact('logs', 'projects', 'currentPeriode'));
    }

    public function initiateSoe()
    {
        $currentPeriode = now()->format('Y-m');
        $logs           = ScallingImport::where('type', 'initiate')->where('segment', 'soe')->latest()->paginate(10);
        $projects       = ScallingData::with('scallingImport')
            ->whereHas('scallingImport', function ($query) {
                $query->where('type', 'initiate')->where('segment', 'soe');
            })
            ->latest()
            ->paginate(10);
        $periodes = ScallingImport::whereHas('scallingData')
            ->selectRaw("DATE_FORMAT(periode, '%Y-%m') as periode")
            ->where('type', 'initiate')
            ->where('segment', 'soe')
            ->distinct()
            ->orderBy('periode', 'asc')
            ->pluck('periode');

        return view('admin.scalling.soe.initiate', compact('logs', 'projects', 'periodes', 'currentPeriode'));
    }

    // ── PRIVATE ───────────────────────────────────────────────────────────────

    public function onHandPrivate()
    {
        $logs     = ScallingImport::where('type', 'on-hand')->where('segment', 'private')->latest()->paginate(10);
        $projects = ScallingData::with('scallingImport')->latest()->paginate(20);
        $currentPeriode = request()->get('periode', date('Y-m'));
        return view('admin.scalling.private.onHand', compact('logs', 'projects', 'currentPeriode'));
    }

    // public function koreksiPrivate()
    // {
    //     $logs     = ScallingImport::where('type', 'koreksi')->where('segment', 'private')->latest()->paginate(10);
    //     $projects = ScallingData::with('scallingImport')->latest()->paginate(20);
    //     return view('admin.scalling.private.koreksi', compact('logs', 'projects'));
    // }

    public function qualifiedPrivate()
    {
        $logs     = ScallingImport::where('type', 'qualified')->where('segment', 'private')->latest()->paginate(10);
        $projects = ScallingData::with('scallingImport')->latest()->paginate(20);
        $currentPeriode = request()->get('periode', date('Y-m'));
        return view('admin.scalling.private.qualified', compact('logs', 'projects', 'currentPeriode'));
    }

    public function initiatePrivate()
    {
        $currentPeriode = now()->format('Y-m');
        $logs           = ScallingImport::where('type', 'initiate')->where('segment', 'private')->latest()->paginate(10);
        $projects       = ScallingData::with('scallingImport')
            ->whereHas('scallingImport', function ($query) {
                $query->where('type', 'initiate')->where('segment', 'private');
            })
            ->latest()
            ->paginate(10);
        $periodes = ScallingImport::whereHas('scallingData')
            ->selectRaw("DATE_FORMAT(periode, '%Y-%m') as periode")
            ->where('type', 'initiate')
            ->where('segment', 'private')
            ->distinct()
            ->orderBy('periode', 'asc')
            ->pluck('periode');

        return view('admin.scalling.private.initiate', compact('logs', 'projects', 'periodes', 'currentPeriode'));
    }

    // ── SME ───────────────────────────────────────────────────────────────────

    public function onHandSme()
    {
        $logs     = ScallingImport::where('type', 'on-hand')->where('segment', 'sme')->latest()->paginate(10);
        $projects = ScallingData::with('scallingImport')->latest()->paginate(20);
        $currentPeriode = request()->get('periode', date('Y-m'));
        return view('admin.scalling.sme.onHand', compact('logs', 'projects', 'currentPeriode'));
    }

    // public function koreksiSme()
    // {
    //     $logs     = ScallingImport::where('type', 'koreksi')->where('segment', 'sme')->latest()->paginate(10);
    //     $projects = ScallingData::with('scallingImport')->latest()->paginate(20);
    //     return view('admin.scalling.sme.koreksi', compact('logs', 'projects'));
    // }

    public function qualifiedSme()
    {
        $logs     = ScallingImport::where('type', 'qualified')->where('segment', 'sme')->latest()->paginate(10);
        $projects = ScallingData::with('scallingImport')->latest()->paginate(20);
        $currentPeriode = request()->get('periode', date('Y-m'));
        return view('admin.scalling.sme.qualified', compact('logs', 'projects', 'currentPeriode'));
    }

    public function initiateSme()
    {
        $currentPeriode = now()->format('Y-m');
        $logs           = ScallingImport::where('type', 'initiate')->where('segment', 'sme')->latest()->paginate(10);
        $projects       = ScallingData::with('scallingImport')
            ->whereHas('scallingImport', function ($query) {
                $query->where('type', 'initiate')->where('segment', 'sme');
            })
            ->latest()
            ->paginate(10);
        $periodes = ScallingImport::whereHas('scallingData')
            ->selectRaw("DATE_FORMAT(periode, '%Y-%m') as periode")
            ->where('type', 'initiate')
            ->where('segment', 'sme')
            ->distinct()
            ->orderBy('periode', 'asc')
            ->pluck('periode');

        return view('admin.scalling.sme.initiate', compact('logs', 'projects', 'periodes', 'currentPeriode'));
    }

    // ── IMPORT ────────────────────────────────────────────────────────────────

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [
            'excel_file.required' => 'File Excel wajib diunggah.',
            'excel_file.mimes'    => 'File harus berformat .xlsx, .xls, atau .csv.',
            'excel_file.max'      => 'Ukuran file maksimal 10 MB.',
        ]);
        $request->validate([
            'periode'  => 'required|date_format:Y-m',
            'type'     => 'required|nullable',
            'segment'  => 'required|nullable',
        ]);

        $periodeDate      = $request->periode . '-01';
        $file             = $request->file('excel_file');
        $originalFilename = $file->getClientOriginalName();
        $tempPath         = $file->getRealPath();

        try {
            // ── 1. Baca & parsing Excel dulu — SEBELUM menyentuh database ──
            $spreadsheet = IOFactory::load($tempPath);
            $sheet       = $spreadsheet->getActiveSheet();

            $headerMap  = $this->readAndValidateHeaders($sheet);
            $rows       = [];
            $highestRow = $sheet->getHighestRow();

            for ($rowIndex = self::DATA_START_ROW; $rowIndex <= $highestRow; $rowIndex++) {
                if ($this->isTotalRow($sheet, $rowIndex)) {
                    break;
                }

                $rowData = $this->readRow($sheet, $rowIndex, $headerMap);

                if ($this->isRowEmpty($rowData)) {
                    continue;
                }

                $rows[] = $rowData;
            }

            if (empty($rows)) {
                throw new \Exception('Tidak ada data valid yang ditemukan di dalam file Excel.');
            }

            // ── 2. Semua parsing berhasil — baru simpan ke database ──
            DB::transaction(function () use ($rows, $originalFilename, $request, $periodeDate) {
                $log = ScallingImport::where('periode', $periodeDate)
                    ->where('type', $request->type)
                    ->where('segment', $request->segment)
                    ->first();

                if ($log) {
                    $log->update([
                        'original_filename'   => $originalFilename,
                        'status'              => 'active',
                        'type'                => $request->type,
                        'segment'             => $request->segment,
                        'total_rows_imported' => count($rows),
                        'uploaded_by'         => auth()->user()->name ?? $request->ip(),
                    ]);

                    // ✅ Hanya hapus data dari import Excel sebelumnya, biarkan data manual tetap ada
                    $importedIds = ScallingData::where('imports_log_id', $log->id)
                        ->where('is_manual', false)
                        ->pluck('id');

                    if ($importedIds->isNotEmpty()) {
                        FunnelTracking::whereIn('data_id', $importedIds)->each(function ($ft) {
                            TaskProgress::where('task_id', $ft->id)->delete();
                        });
                        FunnelTracking::whereIn('data_id', $importedIds)->delete();
                        ScallingData::whereIn('id', $importedIds)->delete();
                    }
                } else {
                    $log = ScallingImport::create([
                        'original_filename'   => $originalFilename,
                        'status'              => 'active',
                        'type'                => $request->type,
                        'segment'             => $request->segment,
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
                    ScallingData::insert($chunk);
                }

                 // ✅ SETELAH FOREACH — update total dan renumber no
                $log->update([
                    'total_rows_imported' => ScallingData::where('imports_log_id', $log->id)->count(),
                ]);

                // Renumber: data manual duluan, lalu data import urut by id
                $allRows = ScallingData::where('imports_log_id', $log->id)
                    ->orderBy('is_manual', 'desc')
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($allRows as $index => $row) {
                    $row->update(['no' => $index + 1]);
                }
            });

            @unlink($tempPath);

            $count = count($rows);
            return redirect()->back()
                ->with('success', "Import berhasil! {$count} baris diimpor dari \"{$originalFilename}\".");

        } catch (\Throwable $e) {
            @unlink($tempPath);
            return redirect()->back()
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    // ── SHOW / DESTROY ────────────────────────────────────────────────────────

    public function show(ScallingImport $scallingImport)
    {
        $projects = $scallingImport->scallingData()->paginate(20);
        return view('admin.scalling.show', compact('scallingImport', 'projects'));
    }

    public function destroy(ScallingImport $scallingImport)
    {
        DB::transaction(function () use ($scallingImport) {
            $scallingImport->scallingData()->delete();
            $scallingImport->delete();
        });

        return redirect()->route('admin.scalling.index')
            ->with('success', 'Log dan data terkait berhasil dihapus.');
    }

    // ── STORE DATA (shared: gov, soe, private, sme) ───────────────────────────

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
            'imports_log_id'           => $log->id,
            'is_manual'                => true,
            'no'                       => $log->data()->count() + 1,
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
            'no'                       => 1,
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

    // ── TOGGLE STATUS ─────────────────────────────────────────────────────────

    public function toggleStatus($id)
    {
        $collection         = ScallingImport::findOrFail($id);
        $collection->status = $collection->status === 'active' ? 'inactive' : 'active';
        $collection->save();

        return back()->with('success', 'Status berhasil diubah');
    }

    // ── PRIVATE HELPERS ───────────────────────────────────────────────────────

    private function sharedViewData(): array
    {
        return [
            'logs'     => ScallingImport::latest()->paginate(10),
            'projects' => ScallingData::with('scallingImport')->latest()->paginate(20),
        ];
    }

    private function readAndValidateHeaders(Worksheet $sheet): array
    {
        $headerMap    = [];
        $foundHeaders = [];
        $headerRow    = self::HEADER_ROW;

        for ($col = 1; $col <= self::MAX_COL; $col++) {
            $coordinate = Coordinate::stringFromColumnIndex($col) . $headerRow;
            $rawValue   = $sheet->getCell($coordinate)->getValue();
            $normalized = preg_replace('/\s+/', ' ', strtoupper(trim((string) $rawValue)));

            if (isset($this->columnMap[$normalized])) {
                $headerMap[$col] = $this->columnMap[$normalized];
                $foundHeaders[]  = $normalized;
            }
        }

        if ( !in_array('PROJECT', $foundHeaders)) {
            $found = implode(', ', $foundHeaders) ?: '(tidak ada)';
            throw new \Exception(
                "Header tidak ditemukan di baris ke-" . self::HEADER_ROW . ". " .
                "Kolom terbaca: {$found}. Pastikan header PROJECT ada di baris " . self::HEADER_ROW . "."
            );
        }

        return $headerMap;
    }

    private function isTotalRow(Worksheet $sheet, int $rowIndex): bool
    {
        $rowNum = $rowIndex;

        for ($col = 1; $col <= self::MAX_COL; $col++) {
            $coordinate = Coordinate::stringFromColumnIndex($col) . $rowNum;
            $value      = strtoupper(trim((string) $sheet->getCell($coordinate)->getValue()));

            if (str_contains($value, 'TOTAL')) {
                return true;
            }
        }

        return false;
    }

    private function readRow(Worksheet $sheet, int $rowIndex, array $headerMap): array
    {
        $rowData = [];
        $rowNum  = $rowIndex;

        for ($col = 1; $col <= self::MAX_COL; $col++) {
            if (!isset($headerMap[$col])) continue;

            $dbColumn   = $headerMap[$col];
            $coordinate = Coordinate::stringFromColumnIndex($col) . $rowNum;
            $raw        = $sheet->getCell($coordinate)->getValue();
            $cellValue  = trim((string) $raw);

            if (in_array($dbColumn, ['no', 'plan_bulan_billcomp_2025'])) {
                $cellValue = is_numeric($cellValue) ? (int) $cellValue : null;

            } elseif ($dbColumn === 'est_nilai_bc') {
                if (is_numeric($raw)) {
                    $cellValue = (float) $raw;
                } else {
                    $clean     = preg_replace('/[^\d,.]/', '', $cellValue);
                    $clean     = str_replace('.', '', $clean);
                    $clean     = str_replace(',', '.', $clean);
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
}
