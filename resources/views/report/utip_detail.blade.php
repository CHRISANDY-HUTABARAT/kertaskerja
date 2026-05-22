@extends('layouts.app')

@section('title', 'Detail UTIP — ' . $typeLabel)

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
                            Detail <span class="text-red-600">UTIP</span>
                        </h1>
                        <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-tight">
                            {{ $typeLabel }}
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

        {{-- ══ META + SUMMARY CARDS ══ --}}
        @php
            $totalPopulasi = $rows->sum('plan');
            $totalFlagHi   = $rows->sum('real_ratio');
            $totalOlFm     = $rows->sum('ol_fm');
            $totalSisa     = $totalPopulasi - $totalOlFm;
            $totalAch      = $totalPopulasi > 0 ? ($totalFlagHi / $totalPopulasi) * 100 : 0;
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-1">Total Populasi</p>
                <p class="text-2xl font-black text-slate-900">{{ number_format($totalPopulasi, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-1">Flag sd HI</p>
                <p class="text-2xl font-black text-red-600">{{ number_format($totalFlagHi, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-1">OL FM</p>
                <p class="text-2xl font-black text-slate-700">{{ number_format($totalOlFm, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-1">Sisa Saldo EoM</p>
                <p class="text-2xl font-black text-slate-900">{{ number_format($totalSisa, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-1">Ach</p>
                <p class="text-2xl font-black {{ $totalAch >= 100 ? 'text-green-600' : ($totalAch >= 80 ? 'text-yellow-500' : 'text-red-600') }}">
                    {{ number_format($totalAch, 1, ',', '.') }}%
                </p>
            </div>
        </div>

        {{-- ══ TABEL ══ --}}
@php
$kondisiList = [
    'Sudah BC, Potensi Flag',
    'Sudah BC, Over Payment',
    'Sudah BC, Rekon Kontrak & Tunggakan',
    'Sudah BC, Deposit',
    'Sudah BC, Pembayaran Kurang',
    'Belum BC, Late Input',
    'Belum teridentifikasi',
];
// Map data by kondisi untuk lookup cepat
$rowMap = $rows->keyBy('kondisi');
@endphp

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full" id="utipDetailTable">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-200 w-10">No</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-200">Kondisi</th>
                    <th class="px-4 py-3 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-200 w-36">Total Populasi</th>
                    <th class="px-4 py-3 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-200 w-32">Flag sd HI</th>
                    <th class="px-4 py-3 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-200 w-28">OL FM</th>
                    <th class="px-4 py-3 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-200 w-32">Ach</th>
                    <th class="px-4 py-3 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest w-32">Sisa Saldo EoM</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($kondisiList as $i => $kondisiName)
                @php
                    $row      = $rowMap->get($kondisiName);
                    $populasi = $row ? (float) ($row->plan ?? 0) : null;
                    $flagHi   = $row ? (float) ($row->real_ratio ?? 0) : null;
                    $olFm     = $row ? (float) ($row->ol_fm ?? 0) : null;
                    $sisa     = (!is_null($populasi) && !is_null($olFm)) ? $populasi - $olFm : null;
                    $ach      = (!is_null($populasi) && $populasi > 0 && !is_null($flagHi))
                                ? ($flagHi / $populasi) * 100 : null;
                    $achColor = is_null($ach) ? 'text-slate-400'
                        : ($ach >= 100 ? 'text-green-600' : ($ach >= 80 ? 'text-yellow-500' : 'text-red-600'));
                    $achBg    = is_null($ach) ? ''
                        : ($ach >= 100 ? 'bg-green-50' : ($ach >= 80 ? 'bg-yellow-50' : 'bg-red-50'));
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-center font-bold text-slate-400 border-r border-slate-100 text-sm">
                        {{ $i + 1 }}
                    </td>
                    <td class="px-4 py-3 border-r border-slate-100">
                        <span class="text-xs font-bold text-slate-700">{{ $kondisiName }}</span>
                    </td>
                    <td class="px-4 py-3 text-right font-black text-slate-800 border-r border-slate-100 tabular-nums text-sm">
                        {{ !is_null($populasi) && $populasi > 0 ? number_format($populasi, 0, ',', '.') : '—' }}
                    </td>
                    <td class="px-4 py-3 text-right font-black text-red-600 border-r border-slate-100 tabular-nums text-sm">
                        {{ !is_null($flagHi) && $flagHi > 0 ? number_format($flagHi, 0, ',', '.') : '—' }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-slate-600 border-r border-slate-100 tabular-nums text-sm">
                        {{ !is_null($olFm) && $olFm > 0 ? number_format($olFm, 0, ',', '.') : '—' }}
                    </td>
                    <td class="px-4 py-3 text-right border-r border-slate-100 {{ $achBg }}">
                        <span class="font-black text-sm {{ $achColor }}">
                            {{ is_null($ach) ? '—' : number_format($ach, 1, ',', '.').'%' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right {{ !is_null($sisa) ? 'bg-emerald-50' : '' }}">
                        <span class="font-black tabular-nums text-sm {{ !is_null($sisa) ? 'text-emerald-700' : 'text-slate-400' }}">
                            {{ !is_null($sisa) ? number_format($sisa, 0, ',', '.') : '—' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-slate-200 bg-slate-50">
                    <td colspan="2" class="px-4 py-3 text-right text-xs font-black text-slate-500 uppercase tracking-widest">TOTAL</td>
                    <td class="px-4 py-3 text-right font-black text-slate-800 tabular-nums border-l border-slate-200">
                        {{ $totalPopulasi > 0 ? number_format($totalPopulasi, 0, ',', '.') : '—' }}
                    </td>
                    <td class="px-4 py-3 text-right font-black text-red-600 tabular-nums border-l border-slate-200">
                        {{ $totalFlagHi > 0 ? number_format($totalFlagHi, 0, ',', '.') : '—' }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-slate-600 tabular-nums border-l border-slate-200">
                        {{ $totalOlFm > 0 ? number_format($totalOlFm, 0, ',', '.') : '—' }}
                    </td>
                    <td class="px-4 py-3 text-right border-l border-slate-200">
                        <span class="font-black text-sm {{ $totalAch >= 100 ? 'text-green-600' : ($totalAch >= 80 ? 'text-yellow-500' : 'text-red-600') }}">
                            {{ $totalPopulasi > 0 ? number_format($totalAch, 1, ',', '.').'%' : '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right border-l border-slate-200 bg-emerald-50">
                        <span class="font-black text-emerald-700 tabular-nums">
                            {{ $totalPopulasi > 0 ? number_format($totalSisa, 0, ',', '.') : '—' }}
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#utipDetailTable tbody tr');

        rows.forEach(function (row) {
            const kondisiCell = row.querySelector('td:nth-child(2)');
            if (!kondisiCell) return;
            const match = kondisiCell.textContent.toLowerCase().includes(keyword);
            row.style.display = match ? '' : 'none';
        });
    });
});
</script>

@endsection
