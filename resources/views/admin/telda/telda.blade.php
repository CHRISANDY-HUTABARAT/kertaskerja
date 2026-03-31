@extends('layouts.app')

@section('title', 'Scaling Telda - Admin')

@section('content')
    <div class="min-h-screen" style="background:#f1f5f9;">
        <div class="max-w-7xl mx-auto px-8 py-10">

            {{-- ══ HEADER ══ --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-10 py-7 mb-8 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5"
                    style="background: linear-gradient(90deg, #dc2626, #ef4444, #dc2626);"></div>
                <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full opacity-[0.04]" style="background: #dc2626;">
                </div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <img src="{{ asset('img/Telkom.png') }}" alt="Telkom" class="h-12 w-auto">
                        <div class="w-px h-12 bg-slate-200"></div>
                        <div>
                            <p class="text-[10px] font-black tracking-[0.3em] text-red-600 uppercase mb-1">Witel Sumut</p>
                            <h1 class="text-2xl font-black tracking-tight text-slate-900 leading-none uppercase">
                                Scaling <span class="text-red-600">Telda</span>
                            </h1>
                            <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-tight">9 Telekomunikasi
                                Daerah · Commitment & Realisasi</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.index') }}"
                            class="flex items-center space-x-2.5 bg-white border-2 border-slate-900 hover:bg-slate-900 text-slate-900 hover:text-white px-6 py-3 rounded-xl font-black text-xs transition-all duration-300 shadow-sm uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Back to Dashboard</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="group flex items-center space-x-2.5 bg-slate-900 hover:bg-red-600 text-white font-bold text-sm px-5 py-3 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-red-200">
                                <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-12" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ══ FLASH MESSAGES ══ --}}
            @if(session('success'))
                <div
                    class="flex items-center space-x-3 bg-green-50 border border-green-200 text-green-800 px-5 py-3.5 mb-6 rounded-xl text-sm font-semibold">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div
                    class="flex items-center space-x-3 bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 mb-6 rounded-xl text-sm font-semibold">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- ══ INPUT FORM — TELDA CARDS ══ --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-1 h-6 bg-red-600 rounded-full"></div>
                        <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">
                            Input Data Telda —
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedPeriode)->format('F Y') }}
                        </h2>
                    </div>
                    <p class="text-xs text-amber-500 font-medium">
                        ⚠ Realisasi hanya aktif jika Commitment sudah diisi
                    </p>
                </div>

                <form action="{{ route('admin.telda.store') }}" method="POST" id="teldaForm">
                    @csrf
                    <div class="mb-6 max-w-xs">
                        <label
                            class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Periode</label>
                        <input type="month" name="periode" required value="{{ $selectedPeriode }}"
                            onchange="window.location.href = '?periode=' + this.value"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors">
                        @error('periode')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-6">
                        @foreach($teldas as $regionKey => $label)
                            @php
                                $record = $existingByRegion[$regionKey] ?? null;
                                $commitmentVal = $record ? $record->commitment : null;
                                $realVal = $record ? $record->real_ratio : null;
                                $realDisabled = is_null($commitmentVal);
                            @endphp
                            <div
                                class="border border-slate-200 rounded-xl p-5 hover:border-red-200 hover:shadow-sm transition-all duration-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">{{ $label }}</h3>
                                    </div>
                                    @if($record)
                                        <span
                                            class="text-[9px] font-black text-blue-600 bg-blue-50 border border-blue-100 rounded px-1.5 py-0.5 uppercase">Update</span>
                                    @endif
                                </div>

                                <input type="hidden" name="regions[{{ $regionKey }}][region]" value="{{ $regionKey }}"
                                    class="telda-region-key">
                                <input type="hidden" name="regions[{{ $regionKey }}][status]" value="active">

                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Komitmen (Rp)</label>
                                <input type="number" name="regions[{{ $regionKey }}][commitment]"
                                    id="{{ $regionKey }}_commitment" value="{{ $commitmentVal ?? '' }}"
                                    placeholder="1000000" min="0" step="0.01" data-telda="{{ $regionKey }}"
                                    data-original="{{ $commitmentVal ?? '' }}"
                                    class="telda-commitment w-full px-3 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors">

                                <div>
                                    <label
                                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Realisasi (Rp)</label>
                                    <input type="number" name="regions[{{ $regionKey }}][real_ratio]" id="{{ $regionKey }}_real"
                                        value="{{ $realVal ?? '' }}"
                                        placeholder="{{ $realDisabled ? 'Isi commitment dulu' : '1000000' }}" min="0"
                                        step="0.01" inputmode="decimal" {{ $realDisabled ? 'disabled' : '' }}
                                        data-original="{{ $realVal ?? '' }}"
                                        class="telda-real w-full px-3 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors disabled:bg-slate-50 disabled:text-slate-300 disabled:cursor-not-allowed">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="flex items-center space-x-2 bg-slate-900 hover:bg-red-600 text-white font-bold text-xs px-6 py-2.5 rounded-lg transition-all duration-200 uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Semua Data Telda</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                <div class="px-8 py-5 border-b border-slate-100">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center space-x-3">
                            <div class="w-1 h-6 bg-red-600 rounded-full"></div>
                            <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Riwayat Data Telda</h2>
                        </div>
                        <span
                            class="text-xs font-bold text-slate-400 bg-slate-50 border border-slate-200 rounded-full px-3 py-1">
                            {{ $history->total() }} records
                        </span>
                    </div>

                    <form method="GET" action="{{ route('admin.telda.index') }}" class="grid grid-cols-4 gap-3">
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Bulan</label>
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
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Tahun</label>
                            <select name="tahun" onchange="this.form.submit()"
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:border-red-400 bg-white">
                                <option value="">Semua Tahun</option>
                                @foreach($tahuns as $t)
                                    <option value="{{ $t }}" {{ ($selectedTahun ?? '') == $t ? 'selected' : '' }}>{{ $t }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Region</label>
                            <select name="region" onchange="this.form.submit()"
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:border-red-400 bg-white">
                                <option value="">Semua Region</option>
                                @foreach($teldas as $key => $label)
                                    <option value="{{ $key }}" {{ ($selectedRegion ?? '') === $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 invisible">Reset</label>
                            <a href="{{ route('admin.telda.index') }}"
                                class="flex items-center justify-center w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-lg transition-colors uppercase tracking-wider">
                                Reset Filter
                            </a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th
                                    class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    No</th>
                                <th
                                    class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Tanggal Input</th>
                                <th
                                    class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Periode</th>
                                <th
                                    class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Region</th>
                                <th
                                    class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Komitmen</th>
                                <th
                                    class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Realisasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($history as $item)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 text-sm font-bold text-slate-400">
                                        {{ $history->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-400 whitespace-nowrap">
                                        {{ $item->created_at->format('d M Y, H:i') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            class="text-xs font-bold text-red-600 bg-red-50 border border-red-100 rounded-md px-2.5 py-1">
                                            {{ \Carbon\Carbon::parse($item->periode)->format('M Y') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-700">
                                        {{ $teldas[$item->region] ?? $item->region }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if(!is_null($item->commitment))
                                            <span
                                                class="text-sm font-black text-slate-800">Rp{{ number_format($item->commitment, 2, ',', '.') }}</span>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if(!is_null($item->real_ratio))
                                            <span
                                                class="text-sm font-black text-red-600">Rp{{ number_format($item->real_ratio, 2, ',', '.') }}</span>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-16 text-center">
                                        <svg class="mx-auto w-10 h-10 text-slate-200 mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm font-bold text-slate-400">Belum Ada Data Telda</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($history->hasPages())
                    <div class="px-8 py-4 border-t border-slate-100 flex items-center justify-between">
                        <p class="text-xs font-semibold text-slate-400">
                            Menampilkan {{ $history->firstItem() }}–{{ $history->lastItem() }} dari {{ $history->total() }} data
                        </p>
                        <div class="flex items-center gap-1">
                            @if($history->onFirstPage())
                                <span
                                    class="px-3 py-1.5 text-xs font-bold text-slate-300 bg-slate-50 border border-slate-200 rounded-lg cursor-not-allowed">‹</span>
                            @else
                                <a href="{{ $history->previousPageUrl() }}"
                                    class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">‹</a>
                            @endif
                            @foreach($history->getUrlRange(1, $history->lastPage()) as $page => $url)
                                @if($page == $history->currentPage())
                                    <span
                                        class="px-3 py-1.5 text-xs font-bold text-white bg-slate-900 border border-slate-900 rounded-lg">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}"
                                        class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach
                            @if($history->hasMorePages())
                                <a href="{{ $history->nextPageUrl() }}"
                                    class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">›</a>
                            @else
                                <span
                                    class="px-3 py-1.5 text-xs font-bold text-slate-300 bg-slate-50 border border-slate-200 rounded-lg cursor-not-allowed">›</span>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>

    <script>
        document.querySelectorAll('.telda-commitment').forEach(function (input) {
            input.addEventListener('input', function () {
                const telda = this.getAttribute('data-telda');
                const realEl = document.getElementById(telda + '_real');
                const hasVal = this.value.trim() !== '';
                realEl.disabled = !hasVal;
                realEl.placeholder = hasVal ? 'Masukkan realisasi' : 'Isi commitment dulu';
                if (!hasVal) realEl.value = '';
            });
        });

        document.getElementById('teldaForm').addEventListener('submit', function () {
            document.querySelectorAll('.telda-commitment').forEach(function (commitEl) {
                const telda = commitEl.getAttribute('data-telda');
                const realEl = document.getElementById(telda + '_real');
                const commitVal = commitEl.value.trim();
                const realVal = realEl.value.trim();
                const commitOrig = commitEl.getAttribute('data-original').trim();
                const realOrig = realEl.getAttribute('data-original').trim();

                const commitChanged = commitVal !== commitOrig;
                const realChanged = realVal !== realOrig;

                if (!commitChanged && !realChanged) {
                    commitEl.disabled = true;
                    realEl.disabled = true;
                    const regionInput = document.querySelector(`input[name="regions[${telda}][region]"]`);
                    const statusInput = document.querySelector(`input[name="regions[${telda}][status]"]`);
                    if (regionInput) regionInput.disabled = true;
                    if (statusInput) statusInput.disabled = true;
                }
            });
        });
    </script>
@endsection
