@extends('layouts.app')

@section('title', 'Report')

@section('content')

    @php
        function getColorClass($value, $fairness)
        {
            if ($value == '-' || $value === null) return '';
            $v = floatval(str_replace('%', '', $value));
            switch ($fairness) {
                case '96-100':
                    if ($v < 98)    return 'bg-black text-white';
                    if ($v < 99.2)  return 'bg-red-500 text-white';
                    if ($v < 100)   return 'bg-yellow-300';
                    return 'bg-green-500 text-white';
                case '99-100':
                    if ($v < 99.5)  return 'bg-black text-white';
                    if ($v < 99.8)  return 'bg-red-500 text-white';
                    if ($v < 100)   return 'bg-yellow-300';
                    return 'bg-green-500 text-white';
                case '90-100':
                    if ($v < 95)    return 'bg-black text-white';
                    if ($v < 98)    return 'bg-red-500 text-white';
                    if ($v < 100)   return 'bg-yellow-300';
                    return 'bg-green-500 text-white';
                case '0-100':
                    if ($v < 50)    return 'bg-black text-white';
                    if ($v < 80)    return 'bg-red-500 text-white';
                    if ($v < 100)   return 'bg-yellow-300';
                    return 'bg-green-500 text-white';
                case '20-50':
                    if ($v < 35)    return 'bg-black text-white';
                    if ($v < 44)    return 'bg-red-500 text-white';
                    if ($v < 50)    return 'bg-yellow-300';
                    return 'bg-green-500 text-white';
                case '0-70':
                    if ($v < 35)    return 'bg-black text-white';
                    if ($v < 56)    return 'bg-red-500 text-white';
                    if ($v < 70)    return 'bg-yellow-300';
                    return 'bg-green-500 text-white';
                case '0-45':
                    if ($v < 22.5)  return 'bg-black text-white';
                    if ($v < 36)    return 'bg-red-500 text-white';
                    if ($v < 45)    return 'bg-yellow-300';
                    return 'bg-green-500 text-white';
                case '100-30':
                    if ($v > 70)    return 'bg-black text-white';
                    if ($v > 50)    return 'bg-red-500 text-white';
                    if ($v > 30)    return 'bg-yellow-300';
                    return 'bg-green-500 text-white';
                default:
                    return '';
            }
        }

        function scalingAchColor($ach) {
            if ($ach === null) return ['bg' => '', 'text' => 'text-slate-400', 'label' => '-'];
            if ($ach >= 100)   return ['bg' => 'background:#16a34a;', 'text' => 'text-white', 'label' => number_format($ach,2,',','.') . '%'];
            if ($ach >= 80)    return ['bg' => 'background:#eab308;', 'text' => 'text-slate-900', 'label' => number_format($ach,2,',','.') . '%'];
            if ($ach >= 50)    return ['bg' => 'background:#ef4444;', 'text' => 'text-white', 'label' => number_format($ach,2,',','.') . '%'];
            return                    ['bg' => 'background:#000000;', 'text' => 'text-white', 'label' => number_format($ach,2,',','.') . '%'];
        }

        function scalingKoreksiColor($ach) {
            if ($ach === null) return ['bg' => '', 'text' => 'text-slate-400', 'label' => '-'];
            if ($ach > 100) return ['bg' => 'background:#000000;', 'text' => 'text-white',      'label' => number_format($ach,2,',','.') . '%'];
            if ($ach > 80)  return ['bg' => 'background:#ef4444;', 'text' => 'text-white',      'label' => number_format($ach,2,',','.') . '%'];
            if ($ach > 50)  return ['bg' => 'background:#eab308;', 'text' => 'text-slate-900',  'label' => number_format($ach,2,',','.') . '%'];
            return                 ['bg' => 'background:#16a34a;', 'text' => 'text-white',      'label' => number_format($ach,2,',','.') . '%'];
        }

        function worstColor($colors) {
            $priority = ['bg-black text-white' => 4, 'bg-red-500 text-white' => 3, 'bg-yellow-300' => 2, 'bg-green-500 text-white' => 1];
            $worst = 0;
            $worstClass = '';
            foreach ($colors as $c) {
                $p = $priority[$c] ?? 0;
                if ($p > $worst) { $worst = $p; $worstClass = $c; }
            }
            return $worstClass;
        }

        $bulanNames = ['','Januari','Februari','Maret','April','Mei','Juni',
                       'Juli','Agustus','September','Oktober','November','Desember'];

        $periodeLabel = 'Semua Data';
        if ($filtered) {
            if ($filterBulan && $filterTahun && ($filterTanggal ?? null))
                $periodeLabel = $filterTanggal . ' ' . $bulanNames[$filterBulan] . ' ' . $filterTahun;
            elseif ($filterBulan && $filterTahun)
                $periodeLabel = $bulanNames[$filterBulan] . ' ' . $filterTahun;
            elseif ($filterTahun)
                $periodeLabel = 'Tahun ' . $filterTahun;
            elseif ($filterBulan)
                $periodeLabel = $bulanNames[$filterBulan] . ' Semua Tahun';
        }

        $fairnessC3mr   = '96-100';
        $scoreC3mr      = $c3mrKomitmen == 0 ? '-' : number_format(($c3mrRealisasi / $c3mrKomitmen) * 100, 2, ',', '.') . '%';
        $colorC3mr      = getColorClass($scoreC3mr, $fairnessC3mr);

        $fairnessBilper = '99-100';
        $scoreBilper    = $bilperKomitmen == 0 ? '-' : number_format(($bilperRealisasi / $bilperKomitmen) * 100, 2, ',', '.') . '%';
        $colorBilper    = getColorClass($scoreBilper, $fairnessBilper);

        $fairnessCR     = '90-100';
        $crScores = []; $crVals = [];
        foreach ($crData as $seg => $val) {
            $pct = $val['komitmen'] == 0 ? '-' : number_format(($val['realisasi'] / $val['komitmen']) * 100, 2, ',', '.') . '%';
            $crScores[$seg] = ['text' => $pct, 'color' => getColorClass($pct, $fairnessCR)];
            $crVals[$seg]   = $val['komitmen'] == 0 ? 0 : ($val['realisasi'] / $val['komitmen']) * 100;
        }
        $crTotalPct   = number_format(($crVals['GOV']*0.4)+($crVals['SME']*0.2)+($crVals['PRIVATE']*0.2)+($crVals['SOE']*0.2),2, ',', '.') . '%';
        $colorCRTotal = getColorClass($crTotalPct, $fairnessCR);

        $fairnessUTIP        = '0-100';
        $achCorrective       = $utipCorrective['commitRp'] == 0 ? '-' : number_format(($utipCorrective['realRp'] / $utipCorrective['commitRp']) * 100, 2, ',', '.') . '%';
        $colorAchCorrective  = getColorClass($achCorrective, $fairnessUTIP);

        $allUtipRows      = array_merge([$utipCorrective], $newUtipPeriodes);
        $totalCommitUTIP  = array_sum(array_column($allUtipRows, 'commitRp'));
        $totalRealUTIP    = array_sum(array_column($allUtipRows, 'realRp'));
        $scoreUTIP        = $totalCommitUTIP == 0 ? '-' : number_format(($totalRealUTIP / $totalCommitUTIP) * 100, 2) . '%';
        $colorScoreUTIP   = getColorClass($scoreUTIP, $fairnessUTIP);
        $utipRowspan      = 1 + count($newUtipPeriodes);

        $fairnessCt0     = '20-50';
        $fairnessCtc     = '0-100';
        $fairnessLoss    = '100-30';
        $ct0RowCount     = count($ct0Data);
        $ctcRowCount     = 5;
        $ctcSectionRows  = $ct0RowCount + $ctcRowCount;

        foreach ($ct0Data as &$row) {
            $row['achColor'] = getColorClass($row['ach'], $fairnessCt0);
        }
        unset($row);
        $ct0ScoreColor = getColorClass($ct0Score, $fairnessCt0);

        foreach (['Sales HSI (all)', 'Churn', 'Winback'] as $seg) {
            $ctcData[$seg]['achColor'] = getColorClass($ctcData[$seg]['ach'], $fairnessCtc);
        }
        $lossRateColor = getColorClass($lossRateReal, $fairnessLoss);

        $fairnessRs   = '0-100';
        $fairnessB4   = '0-70';

        foreach ($b1Data as &$row) {
            $rowPct = $row['commit'] > 0 ? number_format($row['ratio'], 2, ',', '.') . '%' : '-';
            $row['realPct']   = $rowPct;
            $row['achColor']  = getColorClass($rowPct, $fairnessRs);
        }
        unset($row);
        $b1RowColors  = array_column(array_map(fn($r) => ['c' => $r['achColor']], array_values($b1Data)), 'c');
        $b1ScoreColor = worstColor($b1RowColors) ?: getColorClass($b1Score, $fairnessRs);

        foreach ($b2Data as &$row) {
            $rowPct = $row['commit'] > 0 ? number_format($row['ratio'], 2, ',', '.') . '%' : '-';
            $row['realPct']   = $rowPct;
            $row['achColor']  = getColorClass($rowPct, $fairnessRs);
        }
        unset($row);
        $b2RowColors  = array_column(array_map(fn($r) => ['c' => $r['achColor']], array_values($b2Data)), 'c');
        $b2ScoreColor = worstColor($b2RowColors) ?: getColorClass($b2Score, $fairnessRs);

        $b3RowColors  = array_map(fn($r) => getColorClass(
            $r['commit'] > 0 ? number_format($r['real'] / $r['commit'] * 100, 2).'%' : '-',
            $fairnessRs
        ), array_values($b3Data));
        $b3ScoreColor = worstColor($b3RowColors) ?: getColorClass($b3Score, $fairnessRs);

        $b4RowColors  = array_map(fn($r) => getColorClass(
            $r['commit'] > 0 ? number_format($r['real'] / $r['commit'] * 100, 2).'%' : '-',
            $fairnessB4
        ), array_values($b4Data));
        $b4ScoreColor = worstColor($b4RowColors) ?: getColorClass($b4Score, $fairnessB4);
        $b4RpMillionFmt = number_format($b4RpMillion, 1);

        $scalingDetailRoutes = [
    'gov'     => [
        'on-hand'   => route('report.detail', ['segment' => 'gov',     'type' => 'on-hand',   'periode' => $scalingPeriodeYm]),
        'qualified' => route('report.detail', ['segment' => 'gov',     'type' => 'qualified', 'periode' => $scalingPeriodeYm]),
        'initiate'  => route('report.detail', ['segment' => 'gov',     'type' => 'initiate',  'periode' => $scalingPeriodeYm]),
        'koreksi'   => route('report.detail', ['segment' => 'gov',     'type' => 'koreksi',   'periode' => $scalingPeriodeYm]),
    ],
    'private' => [
        'on-hand'   => route('report.detail', ['segment' => 'private', 'type' => 'on-hand',   'periode' => $scalingPeriodeYm]),
        'qualified' => route('report.detail', ['segment' => 'private', 'type' => 'qualified', 'periode' => $scalingPeriodeYm]),
        'initiate'  => route('report.detail', ['segment' => 'private', 'type' => 'initiate',  'periode' => $scalingPeriodeYm]),
        'koreksi'   => route('report.detail', ['segment' => 'private', 'type' => 'koreksi',   'periode' => $scalingPeriodeYm]),
    ],
    'soe'     => [
        'on-hand'   => route('report.detail', ['segment' => 'soe',     'type' => 'on-hand',   'periode' => $scalingPeriodeYm]),
        'qualified' => route('report.detail', ['segment' => 'soe',     'type' => 'qualified', 'periode' => $scalingPeriodeYm]),
        'initiate'  => route('report.detail', ['segment' => 'soe',     'type' => 'initiate',  'periode' => $scalingPeriodeYm]),
        'koreksi'   => route('report.detail', ['segment' => 'soe',     'type' => 'koreksi',   'periode' => $scalingPeriodeYm]),
    ],
    'sme'     => [
        'on-hand'   => route('report.detail', ['segment' => 'sme',     'type' => 'on-hand',   'periode' => $scalingPeriodeYm]),
        'qualified' => route('report.detail', ['segment' => 'sme',     'type' => 'qualified', 'periode' => $scalingPeriodeYm]),
        'initiate'  => route('report.detail', ['segment' => 'sme',     'type' => 'initiate',  'periode' => $scalingPeriodeYm]),
        'koreksi'   => route('report.detail', ['segment' => 'sme',     'type' => 'koreksi',   'periode' => $scalingPeriodeYm]),
    ],
];

    @endphp

    <div class="min-h-screen" style="background:#f1f5f9;">
        <div class="max-w-7xl mx-auto px-8 py-10">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-10 py-7 mb-8 relative overflow-hidden no-print">
                <div class="absolute top-0 left-0 right-0 h-1.5" style="background: linear-gradient(90deg, #dc2626, #ef4444, #dc2626);"></div>
                <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full opacity-[0.04]" style="background: #dc2626;"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <img src="{{ asset('img/Telkom.png') }}" alt="Telkom" class="h-12 w-auto">
                        <div class="w-px h-12 bg-slate-200"></div>
                        <div>
                            <p class="text-[10px] font-black tracking-[0.3em] text-red-600 uppercase mb-1">Witel Sumut</p>
                            <h1 class="text-2xl font-black tracking-tight text-slate-900 leading-none uppercase">
                                Report <span class="text-red-600">Data</span>
                            </h1>
                            <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-tight">Periode: {{ $periodeLabel }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        @php
                            $dashboardRoute = match(Auth::user()->role) {
                                'admin'      => route('admin.index'),
                                'gov'        => route('dashboard.gov'),
                                'soe'        => route('dashboard.soe'),
                                'sme'        => route('dashboard.sme'),
                                'private'    => route('dashboard.private'),
                                'collection' => route('dashboard.collection'),
                                'ctc'        => route('dashboard.ctc'),
                                'risingStar' => route('dashboard.rising-star'),
                                default      => '/',
                            };
                        @endphp
                        <a href="{{ $dashboardRoute }}"
                            class="flex items-center space-x-2.5 bg-white border-2 border-slate-900 hover:bg-red-600 hover:border-red-600 text-slate-900 hover:text-white px-6 py-3 rounded-xl font-black text-xs transition-all duration-300 shadow-sm group uppercase tracking-wider">
                            <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span>Back to Dashboard</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="group flex items-center space-x-2.5 bg-slate-900 hover:bg-red-600 text-white font-bold text-sm px-5 py-3 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-red-200">
                                <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-5 mb-8 no-print">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                    <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Filter Periode</h2>
                </div>
                <form method="GET" action="{{ route('report.index') }}">
                    <input type="hidden" name="filter" value="1">
                    <div class="flex items-end justify-between gap-4">
                        <div class="flex items-end gap-4 flex-1">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Tanggal</label>
                                <select name="tanggal" id="select-tanggal" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 bg-white">
                                    <option value="">— Semua Tanggal —</option>
                                    @for($d = 1; $d <= 31; $d++)
                                        <option value="{{ $d }}" {{ ($filterTanggal ?? '') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Bulan</label>
                                <select name="bulan" id="select-bulan" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 bg-white">
                                    <option value="">— Semua Bulan —</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $filterBulan == $m ? 'selected' : '' }}>{{ $bulanNames[$m] }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Tahun</label>
                                <select name="tahun" id="select-tahun" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 bg-white">
                                    <option value="">— Semua Tahun —</option>
                                    @for($y = 2025; $y <= 2026; $y++)
                                        <option value="{{ $y }}" {{ $filterTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex items-center space-x-1.5 bg-slate-900 hover:bg-red-600 text-white font-bold text-xs px-4 py-2 rounded-lg transition-all uppercase tracking-wider whitespace-nowrap">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                                </svg>
                                <span>Tampilkan</span>
                            </button>
                            <a href="{{ route('report.index') }}" class="flex items-center space-x-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs px-4 py-2 rounded-lg transition-all uppercase tracking-wider whitespace-nowrap">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span>Reset</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between no-print">
                    <div class="flex items-center space-x-3">
                        <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                        <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Report Data — {{ $periodeLabel }}</h2>
                    </div>
                    <div class="relative" id="export-dropdown-wrapper">
                        <button id="btn-export-jpg"
                            class="flex items-center space-x-1.5 bg-slate-900 hover:bg-red-600 text-white font-bold text-xs px-4 py-2 rounded-lg transition-colors uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span id="export-btn-label">Export JPG</span>
                            <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="export-dropdown"
                            class="hidden absolute right-0 mt-1.5 w-52 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50">
                            <div class="px-3 py-2 border-b border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Export</p>
                            </div>
                            <button data-export="all"
                                class="export-option w-full flex items-center space-x-2.5 px-4 py-2.5 hover:bg-red-50 hover:text-red-600 text-slate-700 text-xs font-bold transition-colors text-left">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Full Report</span>
                            </button>
                            <div class="border-t border-slate-100"></div>
                            <button data-export="1"
                                class="export-option w-full flex items-center space-x-2.5 px-4 py-2.5 hover:bg-slate-50 text-slate-600 text-xs font-semibold transition-colors text-left">
                                <span class="w-4 h-4 rounded bg-slate-200 text-slate-700 text-[9px] font-black flex items-center justify-center flex-shrink-0">1</span>
                                <span>Scaling</span>
                            </button>
                            <button data-export="2"
                                class="export-option w-full flex items-center space-x-2.5 px-4 py-2.5 hover:bg-slate-50 text-slate-600 text-xs font-semibold transition-colors text-left">
                                <span class="w-4 h-4 rounded bg-slate-200 text-slate-700 text-[9px] font-black flex items-center justify-center flex-shrink-0">2</span>
                                <span>Collection</span>
                            </button>
                            <button data-export="3"
                                class="export-option w-full flex items-center space-x-2.5 px-4 py-2.5 hover:bg-slate-50 text-slate-600 text-xs font-semibold transition-colors text-left">
                                <span class="w-4 h-4 rounded bg-slate-200 text-slate-700 text-[9px] font-black flex items-center justify-center flex-shrink-0">3</span>
                                <span>Combat The Churn</span>
                            </button>
                            <button data-export="4"
                                class="export-option w-full flex items-center space-x-2.5 px-4 py-2.5 hover:bg-slate-50 text-slate-600 text-xs font-semibold transition-colors text-left">
                                <span class="w-4 h-4 rounded bg-slate-200 text-slate-700 text-[9px] font-black flex items-center justify-center flex-shrink-0">4</span>
                                <span>Rising Star</span>
                            </button>
                            <button data-export="5"
                                class="export-option w-full flex items-center space-x-2.5 px-4 py-2.5 hover:bg-slate-50 text-slate-600 text-xs font-semibold transition-colors text-left">
                                <span class="w-4 h-4 rounded bg-slate-200 text-slate-700 text-[9px] font-black flex items-center justify-center flex-shrink-0">5</span>
                                <span>PSAK</span>
                            </button>
                            <button data-export="6"
                                class="export-option w-full flex items-center space-x-2.5 px-4 py-2.5 hover:bg-slate-50 text-slate-600 text-xs font-semibold transition-colors text-left">
                                <span class="w-4 h-4 rounded bg-slate-200 text-slate-700 text-[9px] font-black flex items-center justify-center flex-shrink-0">6</span>
                                <span>New GTMA</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-6 overflow-x-auto">
                    <div class="print-header">
                        <p style="font-size:10px;font-weight:900;letter-spacing:0.2em;color:#dc2626;text-transform:uppercase;margin-bottom:2px;">Witel Sumut</p>
                        <h1 style="font-size:16px;font-weight:900;text-transform:uppercase;margin:0;">Report Data</h1>
                        <p style="font-size:9px;color:#555;margin-top:2px;">Periode: {{ $periodeLabel }}</p>
                        <hr style="margin:6px 0;border-color:#ccc;">
                    </div>

                    <table class="min-w-full border-collapse border border-gray-400 text-[11px] font-sans">
                        <thead class="bg-[#4a7795] text-white">
                            <tr>
                                <th rowspan="2" class="border border-gray-300 px-2 py-2 w-8 text-center">No</th>
                                <th rowspan="2" class="border border-gray-300 px-2 py-2 text-center">Unit / Scope</th>
                                <th rowspan="2" class="border border-gray-300 px-2 py-2 text-center">Indicator</th>
                                <th rowspan="2" class="border border-gray-300 px-2 py-2 w-10 text-center">Denom</th>
                                <th colspan="2" class="border border-gray-300 px-2 py-1 text-center">Commitment</th>
                                <th colspan="2" class="border border-gray-300 px-2 py-1 text-center">Real</th>
                                <th rowspan="2" class="border border-gray-300 px-2 py-2 w-14 text-center">Fairness</th>
                                <th rowspan="2" class="border border-gray-300 px-2 py-2 w-14 text-center">Ach</th>
                                <th rowspan="2" class="border border-gray-300 px-2 py-2 w-14 text-center">Score</th>
                                <th rowspan="2" class="border border-gray-300 px-2 py-2 w-32 text-center no-print">Last Update</th>
                            </tr>
                            <tr class="text-center">
                                <th class="border border-gray-300 px-2 py-1">Amount</th>
                                <th class="border border-gray-300 px-2 py-1">Rp Million</th>
                                <th class="border border-gray-300 px-2 py-1">Amount</th>
                                <th class="border border-gray-300 px-2 py-1">Rp Million</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800">

                            <tr class="border-b-2 border-black font-bold">
                                <td class="border border-gray-400 text-center">1</td>
                                <td colspan="10" class="border border-gray-400 px-2 py-1 uppercase bg-gray-50">Scaling</td>
                            </tr>

                            @php
                                $segLetters = ['gov' => 'a', 'private' => 'b', 'soe' => 'c', 'sme' => 'd'];
                                $typeKeys   = array_keys($scallingTypes);
                                $typeCount  = count($typeKeys);
                            @endphp

                            @foreach($scallingSegments as $segKey => $seg)
                                @php
                                    $letter   = $segLetters[$segKey];
                                    $typeRows = $scallingData[$segKey];
                                    $scoreCommit = 0; $scoreReal = 0;
                                    foreach(['on-hand','qualified','initiate'] as $t) {
                                        $scoreCommit += $typeRows[$t]['commit_rp'];
                                        $scoreReal   += $typeRows[$t]['real_rp'];
                                    }
                                    $scoreVal = $scoreCommit > 0 ? ($scoreReal / $scoreCommit) * 100 : null;
                                    $scoreC   = scalingAchColor($scoreVal);
                                @endphp

                                @foreach($typeKeys as $ti => $typeKey)
                                    @php
                                        $row      = $typeRows[$typeKey];
                                        $isFirst  = ($ti === 0);
                                        $cRp      = $row['commit_rp'];
                                        $rRp      = $row['real_rp'];
                                        $achVal   = $cRp > 0 ? ($rRp / $cRp) * 100 : null;
                                        $achC     = ($typeKey === 'koreksi') ? scalingKoreksiColor($achVal) : scalingAchColor($achVal);
                                        $showScore = ($typeKey === 'on-hand');
                                    @endphp
                                    <tr id="scaling-row-{{ $segKey }}-{{ $typeKey }}">
                                        @if($segKey === 'gov' && $isFirst)
                                            @php $fairnessScalingRowspan = $typeCount * count($scallingSegments) + 1 + count($teldaRegions) + 1; @endphp
                                            <td rowspan="{{ $typeCount * count($scallingSegments) + 1 + count($teldaRegions) + 1 }}" class="border border-gray-400 text-center align-middle"></td>
                                        @endif

                                        @if($isFirst)
                                        <td class="border border-gray-400 px-2 py-1 font-semibold align-top" rowspan="{{ $typeCount }}">{{ $letter }}&nbsp;&nbsp;{{ $seg['label'] }}</td>
                                        @endif

                                        <td class="border border-gray-400 px-2 py-1">{{ $row['label'] }}</td>
                                        <td class="border border-gray-400 text-center">lop</td>
                                        <td class="border border-gray-400 px-2 text-right {{ $typeKey === 'initiate' ? 'font-bold' : '' }}">
                                            <span style="{{ ($typeKey === 'initiate' && $row['commit_amount'] > 0)
                                                ? 'background:#ff7f50; color:#fff; padding:2px 4px; border-radius:3px;'
                                                : '' }}">
                                                @if($typeKey === 'initiate')
                                                {{ $row['commit_amount'] > 0
                                                    ? number_format($row['commit_amount'], fmod($row['commit_amount'], 1) == 0 ? 0 : 2, ',', '.')
                                                    : ''
                                                }}
                                                @else
                                                    {{ $row['commit_amount'] > 0 ? $row['commit_amount'] : ($cRp > 0 ? '' : '') }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="border border-gray-400 px-2 text-right {{ $typeKey === 'initiate' ? 'font-bold' : '' }}">
                                            <span style="{{ ($typeKey === 'initiate' && $row['commit_amount'] >= 0)
                                                ? 'background:#ff7f50; color:#fff; padding:2px 4px; border-radius:3px;'
                                                : '' }}">
                                                @if($typeKey === 'initiate')
                                                    {{ $row['use_data_gaps']
                                                        ? number_format($cRp, fmod($cRp, 1) == 0 ? 0 : 2, ',', '.')
                                                        : '0,00' }}
                                                @elseif($row['commit_amount'] > 0)
                                                    {{ number_format($cRp, fmod($cRp, 1) == 0 ? 0 : 2, ',', '.') }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="border border-gray-400 px-2 text-right">
                                            {{ $row['real_amount'] > 0 ? $row['real_amount'] : ($rRp > 0 ? '-' : '') }}
                                        </td>
                                        <td class="border border-gray-400 px-2 text-right">
                                            @if(!is_null($rRp) && $row['real_amount'] > 0)
                                            {{ $rRp >= 0 ? number_format($rRp, fmod($rRp, 1) == 0 ? 0 : 2, ',', '.') : '' }}
                                            @endif
                                        </td>

                                        @if($segKey === 'gov' && $isFirst)
                                            <td class="border border-gray-400 text-center align-middle" rowspan="{{ $fairnessScalingRowspan }}">0-100</td>
                                        @endif

                                        <td class="border border-gray-400 text-right font-bold" style="{{ $achC['bg'] }}">
                                            <span class="{{ $achC['text'] }}">{{ $achC['label'] }}</span>
                                        </td>

                                        @if($showScore)
                                        <td class="border border-gray-400 text-right font-bold align-middle" rowspan="3" style="{{ $scoreC['bg'] }}">
                                            <span class="{{ $scoreC['text'] }}">{{ $scoreC['label'] }}</span>
                                        </td>
                                        @elseif($typeKey === 'koreksi')
                                        <td class="border-t border-b border-r border-gray-400"></td>
                                        @endif
                                        <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $row['updated_at'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach

                            @php
                                $hsiCommit = $hsiData['commit_amount'];
                                $hsiReal   = $hsiData['real_amount'];
                                $hsiAchVal = $hsiCommit > 0 ? ($hsiReal / $hsiCommit) * 100 : null;
                                $hsiC      = scalingAchColor($hsiAchVal);
                            @endphp
                            <tr>
                                <td class="border border-gray-400 px-2 py-1 font-semibold">e&nbsp;&nbsp;HSI Agency</td>
                                <td class="border border-gray-400 px-2 py-1">Sales HSI Non AM Non Telda</td>
                                <td class="border border-gray-400 text-center">ssl</td>
                                <td class="border border-gray-400 px-2 text-right">
                                    {{ $hsiCommit > 0 ? number_format($hsiCommit, 0) : '' }}
                                </td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400 px-2 text-right">
                                    {{ $hsiReal > 0 ? number_format($hsiReal, 0) : '' }}
                                </td>
                                <td class="border border-gray-400"></td>
                                <td colspan="2" class="border border-gray-400 text-right font-bold" style="{{ $hsiC['bg'] }}">
                                    <span class="{{ $hsiC['text'] }}">{{ $hsiC['label'] }}</span>
                                </td>
                                <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $hsiData['updated_at'] ?? '-' }}</td>
                            </tr>

                            @php
                                $teldaCount      = count($teldaRegions);
                                $teldaSumCommit  = collect($teldaData)->sum('commit_rp');
                                $teldaSumReal    = collect($teldaData)->sum('real_rp');
                                $teldaScoreVal   = $teldaSumCommit > 0 ? ($teldaSumReal / $teldaSumCommit) * 100 : null;
                                $teldaScoreC     = scalingAchColor($teldaScoreVal);
                            @endphp
                            @foreach($teldaData as $rKey => $region)
                                @php
                                    $isTFirst   = $loop->first;
                                    $cRp        = $region['commit_rp'];
                                    $rRp        = $region['real_rp'];
                                    $tAchVal    = (!is_null($cRp) && $cRp > 0) ? ($rRp / $cRp) * 100 : null;
                                    $tAchC      = scalingAchColor($tAchVal);
                                @endphp
                                <tr>
                                    @if($isTFirst)
                                    <td class="border border-gray-400 px-2 py-1 font-semibold align-top" rowspan="{{ $teldaCount }}">f&nbsp;&nbsp;Telda</td>
                                    @endif

                                    <td class="border border-gray-400 px-2 py-1">{{ $region['label'] }}</td>
                                    <td class="border border-gray-400 text-center">Rp</td>
                                    <td class="border border-gray-400"></td>
                                    <td class="border border-gray-400 px-2 text-right">
                                        {{ !is_null($cRp) && $cRp > 0 ? number_format($cRp, fmod($cRp, 1) == 0 ? 0 : 2, ',', '.') : ($cRp === 0.0 ? '' : '-') }}
                                    </td>
                                    <td class="border border-gray-400"></td>
                                    <td class="border border-gray-400 px-2 text-right">
                                        {{ !is_null($rRp) ? number_format($rRp, fmod($rRp, 1) == 0 ? 0 : 2, ',', '.') : '' }}
                                    </td>

                                    <td class="border border-gray-400 text-right font-bold" style="{{ $tAchC['bg'] }}">
                                        <span class="{{ $tAchC['text'] }}">{{ $tAchC['label'] }}</span>
                                    </td>

                                    @if($isTFirst)
                                    <td class="border border-gray-400 text-right font-bold align-middle" rowspan="{{ $teldaCount }}" style="{{ $teldaScoreC['bg'] }}">
                                        <span class="{{ $teldaScoreC['text'] }}">{{ $teldaScoreC['label'] }}</span>
                                    </td>
                                    @endif
                                    <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $region['updated_at'] ?? '-' }}</td>
                                </tr>
                            @endforeach

                            @php
                                $upCommit = (float) $upsellingData['commit_rp'];
                                $upReal   = (float) $upsellingData['real_rp'];
                                $upAchVal = $upCommit > 0 ? ($upReal / $upCommit) * 100 : null;
                                $upC      = scalingAchColor($upAchVal);
                            @endphp
                            <tr>
                                <td class="border border-gray-400 px-2 py-1 font-semibold">g&nbsp;&nbsp;Upselling HSI</td>
                                <td class="border border-gray-400 px-2 py-1">Next Level HSI</td>
                                <td class="border border-gray-400 text-center">Rp</td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400 px-2 text-right">
                                    {{ $upCommit > 0 ? number_format($upCommit, fmod($upCommit, 1) == 0 ? 0 : 2, ',', '.') : '' }}
                                </td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400 px-2 text-right">
                                    {{ $upReal > 0 ? number_format($upReal, fmod($upReal, 1) == 0 ? 0 : 2, ',', '.') : '' }}
                                </td>
                                <td colspan="2" class="border border-gray-400 text-right font-bold" style="{{ $upC['bg'] }}">
                                    <span class="{{ $upC['text'] }}">{{ $upC['label'] }}</span>
                                </td>
                                <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $upsellingData['updated_at'] ?? '-' }}</td>
                            </tr>

                            <tr class="border-b-2 border-black font-bold">
                                <td class="border border-gray-400 text-center">2</td>
                                <td colspan="10" class="border border-gray-400 px-2 py-1 uppercase bg-gray-50">Collection</td>
                            </tr>

                            @php $sectionRowspan = 1 + 1 + 4 + $utipRowspan; @endphp

                            <tr>
                                <td rowspan="{{ $sectionRowspan }}" class="border border-gray-400"></td>
                                <td class="border border-gray-400 px-2 py-1 font-semibold">a&nbsp;&nbsp;C3MR</td>
                                <td class="border border-gray-400 px-2 py-1"></td>
                                <td class="border border-gray-400 text-center">%</td>
                                <td class="border border-gray-400 px-2 text-right">{{ $c3mrKomitmen > 0 ? number_format($c3mrKomitmen, 2, ',', '.') : '' }}</td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400 px-2 text-right">{{ $c3mrRealisasi > 0 ? number_format($c3mrRealisasi, 2, ',', '.') : '' }}</td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400 text-center">{{ $fairnessC3mr }}</td>
                                <td colspan="2" class="border border-gray-400 text-right font-bold {{ $colorC3mr }}">{{ $scoreC3mr }}</td>
                                <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $c3mrUpdatedAt }}</td>
                            </tr>

                            <tr>
                                <td class="border border-gray-400 px-2 py-1 font-semibold">b&nbsp;&nbsp;Bilper</td>
                                <td class="border border-gray-400 px-2 py-1"></td>
                                <td class="border border-gray-400 text-center">%</td>
                                <td class="border border-gray-400 px-2 text-right">{{ $bilperKomitmen > 0 ? number_format($bilperKomitmen, 2, ',', '.') : '' }}</td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400 px-2 text-right">{{ $bilperRealisasi > 0 ? number_format($bilperRealisasi, 2, ',', '.') : '' }}</td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400 text-center">{{ $fairnessBilper }}</td>
                                <td colspan="2" class="border border-gray-400 text-right font-bold {{ $colorBilper }}">{{ $scoreBilper }}</td>
                                <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $bilperUpdatedAt }}</td>
                            </tr>

                            @foreach(['GOV', 'SME', 'PRIVATE', 'SOE'] as $si => $seg)
                                <tr>
                                    @if($si === 0)
                                        <td rowspan="4" class="border border-gray-400 px-2 py-1 align-top font-semibold">c&nbsp;&nbsp;CR</td>
                                    @endif
                                    <td class="border border-gray-400 px-2 py-1">CR {{ $seg }}</td>
                                    <td class="border border-gray-400 text-center">%</td>
                                    <td class="border border-gray-400 px-2 text-right">{{ $crData[$seg]['komitmen'] > 0 ? number_format($crData[$seg]['komitmen'], 2, ',', '.') : '' }}</td>
                                    <td class="border border-gray-400"></td>
                                    <td class="border border-gray-400 px-2 text-right">{{ $crData[$seg]['realisasi'] > 0 ? number_format($crData[$seg]['realisasi'], 2, ',', '.') : '' }}</td>
                                    <td class="border border-gray-400"></td>
                                    @if($si === 0)
                                        <td rowspan="4" class="border border-gray-400 text-center align-middle">{{ $fairnessCR }}</td>
                                    @endif
                                    <td class="border border-gray-400 text-right font-bold {{ $crScores[$seg]['color'] }}">{{ $crScores[$seg]['text'] }}</td>
                                    @if($si === 0)
                                        <td rowspan="4" class="border border-gray-400 text-right font-bold align-middle {{ $colorCRTotal }}">{{ $crTotalPct }}</td>
                                    @endif
                                    <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $crUpdatedAt[$seg] ?? '-' }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <td rowspan="{{ $utipRowspan }}" class="border border-gray-400 px-2 py-1 align-top font-semibold">d&nbsp;&nbsp;UTIP</td>
                                <td class="border border-gray-400 px-2 py-1">{{ $utipCorrective['label'] }}</td>
                                <td class="border border-gray-400 text-center">Rp</td>
                                <td class="border border-gray-400 px-2 text-right">{{ $utipCorrective['planRp'] > 0 ? number_format($utipCorrective['planRp'], 2, ',', '.') : '' }}</td>
                                <td class="border border-gray-400 px-2 text-right">{{ $utipCorrective['commitRp'] > 0 ? number_format($utipCorrective['commitRp'], 2, ',', '.') : '' }}</td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400 px-2 text-right">{{ $utipCorrective['realRp'] > 0 ? number_format($utipCorrective['realRp'], 2, ',', '.') : '' }}</td>
                                <td rowspan="{{ $utipRowspan }}" class="border border-gray-400 text-center align-middle">{{ $fairnessUTIP }}</td>
                                <td class="border border-gray-400 text-right font-bold {{ $colorAchCorrective }}">{{ $achCorrective }}</td>
                                <td rowspan="{{ $utipRowspan }}" class="border border-gray-400 text-right font-bold align-middle {{ $colorScoreUTIP }}">{{ $scoreUTIP }}</td>
                                <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $utipCorrective['updated_at'] ?? '-' }}</td>
                            </tr>

                            @foreach($newUtipPeriodes as $utip)
                                @php
                                    $achU      = $utip['commitRp'] == 0 ? '-' : number_format(($utip['realRp'] / $utip['commitRp']) * 100, 2, ',', '.') . '%' ;
                                    $colorAchU = getColorClass($achU, $fairnessUTIP);
                                @endphp
                                <tr>
                                    <td class="border border-gray-400 px-2 py-1">{{ $utip['label'] }}</td>
                                    <td class="border border-gray-400 text-center">Rp</td>
                                    <td class="border border-gray-400 px-2 text-right">{{ $utip['planRp'] > 0 ? number_format($utip['planRp'], 2, ',', '.') : '' }}</td>
                                    <td class="border border-gray-400 px-2 text-right">{{ $utip['commitRp'] > 0 ? number_format($utip['commitRp'], 2, ',', '.') : '' }}</td>
                                    <td class="border border-gray-400"></td>
                                    <td class="border border-gray-400 px-2 text-right">{{ $utip['realRp'] > 0 ? number_format($utip['realRp'], 2, ',', '.') : '' }}</td>
                                    <td class="border border-gray-400 text-right font-bold {{ $colorAchU }}">{{ $achU }}</td>
                                    <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $utip['updated_at'] ?? '-' }}</td>
                                </tr>
                            @endforeach

                            <tr class="border-b-2 border-black font-bold">
                                <td class="border border-gray-400 text-center">3</td>
                                <td colspan="10" class="border border-gray-400 px-2 py-1 uppercase bg-gray-50">Combat The Churn</td>
                            </tr>

                            @foreach($ct0Data as $ri => $region)
                                <tr>
                                    @if($ri === 0)
                                        <td rowspan="{{ $ctcSectionRows }}" class="border border-gray-400"></td>
                                    @endif
                                    @if($ri === 0)
                                        <td rowspan="{{ $ct0RowCount }}" class="border border-gray-400 px-2 py-1 align-top font-semibold">a&nbsp;&nbsp;Paid Pra CT0</td>
                                    @endif
                                    <td class="border border-gray-400 px-2 py-1">{{ $region['label'] }}</td>
                                    <td class="border border-gray-400 text-center">Rp</td>
                                    <td class="border border-gray-400 px-2 text-right">{{ $region['plan'] > 0 ? number_format($region['plan'], fmod($region['plan'], 1)==0 ? 0 : 2, ',', '.') : '' }}</td>
                                    <td class="border border-gray-400 px-2 text-right">{{ $region['commit'] > 0 ? number_format($region['commit'], fmod($region['commit'], 1)==0 ? 0 : 2, ',', '.') : '' }}</td>
                                    <td class="border border-gray-400"></td>
                                    <td class="border border-gray-400 px-2 text-right">{{ $region['real'] > 0 ? number_format($region['real'],   fmod($region['real'],   1)==0 ? 0 : 2, ',', '.') : '' }}</td>
                                    @if($ri === 0)
                                        <td rowspan="{{ $ct0RowCount }}" class="border border-gray-400 text-center align-middle">{{ $fairnessCt0 }}</td>
                                    @endif
                                    <td class="border border-gray-400 text-right font-bold {{ $region['achColor'] }}">{{ $region['ach'] }}</td>
                                    @if($ri === 0)
                                        <td rowspan="{{ $ct0RowCount }}" class="border border-gray-400 text-right font-bold align-middle {{ $ct0ScoreColor }}">{{ $ct0Score }}</td>
                                    @endif
                                    <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $region['updated_at'] ?? '-' }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <td rowspan="{{ $ctcRowCount }}" class="border border-gray-400 px-2 py-1 align-top font-semibold">b&nbsp;&nbsp;CTC</td>
                                <td class="border border-gray-400 px-2 py-1">CT0</td>
                                <td class="border border-gray-400 text-center">ssl</td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400 px-2 text-right font-semibold">{{ $ctcCt0Real > 0 ? number_format($ctcCt0Real, 0) : '' }}</td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400"></td>
                                <td colspan="2" class="border border-gray-400"></td>
                                <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $ctcCt0UpdatedAt }}</td>
                            </tr>

                            @foreach(['Sales HSI (all)', 'Churn', 'Winback'] as $seg)
                                <tr>
                                    <td class="border border-gray-400 px-2 py-1">{{ $seg }}</td>
                                    <td class="border border-gray-400 text-center">ssl</td>
                                    <td class="border border-gray-400 px-2 text-right">{{ $ctcData[$seg]['commit'] > 0 ? number_format($ctcData[$seg]['commit'], 0) : '' }}</td>
                                    <td class="border border-gray-400"></td>
                                    <td class="border border-gray-400 px-2 text-right">{{ $ctcData[$seg]['real'] > 0 ? number_format($ctcData[$seg]['real'], 0) : '' }}</td>
                                    <td class="border border-gray-400"></td>
                                    @if($loop->first)
                                        <td rowspan="3" class="border border-gray-400 text-center align-middle">{{ $fairnessCtc }}</td>
                                    @endif
                                    <td colspan="2" class="border border-gray-400 text-right font-bold {{ $ctcData[$seg]['achColor'] }}">{{ $ctcData[$seg]['ach'] }}</td>
                                    <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $ctcData[$seg]['updated_at'] ?? '-' }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <td class="border border-gray-400 px-2 py-1">Loss Rate</td>
                                <td class="border border-gray-400 text-center">%</td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400 px-2 text-right font-semibold">{{ $lossRateReal }}</td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400 text-center">{{ $fairnessLoss }}</td>
                                <td colspan="2" class="border border-gray-400 text-right font-bold {{ $lossRateColor }}">{{ $lossRateAch }}</td>
                                <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $lossRateUpdatedAt }}</td>
                            </tr>

                            @php $rsSectionRows = count($b1Data) + count($b2Data) + count($b3Data) + count($b4Data); @endphp

                            <tr class="border-b-2 border-black font-bold">
                                <td class="border border-gray-400 text-center">4</td>
                                <td colspan="10" class="border border-gray-400 px-2 py-1 uppercase bg-gray-50">Rising Star</td>
                            </tr>

                            @foreach($b1Data as $ti => $row)
                                <tr>
                                    @if($loop->first)
                                        <td rowspan="{{ $rsSectionRows }}" class="border border-gray-400"></td>
                                        <td rowspan="{{ count($b1Data) }}" class="border border-gray-400 px-2 py-1 align-top font-semibold">a&nbsp;&nbsp;Bintang 1</td>
                                    @endif
                                    <td class="border border-gray-400 px-2 py-1">{{ $row['label'] }}</td>
                                    <td class="border border-gray-400 text-center">%</td>
                                    <td class="border border-gray-400 px-2 text-right">{{ $row['commit'] !== null ? number_format($row['commit'], 2, ',', '.') : '' }}</td>
                                    <td class="border border-gray-400"></td>
                                    <td class="border border-gray-400 px-2 text-right font-bold {{ $row['achColor'] }}">{{ $row['real'] !== null ? number_format($row['real'], 2, ',', '.') : '' }}</td>
                                    <td class="border border-gray-400"></td>
                                    @if($loop->first)
                                        <td rowspan="{{ count($b1Data) }}" class="border border-gray-400 text-center align-middle">{{ $fairnessRs }}</td>
                                        <td rowspan="{{ count($b1Data) }}" colspan="2" class="border border-gray-400 text-center font-bold align-middle {{ $b1ScoreColor }}">{{ $b1Score }}</td>
                                    @endif
                                    <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $row['updated_at'] ?? '-' }}</td>
                                </tr>
                            @endforeach

                            @foreach($b2Data as $ti => $row)
                                <tr>
                                    @if($loop->first)
                                        <td rowspan="{{ count($b2Data) }}" class="border border-gray-400 px-2 py-1 align-top font-semibold">b&nbsp;&nbsp;Bintang 2</td>
                                    @endif
                                    <td class="border border-gray-400 px-2 py-1">{{ $row['label'] }}</td>
                                    <td class="border border-gray-400 text-center">%</td>
                                    <td class="border border-gray-400 px-2 text-right">{{ $row['commit'] !== null ? number_format($row['commit'], 2, ',', '.') : '' }}</td>
                                    <td class="border border-gray-400"></td>
                                    <td class="border border-gray-400 px-2 text-right font-bold {{ $row['achColor'] }}">{{ $row['real'] !== null ? number_format($row['real'], 2, ',', '.') : '' }}</td>
                                    <td class="border border-gray-400"></td>
                                    @if($loop->first)
                                        <td rowspan="{{ count($b2Data) }}" class="border border-gray-400 text-center align-middle">{{ $fairnessRs }}</td>
                                        <td rowspan="{{ count($b2Data) }}" colspan="2" class="border border-gray-400 text-center font-bold align-middle {{ $b2ScoreColor }}">{{ $b2Score }}</td>
                                    @endif
                                    <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $row['updated_at'] ?? '-' }}</td>
                                </tr>
                            @endforeach

                            @foreach($b3Data as $tid => $row)
                            @php $b3RowPct = $row['commit'] > 0 ? number_format($row['ratio'], 2) . '%' : '-'; @endphp
                            <tr>
                                @if($loop->first)
                                    <td rowspan="{{ count($b3Data) }}" class="border border-gray-400 px-2 py-1 font-semibold align-top">c&nbsp;&nbsp;Bintang 3</td>
                                @endif
                                <td class="border border-gray-400 px-2 py-1">{{ $row['label'] }}</td>
                                <td class="border border-gray-400 text-center">%</td>
                                <td class="border border-gray-400 px-2 text-right">{{ $row['commit'] !== null ? number_format($row['commit'], 2, ',', '.') : '' }}</td>
                                <td class="border border-gray-400"></td>
                                <td class="border border-gray-400 px-2 text-right font-bold {{ getColorClass($b3RowPct, $fairnessRs) }}">{{ $row['real'] !== null ? number_format($row['real'], 2, ',', '.') : '' }}</td>
                                <td class="border border-gray-400"></td>
                                @if($loop->first)
                                    <td rowspan="{{ count($b3Data) }}" class="border border-gray-400 text-center align-middle">{{ $fairnessRs }}</td>
                                    <td rowspan="{{ count($b3Data) }}" colspan="2" class="border border-gray-400 text-center font-bold align-middle {{ $b3ScoreColor }}">{{ $b3Score }}</td>
                                @endif
                                <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $row['updated_at'] ?? '-' }}</td>
                            </tr>
                        @endforeach

                            @foreach($b4Data as $bi => $b4row)
                            <tr>
                                @if($loop->first)
                                    <td rowspan="{{ count($b4Data) }}" class="border border-gray-400 px-2 py-1 align-top font-semibold">d&nbsp;&nbsp;Bintang 4</td>
                                @endif
                                <td class="border border-gray-400 px-2 py-1">{{ $b4row['label'] }}</td>
                                <td class="border border-gray-400 text-center">%</td>
                                <td class="border border-gray-400 px-2 text-right">{{ $b4row['commit'] !== null ? number_format($b4row['commit'], 2, ',', '.') : '' }}</td>
                                <td class="border border-gray-400"></td>
                                @php
                                    $b4RowPct   = $b4row['commit'] > 0 ? number_format($b4row['real'] / $b4row['commit'] * 100, 2, ',', '.') . '%' : '-';
                                    $b4RowColor = getColorClass($b4RowPct, $fairnessB4);
                                @endphp
                                <td class="border border-gray-400 px-2 text-right font-bold {{ $b4RowColor }}">{{ $b4row['real'] !== null ? number_format($b4row['real'], 2, ',', '.') : '' }}</td>
                                @if($loop->first)
                                    <td rowspan="{{ count($b4Data) }}" class="border border-gray-400 px-2 text-right align-middle">{{ $b4RpMillion > 0 ? $b4RpDisplay . '%' : '' }}</td>
                                    <td rowspan="{{ count($b4Data) }}" class="border border-gray-400 text-center align-middle">{{ $fairnessB4 }}</td>
                                    <td rowspan="{{ count($b4Data) }}" colspan="2" class="border border-gray-400 text-center font-bold align-middle {{ $b4ScoreColor }}">{{ $b4Score }}</td>
                                @endif
                                <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $b4row['updated_at'] ?? '-' }}</td>
                            </tr>
                        @endforeach

                            @php
                                $fairnessPsak = '0-100';
                                $psakSubLabels = ['a','b','c','d'];
                                $psakIndCount  = 6;
                                $psakTotal     = count($psakData);
                                $psakAllRows   = $psakTotal * $psakIndCount;
                            @endphp

                            <tr class="border-b-2 border-black font-bold">
                                <td class="border border-gray-400 text-center">5</td>
                                <td colspan="10" class="border border-gray-400 px-2 py-1 uppercase bg-gray-50">PSAK</td>
                            </tr>

                            @foreach($psakData as $typeKey => $typeData)
                                @php
                                    $subLabel   = $psakSubLabels[array_search($typeKey, array_keys($psakData))];
                                    $scoreColor = getColorClass($typeData['score'], $fairnessPsak);
                                    $indicators = array_values($typeData['indicators']);
                                    $indCount   = count($indicators);
                                @endphp
                                @foreach($indicators as $idx => $ind)
                                    @php $achColor = getColorClass($ind['ach'], $fairnessPsak); @endphp
                                    <tr>
                                        @if($loop->parent->first && $idx === 0)
                                            <td rowspan="{{ $psakAllRows }}" class="border border-gray-400"></td>
                                        @endif
                                        @if($idx === 0)
                                            <td rowspan="{{ $indCount }}" class="border border-gray-400 px-2 py-1 align-top font-semibold">
                                                {{ $subLabel }}&nbsp;&nbsp;{{ $typeData['label'] }}
                                            </td>
                                        @endif
                                        <td class="border border-gray-400 px-2 py-1">{{ $ind['label'] }}</td>
                                        <td class="border border-gray-400 text-center text-xs">Rp</td>
                                        <td class="border border-gray-400 px-2 text-right">
                                            {{ $ind['commSsl'] > 0 ? number_format($ind['commSsl'], 0) : ($ind['commRp'] > 0 || $ind['realRp'] > 0 ? '-' : '') }}
                                        </td>
                                        <td class="border border-gray-400 px-2 text-right">
                                            {{ $ind['commRp'] > 0 ? number_format($ind['commRp'], fmod($ind['commRp'], 1)==0 ? 0 : 2, ',', '.') : ($ind['realRp'] > 0 ? '-' : '') }}
                                        </td>
                                        <td class="border border-gray-400 px-2 text-right">
                                            {{ $ind['realSsl'] > 0 ? number_format($ind['realSsl'], 0) : ($ind['realRp'] > 0 ? '-' : '') }}
                                        </td>
                                        <td class="border border-gray-400 px-2 text-right">
                                            {{ $ind['realRp'] > 0 ? number_format($ind['realRp'], fmod($ind['realRp'], 1)==0 ? 0 : 2, ',', '.') : '' }}
                                        </td>
                                        @if($loop->parent->first && $idx === 0)
                                            <td rowspan="{{ $psakAllRows }}" class="border border-gray-400 text-center align-middle">{{ $fairnessPsak }}</td>
                                        @endif
                                        <td class="border border-gray-400 px-2 text-right font-bold {{ $achColor }}">{{ $ind['ach'] }}</td>
                                        @if($idx === 0)
                                            <td rowspan="{{ $indCount }}" class="border border-gray-400 text-right font-bold align-middle {{ $scoreColor }}">{{ $typeData['score'] }}</td>
                                        @endif
                                        <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $ind['updated_at'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                                @endforeach

                                    <tr class="border-b-2 border-black font-bold">
                                        <td class="border border-gray-400 text-center">6</td>
                                        <td colspan="10" class="border border-gray-400 px-2 py-1 uppercase bg-gray-50">New GTMA</td>
                                    </tr>

                                    @php $ngtmaSubMap = ['gov' => 'a', 'private' => 'b', 'soe' => 'c', 'sme' => 'd']; @endphp

                                    @foreach($ngtmaSegments as $segKey => $seg)
                                    @php
                                        $row      = $ngtmaData[$segKey];
                                        $subLabel = $ngtmaSubMap[$segKey];
                                        $cRp      = $row['commit_rp'];
                                        $rRp      = $row['real_rp'];
                                        $achVal   = $cRp > 0 ? ($rRp / $cRp) * 100 : null;
                                        $achC     = scalingAchColor($achVal);
                                        $scoreC   = scalingAchColor($achVal);
                                    @endphp
                                    <tr id="ngtma-row-{{ $segKey }}">
                                        @if($loop->first)
                                            <td rowspan="{{ count($ngtmaSegments) }}" class="border border-gray-400"></td>
                                        @endif
                                        <td class="border border-gray-400 px-2 py-1 font-semibold">{{ $subLabel }}&nbsp;&nbsp;{{ $row['label'] }}</td>
                                        <td class="border border-gray-400 px-2 py-1">On Hand</td>
                                        <td class="border border-gray-400 text-center">lop</td>
                                        <td class="border border-gray-400 px-2 text-right">{{ $row['commit_amount'] > 0 ? $row['commit_amount'] : '' }}</td>
                                        <td class="border border-gray-400 px-2 text-right">{{ $cRp > 0 ? number_format($cRp, fmod($cRp,1)==0?0:2,',','.') : '' }}</td>
                                        <td class="border border-gray-400 px-2 text-right">{{ $row['real_amount'] > 0 ? $row['real_amount'] : '' }}</td>
                                        <td class="border border-gray-400 px-2 text-right">{{ $rRp > 0 ? number_format($rRp, fmod($rRp,1)==0?0:2,',','.') : '' }}</td>
                                        @if($loop->first)
                                            <td rowspan="{{ count($ngtmaSegments) }}" class="border border-gray-400 text-center align-middle">0-100</td>
                                        @endif
                                        <td colspan="2" class="border border-gray-400 text-right font-bold align-middle" style="{{ $achC['bg'] }}">
                                            <span class="{{ $achC['text'] }}">{{ $achC['label'] }}</span>
                                        </td>
                                        <td class="border border-gray-300 px-2 py-1 text-center text-[10px] no-print">{{ $row['updated_at'] ?? '-' }}</td>
                                    </tr>
                                    @endforeach

                        </tbody>
                    </table>
                    <div id="scaling-detail-btns" style="position:relative;height:0;overflow:visible;pointer-events:none;"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-5 mt-6 no-print">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                    <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Keterangan Warna</h2>
                </div>
                <div class="flex flex-wrap gap-5 text-xs font-semibold">
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 rounded bg-green-500"></div>
                        <span class="text-slate-600">≥ 100% — Tercapai</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 rounded bg-yellow-300 border border-slate-200"></div>
                        <span class="text-slate-600">Mendekati target</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 rounded bg-red-500"></div>
                        <span class="text-slate-600">Di bawah target</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 rounded bg-black"></div>
                        <span class="text-slate-600">Jauh di bawah target</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

<style>
    .print-header { display: none; }
    @media print { .no-print { display: none !important; } }
    #export-overlay {
        display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);
        z-index:9999;align-items:center;justify-content:center;flex-direction:column;gap:16px;
    }
    #export-overlay.active { display:flex; }
    #export-overlay p { color:white;font-weight:900;font-size:13px;letter-spacing:0.12em;text-transform:uppercase; }
    #export-progress-bar-wrap { width:280px;height:6px;background:rgba(255,255,255,0.2);border-radius:99px;overflow:hidden; }
    #export-progress-bar { height:100%;background:#ef4444;border-radius:99px;transition:width 0.3s;width:0%; }
    .export-spinner { width:36px;height:36px;border:4px solid rgba(255,255,255,0.3);border-top-color:white;border-radius:50%;animation:espin 0.8s linear infinite; }
    @keyframes espin { to { transform:rotate(360deg); } }
    #export-root {
        position:fixed;left:-99999px;top:0;z-index:-1;background:white;
        font-family:'Inter','Noto Sans',Arial,sans-serif!important;
    }
    .export-page {
        width:860px;
        background:white;
        padding:24px 24px 28px;
        font-family:'Inter','Noto Sans',Arial,sans-serif;
        box-sizing:border-box;
        -webkit-font-smoothing:antialiased;
    }
    .export-page-header {
        display:flex;align-items:center;gap:16px;
        margin-bottom:4px;padding-bottom:10px;
        border-bottom:3px solid #dc2626;
    }
    .export-page-header img { height:36px; }
    .export-page-header-divider { width:1px;height:36px;background:#e2e8f0; }
    .export-org {
        font-size:8.5px;font-weight:800;letter-spacing:0.3em;
        color:#dc2626;text-transform:uppercase;margin-bottom:2px;
    }
    .export-title {
        font-size:15px;font-weight:900;text-transform:uppercase;
        color:#0f172a;line-height:1;letter-spacing:0.02em;
    }
    .export-subtitle {
        font-size:8px;color:#64748b;font-weight:600;
        margin-top:3px;text-transform:uppercase;letter-spacing:0.05em;
    }
    .export-section-badge {
        display:block;font-size:9px;font-weight:800;
        text-transform:uppercase;letter-spacing:0.15em;
        color:#dc2626;background:#fef2f2;
        border-left:3px solid #dc2626;padding:2px 12px 10px 12px;;
        margin-top:6px;margin-bottom:6px;
    }
    .export-table {
        width:100%;border-collapse:collapse;
        font-family:'Inter','Noto Sans',Arial,sans-serif;
        font-size:8.5px;
        table-layout:fixed;
    }
    .export-table th {
        background:#4a7795;color:white;
        padding:5px 6px;
        border:1px solid rgba(255,255,255,0.4);
        text-align:center;
        font-weight:700;font-size:7.5px;
        text-transform:uppercase;
        letter-spacing:0.04em;
        line-height:1.3;
        word-break:break-word;
    }
    .export-table td {
        padding:3px 6px;
        border:1px solid #cbd5e1;
        font-size:8px;
        vertical-align:middle;
        line-height:1.4;
        word-break:break-word;
        overflow-wrap:break-word;
    }
    .export-table .col-no      { width:22px; }
    .export-table .col-scope   { width:115px; }
    .export-table .col-ind     { width:140px; }
    .export-table .col-denom   { width:30px; }
    .export-table .col-num     { width:65px; }
    .export-table .col-fair    { width:42px; }
    .export-table .col-ach     { width:42px; }
    .export-table .col-score   { width:42px; }
</style>

<div id="export-overlay">
    <div class="export-spinner"></div>
    <p id="export-status">Mempersiapkan...</p>
    <div id="export-progress-bar-wrap"><div id="export-progress-bar"></div></div>
</div>
<div id="export-root"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
(function () {

    var detailRoutes = [
        { id:'scaling-row-gov-on-hand',      href:'{{ $scalingDetailRoutes['gov']['on-hand'] }}' },
        { id:'scaling-row-gov-qualified',    href:'{{ $scalingDetailRoutes['gov']['qualified'] }}' },
        { id:'scaling-row-gov-initiate',     href:'{{ $scalingDetailRoutes['gov']['initiate'] }}' },
        { id:'scaling-row-gov-koreksi',      href:'{{ $scalingDetailRoutes['gov']['koreksi'] }}' },
        { id:'scaling-row-private-on-hand',  href:'{{ $scalingDetailRoutes['private']['on-hand'] }}' },
        { id:'scaling-row-private-qualified',href:'{{ $scalingDetailRoutes['private']['qualified'] }}' },
        { id:'scaling-row-private-initiate', href:'{{ $scalingDetailRoutes['private']['initiate'] }}' },
        { id:'scaling-row-private-koreksi',  href:'{{ $scalingDetailRoutes['private']['koreksi'] }}' },
        { id:'scaling-row-soe-on-hand',      href:'{{ $scalingDetailRoutes['soe']['on-hand'] }}' },
        { id:'scaling-row-soe-qualified',    href:'{{ $scalingDetailRoutes['soe']['qualified'] }}' },
        { id:'scaling-row-soe-initiate',     href:'{{ $scalingDetailRoutes['soe']['initiate'] }}' },
        { id:'scaling-row-soe-koreksi',      href:'{{ $scalingDetailRoutes['soe']['koreksi'] }}' },
        { id:'scaling-row-sme-on-hand',      href:'{{ $scalingDetailRoutes['sme']['on-hand'] }}' },
        { id:'scaling-row-sme-qualified',    href:'{{ $scalingDetailRoutes['sme']['qualified'] }}' },
        { id:'scaling-row-sme-initiate',     href:'{{ $scalingDetailRoutes['sme']['initiate'] }}' },
        { id:'scaling-row-sme-koreksi',      href:'{{ $scalingDetailRoutes['sme']['koreksi'] }}' },
        { id:'ngtma-row-gov',     href:'{{ $ngtmaDetailRoutes['gov'] }}' },
        { id:'ngtma-row-private', href:'{{ $ngtmaDetailRoutes['private'] }}' },
        { id:'ngtma-row-soe',     href:'{{ $ngtmaDetailRoutes['soe'] }}' },
        { id:'ngtma-row-sme',     href:'{{ $ngtmaDetailRoutes['sme'] }}' },
    ];

    function renderDetailButtons() {
        var container = document.getElementById('scaling-detail-btns');
        if (!container) return;
        var wrapper = container.closest('.overflow-x-auto');
        if (!wrapper) return;
        var table = wrapper.querySelector('table');
        if (!table) return;
        container.innerHTML = '';
        container.style.pointerEvents = 'none';
        var tableRight = table.offsetWidth + 6;
        var containerTopInScroll = table.offsetTop + table.offsetHeight;
        detailRoutes.forEach(function(g) {
            var tr = document.getElementById(g.id);
            if (!tr) return;
            var btnTop = table.offsetTop + tr.offsetTop + (tr.offsetHeight/2) - 10 - containerTopInScroll;
            var btn = document.createElement('a');
            btn.href = g.href; btn.textContent = 'detail';
            btn.style.cssText = 'position:absolute;left:'+(tableRight+6)+'px;top:'+btnTop+'px;display:inline-flex;align-items:center;padding:2px 8px;background:#1e293b;color:white;font-size:10px;font-weight:900;border-radius:5px;text-transform:uppercase;letter-spacing:0.05em;text-decoration:none;white-space:nowrap;box-shadow:0 1px 3px rgba(0,0,0,.3);pointer-events:all;z-index:50;';
            btn.onmouseenter=function(){this.style.background='#dc2626';};
            btn.onmouseleave=function(){this.style.background='#1e293b';};
            container.appendChild(btn);
        });
    }
    function init(){ setTimeout(renderDetailButtons, 80); }
    if (document.readyState==='loading') document.addEventListener('DOMContentLoaded',init); else init();
    window.addEventListener('resize', renderDetailButtons);

    document.addEventListener('DOMContentLoaded', function() {
        var btn      = document.getElementById('btn-export-jpg');
        var dropdown = document.getElementById('export-dropdown');
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', function() { dropdown.classList.add('hidden'); });
        dropdown.addEventListener('click', function(e){ e.stopPropagation(); });
        document.querySelectorAll('.export-option').forEach(function(el) {
            el.addEventListener('click', function() {
                dropdown.classList.add('hidden');
                var target = this.getAttribute('data-export');
                if (target === 'all') doExportFull();
                else doExportSection(parseInt(target));
            });
        });
    });

    var periodeLabel = @json($periodeLabel);
    var logoUrl      = '{{ asset('img/Telkom.png') }}';

    var SECTIONS = [
        { no:1, name:'Scaling',          keyword:'SCALING' },
        { no:2, name:'Collection',       keyword:'COLLECTION' },
        { no:3, name:'Combat The Churn', keyword:'COMBAT THE CHURN' },
        { no:4, name:'Rising Star',      keyword:'RISING STAR' },
        { no:5, name:'PSAK',             keyword:'PSAK' },
        { no:6, name:'New GTMA',         keyword:'NEW GTMA' },
    ];

    function setProgress(pct) { document.getElementById('export-progress-bar').style.width = pct+'%'; }
    function setStatus(txt)   { document.getElementById('export-status').textContent = txt; }
    function showOverlay()    { document.getElementById('export-overlay').classList.add('active'); setProgress(0); }
    function hideOverlay()    { document.getElementById('export-overlay').classList.remove('active'); }

    function getLiveRows() {
        var liveTable = document.querySelector('.min-w-full.border-collapse');
        if (!liveTable) return [];
        return Array.from(liveTable.querySelectorAll('tbody tr'));
    }

    function isSectionHeader(tr) {
        var tds = tr.querySelectorAll('td');
        for (var i = 0; i < tds.length; i++) {
            if (tds[i].getAttribute('colspan') === '10') return true;
        }
        return false;
    }

    function getSectionHeaderKeyword(tr) {
        var tds = tr.querySelectorAll('td');
        for (var i = 0; i < tds.length; i++) {
            if (tds[i].getAttribute('colspan') === '10') {
                return tds[i].textContent.trim().toUpperCase();
            }
        }
        return '';
    }

    function resolveColor(td) {
        var cls      = td.className || '';
        var inlineSt = td.getAttribute('style') || '';

        if (cls.includes('bg-green-500') || inlineSt.includes('#16a34a'))
            return 'background:#16a34a;color:#ffffff;';
        if (cls.includes('bg-yellow-300') || inlineSt.includes('#eab308') || inlineSt.includes('#fde047'))
            return 'background:#fde047;color:#1e293b;';
        if (cls.includes('bg-red-500') || inlineSt.includes('#ef4444'))
            return 'background:#ef4444;color:#ffffff;';
        if (cls.includes('bg-black') || inlineSt.includes('#000000') || inlineSt.includes('#000;'))
            return 'background:#000000;color:#ffffff;';
        if (cls.includes('bg-gray-50'))
            return 'background:#f8fafc;color:#1e293b;';

        return '';
    }

    function resolveAlign(cls) {
        if (cls.includes('text-right'))  return 'text-align:right;';
        if (cls.includes('text-center')) return 'text-align:center;';
        return 'text-align:left;';
    }

    function resolveWeight(cls) {
        if (cls.includes('font-bold') || cls.includes('font-semibold') || cls.includes('font-black'))
            return 'font-weight:700;';
        return 'font-weight:400;';
    }

    function resolveVAlign(cls, rowspan) {
        if (cls.includes('align-top'))    return 'vertical-align:top;';
        if (cls.includes('align-middle')) return 'vertical-align:middle;';
        if (rowspan && parseInt(rowspan) > 1) return 'vertical-align:top;';
        return 'vertical-align:middle;';
    }

    // ═══ Clone tr → HTML string ═══
    function cloneTrFull(tr, skipFirstTd) {
    var tds = Array.from(tr.querySelectorAll('td, th'));
        if (!tds.length) return '';
        var startIdx = 0;
        if (skipFirstTd) {
            var first = tds[0];
            var firstText = first.textContent.trim();
            var hasColspan = first.getAttribute('colspan');
            if (!hasColspan && /^\d*$/.test(firstText)) startIdx = 1;
        }

        var html = '<tr>';
        for (var i = startIdx; i < tds.length; i++) {
            var td         = tds[i];
            var tag        = td.tagName.toLowerCase();
            var rowspanVal = td.getAttribute('rowspan');
            var colspanVal = td.getAttribute('colspan');
            var rs         = rowspanVal ? ' rowspan="'+rowspanVal+'"' : '';
            var cs         = colspanVal ? ' colspan="'+colspanVal+'"' : '';
            var cls        = td.className || '';

            var isSectionHeaderCell = (colspanVal === '10' || colspanVal === '11');

            var isNoPrint = td.classList.contains('no-print');
            var style;
            if (isSectionHeaderCell) {
                style = 'background:#f8fafc;color:#1e293b;font-weight:700;font-size:8.5px;'
                      + 'text-transform:uppercase;padding:5px 8px;letter-spacing:0.04em;'
                      + 'border:1px solid #d1d5db;'
                      + 'text-align:left;vertical-align:middle;'
                      + 'word-break:break-word;overflow-wrap:break-word;line-height:1.4;';
            } else if (isNoPrint) {
                style = 'text-align:center;vertical-align:top;'
          + 'padding:2px 4px 8px 4px;border:1px solid #cbd5e1;'
          + 'font-size:8px;color:#1e293b;font-weight:400;'
          + 'line-height:1.4;word-break:break-word;overflow-wrap:break-word;';
            } else {
                style = resolveColor(td)
                      + resolveAlign(cls)
                      + resolveWeight(cls)
                      + resolveVAlign(cls, rowspanVal)
                      + 'padding:2px 6px 8px 6px;border:1px solid #cbd5e1;font-size:8px;'
                      + 'line-height:1.4;word-break:break-word;overflow-wrap:break-word;';
            }

            var content = td.textContent.trim();
            html += '<'+tag+rs+cs+' style="'+style+'">'+content+'</'+tag+'>';
        }
        html += '</tr>';
        return html;
    }

    function getSectionRows(keyword) {
        var allRows = getLiveRows();
        var result  = [];
        var capture = false;
        for (var i = 0; i < allRows.length; i++) {
            var tr = allRows[i];
            if (isSectionHeader(tr)) {
                var kw = getSectionHeaderKeyword(tr);
                if (kw === keyword) { capture = true; continue; }
                else if (capture)   break;
            }
            if (capture) result.push(tr);
        }
        return result;
    }

    function colgroupHtml(withNo) {
        var cols = withNo
            ? '<col style="width:20px"><col style="width:90px"><col style="width:110px"><col style="width:32px"><col style="width:50px"><col style="width:50px"><col style="width:50px"><col style="width:50px"><col style="width:40px"><col style="width:38px"><col style="width:38px"><col style="width:72px">'
            : '<col style="width:90px"><col style="width:110px"><col style="width:32px"><col style="width:50px"><col style="width:50px"><col style="width:50px"><col style="width:50px"><col style="width:40px"><col style="width:38px"><col style="width:38px"><col style="width:72px">';
        return '<colgroup>'+cols+'</colgroup>';
    }

    function colHeaderHtml(withNo) {
        var thStyle = 'style="border:1px solid rgba(255,255,255,0.4);padding:2px 6px 9px 6px;background:#4a7795;color:white;font-weight:700;font-size:7.5px;text-transform:uppercase;letter-spacing:0.04em;line-height:1.3;text-align:center;"';
        var noTh = withNo ? '<th rowspan="2" style="border:1px solid rgba(255,255,255,0.4);padding:9px 6px 2px 6px;background:#4a7795;color:white;font-weight:700;font-size:7.5px;text-transform:uppercase;width:22px;text-align:center;">No</th>' : '';
        return '<tr>'+noTh
             + '<th rowspan="2" '+thStyle+'>Unit / Scope</th>'
             + '<th rowspan="2" '+thStyle+'>Indicator</th>'
             + '<th rowspan="2" '+thStyle+'>Denom</th>'
             + '<th colspan="2" '+thStyle+'>Commitment</th>'
             + '<th colspan="2" '+thStyle+'>Real</th>'
             + '<th rowspan="2" '+thStyle+'>Fairness</th>'
             + '<th rowspan="2" '+thStyle+'>Ach</th>'
             + '<th rowspan="2" '+thStyle+'>Score</th>'
             + '<th rowspan="2" '+thStyle+'>Last Update</th>'
             + '</tr>'
             + '<tr>'
             + '<th '+thStyle+'>Amount</th><th '+thStyle+'>Rp (Mio)</th>'
             + '<th '+thStyle+'>Amount</th><th '+thStyle+'>Rp (Mio)</th>'
             + '</tr>';
    }

    function sectionHeaderRowHtml(no, name, withNo) {
        var colspan = withNo ? 11 : 10;
        return '<tr><td colspan="'+colspan+'" style="background:#f8fafc;color:#1e293b;font-weight:700;'
             + 'font-size:8.5px;text-transform:uppercase;padding:5px 8px;letter-spacing:0.04em;'
             + 'border:1px solid #d1d5db;'
             + 'text-align:left;vertical-align:middle;">'+no+'. '+name+'</td></tr>';
    }

    function pageHeaderHtml(subtitle) {
        return '<div class="export-page-header">'
             +   '<img src="'+logoUrl+'" crossorigin="anonymous">'
             +   '<div class="export-page-header-divider"></div>'
             +   '<div>'
             +     '<div class="export-org">Witel Sumut</div>'
             +     '<div class="export-title">Report Data</div>'
             +     '<div class="export-subtitle">Periode: '+periodeLabel+(subtitle ? ' &nbsp;|&nbsp; '+subtitle : '')+'</div>'
             +   '</div>'
             + '</div>';
    }

    function buildSectionPageEl(sec, trElements) {
        var bodyHtml = '';
        trElements.forEach(function(tr) { bodyHtml += cloneTrFull(tr, true); });

        var div = document.createElement('div');
        div.className = 'export-page';
        div.innerHTML =
            pageHeaderHtml(sec.name)
        + '<div class="export-section-badge">'+sec.name+'</div>'
        + '<table class="export-table" style="table-layout:fixed;width:100%;">'
        +   colgroupHtml(false)
        +   '<thead style="background:#4a7795;">'+colHeaderHtml(false)+'</thead>'
        +   '<tbody>'+bodyHtml+'</tbody>'
        + '</table>';
        return div;
    }

    function buildFullReportEl() {
        var allBodyHtml = '';
        SECTIONS.forEach(function(sec) {
            var trElements = getSectionRows(sec.keyword);
            allBodyHtml += sectionHeaderRowHtml(sec.no, sec.name, true);
            trElements.forEach(function(tr) { allBodyHtml += cloneTrFull(tr, false); });
        });

        var wrapper = document.createElement('div');
        wrapper.className = 'export-page';
        wrapper.innerHTML =
            pageHeaderHtml(null)
          + '<table class="export-table" style="table-layout:fixed;width:100%;">'
          +   colgroupHtml(true)
          +   '<thead style="background:#4a7795;">'+colHeaderHtml(true)+'</thead>'
          +   '<tbody>'+allBodyHtml+'</tbody>'
          + '</table>';
        return wrapper;
    }

    async function renderToCanvas(el) {
        var fontLink = document.getElementById('export-font-link');
        if (!fontLink) {
            fontLink = document.createElement('link');
            fontLink.id   = 'export-font-link';
            fontLink.rel  = 'stylesheet';
            fontLink.href = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap';
            document.head.appendChild(fontLink);
        }

        var root = document.getElementById('export-root');
        root.appendChild(el);

        await document.fonts.ready;
        await new Promise(function(r){ setTimeout(r, 200); });

        var canvas = await html2canvas(el, {
            scale: 2.5,
            useCORS: true,
            backgroundColor: '#ffffff',
            logging: false,
            imageTimeout: 15000,
            onclone: function(doc) {
                Array.from(document.styleSheets).forEach(function(sheet) {
                    try {
                        if (sheet.href) {
                            var link = doc.createElement('link');
                            link.rel  = 'stylesheet';
                            link.href = sheet.href;
                            doc.head.appendChild(link);
                        } else if (sheet.ownerNode && sheet.ownerNode.tagName === 'STYLE') {
                            var style = doc.createElement('style');
                            try {
                                style.textContent = Array.from(sheet.cssRules)
                                    .map(function(r){ return r.cssText; }).join('\n');
                            } catch(e) {
                                style.textContent = sheet.ownerNode.textContent;
                            }
                            doc.head.appendChild(style);
                        }
                    } catch(e) {}
                });

                var s = doc.createElement('style');
                s.textContent = [
                    "@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap');",
                    "* { font-family: 'Inter', ui-sans-serif, system-ui, Arial, sans-serif !important; -webkit-font-smoothing: antialiased; }",
                    ".export-page { background: #ffffff !important; }",
                    ".bg-green-500  { background-color: #22c55e !important; color: #ffffff !important; }",
                    ".bg-yellow-300 { background-color: #fde047 !important; color: #1e293b !important; }",
                    ".bg-red-500    { background-color: #ef4444 !important; color: #ffffff !important; }",
                    ".bg-black      { background-color: #000000 !important; color: #ffffff !important; }",
                    ".bg-gray-50    { background-color: #f9fafb !important; }",
                    ".text-white    { color: #ffffff !important; }",
                    ".text-slate-400{ color: #94a3b8 !important; }",
                    ".text-slate-900{ color: #0f172a !important; }",
                ].join('\n');
                doc.head.appendChild(s);
            }
        });

        root.removeChild(el);
        return canvas;
    }

    function downloadCanvas(canvas, filename) {
        var a = document.createElement('a');
        a.href = canvas.toDataURL('image/jpeg', 0.95);
        a.download = filename;
        a.click();
    }

    function slugify(str) { return str.toLowerCase().replace(/[\s\/]+/g, '-').replace(/[^a-z0-9\-]/g,''); }

    async function doExportSection(no) {
        var sec = SECTIONS.find(function(s){ return s.no === no; });
        if (!sec) return;
        showOverlay();
        setStatus('Memproses '+sec.no+'. '+sec.name+'...');
        setProgress(20);
        var trElements = getSectionRows(sec.keyword);
        var pageEl     = buildSectionPageEl(sec, trElements);
        setProgress(55);
        setStatus('Merender...');
        var canvas = await renderToCanvas(pageEl);
        setProgress(90);
        downloadCanvas(canvas, 'report-'+sec.no+'-'+slugify(sec.name)+'-'+slugify(periodeLabel)+'.jpg');
        setProgress(100);
        await new Promise(function(r){ setTimeout(r, 400); });
        hideOverlay();
    }

    async function doExportFull() {
    showOverlay();
    for (var i = 0; i < SECTIONS.length; i++) {
        var sec = SECTIONS[i];
        var pct = Math.round(10 + (i / SECTIONS.length) * 80);
        setProgress(pct);
        setStatus('Memproses '+sec.no+'. '+sec.name+'... ('+( i+1 )+'/'+SECTIONS.length+')');

        var trElements = getSectionRows(sec.keyword);
        var bodyHtml = '';
        trElements.forEach(function(tr) { bodyHtml += cloneTrFull(tr, true); });

        var div = document.createElement('div');
        div.className = 'export-page';
        div.innerHTML =
            pageHeaderHtml(sec.no+'. '+sec.name)
          + '<div class="export-section-badge">'+sec.no+'. '+sec.name+'</div>'
          + '<table class="export-table" style="table-layout:fixed;width:100%;">'
          +   colgroupHtml(false)
          +   '<thead style="background:#4a7795;">'+colHeaderHtml(false)+'</thead>'
          +   '<tbody>'+bodyHtml+'</tbody>'
          + '</table>';

        var canvas = await renderToCanvas(div);
        downloadCanvas(canvas, 'report-'+sec.no+'-'+slugify(sec.name)+'-'+slugify(periodeLabel)+'.jpg');

        await new Promise(function(r){ setTimeout(r, 150); });
    }
    setProgress(100);
    setStatus('Selesai!');
    await new Promise(function(r){ setTimeout(r, 400); });
    hideOverlay();
}

})();
</script>

<script>
(function () {
    var selBulan   = document.getElementById('select-bulan');
    var selTahun   = document.getElementById('select-tahun');
    var selTanggal = document.getElementById('select-tanggal');

    if (!selBulan || !selTahun || !selTanggal) return;

    function getDaysInMonth(month, year) {
        if (!month) return 31;
        if (!year)  return 31;
        return new Date(year, month, 0).getDate();
    }

    function updateTanggalOptions() {
        var m   = parseInt(selBulan.value)   || 0;
        var y   = parseInt(selTahun.value)   || 0;
        var max = getDaysInMonth(m, y);
        var cur = parseInt(selTanggal.value) || 0;

        Array.from(selTanggal.options).forEach(function (opt) {
            var v = parseInt(opt.value) || 0;
            if (v === 0) return;
            opt.hidden    = (v > max);
            opt.disabled  = (v > max);
        });

        if (cur > max) {
            selTanggal.value = '';
        }
    }

    selBulan.addEventListener('change', updateTanggalOptions);
    selTahun.addEventListener('change', updateTanggalOptions);

    updateTanggalOptions();
})();
</script>

@endsection
