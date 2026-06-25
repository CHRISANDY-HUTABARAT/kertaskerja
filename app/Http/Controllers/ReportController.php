<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Ct0;
use App\Models\Ctc;
use App\Models\RisingStar;
use App\Models\Gap;
use App\Models\Psak;
use App\Models\ScallingImport;
use App\Models\ScallingData;
use App\Models\FunnelTracking;
use App\Models\Hsi;
use App\Models\Telda;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filtered      = $request->has('filter');
        $filterBulan   = $request->input('bulan');
        $filterTahun   = $request->input('tahun');
        $filterTanggal = $request->input('tanggal');

        if (!$filtered) {
            $filtered      = true;
            $filterBulan   = Carbon::now()->month;
            $filterTahun   = Carbon::now()->year;
            $filterTanggal = Carbon::now()->day;
        }

        $isLastDayOfMonth = false;
        if ($filtered && $filterTanggal && $filterBulan && $filterTahun) {
            $daysInMonth      = Carbon::createFromDate($filterTahun, $filterBulan, 1)->daysInMonth;
            $isLastDayOfMonth = ((int)$filterTanggal >= $daysInMonth);
        }

        $filterPeriode = function ($q) use ($filtered, $filterBulan, $filterTahun, $filterTanggal, $isLastDayOfMonth) {
            if ($filtered && $filterBulan) {
                $q->whereMonth('periode', $filterBulan);
            }
            if ($filtered && $filterTahun) {
                $q->whereYear('periode', $filterTahun);
            }
            if ($filtered && $filterTanggal && $filterBulan && $filterTahun && !$isLastDayOfMonth) {
                $cutoff = Carbon::createFromDate($filterTahun, $filterBulan, $filterTanggal)->endOfDay();
                $q->where('created_at', '<=', $cutoff);
            }
        };

        // $toFloat didefinisikan di sini agar tersedia untuk semua query di bawah
        $toFloat = fn($v) => floatval(str_replace(',', '.', $v ?? 0));

        $latestVal = function (string $type, string $col, ?string $segment = null) use ($filterPeriode) {
            $row = Collection::where('type', $type)
                ->when($segment, fn($q) => $q->where('segment', $segment))
                ->tap($filterPeriode)
                ->orderBy('created_at', 'desc')
                ->first();
            return $row ? (float) str_replace(',', '.', $row->{$col} ?? 0) : null;
        };

        $c3mrKomitmen  = $latestVal('C3MR', 'commitment');
        $c3mrRealisasi = $latestVal('C3MR', 'real_ratio');
        $c3mrRow = Collection::where('type','C3MR')
            ->tap($filterPeriode)->orderBy('created_at','desc')->first();
        $c3mrUpdatedAt = $c3mrRow?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-';

        $bilperKomitmen  = $latestVal('Billing Perdana', 'commitment');
        $bilperRealisasi = $latestVal('Billing Perdana', 'real_ratio');
        $bilperRow = Collection::where('type','Billing Perdana')
            ->tap($filterPeriode)->orderBy('created_at','desc')->first();
        $bilperUpdatedAt = $bilperRow?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-';

        $crData = [];
        $segmentMap = [
            'GOV'     => 'Government',
            'PRIVATE' => 'Private',
            'SME'     => 'SME',
            'SOE'     => 'SOE',
        ];
        $crUpdatedAt = [];
        foreach ($segmentMap as $key => $dbSegment) {
            $crData[$key] = [
                'komitmen'  => $latestVal('Collection Ratio', 'commitment', $dbSegment),
                'realisasi' => $latestVal('Collection Ratio', 'real_ratio', $dbSegment),
            ];
            $crRow = Collection::where('type','Collection Ratio')
                ->where('segment',$dbSegment)->tap($filterPeriode)
                ->orderBy('created_at','desc')->first();
            $crUpdatedAt[$key] = $crRow?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-';
        }

        $cycSegmentMap = [
            'GOV'     => 'Government',
            'PRIVATE' => 'Private',
            'SME'     => 'SME',
            'SOE'     => 'SOE',
        ];
        $cycData = [];
        $cycUpdatedAt = [];
        foreach ($cycSegmentMap as $key => $dbSegment) {
            $cycData[$key] = [
                'komitmen'  => $latestVal('CYC', 'commitment', $dbSegment),
                'realisasi' => $latestVal('CYC', 'real_ratio', $dbSegment),
            ];
            $cycRow = Collection::where('type', 'CYC')
                ->where('segment', $dbSegment)->tap($filterPeriode)
                ->orderBy('created_at', 'desc')->first();
            $cycUpdatedAt[$key] = $cycRow?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-';
        }

        $cycVals = [];
        foreach ($cycData as $seg => $val) {
            $cycVals[$seg] = $val['komitmen'] == 0 ? 0 : ($val['realisasi'] / $val['komitmen']) * 100;
        }

        // UTIP Corrective — ambil record terbaru dalam periode filter
        $utipCorRows = Collection::where('type', 'UTIP Corrective')
            ->whereIn('id', function($q) {
                $q->selectRaw('MAX(id)')
                ->from('collections')
                ->where('type', 'UTIP Corrective')
                ->groupBy('kondisi', 'periode');
            })
            ->get();

        $utipCorPlan = $utipCorRows->sum(fn($r) => $toFloat($r->plan));
        $utipCorReal = $utipCorRows->sum(fn($r) => $toFloat($r->real_ratio));
        $utipCorUpdated = $utipCorRows->whereNotNull('real_updated_at')->sortByDesc('real_updated_at')->first();

        $utipCorrective = [
            'label'      => 'UTIP Corrective',
            'planRp'     => $utipCorRows->isEmpty() ? null : round($utipCorPlan / 1000000, 2),
            'commitRp'   => $utipCorRows->isEmpty() ? null : round($utipCorPlan / 1000000, 2),
            'realRp'     => $utipCorRows->isEmpty() ? null : round($utipCorReal / 1000000, 2),
            'updated_at' => $utipCorUpdated?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
        ];

        $arSegments = ['DGS', 'DPS', 'DSS', 'RBS'];
        $arRows = [];
        foreach ($arSegments as $segment) {
            $rows = Collection::where('type', 'ar')
                ->where('segment', $segment)
                ->whereIn('id', function ($q) use ($segment) {
                    $q->selectRaw('MAX(id)')
                        ->from('collections')
                        ->where('type', 'ar')
                        ->where('segment', $segment)
                        ->groupBy('kondisi', 'periode');
                })
                ->tap($filterPeriode)
                ->get();

            $realRp = $rows->isEmpty() ? null : round($rows->sum(fn($r) => $toFloat($r->real_ratio)) / 1000000, 2);
            $rowUpdated = $rows->whereNotNull('real_updated_at')->sortByDesc('real_updated_at')->first();

            $arRows[] = [
                'label'      => 'AR ' . $segment,
                'slug'       => strtolower($segment),
                'realRp'     => $realRp,
                'updated_at' => $rowUpdated?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
                'ach'        => '-',
            ];
        }
        $arRowspan   = count($arRows);
        $fairnessAR  = '0-100';

        $periodes = [
            ['bulan' => 7,  'tahun' => 2025, 'label' => 'New UTIP Jul 2025', 'type' => 'New UTIP Jul 2025'],
            ['bulan' => 8,  'tahun' => 2025, 'label' => 'New UTIP Aug 2025', 'type' => 'New UTIP Aug 2025'],
            ['bulan' => 9,  'tahun' => 2025, 'label' => 'New UTIP Sep 2025', 'type' => 'New UTIP Sep 2025'],
            ['bulan' => 10, 'tahun' => 2025, 'label' => 'New UTIP Okt 2025', 'type' => 'New UTIP Okt 2025'],
            ['bulan' => 11, 'tahun' => 2025, 'label' => 'New UTIP Nov 2025', 'type' => 'New UTIP Nov 2025'],
            ['bulan' => 12, 'tahun' => 2025, 'label' => 'New UTIP Des 2025', 'type' => 'New UTIP Des 2025'],
            ['bulan' => 1,  'tahun' => 2026, 'label' => 'New UTIP Jan 2026', 'type' => 'New UTIP Jan 2026'],
            ['bulan' => 2,  'tahun' => 2026, 'label' => 'New UTIP Feb 2026', 'type' => 'New UTIP Feb 2026'],
            ['bulan' => 3,  'tahun' => 2026, 'label' => 'New UTIP Mar 2026', 'type' => 'New UTIP Mar 2026'],
            ['bulan' => 4,  'tahun' => 2026, 'label' => 'New UTIP Apr 2026', 'type' => 'New UTIP Apr 2026'],
            ['bulan' => 5,  'tahun' => 2026, 'label' => 'New UTIP Mei 2026', 'type' => 'New UTIP Mei 2026'],
            ['bulan' => 6,  'tahun' => 2026, 'label' => 'New UTIP Jun 2026', 'type' => 'New UTIP Jun 2026'],
            ['bulan' => 7,  'tahun' => 2026, 'label' => 'New UTIP Jul 2026', 'type' => 'New UTIP Jul 2026'],
            ['bulan' => 8,  'tahun' => 2026, 'label' => 'New UTIP Aug 2026', 'type' => 'New UTIP Aug 2026'],
            ['bulan' => 9,  'tahun' => 2026, 'label' => 'New UTIP Sep 2026', 'type' => 'New UTIP Sep 2026'],
            ['bulan' => 10, 'tahun' => 2026, 'label' => 'New UTIP Okt 2026', 'type' => 'New UTIP Okt 2026'],
            ['bulan' => 11, 'tahun' => 2026, 'label' => 'New UTIP Nov 2026', 'type' => 'New UTIP Nov 2026'],
            ['bulan' => 12, 'tahun' => 2026, 'label' => 'New UTIP Des 2026', 'type' => 'New UTIP Des 2026'],
        ];

        // New UTIP — exact match by type, ambil record terbaru
        $newUtipPeriodes = [];
        $filterDate = Carbon::createFromDate($filterTahun, $filterBulan, 1);

        foreach ($periodes as $p) {
            $rows = Collection::where('type', $p['type'])
                ->whereIn('id', function($q) use ($p) {
                    $q->selectRaw('MAX(id)')
                    ->from('collections')
                    ->where('type', $p['type'])
                    ->groupBy('kondisi', 'periode');
                })
                ->get();

            $planRaw = $rows->sum(fn($r) => $toFloat($r->plan));
            $realRp  = round($rows->sum(fn($r) => $toFloat($r->real_ratio)) / 1000000, 2);
            $rowUpdated = $rows->whereNotNull('real_updated_at')->sortByDesc('real_updated_at')->first();

            $rowDate    = Carbon::createFromDate($p['tahun'], $p['bulan'], 1);
            $monthsDiff = (($p['tahun'] - $filterTahun) * 12) + ($p['bulan'] - $filterBulan);

            if ($monthsDiff >= 0) {
                $commitMultiplier = 0;
            } elseif ($monthsDiff === -1) {
                $commitMultiplier = 0.30;
            } elseif ($monthsDiff === -2) {
                $commitMultiplier = 0.60;
            } else {
                $commitMultiplier = 1.00;
            }

            $newUtipPeriodes[] = [
                'label'      => $p['label'],
                'planRp'     => $rows->isEmpty() ? null : round($planRaw / 1000000, 2),
                'commitRp'   => $rows->isEmpty() ? null : round(($planRaw * $commitMultiplier) / 1000000, 2),
                'realRp'     => $rows->isEmpty() ? null : $realRp,
                'updated_at' => $rowUpdated?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
                'raw_updated_at' => $rowUpdated?->real_updated_at,
            ];
        }


        $filterPeriodeCt0 = function ($q) use ($filtered, $filterBulan, $filterTahun, $filterTanggal, $isLastDayOfMonth) {
            if ($filtered && $filterBulan) $q->whereMonth('periode', $filterBulan);
            if ($filtered && $filterTahun) $q->whereYear('periode', $filterTahun);
            if ($filtered && $filterTanggal && $filterBulan && $filterTahun && !$isLastDayOfMonth) {
                $cutoff = Carbon::createFromDate($filterTahun, $filterBulan, $filterTanggal)->endOfDay();
                $q->where('created_at', '<=', $cutoff);
            }
        };

        $ct0Regions = [
            'Inner Sumut', 'Telda Lubuk Pakam', 'Telda Binjai', 'Telda Kisaran',
            'Telda Siantar', 'Telda Kabanjahe', 'Telda Rantauprapat',
            'Telda Toba', 'Telda Sibolga', 'Telda Padang Sidempuan',
        ];
        $ct0RegionLabels = [
            'Inner Sumut'            => 'Inner Sumut',
            'Telda Lubuk Pakam'      => 'Lubuk Pakam',
            'Telda Binjai'           => 'Binjai',
            'Telda Kisaran'          => 'Kisaran',
            'Telda Siantar'          => 'Siantar',
            'Telda Kabanjahe'        => 'Kabanjahe',
            'Telda Rantauprapat'     => 'Rantau Prapat',
            'Telda Toba'             => 'Toba',
            'Telda Sibolga'          => 'Sibolga',
            'Telda Padang Sidempuan' => 'Padang Sidempuan',
        ];

        $ct0Data = [];
        foreach ($ct0Regions as $region) {
            $row    = Ct0::where('region', $region)->tap($filterPeriodeCt0)->orderBy('created_at', 'desc')->first();
            $plan   = $row ? $toFloat($row->plan)       : null;
            $commit = $row ? $toFloat($row->commitment) : null;
            $real   = $row ? $toFloat($row->real_ratio) : null;
            $ach    = is_null($plan) || $plan == 0 ? '-' : number_format(($real / $plan) * 100, 2, ',', '.') . '%';

            $ct0Data[] = [
                'label'      => $ct0RegionLabels[$region] ?? $region,
                'plan'       => !is_null($plan)   ? round($plan   / 1000000, 2) : null,
                'commit'     => !is_null($commit) ? round($commit / 1000000, 2) : null,
                'real'       => !is_null($real)   ? round($real   / 1000000, 2) : null,
                'ach'        => $ach,
                'updated_at' => $row?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
            ];
        }

        $ct0TotalPlan = array_sum(array_column($ct0Data, 'plan'));
        $ct0TotalReal = array_sum(array_column($ct0Data, 'real'));
        $ct0Score = $ct0TotalPlan == 0 ? '-' : number_format(($ct0TotalReal / $ct0TotalPlan) * 100, 2, ',', '.') . '%';

        $ctcCt0Row       = Ctc::where('segment', 'CT0')->tap($filterPeriodeCt0)->orderBy('created_at', 'desc')->first();
        $ctcCt0Real      = $ctcCt0Row ? $toFloat($ctcCt0Row->real_ratio) : 0;
        $ctcCt0UpdatedAt = $ctcCt0Row?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-';

        $ctcSegments = ['Sales HSI (all)', 'Churn', 'Winback'];
        $ctcData = [];
        foreach ($ctcSegments as $seg) {
            $row    = Ctc::where('segment', $seg)->tap($filterPeriodeCt0)->orderBy('created_at', 'desc')->first();
            $commit = $row ? $toFloat($row->commitment) : 0;
            $real   = $row ? $toFloat($row->real_ratio) : 0;
            if ($seg === 'Churn') {
                $ach = $real == 0 ? '-' : number_format(($commit / $real) * 100, 2) . '%';
            } else {
                $ach = $commit == 0 ? '-' : number_format(($real / $commit) * 100, 2, ',', '.') . '%';
            }
            $ctcData[$seg] = [
                'commit'     => $commit,
                'real'       => $real,
                'ach'        => $ach,
                'updated_at' => $row?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
            ];
        }

        $lossRateDenom = $ctcData['Sales HSI (all)']['real'] + $ctcData['Winback']['real'];
        $lossRateReal  = $lossRateDenom == 0 ? '-'
            : number_format((($ctcCt0Real + $ctcData['Churn']['real']) / $lossRateDenom) * 100, 2, ',', '.') . '%';
        $lossRateAch   = $lossRateReal;
        $lossRateUpdatedAt = '-';

        $filterPeriodeRs = function ($q) use ($filtered, $filterBulan, $filterTahun, $filterTanggal, $isLastDayOfMonth) {
            if ($filtered && $filterBulan) $q->whereMonth('periode', $filterBulan);
            if ($filtered && $filterTahun) $q->whereYear('periode', $filterTahun);
            if ($filtered && $filterTanggal && $filterBulan && $filterTahun && !$isLastDayOfMonth) {
                $cutoff = Carbon::createFromDate($filterTahun, $filterBulan, $filterTanggal)->endOfDay();
                $q->where('created_at', '<=', $cutoff);
            }
        };

        $rsLatest = function (int $typeId) use ($filterPeriodeRs) {
            return RisingStar::where('type_id', $typeId)
                ->tap($filterPeriodeRs)
                ->orderBy('created_at', 'desc')
                ->first();
        };

        $b1TypeIds = [3, 1, 2, 4];
        $b1Labels  = [1 => 'Visiting AM Gov', 2 => 'Visiting AM SME', 3 => 'Visiting GM', 4 => 'Visiting HOTD'];
        $b1Data = [];
        $b1AchSum = 0; $b1AchCount = 0;
        foreach ($b1TypeIds as $tid) {
            $row    = $rsLatest($tid);
            $commit = $row ? $toFloat($row->commitment) : null;
            $real   = $row ? $toFloat($row->real_ratio) : null;
            $ratio  = $commit > 0 ? ($real / $commit) * 100 : 0;
            if ($commit > 0) { $b1AchSum += $ratio; $b1AchCount++; }
            $b1Data[$tid] = [
                'label'  => $b1Labels[$tid],
                'commit' => $commit,
                'real'   => $real,
                'ratio'  => $ratio,
                'updated_at' => $row?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
            ];
        }
        $gmRatio   = $b1Data[3]['commit'] > 0 ? $b1Data[3]['real'] / $b1Data[3]['commit'] : 0;
        $govRatio  = $b1Data[1]['commit'] > 0 ? $b1Data[1]['real'] / $b1Data[1]['commit'] : 0;
        $smeRatio  = $b1Data[2]['commit'] > 0 ? $b1Data[2]['real'] / $b1Data[2]['commit'] : 0;
        $hotdRatio = $b1Data[4]['commit'] > 0 ? $b1Data[4]['real'] / $b1Data[4]['commit'] : 0;
        $b1Ach    = ($gmRatio * 0.40) + ((($govRatio + $smeRatio + $hotdRatio) / 3) * 0.60);
        $b1Score  = number_format($b1Ach * 100, 2, ',', '.') . '%';

        $b2TypeIds = [5, 6, 7, 8];
        $b2Labels  = [5 => 'Profiling MAPS AM Gov', 6 => 'Profiling MAPS AM SME', 7 => 'Profiling HOTD: LEGS', 8 => 'Profiling HOTD: SME'];
        $b2Data = [];
        $b2AchSum = 0; $b2AchCount = 0;
        foreach ($b2TypeIds as $tid) {
            $row    = $rsLatest($tid);
            $commit = $row ? $toFloat($row->commitment) : null;
            $real   = $row ? $toFloat($row->real_ratio) : null;
            $ratio  = $commit > 0 ? ($real / $commit) * 100 : 0;
            if ($commit > 0) { $b2AchSum += $ratio; $b2AchCount++; }
            $b2Data[$tid] = [
                'label'  => $b2Labels[$tid],
                'commit' => $commit,
                'real'   => $real,
                'ratio'  => $ratio,
                'updated_at' => $row?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
            ];
        }
        $mapsReal   = $b2Data[5]['real']   + $b2Data[6]['real'];
        $mapsCommit = $b2Data[5]['commit'] + $b2Data[6]['commit'];
        $mapsRatio  = $mapsCommit > 0 ? $mapsReal / $mapsCommit : 0;
        $legsRatio  = $b2Data[7]['commit'] > 0 ? $b2Data[7]['real'] / $b2Data[7]['commit'] : 0;
        $hotdSmeRatio = $b2Data[8]['commit'] > 0 ? $b2Data[8]['real'] / $b2Data[8]['commit'] : 0;
        $b2Ach    = ($mapsRatio + $legsRatio + $hotdSmeRatio) / 3;
        $b2Score  = number_format($b2Ach * 100, 2, ',', '.') . '%';

        $b3TypeIds = [9, 10];
        $b3Labels  = [9 => 'Kecukupan LOP: Gov', 10 => 'Kecukupan LOP: SME'];
        $b3Data = [];
        $b3AchSum = 0; $b3AchCount = 0;
        foreach ($b3TypeIds as $tid) {
            $row    = $rsLatest($tid);
            $commit = $row ? $toFloat($row->commitment) : null;
            $real   = $row ? $toFloat($row->real_ratio) : null;
            $ratio  = $commit > 0 ? ($real / $commit) * 100 : 0;
            if ($commit > 0) { $b3AchSum += $ratio; $b3AchCount++; }
            $b3Data[$tid] = [
                'label'      => $b3Labels[$tid],
                'commit'     => $commit,
                'real'       => $real,
                'ratio'      => $ratio,
                'updated_at' => $row?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
            ];
        }
        $b3TotalReal   = $b3Data[9]['real']   + $b3Data[10]['real'];
        $b3TotalCommit = $b3Data[9]['commit'] + $b3Data[10]['commit'];
        $b3Ach   = $b3TotalCommit > 0 ? $b3TotalReal / $b3TotalCommit : 0;
        $b3Score = number_format($b3Ach * 100, 2, ',', '.') . '%';
        $b3UpdatedAt = $b3Data[9]['updated_at'] !== '-' ? $b3Data[9]['updated_at'] : ($b3Data[10]['updated_at'] ?? '-');

        $rsLatestByUser = function (int $typeId, int $userId) use ($filterPeriodeRs) {
            return RisingStar::where('type_id', $typeId)
                ->where('user_id', $userId)
                ->tap($filterPeriodeRs)
                ->orderBy('created_at', 'desc')
                ->first();
        };

        $b4Rows = [
            ['label' => 'AOSODOMORO 0-3 Bulan: Gov', 'row' => $rsLatestByUser(11, 2)],
            ['label' => 'AOSODOMORO 0-3 Bulan: SME', 'row' => $rsLatestByUser(11, 4)],
            ['label' => 'AOSODOMORO >3 Bulan: Gov',  'row' => $rsLatestByUser(12, 2)],
            ['label' => 'AOSODOMORO >3 Bulan: SME',  'row' => $rsLatestByUser(12, 4)],
        ];

        $b4Data = [];
        foreach ($b4Rows as $item) {
            $row    = $item['row'];
            $commit = $row ? $toFloat($row->commitment) : null;
            $real   = $row ? $toFloat($row->real_ratio) : null;
            $b4Data[] = [
                'label'      => $item['label'],
                'commit'     => $commit,
                'real'       => $real,
                'updated_at' => $row?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
            ];
        }

        $real03   = $b4Data[0]['real']   + $b4Data[1]['real'];
        $commit03 = $b4Data[0]['commit'] + $b4Data[1]['commit'];
        $real3p   = $b4Data[2]['real']   + $b4Data[3]['real'];
        $commit3p = $b4Data[2]['commit'] + $b4Data[3]['commit'];
        $ratio03  = $commit03 > 0 ? $real03 / $commit03 : 0;
        $ratio3p  = $commit3p > 0 ? $real3p / $commit3p : 0;
        $b4RpMillion = (0.30 * $ratio03) + (0.70 * $ratio3p);
        $b4Ach       = $b4RpMillion > 0 ? $b4RpMillion / 0.70 : 0;
        $b4Score     = number_format($b4Ach * 100, 2, ',', '.') . '%';
        $b4RpDisplay = number_format($b4RpMillion * 100, 2, ',', '');
        $b4UpdatedAt = collect($b4Data)->first(fn($d) => $d['updated_at'] !== '-')['updated_at'] ?? '-';

        $filterPeriodePsak = function ($q) use ($filtered, $filterBulan, $filterTahun, $filterTanggal, $isLastDayOfMonth) {
            if ($filtered && $filterBulan) $q->whereMonth('periode', $filterBulan);
            if ($filtered && $filterTahun) $q->whereYear('periode', $filterTahun);
            if ($filtered && $filterTanggal && $filterBulan && $filterTahun && !$isLastDayOfMonth) {
                $cutoff = Carbon::createFromDate($filterTahun, $filterBulan, $filterTanggal)->endOfDay();
                $q->where('created_at', '<=', $cutoff);
            }
        };

        $psakSegments = [
            'Government' => 'PSAK Gov',
            'Private'    => 'PSAK Private',
            'SOE'        => 'PSAK SOE',
            'SME'        => 'PSAK SME',
        ];

        $psakIndicators = [
            'nc_step14'       => 'Not Close Step 1-4',
            'nc_step5'        => 'Not Close Step 5',
            'nc_konfirmasi'   => 'Not Close Konfirmasi',
            'nc_splitbill'    => 'Not Close Splitt Bill',
            'nc_crvariable'   => 'Not Close CR Variable',
            'nc_unidentified' => 'Not Close Unidentified KB',
        ];

        $psakData = [];
        foreach ($psakSegments as $typeKey => $typeLabel) {
            $indicators  = [];
            $totalCommRp = 0;
            $totalRealRp = 0;

            foreach ($psakIndicators as $segKey => $segLabel) {
                $row = Psak::where('type', $typeKey)
                    ->where('segment', $segKey)
                    ->tap($filterPeriodePsak)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $commSsl = $row ? $toFloat($row->comm_ssl) : 0;
                $commRp  = $row ? round($toFloat($row->comm_rp) / 1000000, 2)  : 0;
                $realSsl = $row ? $toFloat($row->real_ssl) : 0;
                $realRp  = $row ? round($toFloat($row->real_rp) / 1000000, 2)  : 0;
                $ach     = $commRp == 0 ? '-' : number_format(($realRp / $commRp) * 100, 2, ',', '.') . '%';

                $totalCommRp += $commRp;
                $totalRealRp += $realRp;

                $indicators[$segKey] = [
                    'label'   => $segLabel,
                    'commSsl' => $commSsl,
                    'commRp'  => $commRp,
                    'realSsl' => $realSsl,
                    'realRp'  => $realRp,
                    'ach'     => $ach,
                    'updated_at' => $row?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
                ];
            }

            $score = $totalCommRp == 0 ? '-' : number_format(($totalRealRp / $totalCommRp) * 100, 2, ',', '.') . '%';

            $psakData[$typeKey] = [
                'label'      => $typeLabel,
                'indicators' => $indicators,
                'score'      => $score,
            ];
        }

        $scalingBulan = $filtered && $filterBulan ? (int) $filterBulan : Carbon::now()->month;
        $scalingTahun = $filtered && $filterTahun ? (int) $filterTahun : Carbon::now()->year;

        if ($filtered && $filterBulan && !$filterTahun) {
            $foundImport = ScallingImport::whereMonth('periode', $scalingBulan)
                ->orderByDesc('periode')
                ->first();
            if ($foundImport) {
                $scalingTahun = (int) Carbon::parse($foundImport->periode)->year;
            }
        }

        $scalingPeriodeDate = Carbon::createFromDate($scalingTahun, $scalingBulan, 1)->format('Y-m-d');
        $scalingPeriodeYm   = Carbon::createFromDate($scalingTahun, $scalingBulan, 1)->format('Y-m');

        $scallingSegments = [
            'gov'     => ['label' => 'Project Gov',     'segment' => 'government'],
            'private' => ['label' => 'Project Private', 'segment' => 'private'],
            'soe'     => ['label' => 'Project SOE',     'segment' => 'soe'],
            'sme'     => ['label' => 'Project SME',     'segment' => 'sme'],
        ];

        $scallingTypes = [
            'on-hand'  => 'On Hand',
            'qualified' => 'Qualified',
            'initiate'  => 'Initiate',
            'koreksi'   => 'Correction',
        ];

        $scallingData = [];
        foreach ($scallingSegments as $segKey => $seg) {
            foreach ($scallingTypes as $type => $typeLabel) {

                $commitAmount = 0;
                $commitRp     = 0;
                $realAmount   = 0;
                $realRp       = 0;
                $useDataGaps  = false;

                if ($type === 'koreksi') {
                    // ── KOREKSI: ambil dari tabel Koreksi langsung ──
                    $import = ScallingImport::where('segment', $seg['segment'])
                        ->where('type', 'koreksi')
                        ->where('periode', $scalingPeriodeDate)
                        ->latest()
                        ->first();

                    if ($import) {
                        $koreksiRows = \App\Models\Koreksi::where('imports_log_id', $import->id)->get();

                        $commitAmount = $koreksiRows->count();
                        $commitRp     = (float) $koreksiRows->sum('nilai_komitmen') / 1000000;
                        $realAmount   = $koreksiRows->whereNotNull('realisasi')->where('realisasi', '>', 0)->count();
                        $realRp       = (float) $koreksiRows->sum('realisasi') / 1000000;
                    }

                } else {
                    // ── ON-HAND / QUALIFIED / INITIATE: dari ScallingData + FunnelTracking ──
                    $import = ScallingImport::where('segment', $seg['segment'])
                        ->where('type', $type)
                        ->where('periode', $scalingPeriodeDate)
                        ->latest()
                        ->first();

                    // ── SPECIAL HANDLING FOR INITIATE: dapat query Gap data tanpa import ──
                    if ($type === 'initiate') {
                        $dataRows = ScallingData::where('imports_log_id', $import?->id)->get();
                        $importedRows = $dataRows->where('is_manual', true);
                        $dataGaps = Gap::where('periode', $scalingPeriodeDate)
                            ->orderBy('created_at', 'desc')
                            ->get()
                            ->groupBy('segment')
                            ->map->first();
                            // dd($dataGaps);

                        $hasDataGaps = (($dataGaps->get($seg['segment'])?->value ?? 0) > 0);
                        $useDataGaps = $hasDataGaps;
                        $commitAmount = (float) $importedRows->sum('est_nilai_bc') / 1000000;
                        $commitRp = (float) (
                            ($hasDataGaps
                                ? $dataGaps->get($seg['segment'])->value
                                : $importedRows->sum('est_nilai_bc'))
                        ) / 1000000;

                        if ($import) {
                            $dataRows = ScallingData::where('imports_log_id', $import->id)->get();
                            $dataIds  = $dataRows->pluck('id');
                            $funnels  = FunnelTracking::whereIn('data_id', $dataIds)
                                ->when($filterTanggal && $filterBulan && $filterTahun && !$isLastDayOfMonth, function ($q) use ($filterTahun, $filterBulan, $filterTanggal) {
                                    $cutoff = Carbon::createFromDate($filterTahun, $filterBulan, $filterTanggal)->endOfDay();
                                    $q->where('updated_at', '<=', $cutoff);
                                })
                                ->get();
                            $realAmount = $funnels->where('delivery_billing_complete', true)->count();
                            $realRp     = (float) $funnels->sum('delivery_nilai_billcomp') / 1000000;
                        }
                    } elseif ($import) {
                        // ── ON-HAND / QUALIFIED: butuh import ──
                        $dataRows = ScallingData::where('imports_log_id', $import->id)->get();
                        $importedRows = $dataRows->where('is_manual', false);
                        $commitAmount = $importedRows->count();
                        $commitRp     = (float) $importedRows->sum('est_nilai_bc') / 1000000;

                        $dataIds  = $dataRows->pluck('id');
                        $funnels  = FunnelTracking::whereIn('data_id', $dataIds)
                            ->when($filterTanggal && $filterBulan && $filterTahun && !$isLastDayOfMonth, function ($q) use ($filterTahun, $filterBulan, $filterTanggal) {
                                $cutoff = Carbon::createFromDate($filterTahun, $filterBulan, $filterTanggal)->endOfDay();
                                $q->where('updated_at', '<=', $cutoff);
                            })
                            ->get();
                        $realAmount = $funnels->where('delivery_billing_complete', true)->count();
                        $realRp     = (float) $funnels->sum('delivery_nilai_billcomp') / 1000000;
                    }
                }

                $funnelUpdatedAt = '-';
                if ($import) {
                    if ($type === 'koreksi') {
                        $updatedAt = $import->updated_at;
                    } elseif ($type === 'initiate' || $type === 'on-hand' || $type === 'qualified') {
                        $dataIds = ScallingData::where('imports_log_id', $import->id)->pluck('id');
                        $latestFunnel = FunnelTracking::whereIn('data_id', $dataIds)
                            ->orderBy('updated_at', 'desc')
                            ->first();
                        $updatedAt = $latestFunnel?->updated_at;
                    }
                    if ($updatedAt ?? null) {
                        if (!$filtered || !$filterTanggal || $isLastDayOfMonth || $updatedAt->lte(Carbon::createFromDate($filterTahun, $filterBulan, $filterTanggal)->endOfDay())) {
                            $funnelUpdatedAt = $updatedAt->translatedFormat('d M Y H:i');
                        }
                    }
                }

                $scallingData[$segKey][$type] = [
                    'label'         => $typeLabel,
                    'commit_amount' => $commitAmount,
                    'commit_rp'     => $commitRp,
                    'real_amount'   => $realAmount,
                    'real_rp'       => $realRp,
                    'updated_at'    => $funnelUpdatedAt,
                    'use_data_gaps' => $useDataGaps,
                ];
            }
        }

        $ngtmaSegments = [
            'gov'     => ['label' => 'Government',     'segment' => 'government'],
            'private' => ['label' => 'Private', 'segment' => 'private'],
            'soe'     => ['label' => 'SOE',     'segment' => 'soe'],
            'sme'     => ['label' => 'SME',     'segment' => 'sme'],
        ];

        $ngtmaData = [];
        foreach ($ngtmaSegments as $segKey => $seg) {
            $import = ScallingImport::where('segment', $seg['segment'])
                ->where('type', 'ngtma')
                ->where('periode', $scalingPeriodeDate)
                ->latest()
                ->first();

            $commitAmount = 0; $commitRp = 0; $realAmount = 0; $realRp = 0;
            $funnelUpdatedAt = '-';

            if ($import) {
                $dataRows = \App\Models\Ngtma::where('imports_log_id', $import->id)->get();
                $commitAmount = $dataRows->count();
                $commitRp     = (float) $dataRows->sum('est_nilai_bc') / 1000000;

                $dataIds  = $dataRows->pluck('id');
                $funnels  = FunnelTracking::whereIn('ngtma_id', $dataIds)
                    ->when($filterTanggal && $filterBulan && $filterTahun && !$isLastDayOfMonth, function ($q) use ($filterTahun, $filterBulan, $filterTanggal) {
                        $cutoff = Carbon::createFromDate($filterTahun, $filterBulan, $filterTanggal)->endOfDay();
                        $q->where('updated_at', '<=', $cutoff);
                    })
                    ->get();
                $realAmount = $funnels->where('delivery_billing_complete', true)->count();
                $realRp     = (float) $funnels->sum('delivery_nilai_billcomp') / 1000000;

                $latestFunnel = FunnelTracking::whereIn('ngtma_id', $dataIds)
                    ->when($filterTanggal && $filterBulan && $filterTahun && !$isLastDayOfMonth, function ($q) use ($filterTahun, $filterBulan, $filterTanggal) {
                        $cutoff = Carbon::createFromDate($filterTahun, $filterBulan, $filterTanggal)->endOfDay();
                        $q->where('updated_at', '<=', $cutoff);
                    })
                    ->orderBy('updated_at', 'desc')
                    ->first();
                $funnelUpdatedAt = $latestFunnel?->updated_at?->translatedFormat('d M Y H:i') ?? '-';
            }

            $ngtmaData[$segKey] = [
                'label'         => $seg['label'],
                'commit_amount' => $commitAmount,
                'commit_rp'     => $commitRp,
                'real_amount'   => $realAmount,
                'real_rp'       => $realRp,
                'updated_at'    => $funnelUpdatedAt,
            ];
        }
            $ngtmaDetailRoutes = [
                'gov'     => route('report.detail', ['segment' => 'gov',     'type' => 'ngtma', 'periode' => $scalingPeriodeYm]),
                'private' => route('report.detail', ['segment' => 'private', 'type' => 'ngtma', 'periode' => $scalingPeriodeYm]),
                'soe'     => route('report.detail', ['segment' => 'soe',     'type' => 'ngtma', 'periode' => $scalingPeriodeYm]),
                'sme'     => route('report.detail', ['segment' => 'sme',     'type' => 'ngtma', 'periode' => $scalingPeriodeYm]),
            ];

        // $hsiAgencyRow = Hsi::where('type', 'Sales HSI Non AM Non Telda')
        //     ->whereYear('periode', $scalingTahun)
        //     ->whereMonth('periode', $scalingBulan)
        //     ->when($filterTanggal && $filterBulan && $filterTahun && !$isLastDayOfMonth, function ($q) use ($filterTahun, $filterBulan, $filterTanggal) {
        //         $cutoff = Carbon::createFromDate($filterTahun, $filterBulan, $filterTanggal)->endOfDay();
        //         $q->where('updated_at', '<=', $cutoff);
        //     })
        //     ->orderBy('created_at', 'desc')
        //     ->first();

        // $hsiData = [
        //     'commit_amount' => (float) ($hsiAgencyRow->commitment ?? 0),
        //     'real_amount'   => (float) ($hsiAgencyRow->real_ratio ?? 0),
        //     'updated_at' => $hsiAgencyRow?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
        // ];

        $salesProductTypes    = ['HSI', 'Wi-Fi', 'Bandwidth'];
        $salesProductSegments = ['Government', 'Private', 'SOE', 'SME'];
        $salesProductData     = [];

        foreach ($salesProductTypes as $spType) {
            $salesProductData[$spType] = [];
            $totalCommit = 0;
            $totalReal   = 0;

            foreach ($salesProductSegments as $spSeg) {
                $spRow = Hsi::where('type', $spType)
                    ->where('segment', $spSeg)
                    ->whereYear('periode', $scalingTahun)
                    ->whereMonth('periode', $scalingBulan)
                    ->when($filterTanggal && $filterBulan && $filterTahun && !$isLastDayOfMonth, function ($q) use ($filterTahun, $filterBulan, $filterTanggal) {
                        $cutoff = Carbon::createFromDate($filterTahun, $filterBulan, $filterTanggal)->endOfDay();
                        $q->where('updated_at', '<=', $cutoff);
                    })
                    ->orderBy('created_at', 'desc')
                    ->first();

                $commit = (float) ($spRow->commitment ?? 0);
                $real   = (float) ($spRow->real_ratio ?? 0);
                $totalCommit += $commit;
                $totalReal   += $real;

                $salesProductData[$spType][$spSeg] = [
                    'commit'     => $commit,
                    'real'       => $real,
                    'updated_at' => $spRow?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
                ];
            }

            $salesProductData[$spType]['_total'] = [
                'commit'     => $totalCommit,
                'real'       => $totalReal,
                'updated_at' => '-',
            ];
        }
        $hsiData = ['commit_amount' => 0, 'real_amount' => 0, 'updated_at' => '-'];

        $teldaRegions = [
            'lubukpakam'      => 'Lubuk Pakam',
            'binjai'          => 'Binjai',
            'siantar'         => 'Pematang Siantar',
            'kisaran'         => 'Kisaran',
            'kabanjahe'       => 'Kabanjahe',
            'rantauprapat'    => 'Rantau Prapat',
            'toba'            => 'Toba',
            'sibolga'         => 'Sibolga',
            'padangsidempuan' => 'Padang Sidempuan',
        ];

        $teldaData = [];
        foreach ($teldaRegions as $regionKey => $regionLabel) {
            $record = Telda::where('region', $regionKey)
                ->whereYear('periode', $scalingTahun)
                ->whereMonth('periode', $scalingBulan)
                ->when($filterTanggal && $filterBulan && $filterTahun && !$isLastDayOfMonth, function ($q) use ($filterTahun, $filterBulan, $filterTanggal) {
                    $cutoff = Carbon::createFromDate($filterTahun, $filterBulan, $filterTanggal)->endOfDay();
                    $q->where('updated_at', '<=', $cutoff);
                })
                ->orderBy('created_at', 'desc')
                ->first();

            $teldaData[$regionKey] = [
                'label'     => $regionLabel,
                'commit_rp' => $record ? round((float) $record->commitment / 1000000, 2) : null,
                'real_rp'   => $record ? round((float) $record->real_ratio / 1000000, 2) : null,
                'updated_at' => $record?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
            ];
        }

        $upsellingRow = Hsi::where('type', 'Next Level HSI')
            ->whereYear('periode', $scalingTahun)
            ->whereMonth('periode', $scalingBulan)
            ->when($filterTanggal && $filterBulan && $filterTahun && !$isLastDayOfMonth, function ($q) use ($filterTahun, $filterBulan, $filterTanggal) {
                $cutoff = Carbon::createFromDate($filterTahun, $filterBulan, $filterTanggal)->endOfDay();
                $q->where('updated_at', '<=', $cutoff);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        $upsellingData = [
            'commit_rp' => round((float) ($upsellingRow->commitment ?? 0) / 1000000, 2),
            'real_rp'   => round((float) ($upsellingRow->real_ratio ?? 0) / 1000000, 2),
            'updated_at' => $upsellingRow?->real_updated_at?->translatedFormat('d M Y H:i') ?? '-',
        ];

        $utipDetailRoutes = [
            'corrective' => route('report.utip.detail', [
                'type'    => 'UTIP Corrective',
                'periode' => $scalingPeriodeYm,
            ]),
        ];
        $utipDownloadRoutes = [
            'corrective' => route('report.utip.download', [
                'type'    => 'UTIP Corrective',
                'periode' => $scalingPeriodeYm,
            ]),
        ];
        $utipHasFile = [
            'corrective' => \App\Models\Collection::where('type', 'UTIP Corrective')
                ->whereNotNull('file_path')->exists(),
        ];
        foreach ($periodes as $p) {
            $slug = strtolower(str_replace(' ', '-', $p['type']));
            $utipDetailRoutes[$slug] = route('report.utip.detail', [
                'type'    => $p['type'],
                'periode' => $scalingPeriodeYm,
            ]);
            $utipDownloadRoutes[$slug] = route('report.utip.download', [
                'type'    => $p['type'],
                'periode' => $scalingPeriodeYm,
            ]);
            $utipHasFile[$slug] = \App\Models\Collection::where('type', $p['type'])
                ->whereNotNull('file_path')->exists();
        }

        $arDownloadRoutes = [];
        $arHasFile = [];
        foreach ($arSegments as $segment) {
            $slug = strtolower($segment);
            $arDownloadRoutes[$slug] = route('report.ar.download', [
                'segment' => $segment,
                'periode' => $scalingPeriodeYm,
            ]);
            $arHasFile[$slug] = \App\Models\Collection::where('type', 'ar')
                ->where('segment', $segment)
                ->where('periode', $scalingPeriodeDate)
                ->whereNotNull('file_path')
                ->exists();
        }

        $utipProgPlanTotal   = array_sum(array_column($newUtipPeriodes, 'planRp'));
        $utipProgCommitTotal = array_sum(array_column($newUtipPeriodes, 'commitRp'));
        $utipProgRealTotal   = array_sum(array_column($newUtipPeriodes, 'realRp'));

        $utipProgUpdatedAt = collect($newUtipPeriodes)
            ->whereNotNull('raw_updated_at')
            ->sortByDesc('raw_updated_at')
            ->first()['updated_at'] ?? '-';

        $utipProgressive = [
            'label'      => 'UTIP Progressive',
            'planRp'     => $utipProgPlanTotal   > 0 ? $utipProgPlanTotal   : null,
            'commitRp'   => $utipProgCommitTotal > 0 ? $utipProgCommitTotal : null,
            'realRp'     => $utipProgRealTotal   > 0 ? $utipProgRealTotal   : null,
            'updated_at' => $utipProgUpdatedAt,
        ];

        return view('report.report', compact(
            'filtered', 'filterBulan', 'filterTahun', 'filterTanggal',
            'c3mrKomitmen', 'c3mrRealisasi', 'c3mrUpdatedAt',
            'bilperKomitmen', 'bilperRealisasi', 'bilperUpdatedAt',
            'crData', 'crUpdatedAt',
            'cycData', 'cycUpdatedAt',
            'utipCorrective',
            'utipProgressive',
            'newUtipPeriodes',
            'utipDetailRoutes',
            'utipDownloadRoutes',
            'utipHasFile',
            'arDownloadRoutes',
            'arHasFile',
            'ct0Data', 'ct0Score', 'ct0TotalReal',
            'ctcCt0Real', 'ctcCt0UpdatedAt','ctcData', 'lossRateReal', 'lossRateAch',
            'b1Data', 'b1Score',
            'b2Data', 'b2Score',
            'b3Data', 'b3Score', 'b3UpdatedAt',
            'b4Data', 'b4RpMillion', 'b4RpDisplay', 'b4Score', 'b4UpdatedAt',
            'psakData',
            'scallingSegments', 'scallingTypes', 'scallingData',
            'ngtmaSegments', 'ngtmaData', 'ngtmaDetailRoutes',
            'arRows', 'arRowspan', 'fairnessAR', 'salesProductData',
            'hsiData', 'teldaData', 'teldaRegions', 'upsellingData',
            'scalingPeriodeYm', 'lossRateUpdatedAt'
        ));
    }

    public function detail(Request $request, string $segment, string $type)
{
    $segmentMap = [
        'gov'     => ['label' => 'Government', 'db' => 'government'],
        'private' => ['label' => 'Private',    'db' => 'private'],
        'soe'     => ['label' => 'SOE',        'db' => 'soe'],
        'sme'     => ['label' => 'SME',        'db' => 'sme'],
    ];

    $typeMap = [
        'on-hand'  => 'On Hand',
        'qualified' => 'Qualified',
        'initiate'  => 'Initiate',
        'koreksi'   => 'Correction',
        'ngtma'     => 'New GTMA',
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

    // ── KOREKSI: data dari tabel Koreksi ──────────────────────────────
    if ($type === 'koreksi') {
        $import = \App\Models\ScallingImport::where('segment', $segmentDb)
            ->where('type', 'koreksi')
            ->where('periode', $periodeDate)
            ->latest()
            ->first();

        $koreksiRows = $import
            ? \App\Models\Koreksi::where('imports_log_id', $import->id)->get()
            : collect();

        return view('report.detail', compact(
            'segment', 'type',
            'segmentLabel', 'typeLabel',
            'periodeLabel', 'currentPeriode',
            'periodOptions',
            'import',
            'koreksiRows',          // ← khusus koreksi
        ))->with([
            'dataRows'  => collect(), // kosong, tidak dipakai
            'funnelMap' => collect(), // kosong, tidak dipakai
        ]);
    }

    if ($type === 'ngtma') {
        $import = \App\Models\ScallingImport::where('segment', $segmentDb)
            ->where('type', 'ngtma')
            ->where('periode', $periodeDate)
            ->latest()
            ->first();

        $ngtmaRows = $import
            ? \App\Models\Ngtma::where('imports_log_id', $import->id)
                ->orderBy('am', 'asc')
                ->orderByRaw('CAST(no AS UNSIGNED) asc')
                ->get()
            : collect();

        $funnelMap = collect();
        if ($ngtmaRows->isNotEmpty()) {
            $ngtmaIds   = $ngtmaRows->pluck('id');
            $allFunnels = \App\Models\FunnelTracking::whereIn('ngtma_id', $ngtmaIds)->get();
            $funnelMap  = $ngtmaIds->mapWithKeys(function ($id) use ($allFunnels) {
                return [$id => $allFunnels->where('ngtma_id', $id)->sortByDesc('updated_at')->first()];
            });
        }

        return view('report.detail', compact(
            'segment', 'type',
            'segmentLabel', 'typeLabel',
            'periodeLabel', 'currentPeriode',
            'periodOptions',
            'import', 'funnelMap'
        ))->with([
            'dataRows'    => $ngtmaRows,
            'koreksiRows' => collect(),
        ]);
    }

    // ── ON-HAND / QUALIFIED / INITIATE: data dari ScallingData ────────
    $import = \App\Models\ScallingImport::where('segment', $segmentDb)
        ->where('type', $type)
        ->where('periode', $periodeDate)
        ->latest()
        ->first();

    $dataRows  = collect();
    $funnelMap = collect();

    if ($type === 'initiate') {
        $importIds = \App\Models\ScallingImport::where('segment', $segmentDb)
            ->where('type', $type)
            ->where('periode', $periodeDate)
            ->pluck('id');

        if ($importIds->isNotEmpty()) {
            $import = \App\Models\ScallingImport::find($importIds->first());
            $dataRows = \App\Models\ScallingData::whereIn('imports_log_id', $importIds)
                ->orderBy('am', 'asc')
                ->orderByRaw('CAST(no AS UNSIGNED) asc')
                ->get()
                ->filter(fn($r) => strtoupper(trim($r->no ?? '')) !== 'TOTAL')
                ->values();
        }
    } else {
        if ($import) {
            $dataRows = \App\Models\ScallingData::where('imports_log_id', $import->id)
                ->orderBy('am', 'asc')
                ->orderByRaw('CAST(no AS UNSIGNED) asc')
                ->get()
                ->filter(fn($r) => strtoupper(trim($r->no ?? '')) !== 'TOTAL')
                ->values();
        }
    }

    if ($dataRows->isNotEmpty()) {
        $dataIds    = $dataRows->pluck('id');
        $allFunnels = \App\Models\FunnelTracking::whereIn('data_id', $dataIds)->get();

        $funnelMap = $dataIds->mapWithKeys(function ($dataId) use ($allFunnels) {
            $latest = $allFunnels->where('data_id', $dataId)
                ->sortByDesc('updated_at')
                ->first();
            return [$dataId => $latest];
        });
    }

    return view('report.detail', compact(
        'segment', 'type',
        'segmentLabel', 'typeLabel',
        'periodeLabel', 'currentPeriode',
        'periodOptions',
        'import', 'dataRows', 'funnelMap'
    ))->with(['koreksiRows' => collect()]);
}

public function progress(Request $request, string $segment, string $type)
{
    $segmentMap = [
        'gov'     => ['label' => 'Government', 'db' => 'government'],
        'private' => ['label' => 'Private',    'db' => 'private'],
        'soe'     => ['label' => 'SOE',        'db' => 'soe'],
        'sme'     => ['label' => 'SME',        'db' => 'sme'],
    ];

    $typeMap = [
        'on-hand'  => 'On Hand',
        'qualified' => 'Qualified',
        'initiate'  => 'Initiate',
        'koreksi'   => 'Correction',
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

    // ── KOREKSI: data dari tabel Koreksi ──────────────────────────────
    if ($type === 'koreksi') {
        $koreksiRows = $import
            ? \App\Models\Koreksi::where('imports_log_id', $import->id)->get()
            : collect();

        $isReadOnly = $import && ($import->status ?? 'active') !== 'active';

        return view('report.progress', compact(
            'segment', 'type',
            'segmentLabel', 'typeLabel',
            'periodeLabel', 'currentPeriode',
            'periodOptions',
            'import',
            'isReadOnly',
            'koreksiRows',          // ← khusus koreksi
        ))->with([
            'dataRows'  => collect(),
            'funnelMap' => collect(),
        ]);
    }

    // ── ON-HAND / QUALIFIED / INITIATE ────────────────────────────────
    $dataRows  = collect();
    $funnelMap = collect();

    if ($import) {
        $dataRows = \App\Models\ScallingData::where('imports_log_id', $import->id)
            ->with(['funnel.todayProgress'])
            ->orderBy('am', 'asc')
            ->orderByRaw('CAST(no AS UNSIGNED) asc')
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
    }

    return view('report.progress', compact(
        'segment', 'type',
        'segmentLabel', 'typeLabel',
        'periodeLabel', 'currentPeriode',
        'periodOptions',
        'import', 'dataRows', 'funnelMap'
    ))->with(['koreksiRows' => collect(), 'isReadOnly' => false]);
}

public function progressKoreksiUpdate(Request $request, string $segment)
{
    $request->validate([
        'id'              => 'required|integer|exists:koreksis,id',
        'realisasi'       => 'sometimes|numeric|min:0',
        'nilai_komitmen'  => 'sometimes|numeric|min:0',
        'progress'        => 'sometimes|string|in:done,on-progress',
    ]);

    $segmentDbMap = [
        'gov'     => 'government',
        'private' => 'private',
        'soe'     => 'soe',
        'sme'     => 'sme',
    ];
    abort_if(!isset($segmentDbMap[$segment]), 404);
    $segmentDb = $segmentDbMap[$segment];

    $koreksi = \App\Models\Koreksi::findOrFail($request->id);
    $import  = $koreksi->scallingImport;
    abort_if(!$import || $import->segment !== $segmentDb, 403);

    $updateData = [];

    if ($request->has('nilai_komitmen')) {
        $updateData['nilai_komitmen'] = $request->nilai_komitmen;
    }
    if ($request->has('progress')) {
        $updateData['progress'] = $request->progress;
    }
    if ($request->has('realisasi')) {
        $updateData['realisasi'] = $request->realisasi;
    }

    if (!empty($updateData)) {
        $koreksi->update($updateData);
    }

    return response()->json(['success' => true]);
}

    public function progressFunnelUpdate(Request $request, string $segment, string $type)
    {
        $controllerMap = [
            'gov'     => \App\Http\Controllers\GovController::class,
            'private' => \App\Http\Controllers\PrivateController::class,
            'soe'     => \App\Http\Controllers\SoeController::class,
            'sme'     => \App\Http\Controllers\SmeController::class,
        ];

        abort_if(!isset($controllerMap[$segment]), 404);

        $controller = app($controllerMap[$segment]);
        return $controller->updateFunnelCheckbox($request);
    }

    public function progressScallingUpdateEstNilai(Request $request, string $segment, string $type)
    {
        try {
            $request->validate([
                'data_id'       => 'required|integer|exists:scalling_data,id',
                'est_nilai_bc'  => 'required|numeric|min:0',
            ]);

            $segmentDbMap = [
                'gov'     => 'government',
                'private' => 'private',
                'soe'     => 'soe',
                'sme'     => 'sme',
            ];
            abort_if(!isset($segmentDbMap[$segment]), 404);
            $segmentDb = $segmentDbMap[$segment];

            $scallingData = \App\Models\ScallingData::findOrFail($request->data_id);
            $import = $scallingData->scallingImport;
            abort_if(!$import || $import->segment !== $segmentDb, 403);

            $scallingData->update([
                'est_nilai_bc' => $request->est_nilai_bc,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('progressScallingUpdateEstNilai error: ' . $e->getMessage(), [
                'segment' => $segment,
                'type' => $type,
                'data_id' => $request->data_id ?? null,
                'est_nilai_bc' => $request->est_nilai_bc ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function progressScallingUpdateField(Request $request, string $segment, string $type)
{
    // Kolom yang boleh diedit — whitelist ketat agar tidak bisa dieksploitasi
    $allowedFields = [
        'project',
        'id_lop',
        'cc',
        'am',
        'mitra',
        'plan_bulan_billcomp_2025',
    ];

    try {
        $request->validate([
            'data_id' => 'required|integer|exists:scalling_data,id',
            'field'   => 'required|string|in:' . implode(',', $allowedFields),
            'value'   => 'nullable|string|max:255',
        ]);

        $segmentDbMap = [
            'gov'     => 'government',
            'private' => 'private',
            'soe'     => 'soe',
            'sme'     => 'sme',
        ];

        abort_if(!isset($segmentDbMap[$segment]), 404);
        $segmentDb = $segmentDbMap[$segment];

        $scallingData = \App\Models\ScallingData::findOrFail($request->data_id);
        $import       = $scallingData->scallingImport;

        // Pastikan baris ini memang milik segment yang benar
        abort_if(!$import || $import->segment !== $segmentDb, 403);

        $scallingData->update([
            $request->field => $request->value ?? '',
        ]);

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        \Log::error('progressScallingUpdateField error: ' . $e->getMessage(), [
            'segment' => $segment,
            'type'    => $type,
            'data_id' => $request->data_id ?? null,
            'field'   => $request->field   ?? null,
            'value'   => $request->value   ?? null,
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
public function utipDetail(Request $request)
{
    $typeLabel   = $request->input('type', '');
    $periodeYm   = $request->input('periode', Carbon::now()->format('Y-m'));

    [$y, $m] = explode('-', $periodeYm);
    $periodeDate  = Carbon::createFromDate((int)$y, (int)$m, 1)->format('Y-m-d');
    $periodeLabel = Carbon::createFromDate((int)$y, (int)$m, 1)->translatedFormat('F Y');

    $rows = \App\Models\Collection::where('type', $typeLabel)
    ->whereIn('id', function($q) use ($typeLabel) {
        $q->selectRaw('MAX(id)')
          ->from('collections')
          ->where('type', $typeLabel)
          ->groupBy('kondisi');
    })
    ->orderBy('kondisi')
    ->get();

    $filterTahun = Carbon::now()->year;
    $filterBulan = Carbon::now()->month;

    if (str_starts_with($typeLabel, 'New UTIP')) {
    $monthStr = str_replace('New UTIP ', '', $typeLabel);
    $bulanMap = [
        'Jan' => 'January',  'Feb' => 'February', 'Mar' => 'March',
        'Apr' => 'April',    'Mei' => 'May',       'Jun' => 'June',
        'Jul' => 'July',     'Aug' => 'August',    'Sep' => 'September',
        'Okt' => 'October',  'Nov' => 'November',  'Des' => 'December',
    ];
    foreach ($bulanMap as $id => $en) {
        $monthStr = str_replace($id, $en, $monthStr);
    }
    $parsedDate = Carbon::parse('01 ' . $monthStr);
        $monthsDiff       = (($parsedDate->year - $filterTahun) * 12) + ($parsedDate->month - $filterBulan);

        if ($monthsDiff >= 0) {
            $commitMultiplier = 0;
        } elseif ($monthsDiff === -1) {
            $commitMultiplier = 0.30;
        } elseif ($monthsDiff === -2) {
            $commitMultiplier = 0.60;
        } else {
            $commitMultiplier = 1.00;
        }
    } else {
        $commitMultiplier = 1.00;
    }

    return view('report.utip_detail', compact(
        'typeLabel', 'periodeLabel', 'rows', 'commitMultiplier'
    ));
}
public function utipDownload(Request $request)
{
    $typeLabel = $request->input('type', '');
    $periodeYm = $request->input('periode', Carbon::now()->format('Y-m'));

    if ($typeLabel === 'all') {
        // Ambil file terbaru dari semua tipe UTIP (corrective + new utip)
        $record = \App\Models\Collection::where(function($q) {
                $q->where('type', 'UTIP Corrective')
                  ->orWhere('type', 'like', 'New UTIP%');
            })
            ->whereNotNull('file_path')
            ->orderBy('created_at', 'desc')
            ->first();
    } else {
        $record = \App\Models\Collection::where('type', $typeLabel)
            ->whereNotNull('file_path')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    if (!$record || !$record->file_path) {
        abort(404, 'File tidak ditemukan.');
    }

    $fullPath = storage_path('app/public/' . $record->file_path);
    if (!file_exists($fullPath)) {
        abort(404, 'File tidak ditemukan di server.');
    }

    return response()->download($fullPath, $record->file_name ?? basename($record->file_path));
}

public function arDownload(Request $request)
{
    $segment   = $request->input('segment', '');
    $periodeYm = $request->input('periode', Carbon::now()->format('Y-m'));

    [$y, $m] = explode('-', $periodeYm);
    $periodeDate = Carbon::createFromDate((int)$y, (int)$m, 1)->format('Y-m-d');

    $record = \App\Models\Collection::where('type', 'ar')
        ->where('segment', $segment)
        ->where('periode', $periodeDate)
        ->whereNotNull('file_path')
        ->orderBy('created_at', 'desc')
        ->first();

    if (!$record || !$record->file_path) {
        abort(404, 'File tidak ditemukan.');
    }

    $fullPath = storage_path('app/public/' . $record->file_path);

    if (!file_exists($fullPath)) {
        abort(404, 'File tidak ditemukan di server.');
    }

    return response()->download($fullPath, $record->file_name ?? basename($record->file_path));
}

}
