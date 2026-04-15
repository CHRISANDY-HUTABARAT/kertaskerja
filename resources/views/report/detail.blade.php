@extends('layouts.app')

@section('title', 'Detail — ' . $typeLabel . ' ' . $segmentLabel)

@section('content')
<div class="min-h-screen" style="background:#f1f5f9;">
    <div class="max-w-7xl mx-auto px-8 py-10">

        {{-- ══ HEADER ══ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-10 py-7 mb-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1.5"
                style="background: linear-gradient(90deg, #dc2626, #ef4444, #dc2626);"></div>
            <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full opacity-[0.04]" style="background: #dc2626;"></div>
            <div class="relative flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <img src="{{ asset('img/Telkom.png') }}" alt="Telkom" class="h-12 w-auto">
                    <div class="w-px h-12 bg-slate-200"></div>
                    <div>
                        <p class="text-[10px] font-black tracking-[0.3em] text-red-600 uppercase mb-1">Witel Sumut</p>
                        <h1 class="text-2xl font-black tracking-tight text-slate-900 leading-none uppercase">
                            Detail <span class="text-red-600">{{ $type === 'ngtma' ? 'NGTMA' : 'Scalling' }}</span>
                        </h1>
                        <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-tight">
                            {{ $segmentLabel }} — {{ $typeLabel }} — {{ $periodeLabel }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ url()->previous() }}"
                        class="flex items-center space-x-2.5 bg-white border-2 border-slate-900 hover:bg-red-600 hover:border-red-600 text-slate-900 hover:text-white px-6 py-3 rounded-xl font-black text-xs transition-all duration-300 shadow-sm group uppercase tracking-wider">
                        <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>Back</span>
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

        {{-- ══ META BADGES ══ --}}
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center space-x-3">
                <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                <h2 class="text-xl font-black text-slate-900 tracking-tight">
                    {{ $type === 'koreksi' ? 'Data Koreksi' : 'Data Funnel' }}
                </h2>
            </div>
            <div class="flex items-center space-x-3 flex-wrap gap-2">
                                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari Project, ID LOP, AM, CC..." class="text-xs font-semibold text-slate-700 bg-white border-2 border-slate-200 hover:border-red-400 focus:border-red-400 focus:outline-none rounded-lg pl-8 pr-3 py-1.5 shadow-sm transition-colors w-64">
                    <div class="pointer-events-none absolute inset-y-0 left-2 flex items-center">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                    </div>
                </div>
                @php
                    $hasData = $type === 'koreksi' ? ($import && $koreksiRows->count()) : ($import && $dataRows->count());
                @endphp

                @if($hasData)
                <button id="btn-export-detail"
                    class="flex items-center space-x-2 bg-slate-900 hover:bg-red-600 text-white font-black text-[11px] px-3 py-1 rounded-md transition-all uppercase tracking-widest">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Export JPG</span>
                </button>
                @endif
                <span class="text-[11px] font-black tracking-widest uppercase px-3 py-1 rounded-md border
                    @if($segment === 'gov')     bg-blue-50   border-blue-200   text-blue-700
                    @elseif($segment === 'private') bg-purple-50 border-purple-200 text-purple-700
                    @elseif($segment === 'soe')    bg-orange-50 border-orange-200 text-orange-700
                    @else                          bg-green-50  border-green-200  text-green-700
                    @endif">{{ $segmentLabel }}</span>
                <span class="text-[11px] font-black tracking-widest uppercase px-3 py-1 rounded-md border bg-slate-50 border-slate-200 text-slate-600">
                    {{ $typeLabel }}
                </span>
                <span class="text-[11px] font-black tracking-widest uppercase px-3 py-1 rounded-md border bg-red-50 border-red-200 text-red-700">
                    {{ $periodeLabel }}
                </span>
                @if($type === 'koreksi')
                <span class="text-[11px] font-black tracking-widest text-slate-500 bg-white border border-slate-200 rounded-md px-3 py-1 shadow-sm">
                    {{ $koreksiRows->count() }} LOP
                </span>
                @else
                <span class="text-[11px] font-black tracking-widest text-slate-500 bg-white border border-slate-200 rounded-md px-3 py-1 shadow-sm">
                    {{ $dataRows->count() }} LOP
                </span>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════
             TAMPILAN KOREKSI
        ══════════════════════════════════════════════════════════════ --}}
        @if($type === 'koreksi')

        @if($import && $koreksiRows->count())
        @php
            $totalKomitmen  = $koreksiRows->sum('nilai_komitmen');
            $totalRealisasi = $koreksiRows->sum('realisasi');
            $pctTotal       = $totalKomitmen > 0 ? ($totalRealisasi / $totalKomitmen) * 100 : 0;
            $sudahRealisasi = $koreksiRows->filter(fn($r) => $r->realisasi > 0)->count();
        @endphp

        {{-- Summary cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-1">Total LOP</p>
                <p class="text-2xl font-black text-slate-900">{{ $koreksiRows->count() }}</p>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Data periode ini</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-1">Nilai Komitmen</p>
                <p class="text-lg font-black text-slate-900">{{ number_format($totalKomitmen, 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Total komitmen (Rp)</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-1">Total Realisasi</p>
                <p class="text-lg font-black text-emerald-600">{{ number_format($totalRealisasi, 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">{{ $sudahRealisasi }} dari {{ $koreksiRows->count() }} LOP</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-1">Pencapaian</p>
                <p class="text-2xl font-black {{ $pctTotal >= 100 ? 'text-emerald-600' : ($pctTotal >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                    {{ number_format($pctTotal, 1, ',', '.') }}%
                </p>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Realisasi / Komitmen</p>
            </div>
        </div>

        {{-- Tabel Koreksi --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-slate-800">
                            <th class="px-3 py-3 text-center text-[10px] font-black text-slate-300 uppercase tracking-widest border-r border-slate-700 w-10">NO</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-slate-300 uppercase tracking-widest border-r border-slate-700">NAMA PELANGGAN</th>
                            <th class="px-4 py-3 text-right text-[10px] font-black text-slate-300 uppercase tracking-widest border-r border-slate-700 w-44">NILAI KOMITMEN (Rp)</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-slate-300 uppercase tracking-widest border-r border-slate-700 w-44">PROGRESS</th>
                            <th class="px-4 py-3 text-right text-[10px] font-black text-emerald-400 uppercase tracking-widest w-44">REALISASI (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($koreksiRows as $row)
                        @php
                            $hasReal  = $row->realisasi !== null && $row->realisasi > 0;
                            $pctRow   = ($row->nilai_komitmen > 0 && $row->realisasi > 0)
                                ? ($row->realisasi / $row->nilai_komitmen) * 100 : 0;
                            $barColor  = $pctRow >= 100 ? '#16a34a' : ($pctRow >= 50 ? '#eab308' : '#ef4444');
                            $textColor = $pctRow >= 100 ? '#16a34a' : ($pctRow >= 50 ? '#ca8a04' : '#ef4444');
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-3 py-3 text-center font-bold text-slate-500 border-r border-slate-100">
                                {{ $row->no ?? $loop->iteration }}
                            </td>
                            <td class="px-4 py-3 text-slate-800 border-r border-slate-100 font-semibold">
                                {{ $row->nama_pelanggan }}
                            </td>
                            <td class="px-4 py-3 text-right font-black text-slate-800 border-r border-slate-100 tabular-nums">
                                @if($row->nilai_komitmen !== null)
                                    {{ number_format($row->nilai_komitmen, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center border-r border-slate-100
                                {{ ($row->progress ?? '') === 'done' ? 'bg-emerald-50' : 'bg-slate-50' }}">
                                @php $isDone = ($row->progress ?? '') === 'done'; @endphp
                                <span class="inline-flex flex-col items-center gap-0.5 select-none">
                                    <div class="w-4 h-4 rounded border-2 flex items-center justify-center
                                        {{ $isDone ? 'bg-emerald-500 border-emerald-500' : 'bg-white border-slate-300' }}">
                                        @if($isDone)
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        @endif
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-wide leading-none
                                        {{ $isDone ? 'text-emerald-600' : 'text-slate-400' }}">
                                        {{ $isDone ? 'Done' : 'On Prog' }}
                                    </span>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right bg-emerald-50 border-l border-emerald-100">
                                @if($hasReal)
                                    <span class="font-black text-emerald-700 tabular-nums">{{ number_format($row->realisasi, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-300 font-bold">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-400 font-bold">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="sticky bottom-0 z-10">
                        <tr class="border-t-2 border-slate-300 bg-slate-800">
                            <td colspan="2" class="px-4 py-3 text-right text-xs font-black text-slate-300 uppercase tracking-widest">TOTAL</td>
                            <td class="px-4 py-3 text-right font-black text-white tabular-nums border-l border-slate-700">
                                {{ number_format($totalKomitmen, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center border-l border-slate-700">
                                <span class="text-xs font-black tabular-nums"
                                    style="color: {{ $pctTotal >= 100 ? '#4ade80' : ($pctTotal >= 50 ? '#fde047' : '#fca5a5') }}">
                                    {{ number_format($pctTotal, 1, ',', '.') }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right border-l border-slate-700">
                                <span class="font-black text-emerald-400 tabular-nums">
                                    {{ number_format($totalRealisasi, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @else
        {{-- Empty state koreksi --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-5"
                style="background: linear-gradient(135deg, #fff1f2, #ffe4e6); border: 2px solid #fecdd3;">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-black text-slate-700 mb-2 uppercase tracking-tight">Data Belum Tersedia</h3>
            <p class="text-sm font-medium text-slate-400">Tidak ada data koreksi untuk periode ini</p>
        </div>
        @endif

        {{-- ══════════════════════════════════════════════════════════════
             TAMPILAN NON-KOREKSI (on-hand / qualified / initiate)
        ══════════════════════════════════════════════════════════════ --}}
        @else

        @if($import && $dataRows->count())

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-x-auto">
            <div style="max-height: 90vh; overflow-y: auto;">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="sticky top-0 z-10">
                        <tr>
                            <th rowspan="2" class="px-3 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-100 bg-slate-50 sticky-col-header">NO</th>
                            <th rowspan="2" class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-100 bg-slate-50 sticky-col-header">PROJECT</th>
                            <th rowspan="2" class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-100 bg-emerald-50 sticky-col-header">ID LOP</th>
                            <th rowspan="2" class="px-3 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-100 bg-slate-50 sticky-col-header">CC</th>
                            <th rowspan="2" class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-100 bg-slate-50">AM</th>
                            <th rowspan="2" class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-100 bg-emerald-50">Mitra</th>
                            <th rowspan="2" class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-100 bg-slate-50">Est Bulan BC</th>
                            <th rowspan="2" class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-100 bg-slate-50">Est Nilai BC</th>
                            <th class="px-3 py-2 text-center text-[10px] font-black text-white border-r border-slate-100 bg-blue-600">F0</th>
                            <th class="px-3 py-2 text-center text-[10px] font-black text-white border-r border-slate-100 bg-purple-600">F1</th>
                            <th colspan="7" class="px-3 py-2 text-center text-[10px] font-black text-white border-r border-slate-100 bg-pink-600">F2</th>
                            <th colspan="3" class="px-3 py-2 text-center text-[10px] font-black text-white border-r border-slate-100 bg-orange-500">F3</th>
                            <th class="px-3 py-2 text-center text-[10px] font-black text-white border-r border-slate-100 bg-teal-600">F4</th>
                            <th colspan="3" class="px-3 py-2 text-center text-[10px] font-black text-white border-r border-slate-100 bg-green-600">F5</th>
                            <th colspan="3" class="px-3 py-2 text-center text-[10px] font-black text-white border-r border-slate-100 bg-emerald-700">DELIVERY</th>
                            <th rowspan="2" class="px-3 py-2 text-center text-[10px] font-black text-white border-r border-slate-100 bg-indigo-600">BILLING<br>COMPLETE</th>
                            <th rowspan="2" class="px-3 py-2 text-center text-[10px] font-black text-white bg-violet-600 min-w-[100px]">NILAI BILL COMP</th>
                            <th rowspan="2" class="px-3 py-2 text-center text-[10px] font-black text-white bg-red-600 min-w-[100px]">CANCEL</th>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-blue-700 bg-blue-50 border-r border-slate-100">Inisiasi</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-purple-700 bg-purple-50 border-r border-slate-100">Tech & Budget</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-pink-700 bg-pink-50 border-r border-slate-100">P0/P1</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-pink-700 bg-pink-50 border-r border-slate-100">P2</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-pink-700 bg-pink-50 border-r border-slate-100">P3</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-pink-700 bg-pink-50 border-r border-slate-100">P4</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-pink-700 bg-pink-50 border-r border-slate-100">Offering</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-pink-700 bg-pink-50 border-r border-slate-100">P5</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-pink-700 bg-pink-50 border-r border-slate-100">Proposal</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-orange-700 bg-orange-50 border-r border-slate-100">P6</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-orange-700 bg-orange-50 border-r border-slate-100">P7</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-orange-700 bg-orange-50 border-r border-slate-100">Submit</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-teal-700 bg-teal-50 border-r border-slate-100">Negosiasi</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-green-700 bg-green-50 border-r border-slate-100">SK Mitra</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-green-700 bg-green-50 border-r border-slate-100">TTD Kontrak</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-green-700 bg-green-50 border-r border-slate-100">P8</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-emerald-700 bg-emerald-50 border-r border-slate-100">Kontrak</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-emerald-700 bg-emerald-50 border-r border-slate-100">BAUT/BAST</th>
                            <th class="px-2 py-1.5 text-center text-[10px] font-black text-emerald-700 bg-emerald-50 border-r border-slate-100">BASO</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @foreach($dataRows as $row)
                        @php
                            $funnel      = $funnelMap[$row->id] ?? null;
                            $denganMitra = strtolower(trim($row->mitra ?? '')) === 'dengan mitra';
                            $val         = fn($field) => $funnel && $funnel->{$field};
                            $check       = fn($checked) => $checked
                                ? '<span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-500"><svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></span>'
                                : '<span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-100"><span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span></span>';
                            $dash = '<span class="text-slate-300 font-bold text-sm">—</span>';
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-3 py-2.5 whitespace-nowrap font-bold text-slate-700 border-r border-slate-100 text-center sticky-col">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2.5 text-slate-600 border-r border-slate-100 font-medium sticky-col">{{ $row->project }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-700 border-r border-slate-100 bg-emerald-50 font-bold sticky-col">{{ $row->id_lop }}</td>
                            <td class="px-3 py-2.5 whitespace-nowrap text-slate-600 border-r border-slate-100 sticky-col">{{ $row->cc }}</td>
                            <td class="px-4 py-2.5 text-slate-600 border-r border-slate-100">{{ $row->am }}</td>
                            <td class="px-4 py-2.5 text-slate-700 border-r border-slate-100 bg-emerald-50 font-semibold">{{ $row->mitra }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 border-r border-slate-100 text-center">{{ $row->plan_bulan_billcomp_2025 }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap font-black text-right text-slate-800 border-r border-slate-100">{{    number_format($row->est_nilai_bc, 0, ',', '.') }}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-blue-50">{!! $check($val('f0_inisiasi_solusi')) !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-purple-50">{!! $check($val('f1_tech_budget')) !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">{!! $denganMitra ? $check($val('f2_p0_p1')) : $dash !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">{!! $denganMitra ? $check($val('f2_p2')) : $dash !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">{!! $denganMitra ? $check($val('f2_p3')) : $dash !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">{!! $denganMitra ? $check($val('f2_p4')) : $dash !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">{!! $denganMitra ? $check($val('f2_offering')) : $dash !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">{!! $denganMitra ? $check($val('f2_p5')) : $dash !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">{!! $check($val('f2_proposal')) !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-orange-50">{!! $denganMitra ? $check($val('f3_p6')) : $dash !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-orange-50">{!! $denganMitra ? $check($val('f3_p7')) : $dash !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-orange-50">{!! $check($val('f3_submit')) !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-teal-50">{!! $check($val('f4_negosiasi')) !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-green-50">{!! $denganMitra ? $check($val('f5_sk_mitra')) : $dash !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-green-50">{!! $check($val('f5_ttd_kontrak')) !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-green-50">{!! $denganMitra ? $check($val('f5_p8')) : $dash !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-emerald-50">{!! $check($val('delivery_kontrak')) !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-emerald-50">{!! $denganMitra ? $check($val('delivery_baut_bast')) : $dash !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-emerald-50">{!! $denganMitra ? $check($val('delivery_baso')) : $dash !!}</td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-indigo-50">{!! $check($val('delivery_billing_complete')) !!}</td>
                            <td class="px-2 py-2.5 text-right bg-violet-50">
                                @if($funnel && $funnel->delivery_billing_complete)
                                    @php
                                        $nilaiToShow = $funnel->delivery_nilai_billcomp;
                                        if (!$nilaiToShow) {
                                            $cleanValue = str_replace(['.', ','], '', $row->est_nilai_bc ?? '0');
                                            $nilaiToShow = (int) $cleanValue;
                                        }
                                    @endphp
                                    <span class="font-black text-slate-800">{{ number_format($nilaiToShow, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-indigo-50">{!! $check($val('cancel')) !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="sticky bottom-0 z-10">
                        <tr class="border-t-2 border-red-200 bg-slate-50">
                            <td colspan="7" class="px-4 py-3 text-right text-xs font-black text-slate-700 uppercase tracking-widest border-r border-slate-100">TOTAL:</td>
                            <td class="px-4 py-3 text-right font-black text-emerald-700 border-r border-slate-100 bg-emerald-50">
                                {{ number_format($dataRows->sum(function ($r) {
                                    $v = $r->est_nilai_bc ?? '0';
                                    if (str_contains($v, ',')) { $v = str_replace('.', '', $v); $v = str_replace(',', '.', $v); }
                                    return floatval($v);
                                }), 0, ',', '.') }}
                            </td>
                            <td colspan="20" class="border-r border-slate-100"></td>
                            <td class="px-4 py-3 text-right font-black text-violet-700 bg-violet-50">
                                {{ number_format($funnelMap->where('delivery_billing_complete', true)->sum('delivery_nilai_billcomp'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Stats cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            @php
                $totalLop      = $dataRows->count();
                $totalBillComp = $funnelMap->filter(fn($f) => $f && $f->delivery_billing_complete)->count();
                $totalDelivery = $funnelMap->filter(fn($f) => $f && $f->delivery_kontrak)->count();
                $totalF5       = $funnelMap->filter(fn($f) => $f && $f->f5_ttd_kontrak)->count();
                $pctBillComp   = $totalLop > 0 ? round(($totalBillComp / $totalLop) * 100) : 0;
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total LOP</p>
                <p class="text-3xl font-black text-slate-900">{{ $totalLop }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">TTD Kontrak (F5)</p>
                <p class="text-3xl font-black text-green-600">{{ $totalF5 }}</p>
                <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $totalLop > 0 ? round(($totalF5/$totalLop)*100) : 0 }}% dari total LOP</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Delivery Kontrak</p>
                <p class="text-3xl font-black text-emerald-600">{{ $totalDelivery }}</p>
                <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $totalLop > 0 ? round(($totalDelivery/$totalLop)*100) : 0 }}% dari total LOP</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Billing Complete</p>
                <p class="text-3xl font-black text-indigo-600">{{ $totalBillComp }}</p>
                <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $pctBillComp }}% dari total LOP</p>
            </div>
        </div>

        {{-- Catatan funnel --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                <h4 class="font-black text-slate-900 text-sm uppercase tracking-wide">Catatan Tahapan Funnel</h4>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 text-xs">
                <div class="bg-blue-50 border border-blue-200 p-3 rounded-xl"><div class="font-black text-blue-700 mb-1.5">F0</div><div class="text-slate-600"><div>• Inisiasi</div></div></div>
                <div class="bg-purple-50 border border-purple-200 p-3 rounded-xl"><div class="font-black text-purple-700 mb-1.5">F1</div><div class="text-slate-600"><div>• Technical & Budget Discussion</div></div></div>
                <div class="bg-pink-50 border border-pink-200 p-3 rounded-xl"><div class="font-black text-pink-700 mb-1.5">F2</div><div class="text-slate-600 space-y-0.5"><div>• P0/P1. Juskeb</div><div>• P2. Evaluasi mitra</div><div>• P3. PPH</div><div>• P4. Rapat Penjelasan</div><div>• Offering & P5. SPH</div><div>• Proposal Solusi</div></div></div>
                <div class="bg-orange-50 border border-orange-200 p-3 rounded-xl"><div class="font-black text-orange-700 mb-1.5">F3</div><div class="text-slate-600 space-y-0.5"><div>• P6. Klarifikasi & Negosiasi</div><div>• P7. Penetapan Mitra</div><div>• Submit Proposal</div></div></div>
                <div class="bg-teal-50 border border-teal-200 p-3 rounded-xl"><div class="font-black text-teal-700 mb-1.5">F4</div><div class="text-slate-600"><div>• Negosiasi</div></div></div>
                <div class="bg-green-50 border border-green-200 p-3 rounded-xl"><div class="font-black text-green-700 mb-1.5">F5</div><div class="text-slate-600 space-y-0.5"><div>• SK Mitra</div><div>• TTD Kontrak</div><div>• P8. Penetapan Mitra</div></div></div>
            </div>
        </div>

        @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-5"
                style="background: linear-gradient(135deg, #fff1f2, #ffe4e6); border: 2px solid #fecdd3;">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-black text-slate-700 mb-2 uppercase tracking-tight">Data Belum Tersedia</h3>
            <p class="text-sm font-medium text-slate-400">Tidak ada data untuk periode ini</p>
        </div>
        @endif

        @endif

    </div>
</div>
<div id="export-overlay"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);z-index:9999;align-items:center;justify-content:center;flex-direction:column;gap:16px;">
    <div style="width:36px;height:36px;border:4px solid rgba(255,255,255,0.3);border-top-color:white;border-radius:50%;animation:espin 0.8s linear infinite;"></div>
    <p style="color:white;font-weight:900;font-size:13px;letter-spacing:0.12em;text-transform:uppercase;" id="export-detail-status">Mempersiapkan...</p>
    <div style="width:280px;height:6px;background:rgba(255,255,255,0.2);border-radius:99px;overflow:hidden;">
        <div id="export-detail-bar" style="height:100%;background:#ef4444;border-radius:99px;transition:width 0.3s;width:0%;"></div>
    </div>
</div>

<div id="export-detail-root"
    style="position:fixed;left:-99999px;top:0;z-index:-1;background:white;font-family:'Inter','Noto Sans',Arial,sans-serif!important;">
</div>

<style>
@keyframes espin { to { transform:rotate(360deg); } }
.sticky-col-header { position:sticky; z-index:3; }
.sticky-col { position:sticky; z-index:1; background:white; }

.detail-export-page {
    width:1300px;
    background:white;
    padding:24px 32px 56px;
    font-family:'Inter','Noto Sans',Arial,sans-serif;
    box-sizing:border-box;
    -webkit-font-smoothing:antialiased;
}
.detail-export-header {
    display:flex;align-items:center;gap:16px;
    margin-bottom:4px;padding-bottom:10px;
    border-bottom:3px solid #dc2626;
}
.detail-export-header img { height:36px; }
.detail-export-header-divider { width:1px;height:36px;background:#e2e8f0; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
(function(){
    var segmentLabel = @json($segmentLabel);
    var typeLabel    = @json($typeLabel);
    var periodeLabel = @json($periodeLabel);
    var logoUrl      = '{{ asset('img/Telkom.png') }}';
    var isKoreksi    = @json($type === 'koreksi');

    function setProgress(p){ document.getElementById('export-detail-bar').style.width = p+'%'; }
    function setStatus(t)  { document.getElementById('export-detail-status').textContent = t; }
    function showOverlay() { var o=document.getElementById('export-overlay'); o.style.display='flex'; setProgress(0); }
    function hideOverlay() { document.getElementById('export-overlay').style.display='none'; }

    function cloneCards() {
        var cards = document.querySelector('.grid.grid-cols-2.md\\:grid-cols-4');
        if (!cards) return '';
        return cards.outerHTML;
    }

function cloneTable() {
    var table = document.querySelector('table');
    if (!table) return '';
    var wrapper = document.createElement('div');
    wrapper.style.cssText = 'overflow:visible;margin-top:14px;';
    var tClone = table.cloneNode(true);

    tClone.style.cssText = 'width:100%;border-collapse:collapse;font-size:8px;font-family:Inter,Arial,sans-serif;table-layout:auto;';

    var oldCg = tClone.querySelector('colgroup');
    if (oldCg) oldCg.remove();

    var firstRow = tClone.querySelector('tbody tr');
    var colCount = firstRow ? firstRow.querySelectorAll('td').length : 30;
    var isKoreksi = colCount <= 5;

    if (isKoreksi) {
        var cgK = document.createElement('colgroup');
        var koreksiWidths = ['40px', '280px', '160px', '120px', '160px'];
        koreksiWidths.forEach(function(w) {
            var col = document.createElement('col');
            col.style.width = w;
            cgK.appendChild(col);
        });
        tClone.insertBefore(cgK, tClone.firstChild);

        tClone.querySelectorAll('th').forEach(function(th) {
            var bg    = window.getComputedStyle(th).backgroundColor;
            var color = window.getComputedStyle(th).color;
            th.style.cssText = 'border:1px solid rgba(255,255,255,0.15);padding:8px 10px;font-size:8px;'
                + 'background:'+bg+';color:'+color+';font-weight:800;'
                + 'text-align:center;vertical-align:middle;'
                + 'text-transform:uppercase;letter-spacing:0.08em;';
        });

        tClone.querySelectorAll('td').forEach(function(td) {
            var bg  = window.getComputedStyle(td).backgroundColor;
            var col = window.getComputedStyle(td).color;
            var fw  = window.getComputedStyle(td).fontWeight;
            var ta  = window.getComputedStyle(td).textAlign;
            td.style.cssText = 'border:1px solid #e2e8f0;padding:6px 10px 10px 10px;font-size:8px;'
                + 'background:'+bg+';color:'+col+';font-weight:'+fw+';'
                + 'text-align:'+ta+';vertical-align:middle;';
        });

        wrapper.appendChild(tClone);
        return wrapper.outerHTML;
    }

    var widths = [
        '35px',
        '130px',
        '65px',
        '65px',
        '105px',
        '70px',
        '40px',
        '110px',
        '60px',
        '65px',
        '55px',
        '26px',
        '26px',
        '26px',
        '75px',
        '26px',
        '75px',
        '26px',
        '26px',
        '60px',
        '80px',
        '55px',
        '70px',
        '26px',
        '70px',
        '90px',
        '55px',
        '75px',
        '65px',
        '12px',
    ];

    var cg = document.createElement('colgroup');
    widths.forEach(function(w) {
        var col = document.createElement('col');
        col.style.width = w;
        cg.appendChild(col);
    });
    tClone.insertBefore(cg, tClone.firstChild);

    var checkCells   = new Set();
    var greyCells    = new Set();

    tClone.querySelectorAll('td').forEach(function(td) {
        if (td.querySelector('span.bg-green-500, span[class*="bg-green-500"]')) checkCells.add(td);
        else if (td.querySelector('span.bg-slate-100, span[class*="bg-slate-100"]')) greyCells.add(td);
    });

    tClone.querySelectorAll('td').forEach(function(td) {
        if (checkCells.has(td)) {
            td.innerHTML = '<div style="width:100%;text-align:center;line-height:1;">'
                + '<svg viewBox="0 0 16 16" width="13" height="13" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle;">'
                + '<circle cx="8" cy="8" r="7.5" fill="#22c55e"/>'
                + '<polyline points="4.5,8.5 7,11 11.5,5.5" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
                + '</svg></div>';
            return;
        }
        if (greyCells.has(td)) {
            td.innerHTML = '<div style="width:100%;text-align:center;line-height:1;">'
                + '<svg viewBox="0 0 16 16" width="10" height="10" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle;">'
                + '<circle cx="8" cy="8" r="7.5" fill="#e2e8f0"/>'
                + '</svg></div>';
            return;
        }
        var dashSpan = td.querySelector('span.text-slate-300');
        if (dashSpan && dashSpan.textContent.trim() === '—') {
            td.innerHTML = '<span style="color:#cbd5e1;font-size:8px;">—</span>';
            return;
        }
    });

    tClone.querySelectorAll('th').forEach(function(th) {
        var bg    = window.getComputedStyle(th).backgroundColor;
        var color = window.getComputedStyle(th).color;
        var rowspan  = th.getAttribute('rowspan');
        var colspan  = th.getAttribute('colspan');
        var tr       = th.parentElement;
        var theadRows = Array.from(th.closest('thead').querySelectorAll('tr'));
        var isFirstRow = theadRows.indexOf(tr) === 0;
        var isMergedDown = rowspan && parseInt(rowspan) > 1;
        var isGroupHeader = colspan && parseInt(colspan) > 1;
        var isSingleTop = isFirstRow && !isMergedDown && !isGroupHeader;

        var padBottom = isMergedDown ? '4px' : (isGroupHeader || isSingleTop) ? '6px' : '20px';
        var valign    = (isMergedDown || isGroupHeader || isSingleTop) ? 'middle' : 'bottom';
        th.style.cssText = 'border:1px solid rgba(0,0,0,0.1);padding:2px 2px '+padBottom+' 2px;font-size:6px;'
            + 'background:'+bg+';color:'+color+';font-weight:800;'
            + 'text-align:center;vertical-align:'+valign+';'
            + 'word-break:break-word;line-height:1.2;'
            + 'text-transform:uppercase;letter-spacing:0.02em;';
    });

    // Style td
    tClone.querySelectorAll('td').forEach(function(td) {
        var bg  = window.getComputedStyle(td).backgroundColor;
        var col = window.getComputedStyle(td).color;
        var fw  = window.getComputedStyle(td).fontWeight;
        var ta  = window.getComputedStyle(td).textAlign;
        var idx = td.cellIndex;

        var isCheck = checkCells.has(td);
        var isGrey  = greyCells.has(td);
        var isCheckCol = (idx >= 8 && idx <= 27) || idx === 29;

        var extraWrap = '';
        if (idx === 4) {
            extraWrap = 'white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
        } else if (idx === 28 || idx === 7) {
            extraWrap = 'white-space:nowrap;';
        } else if (idx <= 5) {
            extraWrap = 'word-break:break-word;overflow-wrap:break-word;';
        } else {
            extraWrap = 'overflow:hidden;';
        }

        var colorOverride = '';
        var fontOverride  = '';
        var sizeOverride  = '';
        if (isCheck || isGrey) {
            colorOverride = '';
            fontOverride  = '';
            sizeOverride  = 'font-size:0px;';
        } else if (idx === 28) {
            colorOverride = 'color:#1e293b;';
            fontOverride  = 'font-weight:900;';
            sizeOverride  = 'font-size:7px;';
        } else if (idx >= 8 && idx !== 7) {
            colorOverride = 'color:#94a3b8;';
            fontOverride  = 'font-weight:600;';
            sizeOverride  = 'font-size:9px;';
        }

        var align = (isCheckCol)
            ? 'text-align:center;vertical-align:middle;'
            : 'text-align:'+ta+';vertical-align:middle;';

        var padding = isCheckCol
            ? 'padding:0px 1px;'
            : 'padding:2px 2px 8px 2px;';

        td.style.cssText = 'border:1px solid #e2e8f0;font-size:7px;'
            + padding
            + 'background:'+bg+';'+colorOverride+fontOverride+sizeOverride
            + align + 'line-height:1.4;' + extraWrap;
    });

    wrapper.appendChild(tClone);
    return wrapper.outerHTML;
}
    function buildExportEl() {
        var headerHtml = '<div class="detail-export-header">'
            + '<img src="'+logoUrl+'" crossorigin="anonymous">'
            + '<div class="detail-export-header-divider"></div>'
            + '<div>'
            +   '<div style="font-size:8.5px;font-weight:800;letter-spacing:0.3em;color:#dc2626;text-transform:uppercase;margin-bottom:2px;">Witel Sumut</div>'
            +   '<div style="font-size:15px;font-weight:900;text-transform:uppercase;color:#0f172a;line-height:1;">Detail ' + (isKoreksi ? 'Koreksi' : (typeLabel === 'New GTMA' ? 'NGTMA' : 'Scalling')) + '</div>'
            +   '<div style="font-size:8px;color:#64748b;font-weight:600;margin-top:3px;text-transform:uppercase;letter-spacing:0.05em;">'
            +     segmentLabel + ' &nbsp;|&nbsp; ' + typeLabel + ' &nbsp;|&nbsp; ' + periodeLabel
            +   '</div>'
            + '</div>'
            + '</div>';

        var cardsHtml = cloneCards();
        var tableHtml = cloneTable();

        var div = document.createElement('div');
        div.className = 'detail-export-page';
        div.innerHTML = headerHtml
            + '<div style="margin-top:12px;">' + cardsHtml + '</div>'
            + tableHtml;
        return div;
    }

    document.getElementById('btn-export-detail').addEventListener('click', async function() {
        showOverlay();
        setStatus('Mempersiapkan...');
        setProgress(20);

        var fontLink = document.getElementById('export-font-link');
        if (!fontLink) {
            fontLink = document.createElement('link');
            fontLink.id   = 'export-font-link';
            fontLink.rel  = 'stylesheet';
            fontLink.href = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap';
            document.head.appendChild(fontLink);
        }

        var el   = buildExportEl();
        var root = document.getElementById('export-detail-root');
        root.appendChild(el);

        await document.fonts.ready;
        await new Promise(function(r){ setTimeout(r, 200); });

        setStatus('Merender...');
        setProgress(60);

        var canvas = await html2canvas(el, {
            scale: 1.8,
            useCORS: true,
            backgroundColor: '#ffffff',
            logging: false,
            imageTimeout: 15000,
        });

        root.removeChild(el);
        setProgress(95);

        var filename = 'detail-'
            + segmentLabel.toLowerCase() + '-'
            + typeLabel.toLowerCase().replace(/\s+/g,'-') + '-'
            + periodeLabel.toLowerCase().replace(/\s+/g,'-') + '.jpg';

        var a = document.createElement('a');
        a.href = canvas.toDataURL('image/jpeg', 0.95);
        a.download = filename;
        a.click();

        setProgress(100);
        await new Promise(function(r){ setTimeout(r, 400); });
        hideOverlay();
    });
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const stickyCount = 4; // NO, PROJECT, ID LOP, CC
    const table = document.querySelector('table');
    if (!table) return;

    // Ambil lebar dari td di baris pertama
    const firstRow = table.querySelector('tbody tr:first-child');
    if (!firstRow) return;
    const cells = firstRow.querySelectorAll('td');

    let pos = 0;
    for (let i = 0; i < stickyCount; i++) {
        const width = cells[i].offsetWidth;

        // Update semua td di kolom ini
        table.querySelectorAll('tbody tr td:nth-child(' + (i+1) + ')').forEach(td => {
            td.style.left = pos + 'px';
        });

        // Update th di kolom ini
        table.querySelectorAll('thead tr th:nth-child(' + (i+1) + ')').forEach(th => {
            th.style.left = pos + 'px';
        });

        pos += width;
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    // Kolom yang akan disearch: NO(0), PROJECT(1), ID LOP(2), CC(3), AM(4)
    const SEARCH_COLS = [0, 1, 2, 3, 4];

    searchInput.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('tbody tr');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const cells = row.querySelectorAll('td');
            const match = SEARCH_COLS.some(function (idx) {
                const cell = cells[idx];
                return cell && cell.textContent.toLowerCase().includes(keyword);
            });

            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        // Tampilkan pesan kosong jika tidak ada hasil
        let emptyMsg = document.getElementById('searchEmptyMsg');
        if (!emptyMsg) {
            emptyMsg = document.createElement('tr');
            emptyMsg.id = 'searchEmptyMsg';
            emptyMsg.innerHTML = '<td colspan="30" class="text-center py-10 text-slate-400 font-bold text-xs uppercase tracking-widest">Tidak ada data yang cocok</td>';
            document.querySelector('tbody').appendChild(emptyMsg);
        }
        emptyMsg.style.display = (visibleCount === 0 && keyword !== '') ? '' : 'none';
    });
});
</script>

@endsection
