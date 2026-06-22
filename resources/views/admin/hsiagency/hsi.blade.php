@extends('layouts.app')

@section('title', 'Scaling Sales Product - Admin')

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
                        <p class="text-[10px] font-black tracking-[0.3em] text-red-600 uppercase mb-1">Witel Sumut</p>
                        <h1 class="text-2xl font-black tracking-tight text-slate-900 leading-none uppercase">
                            Scaling <span class="text-red-600">Sales Product</span>
                        </h1>
                        <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-tight">Admin input Commitment · Realisasi (SSL)</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.index') }}"
                        class="flex items-center space-x-2.5 bg-white border-2 border-slate-900 hover:bg-slate-900 text-slate-900 hover:text-white px-6 py-3 rounded-xl font-black text-xs transition-all duration-300 shadow-sm uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        {{-- ══ FLASH MESSAGES ══ --}}
        @if(session('success'))
        <div class="flex items-center space-x-3 bg-green-50 border border-green-200 text-green-800 px-5 py-3.5 mb-6 rounded-xl text-sm font-semibold">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-center space-x-3 bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 mb-6 rounded-xl text-sm font-semibold">
            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- ══ INPUT FORM ══ --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 mb-8">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-1 h-6 bg-red-600 rounded-full"></div>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Input Data</h2>
            </div>

            <form action="{{ route('admin.hsi-agency.store') }}" method="POST">
                @csrf
                <!-- <input type="hidden" name="type" value="Sales HSI Non AM Non Telda"> -->

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    {{-- Periode --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Periode</label>
                        <input type="month" name="periode" required value="{{ old('periode', request('periode', date('Y-m'))) }}" onchange="window.location.href='?periode=' + this.value"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors">
                        @error('periode')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Segment</label>
                        <select name="segment" required
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors bg-white">
                            <option value="Government" {{ old('segment') === 'Government' ? 'selected' : '' }}>Government</option>
                            <option value="Private" {{ old('segment') === 'Private' ? 'selected' : '' }}>Private</option>
                            <option value="SOE" {{ old('segment') === 'SOE' ? 'selected' : '' }}>SOE</option>
                            <option value="SME" {{ old('segment') === 'SME' ? 'selected' : '' }}>SME</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Type</label>
                        <select name="type" required
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors bg-white">
                            <option value="HSI" {{ old('type') === 'HSI' ? 'selected' : '' }}>HSI</option>
                            <option value="Wi-Fi" {{ old('type') === 'Wi-Fi' ? 'selected' : '' }}>Wi-Fi</option>
                            <option value="Bandwidth" {{ old('type') === 'Bandwidth' ? 'selected' : '' }}>Bandwidth</option>
                        </select>
                    </div>

                    {{-- Commitment --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">
                            Commitment (SSL)
                        </label>
                        <input type="number" name="commitment" id="hsi_commitment"
                            placeholder="Contoh: 150"
                            min="0"
                            value="{{ old('commitment') }}"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors">
                        @error('commitment')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Real — disabled if commitment empty --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">
                            Realisasi (SSL)
                        </label>
                        <input type="number" name="real_ratio" id="hsi_real"
                            placeholder="Contoh: 145"
                            min="0"
                            value="{{ old('real_ratio') }}"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors">
                        @error('real_ratio')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <!-- <p id="hsi_real_hint" class="mt-1 text-xs text-amber-500 font-medium {{ ($existing && !is_null($existing->commitment)) ? 'hidden' : '' }}">
                            ⚠ Input Commitment terlebih dahulu
                        </p> -->
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="flex items-center space-x-2 bg-slate-900 hover:bg-red-600 text-white font-bold text-xs px-6 py-2.5 rounded-lg transition-all duration-200 uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Simpan Data</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- ══ DATA TABLE ══ --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-8 py-5 border-b border-slate-100">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center space-x-3">
                        <div class="w-1 h-6 bg-red-600 rounded-full"></div>
                        <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Riwayat Data Sales Product</h2>
                    </div>
                    <span class="text-xs font-bold text-slate-400 bg-slate-50 border border-slate-200 rounded-full px-3 py-1">
                        {{ $hsi->total() }} records
                    </span>
                </div>

                <form method="GET" action="{{ route('admin.hsi-agency') }}" class="grid grid-cols-3 gap-3">
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
                        <a href="{{ route('admin.hsi-agency') }}"
                            class="flex items-center justify-center w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-lg transition-colors uppercase tracking-wider">
                            Reset Filter
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
                            <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipe</th>
                            <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Segment</th>
                            <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Komitmen (SSL)</th>
                            <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Realisasi (SSL)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($hsi as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-slate-400">{{ $hsi->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4 text-sm text-slate-400">{{ $item->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-100 rounded-md px-2.5 py-1">
                                    {{ \Carbon\Carbon::parse($item->periode)->format('M Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-400">{{$item->type}}</td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-400">{{$item->segment}}</td>
                            <td class="px-6 py-4 text-center">
                                @if(!is_null($item->commitment))
                                    <span class="text-sm font-black text-slate-800">{{ number_format($item->commitment, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-300 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(!is_null($item->real_ratio))
                                    <span class="text-sm font-black text-red-600">{{ number_format($item->real_ratio, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-300 text-sm">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <svg class="mx-auto w-10 h-10 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm font-bold text-slate-400">Belum Ada Data Sales Product</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($hsi->hasPages())
            <div class="px-8 py-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-400">
                    Menampilkan {{ $hsi->firstItem() }}–{{ $hsi->lastItem() }} dari {{ $hsi->total() }} data
                </p>
                <div class="flex items-center gap-1">
                    @if($hsi->onFirstPage())
                        <span class="px-3 py-1.5 text-xs font-bold text-slate-300 bg-slate-50 border border-slate-200 rounded-lg cursor-not-allowed">‹</span>
                    @else
                        <a href="{{ $hsi->previousPageUrl() }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">‹</a>
                    @endif
                    @foreach($hsi->getUrlRange(1, $hsi->lastPage()) as $page => $url)
                        @if($page == $hsi->currentPage())
                            <span class="px-3 py-1.5 text-xs font-bold text-white bg-slate-900 border border-slate-900 rounded-lg">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if($hsi->hasMorePages())
                        <a href="{{ $hsi->nextPageUrl() }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">›</a>
                    @else
                        <span class="px-3 py-1.5 text-xs font-bold text-slate-300 bg-slate-50 border border-slate-200 rounded-lg cursor-not-allowed">›</span>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

<script>
    const commitmentInput = document.getElementById('hsi_commitment');
    const realInput       = document.getElementById('hsi_real');
    const realHint        = document.getElementById('hsi_real_hint');

    function toggleReal() {
        const hasValue = commitmentInput.value.trim() !== '';
        realInput.disabled = !hasValue;
        realHint.classList.toggle('hidden', hasValue);
        if (!hasValue) realInput.value = '';
    }

    commitmentInput.addEventListener('input', toggleReal);
</script>
@endsection
