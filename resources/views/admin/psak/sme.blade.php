@extends('layouts.app')

@section('title', 'Admin - PSAK SME')

@section('content')
<div class="min-h-screen" style="background:#f1f5f9;">
    <div class="max-w-7xl mx-auto px-8 py-10">

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-10 py-7 mb-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background: linear-gradient(90deg, #dc2626, #ef4444, #dc2626);"></div>
            <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full opacity-[0.04]" style="background: #dc2626;"></div>
            <div class="relative flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <img src="{{ asset('img/Telkom.png') }}" alt="Telkom" class="h-12 w-auto">
                    <div class="w-px h-12 bg-slate-200"></div>
                    <div>
                        <p class="text-[10px] font-black tracking-[0.3em] text-red-600 uppercase mb-1">Witel Sumut</p>
                        <h1 class="text-2xl font-black tracking-tight text-slate-900 leading-none uppercase">
                            PSAK <span class="text-red-600">SME</span>
                        </h1>
                        <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-tight">Admin mengelola semua data Komitmen & Realisasi</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.index') }}"
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- FLASH --}}
        @if(session('success'))
        <div class="flex items-center space-x-3 bg-green-50 border border-green-200 text-green-800 px-5 py-3.5 mb-6 rounded-xl text-sm font-semibold">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        {{-- FORM --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 mb-8">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Input / Update Data PSAK</h2>
            </div>

            <form action="{{ route('admin.psak.sme.store') }}" method="POST">
                @csrf

                {{-- Periode + Segmen --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Periode</label>
                        <input type="month" name="periode" required value="{{ old('periode', date('Y-m')) }}"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors">
                        @error('periode')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Pilih Kategori</label>
                        <select name="segment" required
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors bg-white">
                            <option value="">— Pilih Kategori —</option>
                            <option value="nc_step14"      {{ old('segment') === 'nc_step14'       ? 'selected' : '' }}>Not Close Step 1-4</option>
                            <option value="nc_step5"       {{ old('segment') === 'nc_step5'        ? 'selected' : '' }}>Not Close Step 5</option>
                            <option value="nc_konfirmasi"  {{ old('segment') === 'nc_konfirmasi'   ? 'selected' : '' }}>Not Close Konfirmasi</option>
                            <option value="nc_splitbill"   {{ old('segment') === 'nc_splitbill'    ? 'selected' : '' }}>Not Close Split Bill</option>
                            <option value="nc_crvariable"  {{ old('segment') === 'nc_crvariable'   ? 'selected' : '' }}>Not Close CR Variable</option>
                            <option value="nc_unidentified"{{ old('segment') === 'nc_unidentified' ? 'selected' : '' }}>Not Close Unidentified KB</option>
                        </select>
                        @error('segment')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    {{-- Order --}}
                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-5">
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-1 h-5 bg-red-600 rounded-full"></div>
                            <h3 class="text-xs font-black text-slate-700 uppercase tracking-widest">Format Order</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Komitmen (SSL)</label>
                                <input type="number" name="comm_ssl" step="0.01" placeholder="cth: 150" value="{{ old('comm_ssl') }}"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Realisasi (SSL)</label>
                                <input type="number" name="real_ssl" step="0.01" placeholder="cth: 140" value="{{ old('real_ssl') }}"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors bg-white">
                            </div>
                        </div>
                    </div>

                    {{-- Rupiah --}}
                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-5">
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-1 h-5 bg-red-600 rounded-full"></div>
                            <h3 class="text-xs font-black text-slate-700 uppercase tracking-widest">Format Rupiah</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Komitmen (Rp)</label>
                                <input type="number" name="comm_rp" step="0.01" placeholder="cth: 5000000" value="{{ old('comm_rp') }}"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Realisasi (Rp)</label>
                                <input type="number" name="real_rp" step="0.01" placeholder="cth: 4800000" value="{{ old('real_rp') }}"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors bg-white">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="reset"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs px-5 py-2.5 rounded-xl transition-all uppercase tracking-wider">
                        Reset
                    </button>
                    <button type="submit"
                        class="flex items-center space-x-2 bg-slate-900 hover:bg-red-600 text-white font-bold text-xs px-6 py-2.5 rounded-xl transition-all uppercase tracking-wider shadow-md hover:shadow-lg hover:shadow-red-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Simpan / Update</span>
                    </button>
                </div>
            </form>
        </div>

        @php
        $segmentNames = [
            'nc_step14'       => 'Not Close Step 1-4',
            'nc_step5'        => 'Not Close Step 5',
            'nc_konfirmasi'   => 'Not Close Konfirmasi',
            'nc_splitbill'    => 'Not Close Split Bill',
            'nc_crvariable'   => 'Not Close CR Variable',
            'nc_unidentified' => 'Not Close Unidentified KB',
        ];
        @endphp

        {{-- ══ TABEL HISTORY ══ --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-8 py-5 border-b border-slate-100">
                <div class="flex items-center space-x-3 mb-5">
                    <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                    <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Riwayat Data PSAK SME</h2>
                </div>
                <form method="GET" action="{{ route('admin.psak.sme') }}" class="grid grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Kategori</label>
                        <select name="segment" onchange="this.form.submit()"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:border-red-400 bg-white">
                            <option value="">Semua Kategori</option>
                            @foreach($segmentNames as $val => $label)
                                <option value="{{ $val }}" {{ ($selectedSegment ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Bulan</label>
                        <select name="bulan" onchange="this.form.submit()"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:border-red-400 bg-white">
                            <option value="">Semua Bulan</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ ($selectedBulan ?? '') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Tahun</label>
                        <select name="tahun" onchange="this.form.submit()"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:border-red-400 bg-white">
                            <option value="">Semua Tahun</option>
                            @foreach($tahuns as $t)
                                <option value="{{ $t }}" {{ ($selectedTahun ?? '') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 invisible">Reset</label>
                        <a href="{{ route('admin.psak.sme') }}"
                            class="flex items-center justify-center w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-lg transition-colors uppercase tracking-wider">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">No</th>
                            <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal Input</th>
                            <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Periode</th>
                            <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori</th>
                            <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Comm (Order)</th>
                            <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Real (Order)</th>
                            <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Comm (Rp)</th>
                            <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Real (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($smes as $psak)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-slate-400">{{ $smes->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-500 whitespace-nowrap">
                                {{ $psak->created_at->translatedFormat('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-100 rounded-md px-2.5 py-1">
                                    {{ \Carbon\Carbon::parse($psak->periode)->translatedFormat('F Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 rounded-md px-2.5 py-1 whitespace-nowrap">
                                    {{ $segmentNames[$psak->segment] ?? $psak->segment }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-black text-slate-800">
                                {{ $psak->comm_ssl !== null ? number_format($psak->comm_ssl, 0, ',', '.') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-black text-red-600">
                                {{ $psak->real_ssl !== null ? number_format($psak->real_ssl, 0, ',', '.') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-black text-slate-800">
                                {{ $psak->comm_rp !== null ? 'Rp'.number_format($psak->comm_rp, 0, ',', '.') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-black text-red-600">
                                {{ $psak->real_rp !== null ? 'Rp'.number_format($psak->real_rp, 0, ',', '.') : '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <svg class="mx-auto w-10 h-10 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-sm font-bold text-slate-400">Belum ada data PSAK SME</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($smes->hasPages())
            <div class="px-8 py-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-400">
                    Menampilkan {{ $smes->firstItem() }}–{{ $smes->lastItem() }} dari {{ $smes->total() }} data
                </p>
                <div class="flex items-center gap-1">
                    @if($smes->onFirstPage())
                        <span class="px-3 py-1.5 text-xs font-bold text-slate-300 bg-slate-50 border border-slate-200 rounded-lg cursor-not-allowed">‹</span>
                    @else
                        <a href="{{ $smes->previousPageUrl() }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">‹</a>
                    @endif
                    @foreach($smes->getUrlRange(1, $smes->lastPage()) as $page => $url)
                        @if($page == $smes->currentPage())
                            <span class="px-3 py-1.5 text-xs font-bold text-white bg-slate-900 border border-slate-900 rounded-lg">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if($smes->hasMorePages())
                        <a href="{{ $smes->nextPageUrl() }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">›</a>
                    @else
                        <span class="px-3 py-1.5 text-xs font-bold text-slate-300 bg-slate-50 border border-slate-200 rounded-lg cursor-not-allowed">›</span>
                    @endif
                </div>
            </div>
            @endif

        </div>

    </div>
</div>
@endsection
