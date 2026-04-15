@extends('layouts.app')

@section('title', 'Progress — ' . $typeLabel . ' ' . $segmentLabel)

@section('content')
<div class="min-h-screen" style="background:#f1f5f9;">
    <div class="max-w-7xl mx-auto px-8 py-10">

        {{-- ══ HEADER ══ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-10 py-7 mb-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background: linear-gradient(90deg, #dc2626, #ef4444, #dc2626);"></div>
            <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full opacity-[0.04]" style="background: #dc2626;"></div>
            <div class="relative flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <img src="{{ asset('img/Telkom.png') }}" alt="Telkom" class="h-12 w-auto">
                    <div class="w-px h-12 bg-slate-200"></div>
                    <div>
                        <p class="text-[10px] font-black tracking-[0.3em] text-red-600 uppercase mb-1">Witel Sumut — Admin</p>
                        <h1 class="text-2xl font-black tracking-tight text-slate-900 leading-none uppercase">
                            Progress <span class="text-red-600">{{ $typeLabel }}</span>
                        </h1>
                        <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-tight">{{ $segmentLabel }} — Scaling Management System</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.scalling.' . $segment . '.' . ($type === 'on-hand' ? 'on-hand' : $type)) }}"
                        class="flex items-center space-x-2.5 bg-white border-2 border-slate-900 hover:bg-slate-900 text-slate-900 hover:text-white px-6 py-3 rounded-xl font-black text-xs transition-all duration-300 shadow-sm uppercase tracking-wider group">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ══ META INFO ══ --}}
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center space-x-3">
                <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                <h2 class="text-xl font-black text-slate-900 tracking-tight">
                    {{ $type === 'koreksi' ? 'Progress Realisasi Koreksi' : 'Progress Data' }}
                </h2>
            </div>
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari Project, ID LOP, AM, CC..." class="text-xs font-semibold text-slate-700 bg-white border-2 border-slate-200 hover:border-red-400 focus:border-red-400 focus:outline-none rounded-lg pl-8 pr-3 py-1.5 shadow-sm transition-colors w-64">
                    <div class="pointer-events-none absolute inset-y-0 left-2 flex items-center">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                    </div>
                </div>
                @if(count($periodOptions))
                <form method="GET" class="flex items-center">
                    <div class="relative">
                        <select name="periode" onchange="this.form.submit()"
                            class="appearance-none text-xs font-bold text-slate-700 bg-white border-2 border-slate-200 hover:border-red-400 rounded-lg pl-3 pr-8 py-1.5 shadow-sm focus:outline-none focus:border-red-400 cursor-pointer transition-colors">
                            @foreach($periodOptions as $p)
                                <option value="{{ $p }}" {{ $p === $currentPeriode ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $p)->format('F Y') }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </form>
                @endif
                <span class="text-[12px] font-black tracking-widest text-red-700 bg-red-50 border border-red-200 rounded-md px-3 py-1 uppercase">
                    {{ strtoupper(str_replace('-', ' ', $type)) }}
                </span>
                <span class="text-[12px] font-black tracking-widest text-slate-500 bg-white border border-slate-200 rounded-md px-3 py-1 uppercase shadow-sm">
                    {{ strtoupper($segmentLabel) }}
                </span>
                @if(isset($isReadOnly) && $isReadOnly)
                <span class="text-[12px] font-black tracking-widest text-amber-700 bg-amber-50 border border-amber-200 rounded-md px-3 py-1 uppercase">🔒 Locked</span>
                @else
                <span class="text-[12px] font-black tracking-widest text-green-700 bg-green-50 border border-green-200 rounded-md px-3 py-1 uppercase">✏️ Editable</span>
                @endif
            </div>
        </div>

        @if($import)

        {{-- ══════════════════════════════════════════════════════════════
             KOREKSI
        ══════════════════════════════════════════════════════════════ --}}
        @if($type === 'koreksi')

        @php
            $isReadOnly     = isset($isReadOnly) ? $isReadOnly : false;
            $totalKomitmen  = $koreksiRows->sum('nilai_komitmen');
            $totalRealisasi = $koreksiRows->sum('realisasi');
            $sudahDone      = $koreksiRows->filter(fn($r) => ($r->progress ?? '') === 'done')->count();
        @endphp

        {{-- ── Summary Cards ── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-1">Total LOP</p>
                <p class="text-2xl font-black text-slate-900">{{ $koreksiRows->count() }}</p>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Data aktif periode ini</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-1">Nilai Komitmen</p>
                <p class="text-lg font-black text-slate-900" id="summary-total-komitmen">{{ number_format($totalKomitmen, 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Total komitmen (Rp)</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-1">Total Realisasi</p>
                <p class="text-lg font-black text-emerald-600" id="summary-total-realisasi">{{ number_format($totalRealisasi, 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400 mt-0.5 font-medium" id="summary-done-count">{{ $sudahDone }} dari {{ $koreksiRows->count() }} LOP done</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-1">Progress Done</p>
                <p class="text-2xl font-black {{ $sudahDone === $koreksiRows->count() ? 'text-emerald-600' : ($sudahDone > 0 ? 'text-yellow-500' : 'text-red-500') }}" id="summary-done-pct">
                    {{ $koreksiRows->count() > 0 ? number_format(($sudahDone / $koreksiRows->count()) * 100, 0) : 0 }}%
                </p>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">LOP berstatus done</p>
            </div>
        </div>

        {{-- ── Tabel ── --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-x-auto">
            <div style="max-height: 95vh; overflow-y: auto;">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-100 bg-slate-50 w-10">NO</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-100 bg-slate-50">NAMA PELANGGAN</th>
                            <th class="px-4 py-3 text-right text-[10px] font-black text-blue-600 uppercase tracking-widest border-r border-slate-100 bg-blue-50 w-52">NILAI KOMITMEN (Rp)</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-teal-600 uppercase tracking-widest border-r border-slate-100 bg-teal-50 w-36">PROGRESS</th>
                            <th class="px-4 py-3 text-right text-[10px] font-black text-emerald-600 uppercase tracking-widest bg-emerald-50 w-52">REALISASI (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($koreksiRows as $row)
                        @php
                            $isDone  = ($row->progress ?? '') === 'done';
                            $hasReal = $row->realisasi !== null && $row->realisasi > 0;
                        @endphp
                        <tr class="transition-colors koreksi-row {{ $isDone ? 'bg-emerald-50/40' : 'hover:bg-slate-50' }}"
                            data-id="{{ $row->id }}"
                            data-komitmen="{{ $row->nilai_komitmen ?? 0 }}"
                            data-realisasi="{{ $row->realisasi ?? 0 }}"
                            data-done="{{ $isDone ? 'true' : 'false' }}">

                            {{-- NO --}}
                            <td class="px-3 py-2.5 text-center font-bold text-slate-500 border-r border-slate-100">{{ $row->no ?? $loop->iteration }}</td>

                            {{-- NAMA PELANGGAN --}}
                            <td class="px-4 py-2.5 text-slate-700 border-r border-slate-100 font-semibold">{{ $row->nama_pelanggan }}</td>

                            {{-- NILAI KOMITMEN — editable inline --}}
                            <td class="px-3 py-2 border-r border-slate-100 bg-blue-50/60" id="komitmen-cell-{{ $row->id }}">
                                @if(!$isReadOnly)
                                <div class="flex items-center justify-end space-x-1.5">
                                    <div class="komitmen-display font-black tabular-nums cursor-pointer transition-colors text-right
                                        {{ $row->nilai_komitmen ? 'text-slate-800 hover:text-blue-600' : 'text-slate-300 hover:text-slate-400' }}"
                                        id="komitmen-display-{{ $row->id }}"
                                        onclick="startEditKomitmen({{ $row->id }})"
                                        title="Klik untuk edit nilai komitmen">
                                        @if($row->nilai_komitmen)
                                            {{ number_format($row->nilai_komitmen, 0, ',', '.') }}
                                        @else
                                            <span class="text-xs font-medium text-slate-300">— isi</span>
                                        @endif
                                    </div>
                                    <div class="komitmen-edit hidden items-center space-x-1" id="komitmen-edit-{{ $row->id }}">
                                        <input type="number" min="0" step="1"
                                            id="komitmen-input-{{ $row->id }}"
                                            value="{{ $row->nilai_komitmen > 0 ? $row->nilai_komitmen : '' }}"
                                            placeholder="0"
                                            class="w-32 px-2 py-1 border-2 border-blue-400 rounded-lg text-xs font-black text-slate-800 text-right focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100 tabular-nums"
                                            onkeydown="handleKeyKomitmen(event, {{ $row->id }})">
                                        <button onclick="saveKomitmen({{ $row->id }})"
                                            class="p-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors flex-shrink-0" title="Simpan">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <button onclick="cancelEditKomitmen({{ $row->id }})"
                                            class="p-1 bg-slate-200 hover:bg-slate-300 text-slate-600 rounded-md transition-colors flex-shrink-0" title="Batal">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                @else
                                <div class="text-right font-black tabular-nums text-slate-800">
                                    {{ $row->nilai_komitmen ? number_format($row->nilai_komitmen, 0, ',', '.') : '—' }}
                                </div>
                                @endif
                            </td>

                            {{-- PROGRESS — checkbox done / on-progress --}}
                            <td class="px-3 py-2 text-center border-r border-slate-100
                                {{ $isDone ? 'bg-emerald-50' : 'bg-slate-50' }}"
                                id="progress-cell-{{ $row->id }}">
                                @if(!$isReadOnly)
                                <label class="inline-flex flex-col items-center gap-0.5 cursor-pointer group select-none"
                                    title="{{ $isDone ? 'Done — klik untuk ubah ke on-progress' : 'On Progress — klik untuk centang done' }}">
                                    <input type="checkbox" class="progress-checkbox sr-only"
                                        data-id="{{ $row->id }}"
                                        {{ $isDone ? 'checked' : '' }}>
                                    <div class="w-4 h-4 rounded border-2 flex items-center justify-center transition-all duration-150
                                        {{ $isDone
                                            ? 'bg-emerald-500 border-emerald-500'
                                            : 'bg-white border-slate-300 group-hover:border-emerald-400' }}">
                                        @if($isDone)
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        @endif
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-wide leading-none
                                        {{ $isDone ? 'text-emerald-600' : 'text-slate-400' }}"
                                        id="progress-label-{{ $row->id }}">
                                        {{ $isDone ? 'Done' : 'On Prog' }}
                                    </span>
                                </label>
                                @else
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-black uppercase
                                    {{ $isDone ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }}">
                                    @if($isDone)<svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@endif
                                    {{ $isDone ? 'Done' : 'On Prog' }}
                                </span>
                                @endif
                            </td>

                            {{-- REALISASI — read-only --}}
                            <td class="px-4 py-2.5 text-right {{ $isDone ? 'bg-emerald-50' : 'bg-slate-50' }}"
                                id="realisasi-cell-{{ $row->id }}">
                                <span class="{{ $isDone && $hasReal ? 'font-black text-emerald-700 tabular-nums' : 'font-medium text-slate-300' }}"
                                    id="realisasi-display-{{ $row->id }}">
                                    {{ $isDone && $hasReal ? number_format($row->realisasi, 0, ',', '.') : '—' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-400 font-bold">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="sticky bottom-0 z-10">
                        <tr class="border-t-2 border-red-200 bg-slate-50">
                            <td colspan="2" class="px-4 py-3 text-right text-xs font-black text-slate-600 uppercase tracking-widest border-r border-slate-100">TOTAL</td>
                            <td class="px-4 py-3 text-right font-black text-blue-700 tabular-nums border-r border-slate-100 bg-blue-50" id="footer-total-komitmen">
                                {{ number_format($totalKomitmen, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center border-r border-slate-100 bg-teal-50">
                                <span class="text-xs font-black tabular-nums text-teal-700" id="footer-done-count">
                                    {{ $sudahDone }}/{{ $koreksiRows->count() }} done
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right bg-emerald-50">
                                <span class="font-black text-emerald-700 tabular-nums" id="footer-total-realisasi">
                                    {{ number_format($totalRealisasi, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if(!$isReadOnly)
        <div class="mt-5 bg-blue-50 border border-blue-200 rounded-xl px-5 py-3 flex items-start space-x-3">
            <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-xs text-blue-700 leading-relaxed space-y-0.5">
                <div><span class="font-black">Kolom Progress:</span> Centang = <span class="font-black text-emerald-600">Done</span> (realisasi otomatis = nilai komitmen). Hilangkan centang = <span class="font-black text-slate-500">On Progress</span>.</div>
                <div><span class="font-black">Kolom Nilai Komitmen:</span> Klik angka untuk mengedit. Tekan <span class="font-black">Enter</span> atau ✓ untuk simpan, <span class="font-black">Esc</span> atau ✕ untuk batal.</div>
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════════════════════════════
             NON-KOREKSI (on-hand / qualified / initiate)
        ══════════════════════════════════════════════════════════════ --}}
        @else

        @php $isReadOnly = false; @endphp

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-x-auto">
            <div style="max-height: 95vh; overflow-y: auto;">
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
                            <th rowspan="2" class="px-3 py-2 text-center text-[10px] font-black text-white bg-red-600 min-w-[80px]">CANCEL</th>
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
                            $funnel      = $funnelMap->get($row->id);
                            $denganMitra = strtolower(trim($row->mitra ?? ''));
                            $checked     = fn($field) => $funnel && ($funnel->{$field} ?? false);
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors" data-row-id="{{ $row->id }}" data-cancelled="{{ $checked('cancel') ? 'true' : 'false' }}">
                            <td class="px-3 py-2.5 whitespace-nowrap font-bold text-slate-700 border-r border-slate-100 text-center sticky-col">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2.5 text-slate-600 border-r border-slate-100 font-medium sticky-col">{{ $row->project }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-700 border-r border-slate-100 bg-emerald-50 font-bold sticky-col">{{ $row->id_lop }}</td>
                            <td class="px-3 py-2.5 whitespace-nowrap text-slate-600 border-r border-slate-100 sticky-col">{{ $row->cc }}</td>
                            <td class="px-4 py-2.5 text-slate-600 border-r border-slate-100">{{ $row->am }}</td>
                            <td class="px-4 py-2.5 text-slate-700 border-r border-slate-100 bg-emerald-50 font-semibold">{{ $row->mitra }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 border-r border-slate-100 text-center">{{ $row->plan_bulan_billcomp_2025 }}</td>
                            {{-- EST NILAI BC — editable inline --}}
                            <td class="px-3 py-2 border-r border-slate-100 bg-amber-50/40" id="estnilai-cell-{{ $row->id }}" data-row-id="{{ $row->id }}">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <div class="estnilai-display font-black tabular-nums cursor-pointer transition-colors text-right
                                        {{ $row->est_nilai_bc ? 'text-slate-800 hover:text-amber-600' : 'text-slate-300 hover:text-slate-400' }}"
                                        id="estnilai-display-{{ $row->id }}"
                                        onclick="startEditEstNilai({{ $row->id }})"
                                        title="Klik untuk edit estimasi nilai BC">
                                        @if($row->est_nilai_bc)
                                            {{ number_format($row->est_nilai_bc, 0, ',', '.') }}
                                        @else
                                            <span class="text-xs font-medium text-slate-300">— isi</span>
                                        @endif
                                    </div>
                                    <div class="estnilai-edit hidden items-center space-x-1" id="estnilai-edit-{{ $row->id }}">
                                        <input type="number" min="0" step="1"
                                            id="estnilai-input-{{ $row->id }}"
                                            value="{{ $row->est_nilai_bc > 0 ? $row->est_nilai_bc : '' }}"
                                            placeholder="0"
                                            class="w-40 px-2 py-1 border-2 border-amber-400 rounded-lg text-xs font-black text-slate-800 text-right focus:outline-none focus:border-amber-600 focus:ring-1 focus:ring-amber-100 tabular-nums"
                                            onkeydown="handleKeyEstNilai(event, {{ $row->id }})">
                                        <button onclick="saveEstNilai({{ $row->id }})"
                                            class="p-1 bg-amber-600 hover:bg-amber-700 text-white rounded-md transition-colors flex-shrink-0" title="Simpan">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <button onclick="cancelEditEstNilai({{ $row->id }})"
                                            class="p-1 bg-slate-200 hover:bg-slate-300 text-slate-600 rounded-md transition-colors flex-shrink-0" title="Batal">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            {{-- F0 --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-blue-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-blue-600 rounded cursor-pointer"
                                    data-field="f0_inisiasi_solusi" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}"
                                    {{ $checked('f0_inisiasi_solusi') ? 'checked' : '' }}>
                            </td>
                            {{-- F1 --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-purple-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-purple-600 rounded cursor-pointer"
                                    data-field="f1_tech_budget" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}"
                                    {{ $checked('f1_tech_budget') ? 'checked' : '' }}>
                            </td>
                            {{-- F2 --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                @if($denganMitra)<input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_p0_p1" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f2_p0_p1') ? 'checked' : '' }}>
                                @else<span class="text-slate-300 font-bold">—</span>@endif
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                @if($denganMitra)<input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_p2" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f2_p2') ? 'checked' : '' }}>
                                @else<span class="text-slate-300 font-bold">—</span>@endif
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                @if($denganMitra)<input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_p3" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f2_p3') ? 'checked' : '' }}>
                                @else<span class="text-slate-300 font-bold">—</span>@endif
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                @if($denganMitra)<input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_p4" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f2_p4') ? 'checked' : '' }}>
                                @else<span class="text-slate-300 font-bold">—</span>@endif
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                @if($denganMitra)<input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_offering" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f2_offering') ? 'checked' : '' }}>
                                @else<span class="text-slate-300 font-bold">—</span>@endif
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                @if($denganMitra)<input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_p5" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f2_p5') ? 'checked' : '' }}>
                                @else<span class="text-slate-300 font-bold">—</span>@endif
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_proposal" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f2_proposal') ? 'checked' : '' }}>
                            </td>
                            {{-- F3 --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-orange-50">
                                @if($denganMitra)<input type="checkbox" class="funnel-checkbox w-4 h-4 text-orange-600 rounded cursor-pointer" data-field="f3_p6" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f3_p6') ? 'checked' : '' }}>
                                @else<span class="text-slate-300 font-bold">—</span>@endif
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-orange-50">
                                @if($denganMitra)<input type="checkbox" class="funnel-checkbox w-4 h-4 text-orange-600 rounded cursor-pointer" data-field="f3_p7" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f3_p7') ? 'checked' : '' }}>
                                @else<span class="text-slate-300 font-bold">—</span>@endif
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-orange-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-orange-600 rounded cursor-pointer" data-field="f3_submit" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f3_submit') ? 'checked' : '' }}>
                            </td>
                            {{-- F4 --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-teal-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-teal-600 rounded cursor-pointer" data-field="f4_negosiasi" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f4_negosiasi') ? 'checked' : '' }}>
                            </td>
                            {{-- F5 --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-green-50">
                                @if($denganMitra)<input type="checkbox" class="funnel-checkbox w-4 h-4 text-green-600 rounded cursor-pointer" data-field="f5_sk_mitra" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f5_sk_mitra') ? 'checked' : '' }}>
                                @else<span class="text-slate-300 font-bold">—</span>@endif
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-green-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-green-600 rounded cursor-pointer" data-field="f5_ttd_kontrak" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f5_ttd_kontrak') ? 'checked' : '' }}>
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-green-50">
                                @if($denganMitra)<input type="checkbox" class="funnel-checkbox w-4 h-4 text-green-600 rounded cursor-pointer" data-field="f5_p8" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('f5_p8') ? 'checked' : '' }}>
                                @else<span class="text-slate-300 font-bold">—</span>@endif
                            </td>
                            {{-- DELIVERY --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-emerald-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-emerald-600 rounded cursor-pointer" data-field="delivery_kontrak" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('delivery_kontrak') ? 'checked' : '' }}>
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-emerald-50">
                                @if($denganMitra)<input type="checkbox" class="funnel-checkbox w-4 h-4 text-emerald-600 rounded cursor-pointer" data-field="delivery_baut_bast" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('delivery_baut_bast') ? 'checked' : '' }}>
                                @else<span class="text-slate-300 font-bold">—</span>@endif
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-emerald-50">
                                @if($denganMitra)<input type="checkbox" class="funnel-checkbox w-4 h-4 text-emerald-600 rounded cursor-pointer" data-field="delivery_baso" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}" {{ $checked('delivery_baso') ? 'checked' : '' }}>
                                @else<span class="text-slate-300 font-bold">—</span>@endif
                            </td>
                            {{-- BILLING COMPLETE --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-indigo-50">
                                <input type="checkbox" class="billing-checkbox w-4 h-4 text-indigo-600 rounded cursor-pointer"
                                    data-field="delivery_billing_complete" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}"
                                    data-est-nilai="{{ $row->est_nilai_bc }}"
                                    {{ $checked('delivery_billing_complete') ? 'checked' : '' }}>
                            </td>
                            {{-- NILAI BILL COMP --}}
                            <td class="px-2 py-2.5 text-right bg-violet-50 nilai-billcomp-cell" data-row-id="{{ $row->id }}">
                                <span class="font-black text-slate-800">
                                    @php
                                        $masterChecked = $funnel && $funnel->delivery_billing_complete;
                                        if ($masterChecked) {
                                            $nilaiToShow = $funnel->delivery_nilai_billcomp;
                                            if (!$nilaiToShow) {
                                                $cleanValue = str_replace(['.', ','], '', $row->est_nilai_bc ?? '0');
                                                $nilaiToShow = (int) $cleanValue;
                                            }
                                            echo number_format($nilaiToShow, 0, ',', '.');
                                        } else {
                                            echo '<span class="text-slate-300">—</span>';
                                        }
                                    @endphp
                                </span>
                            </td>
                            {{-- CANCEL — class cancel-checkbox, TIDAK masuk funnel-checkbox / billing-checkbox --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-red-50">
                                <input type="checkbox" class="cancel-checkbox w-4 h-4 text-red-600 rounded cursor-pointer"
                                    data-field="cancel" data-data-type="{{ $type }}" data-data-id="{{ $row->id }}"
                                    {{ $checked('cancel') ? 'checked' : '' }}>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="sticky bottom-0 z-10">
                        <tr class="border-t-2 border-red-200 bg-slate-50">
                            <td colspan="7" class="px-4 py-3 text-right text-xs font-black text-slate-700 uppercase tracking-widest border-r border-slate-100">TOTAL:</td>
                            <td class="px-4 py-3 text-right font-black text-emerald-700 border-r border-slate-100 bg-emerald-50" id="footer-total-est-nilai">
                                {{ number_format($dataRows->sum(fn($r) => floatval($r->est_nilai_bc ?? 0)), 0, ',', '.') }}
                            </td>
                            <td colspan="20" class="border-r border-slate-100"></td>
                            <td class="px-4 py-3 text-right font-black text-violet-700 bg-violet-50" id="total-nilai-billcomp">
                                @php
                                    $totalBillComp = $funnelMap->filter(fn($f) => $f && $f->delivery_billing_complete)
                                        ->sum(function($f) use ($dataRows) {
                                            $nilai = $f->delivery_nilai_billcomp;
                                            if (!$nilai) {
                                                $row = $dataRows->firstWhere('id', $f->data_id);
                                                $clean = str_replace(['.', ','], '', $row->est_nilai_bc ?? '0');
                                                $nilai = (float) $clean;
                                            }
                                            return (float) $nilai;
                                        });
                                @endphp
                                <span>{{ number_format($totalBillComp, 0, ',', '.') }}</span>
                            </td>
                            <td class="border-r border-slate-100"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-5 bg-blue-50 border border-blue-200 rounded-xl px-5 py-3 flex items-start space-x-3">
            <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-xs text-blue-700 leading-relaxed space-y-0.5">
                <div><span class="font-black">Kolom Est Nilai BC:</span> Klik angka untuk mengedit. Tekan <span class="font-black">Enter</span> atau ✓ untuk simpan, <span class="font-black">Esc</span> atau ✕ untuk batal. Total otomatis berubah.</div>
            </div>
        </div>

        @endif {{-- end koreksi / non-koreksi --}}

        @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-5"
                style="background: linear-gradient(135deg, #fff1f2, #ffe4e6); border: 2px solid #fecdd3;">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-black text-slate-700 mb-2 uppercase tracking-tight">Data Belum Tersedia</h3>
            <p class="text-sm font-medium text-slate-400">Belum ada data untuk periode ini.</p>
        </div>
        @endif

    </div>
</div>

{{-- ══ SCRIPTS ══ --}}
@if($import)
@if($type === 'koreksi' && !(isset($isReadOnly) && $isReadOnly))
<script>
(function () {
    const CSRF  = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const ROUTE = '{{ route("admin.progress.koreksi.update-realisasi", ["segment" => $segment]) }}';

    // ── State awal per-baris ──
    const rowState = {}; // { id: { komitmen, realisasi, done } }
    document.querySelectorAll('.koreksi-row').forEach(function (tr) {
        rowState[tr.dataset.id] = {
            komitmen  : parseFloat(tr.dataset.komitmen)  || 0,
            realisasi : parseFloat(tr.dataset.realisasi) || 0,
            done      : tr.dataset.done === 'true',
        };
    });

    let totalLOP      = {{ $koreksiRows->count() }};
    let totalKomitmen = {{ $koreksiRows->sum('nilai_komitmen') }};
    let totalReal     = {{ $koreksiRows->sum('realisasi') }};
    let doneCount     = {{ $koreksiRows->filter(fn($r) => ($r->progress ?? '') === 'done')->count() }};

    /* ════════════════════════════════════════════
       PROGRESS CHECKBOX — done / on-progress
    ════════════════════════════════════════════ */
    document.querySelectorAll('.progress-checkbox').forEach(function (cb) {
        cb.addEventListener('change', function () {
            const id    = this.dataset.id;
            const done  = this.checked;
            const state = rowState[id];
            if (!state) return;

            // Update visual checkbox UI langsung
            const label = this.closest('label');
            const box   = label.querySelector('div');
            const lbl   = document.getElementById('progress-label-' + id);
            const cell  = document.getElementById('progress-cell-' + id);
            const row   = document.querySelector('.koreksi-row[data-id="' + id + '"]');

            if (done) {
                box.className = 'w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-200 bg-emerald-500 border-emerald-500 shadow-sm shadow-emerald-200';
                box.innerHTML = '<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
                lbl.textContent = 'Done';
                lbl.className   = 'text-[10px] font-black uppercase tracking-wider text-emerald-600';
                cell.classList.remove('bg-slate-50');
                cell.classList.add('bg-emerald-50');
                row.classList.remove('hover:bg-slate-50');
                row.classList.add('bg-emerald-50');
            } else {
                box.className = 'w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-200 bg-white border-slate-300 group-hover:border-emerald-400';
                box.innerHTML = '';
                lbl.textContent = 'On Prog';
                lbl.className   = 'text-[10px] font-black uppercase tracking-wider text-slate-400';
                cell.classList.remove('bg-emerald-50');
                cell.classList.add('bg-slate-50');
                row.classList.remove('bg-emerald-50');
                row.classList.add('hover:bg-slate-50');
            }

            // Hitung realisasi baru: done → komitmen, on-progress → 0
            const newRealisasi = done ? state.komitmen : 0;
            const oldRealisasi = state.realisasi;

            // Tampilkan realisasi di kolom
            const realCell    = document.getElementById('realisasi-cell-' + id);
            const realDisplay = document.getElementById('realisasi-display-' + id);
            if (done && newRealisasi > 0) {
                realCell.className    = 'px-4 py-3 text-right border-l border-emerald-100 bg-emerald-50';
                realDisplay.className = 'font-black text-emerald-700 tabular-nums';
                realDisplay.textContent = fmt(newRealisasi);
            } else {
                realCell.className    = 'px-4 py-3 text-right border-l border-emerald-100 bg-slate-50';
                realDisplay.className = 'font-bold text-slate-300';
                realDisplay.textContent = '—';
            }

            // Update state lokal
            state.realisasi = newRealisasi;
            state.done      = done;
            totalReal       = totalReal - oldRealisasi + newRealisasi;
            doneCount       = doneCount + (done ? 1 : -1);
            updateFooter();
            updateSummary();

            // Kirim ke server
            fetch(ROUTE, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ id: id, progress: done ? 'done' : 'on-progress', realisasi: newRealisasi }),
            })
            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(function (data) {
                if (!data.success) throw new Error(data.message || 'Gagal');
                // Flash hijau
                row.style.outline = '2px solid #16a34a';
                setTimeout(function () { row.style.outline = ''; }, 1000);
            })
            .catch(function (err) {
                // Rollback
                cb.checked      = !done;
                state.realisasi = oldRealisasi;
                state.done      = !done;
                totalReal       = totalReal - newRealisasi + oldRealisasi;
                doneCount       = doneCount + (done ? -1 : 1);
                // Kembalikan visual ke sebelumnya
                cb.dispatchEvent(new Event('_rollback'));
                updateFooter();
                updateSummary();
                alert('Gagal menyimpan progress: ' + err.message);
            });
        });
    });

    /* ════════════════════════════════════════════
       NILAI KOMITMEN — editable inline
    ════════════════════════════════════════════ */
    window.startEditKomitmen = function (id) {
        // Tutup edit lain yang terbuka
        document.querySelectorAll('.komitmen-edit').forEach(function (el) {
            if (!el.classList.contains('hidden')) {
                cancelEditKomitmen(parseInt(el.id.replace('komitmen-edit-', '')));
            }
        });
        document.getElementById('komitmen-display-' + id).classList.add('hidden');
        var edit = document.getElementById('komitmen-edit-' + id);
        edit.classList.remove('hidden');
        edit.classList.add('flex');
        var input = document.getElementById('komitmen-input-' + id);
        input.focus();
        input.select();
    };

    window.cancelEditKomitmen = function (id) {
        var edit = document.getElementById('komitmen-edit-' + id);
        edit.classList.add('hidden');
        edit.classList.remove('flex');
        document.getElementById('komitmen-display-' + id).classList.remove('hidden');
        var state = rowState[id];
        document.getElementById('komitmen-input-' + id).value = state && state.komitmen > 0 ? state.komitmen : '';
    };

    window.handleKeyKomitmen = function (event, id) {
        if (event.key === 'Enter')  { event.preventDefault(); saveKomitmen(id); }
        else if (event.key === 'Escape') { cancelEditKomitmen(id); }
    };

    window.saveKomitmen = function (id) {
        var input    = document.getElementById('komitmen-input-' + id);
        var rawValue = input.value.trim();
        var newValue = rawValue === '' ? 0 : parseFloat(rawValue);
        if (isNaN(newValue) || newValue < 0) {
            input.classList.add('border-red-500');
            input.focus();
            setTimeout(function () { input.classList.remove('border-red-500'); }, 1500);
            return;
        }
        var display = document.getElementById('komitmen-display-' + id);
        var edit    = document.getElementById('komitmen-edit-' + id);
        display.innerHTML = '<span class="text-slate-400 text-xs font-bold animate-pulse">Menyimpan...</span>';
        display.classList.remove('hidden');
        edit.classList.add('hidden');
        edit.classList.remove('flex');

        var state       = rowState[id];
        var oldKomitmen = state ? state.komitmen : 0;

        fetch(ROUTE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ id: id, nilai_komitmen: newValue }),
        })
        .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(function (data) {
            if (!data.success) throw new Error(data.message || 'Gagal');

            // Update state komitmen
            totalKomitmen = totalKomitmen - oldKomitmen + newValue;
            if (state) state.komitmen = newValue;

            // Jika row ini done, realisasi juga ikut berubah
            if (state && state.done) {
                var oldReal  = state.realisasi;
                totalReal    = totalReal - oldReal + newValue;
                state.realisasi = newValue;
                var realDisp = document.getElementById('realisasi-display-' + id);
                if (realDisp) {
                    realDisp.textContent = newValue > 0 ? fmt(newValue) : '—';
                    realDisp.className   = newValue > 0
                        ? 'font-black text-emerald-700 tabular-nums'
                        : 'font-bold text-slate-300';
                }
                // Simpan realisasi baru ke server juga
                fetch(ROUTE, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({ id: id, realisasi: newValue }),
                }).catch(function(){});
            }

            // Update display komitmen
            input.value = newValue > 0 ? newValue : '';
            display.innerHTML = newValue > 0
                ? fmt(newValue)
                : '<span class="text-xs font-bold">— klik untuk isi</span>';
            display.className = 'komitmen-display font-black tabular-nums cursor-pointer transition-colors ' +
                (newValue > 0 ? 'text-slate-800 hover:text-blue-600' : 'text-slate-300 hover:text-slate-400');
            display.setAttribute('onclick', 'startEditKomitmen(' + id + ')');

            // Update total footer komitmen
            var ftK = document.getElementById('footer-total-komitmen');
            if (ftK) ftK.textContent = fmt(totalKomitmen);
            var smK = document.getElementById('summary-total-komitmen');
            if (smK) smK.textContent = fmt(totalKomitmen);

            updateFooter();
            updateSummary();

            var cell = document.getElementById('komitmen-cell-' + id);
            cell.style.background = '#dbeafe';
            setTimeout(function () { cell.style.background = ''; }, 1200);
        })
        .catch(function (err) {
            if (state) state.komitmen = oldKomitmen;
            display.innerHTML = oldKomitmen > 0
                ? fmt(oldKomitmen)
                : '<span class="text-xs font-bold">— klik untuk isi</span>';
            display.className = 'komitmen-display font-black tabular-nums cursor-pointer transition-colors ' +
                (oldKomitmen > 0 ? 'text-slate-800 hover:text-blue-600' : 'text-slate-300 hover:text-slate-400');
            display.setAttribute('onclick', 'startEditKomitmen(' + id + ')');
            var cell = document.getElementById('komitmen-cell-' + id);
            cell.style.background = '#fee2e2';
            setTimeout(function () { cell.style.background = ''; }, 1500);
            alert('Gagal menyimpan: ' + err.message);
        });
    };

    /* ════════════════════════════════════════════
       UPDATE FOOTER & SUMMARY
    ════════════════════════════════════════════ */
    function updateFooter() {
        var elR = document.getElementById('footer-total-realisasi');
        var elD = document.getElementById('footer-done-count');
        if (elR) elR.textContent = fmt(totalReal);
        if (elD) elD.textContent = doneCount + '/' + totalLOP + ' done';
    }

    function updateSummary() {
        var sr  = document.getElementById('summary-total-realisasi');
        var sp  = document.getElementById('summary-done-pct');
        var sd  = document.getElementById('summary-done-count');
        var pct = totalLOP > 0 ? (doneCount / totalLOP) * 100 : 0;
        if (sr) sr.textContent = fmt(totalReal);
        if (sd) sd.textContent = doneCount + ' dari ' + totalLOP + ' LOP done';
        if (sp) {
            sp.textContent = Math.round(pct) + '%';
            sp.className   = 'text-2xl font-black ' +
                (pct >= 100 ? 'text-emerald-600' : (pct > 0 ? 'text-yellow-500' : 'text-red-500'));
        }
    }

    function fmt(num) {
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
})();
</script>

@elseif($type !== 'koreksi')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) return;

    const updateUrl = '{{ route("admin.progress.funnel.update", ["segment" => $segment, "type" => $type]) }}';

    function formatNumber(num) {
        if (!num) return '-';
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // ── Disable/enable hanya funnel + billing. Cancel TIDAK disentuh. ──
    function setRowDisabled(row, disabled) {
        row.querySelectorAll('.funnel-checkbox, .billing-checkbox').forEach(cb => {
            cb.disabled = disabled;
            if (disabled) {
                cb.classList.add('opacity-40', 'cursor-not-allowed');
                cb.classList.remove('cursor-pointer');
            } else {
                cb.classList.remove('opacity-40', 'cursor-not-allowed');
                cb.classList.add('cursor-pointer');
            }
        });
        if (disabled) {
            row.classList.add('bg-red-50', 'opacity-70');
            row.classList.remove('hover:bg-slate-50');
        } else {
            row.classList.remove('bg-red-50', 'opacity-70');
            row.classList.add('hover:bg-slate-50');
        }
    }

    // Init state dari data-cancelled
    document.querySelectorAll('tr[data-cancelled="true"]').forEach(row => setRowDisabled(row, true));

    // ── RECALC TOTAL NILAI BILLCOMP ──
    function recalcTotal() {
        let total = 0;
        document.querySelectorAll('tbody tr').forEach(function (tr) {
            if (tr.dataset.cancelled === 'true') return;
            const bc = tr.querySelector('.billing-checkbox');
            if (!bc || !bc.checked) return;
            total += parseFloat(bc.dataset.estNilai || '0');
        });
        const tc = document.getElementById('total-nilai-billcomp');
        if (tc) {
            const span = tc.querySelector('span');
            if (span) span.textContent = Math.round(total).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    }

    // ── CANCEL CHECKBOX ──
    document.querySelectorAll('.cancel-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const self      = this;
            const rowId     = this.dataset.dataId;
            const cancelled = this.checked;
            const row       = this.closest('tr');

            setRowDisabled(row, cancelled);
            row.dataset.cancelled = cancelled ? 'true' : 'false';

            if (cancelled) {
                const billingCb = row.querySelector('.billing-checkbox');
                if (billingCb) billingCb.checked = false;
                const nilaiCell = row.querySelector('.nilai-billcomp-cell');
                if (nilaiCell) nilaiCell.innerHTML = '<span class="text-slate-300">—</span>';
            }

            recalcTotal();

            fetch(updateUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ data_type: '{{ $type }}', data_id: rowId, field: 'cancel', value: cancelled })
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    self.checked = !cancelled;
                    setRowDisabled(row, !cancelled);
                    row.dataset.cancelled = (!cancelled) ? 'true' : 'false';
                    recalcTotal();
                    alert('Gagal menyimpan status cancel.');
                }
            })
            .catch(() => {
                self.checked = !cancelled;
                setRowDisabled(row, !cancelled);
                row.dataset.cancelled = (!cancelled) ? 'true' : 'false';
                recalcTotal();
                alert('Error menyimpan status cancel.');
            });
        });
    });

    // ── FUNNEL CHECKBOXES ──
    document.querySelectorAll('.funnel-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const row = this.closest('tr');
            if (row && row.dataset.cancelled === 'true') { this.checked = !this.checked; return; }
            this.parentElement.classList.add('bg-yellow-100');
            saveChange(this.dataset.dataId, this.dataset.dataType, this.dataset.field, this.checked, null, this.parentElement);
            if (this.checked) cascadeBackwards(this);
        });
    });

    // ── BILLING CHECKBOXES ──
    document.querySelectorAll('.billing-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const row = this.closest('tr');
            if (row && row.dataset.cancelled === 'true') { this.checked = !this.checked; return; }
            this.parentElement.classList.add('bg-yellow-100');
            saveChange(this.dataset.dataId, this.dataset.dataType, this.dataset.field, this.checked, this.dataset.estNilai || '0', this.parentElement);
            // Cascade backward dari billing juga (tanpa cancel)
            if (this.checked) cascadeBackwards(this);
        });
    });

    // ── CASCADE BACKWARDS ──
    // Hanya menyentuh .funnel-checkbox dan .billing-checkbox — cancel TIDAK masuk selector ini
    function cascadeBackwards(changedCb) {
        const row = changedCb.closest('tr');
        if (!row) return;
        const boxes = Array.from(row.querySelectorAll('.funnel-checkbox, .billing-checkbox'));
        const idx = boxes.indexOf(changedCb);
        if (idx === -1) return;
        if (!boxes.slice(idx + 1).some(cb => cb.checked)) return;
        boxes.slice(0, idx).forEach(cb => {
            // Guard eksplisit: field cancel tidak boleh disentuh
            if (cb.dataset.field === 'cancel') return;
            if (!cb.checked) {
                cb.checked = true;
                saveChange(cb.dataset.dataId, cb.dataset.dataType, cb.dataset.field, true, null, cb.parentElement);
            }
        });
    }

    // ── SAVE CHANGE ──
    function saveChange(rowId, dataType, field, value, estNilaiBc, container) {
        const payload = { data_type: dataType, data_id: rowId, field, value };
        if (field === 'delivery_billing_complete') payload.est_nilai_bc = estNilaiBc || '0';

        fetch(updateUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
        .then(data => {
            if (data.success) {
                container.classList.remove('bg-yellow-100');
                container.classList.add('bg-green-50');
                setTimeout(() => container.classList.remove('bg-green-50'), 1500);

                if (field === 'delivery_billing_complete') {
                    const row = container.closest('tr');
                    const nilaiCell = row?.querySelector('.nilai-billcomp-cell');
                    if (nilaiCell) {
                        nilaiCell.innerHTML = value && data.nilai_billcomp
                            ? '<span class="font-black text-slate-800">' + formatNumber(data.nilai_billcomp) + '</span>'
                            : '<span class="text-slate-300">—</span>';
                    }
                }

                if (data.total) {
                    const tc = document.getElementById('total-nilai-billcomp');
                    if (tc) tc.querySelector('span').textContent = data.total;
                }

                // auto_fields: JANGAN pernah menyentuh field 'cancel'
                if (data.auto_fields && data.auto_fields.length) {
                    const fields    = Array.isArray(data.auto_fields) ? data.auto_fields : [data.auto_fields];
                    const targetVal = data.auto_value === true || data.auto_value === 'true';
                    const row       = container.closest('tr');
                    const fi        = row?.querySelector('input[data-data-id]');
                    if (fi) {
                        fields.forEach(fld => {
                            if (fld === 'cancel') return; // ← KUNCI: cancel tidak boleh disentuh auto_fields
                            const sib = document.querySelector(
                                `.funnel-checkbox[data-field="${fld}"][data-data-id="${fi.dataset.dataId}"][data-data-type="${fi.dataset.dataType}"]`
                            );
                            if (sib && sib.checked !== targetVal) {
                                sib.checked = targetVal;
                                saveChange(fi.dataset.dataId, fi.dataset.dataType, fld, targetVal, null, sib.parentElement);
                            }
                        });
                    }
                }
            } else {
                container.classList.remove('bg-yellow-100');
                container.classList.add('bg-red-50');
                setTimeout(() => container.classList.remove('bg-red-50'), 1500);
                const cb = container.querySelector('input[type="checkbox"]');
                if (cb) cb.checked = !value;
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(err => {
            container.classList.remove('bg-yellow-100');
            container.classList.add('bg-red-50');
            setTimeout(() => container.classList.remove('bg-red-50'), 1500);
            const cb = container.querySelector('input[type="checkbox"]');
            if (cb) cb.checked = !value;
            alert('Error: ' + err.message);
        });
    }

    // ── EDIT EST NILAI BC ──
    window.startEditEstNilai = function (rowId) {
        const display = document.getElementById('estnilai-display-' + rowId);
        const edit = document.getElementById('estnilai-edit-' + rowId);
        display.classList.add('hidden');
        edit.classList.remove('hidden');
        edit.classList.add('flex');
        const input = document.getElementById('estnilai-input-' + rowId);
        input.focus();
        input.select();
    };

    window.cancelEditEstNilai = function (rowId) {
        const display = document.getElementById('estnilai-display-' + rowId);
        const edit = document.getElementById('estnilai-edit-' + rowId);
        edit.classList.add('hidden');
        edit.classList.remove('flex');
        display.classList.remove('hidden');
    };

    // ── Track est_nilai_bc values globally ──
    const estNilaiMap = {};

    // Initialize dari display values
    document.querySelectorAll('[id^="estnilai-display-"]').forEach(function (display) {
        const rowId = display.id.replace('estnilai-display-', '');
        const text = display.textContent.trim();
        if (text && text !== '— klik untuk isi' && text !== '—') {
            const cleanValue = parseFloat(text.replace(/\./g, ''));
            estNilaiMap[rowId] = isNaN(cleanValue) ? 0 : cleanValue;
        } else {
            estNilaiMap[rowId] = 0;
        }
    });

    window.handleKeyEstNilai = function (event, rowId) {
        if (event.key === 'Enter') { event.preventDefault(); saveEstNilai(rowId); }
        else if (event.key === 'Escape') { cancelEditEstNilai(rowId); }
    };

    window.saveEstNilai = function (rowId) {
        const input = document.getElementById('estnilai-input-' + rowId);
        const rawValue = input.value.trim();
        const newValue = rawValue === '' ? 0 : parseFloat(rawValue);
        if (isNaN(newValue) || newValue < 0) {
            input.classList.add('border-red-500');
            input.focus();
            setTimeout(function () { input.classList.remove('border-red-500'); }, 1500);
            return;
        }

        const display = document.getElementById('estnilai-display-' + rowId);
        const edit = document.getElementById('estnilai-edit-' + rowId);
        display.innerHTML = '<span class="text-slate-400 text-xs font-bold animate-pulse">Menyimpan...</span>';
        display.classList.remove('hidden');
        edit.classList.add('hidden');
        edit.classList.remove('flex');

        fetch('{{ route("admin.progress.scalling.update-est-nilai", ["segment" => $segment, "type" => $type]) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ data_id: rowId, est_nilai_bc: newValue })
        })
        .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(function (data) {
            if (!data.success) throw new Error(data.message || 'Gagal');

            // Update display
            input.value = newValue > 0 ? newValue : '';
            display.innerHTML = newValue > 0
                ? formatNumber(newValue)
                : '<span class="text-xs font-bold text-slate-300">— klik untuk isi</span>';
            display.className = 'estnilai-display font-black tabular-nums cursor-pointer transition-colors text-right ' +
                (newValue > 0 ? 'text-slate-800 hover:text-amber-600' : 'text-slate-300 hover:text-slate-400');
            display.setAttribute('onclick', 'startEditEstNilai(' + rowId + ')');

            // Update tracking map
            estNilaiMap[rowId] = newValue;

            // Update data attribute untuk billing checkbox (agar nilai terbaru digunakan)
            const billingCb = document.querySelector('.billing-checkbox[data-data-id="' + rowId + '"]');
            if (billingCb) {
                billingCb.dataset.estNilai = newValue;
            }

            // Update total est_nilai_bc di footer
            updateEstNilaiTotal();

            // TAMBAHAN: Jika billing complete sudah dicentang, update delivery_nilai_billcomp otomatis
            if (billingCb && billingCb.checked) {
                const nilaiCell = document.querySelector('tr[data-row-id="' + rowId + '"] .nilai-billcomp-cell');
                if (nilaiCell) {
                    nilaiCell.innerHTML = newValue > 0
                        ? '<span class="font-black text-slate-800">' + formatNumber(newValue) + '</span>'
                        : '<span class="text-slate-300">—</span>';
                }

                // Kirim update delivery_nilai_billcomp ke server
                fetch(updateUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({
                        data_type: '{{ $type }}',
                        data_id: rowId,
                        field: 'delivery_billing_complete',
                        value: true,
                        est_nilai_bc: newValue
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.total) {
                        const tc = document.getElementById('total-nilai-billcomp');
                        if (tc) tc.querySelector('span').textContent = data.total;
                    }
                })
                .catch(err => console.error('Error updating delivery value:', err));
            }

            // Recalc total jika ada yang billing complete
            if (billingCb && billingCb.checked) {
                recalcTotal();
            }

            const cell = document.getElementById('estnilai-cell-' + rowId);
            cell.style.background = '#fef2f2';
            setTimeout(function () { cell.style.background = ''; }, 1200);
        })
        .catch(function (err) {
            display.innerHTML = newValue > 0
                ? formatNumber(newValue)
                : '<span class="text-xs font-bold text-slate-300">— klik untuk isi</span>';
            display.className = 'estnilai-display font-black tabular-nums cursor-pointer transition-colors text-right ' +
                (newValue > 0 ? 'text-slate-800 hover:text-amber-600' : 'text-slate-300 hover:text-slate-400');
            display.setAttribute('onclick', 'startEditEstNilai(' + rowId + ')');
            const cell = document.getElementById('estnilai-cell-' + rowId);
            cell.style.background = '#fee2e2';
            setTimeout(function () { cell.style.background = ''; }, 1500);
            alert('Gagal menyimpan: ' + err.message);
        });
    };

    function formatNumber(num) {
        if (!num) return '-';
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function updateEstNilaiTotal() {
    let total = 0;
    for (let rowId in estNilaiMap) {
        total += estNilaiMap[rowId];
    }

    const footer = document.getElementById('footer-total-est-nilai');
    if (footer) {
        footer.textContent = formatNumber(total);
    }
}
});
</script>
@endif
@endif

<style>
.sticky-col-header { position:sticky; z-index:3; }
.sticky-col { position:sticky; z-index:1; background:white; }
</style>

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
