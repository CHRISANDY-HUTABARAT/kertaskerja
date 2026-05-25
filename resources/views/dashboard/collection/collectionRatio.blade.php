@extends('layouts.app')

@section('title', 'Collection - Collection Ratio Management')

@section('content')
    <div class="min-h-screen" style="background:#f1f5f9;">
        <div class="max-w-7xl mx-auto px-8 py-10">

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
                                <span class="text-red-600">Collection</span> Ratio
                            </h1>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('dashboard.collection') }}"
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

            @if(session('success'))
                <div id="flash-message"
                    class="flex items-center space-x-3 bg-green-50 border border-green-200 text-green-800 px-5 py-3.5 mb-6 rounded-xl text-sm font-semibold duration-300">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

        {{-- ══ PERIODE SELECTOR ══ --}}
        @php
            $periodeLabel     = \Carbon\Carbon::createFromFormat('Y-m', $selectedPeriode)->translatedFormat('F Y');
            $isCurrentPeriode = $selectedPeriode === now()->format('Y-m');
        @endphp

        <form method="GET" action="{{ route('collection.cr') }}" class="mb-6" id="form-periode">
            @foreach(request()->except('selected_periode') as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endforeach
            <div class="flex items-center space-x-3">
                <label class="text-xs font-black text-slate-500 uppercase tracking-widest">Periode:</label>
                <div class="relative">
                    <select name="selected_periode" onchange="document.getElementById('form-periode').submit()"
                        class="appearance-none text-sm font-bold text-slate-700 bg-white border-2 border-slate-200 hover:border-red-400 rounded-xl pl-4 pr-10 py-2.5 shadow-sm focus:outline-none focus:border-red-400 cursor-pointer transition-colors">
                        @foreach($periodeOptions as $p)
                            <option value="{{ $p }}" {{ $p === $selectedPeriode ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $p)->translatedFormat('F Y') }}
                                {{ $p === now()->format('Y-m') ? '(Berjalan)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
                @if(!$isCurrentPeriode)
                <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-1.5">
                    Melihat periode lampau
                </span>
                @endif
            </div>
        </form>

        {{-- ══ STATUS CARDS ══ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
            @foreach(['Government', 'Private', 'SOE', 'SME'] as $seg)
            @php
                $latest = $latestSeg->where('segment', $seg)->first();
                $pct    = $latest?->real_ratio;
                $kom    = $latest?->commitment;
                $segLocked = in_array($seg, $lockedSegments);
            @endphp
            <div class="bg-white rounded-2xl border-2 {{ $segLocked ? 'border-slate-200 opacity-70' : 'border-slate-100' }} overflow-hidden">
                <div class="h-1" style="background: {{ $segLocked ? '#cbd5e1' : 'linear-gradient(90deg, #dc2626, #ef4444)' }};"></div>
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $seg }}</span>
                        <div class="flex items-center space-x-1">
                            @if($segLocked)
                            <span class="text-[9px] font-black text-slate-400 bg-slate-100 border border-slate-200 rounded px-1.5 py-0.5 uppercase">🔒 Locked</span>
                            @else
                            <span class="text-[10px] font-bold text-slate-300 uppercase">{{ $periodeLabel }}</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Realisasi</p>
                    @if($kom !== null)
                        @if($pct !== null)
                            <p class="text-4xl font-black {{ $segLocked ? 'text-slate-400' : 'text-green-600' }} leading-none mb-4">
                                {{ number_format($pct, 2) }}<span class="text-xl">%</span>
                            </p>
                        @else
                            <p class="text-3xl font-black text-slate-200 leading-none mb-4">—</p>
                        @endif
                    @else
                        <p class="text-3xl font-black text-slate-200 leading-none mb-4">—</p>
                    @endif
                    <div class="border-t border-slate-100 pt-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Komitmen</span>
                            <span class="text-sm font-black {{ $segLocked ? 'text-slate-400' : 'text-red-600' }}">
                                {{ $kom !== null ? number_format($kom, 2).'%' : '0' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ══ FORM INPUT ══ --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
            <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                    <div>
                        <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Input Realisasi Collection Ratio</h2>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">Pilih segment lalu catat realisasi ratio.</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-[10px] font-black tracking-widest text-red-600 bg-red-50 border border-red-100 rounded-md px-3 py-1 uppercase">
                        {{ $periodeLabel }}
                    </span>
                    @if(!$isCurrentPeriode)
                    <span class="text-[10px] font-black tracking-widest text-amber-700 bg-amber-50 border border-amber-200 rounded-md px-3 py-1 uppercase">
                        Periode Lampau
                    </span>
                    @endif
                </div>
            </div>
            <div class="p-8">
                <form action="{{ route('collection.cr.storeRealisasi') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="status" value="active">
                    <input type="hidden" name="commitment" value="">
                    @php
                        $commitmentMap = [];
                        foreach(['Government', 'Private', 'SOE', 'SME'] as $seg) {
                            $commitmentMap[$seg] = $latestSeg->where('segment', $seg)->first()?->commitment;
                        }
                    @endphp
                    <script>
                        const commitmentMap = @json($commitmentMap);
                        const lockedSegments = @json($lockedSegments);  {{-- ← tambahkan --}}
                    </script>
                    <div class="max-w-md mx-auto space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 text-center">Segment</label>
                            <select name="segment" required onchange="checkCommitment(this.value)"
                                class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors bg-white">
                                <option value="">— Pilih Segment —</option>
                                @foreach(['Government', 'Private', 'SOE', 'SME'] as $seg)
                                    <option value="{{ $seg }}" {{ ($selectedSegment ?? '') == $seg ? 'selected' : '' }}>{{ $seg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="segment-locked-warning" class="hidden flex items-center space-x-2 text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <p class="text-xs font-bold">Segment ini sudah dinonaktifkan untuk periode berjalan.</p>
                        </div>
                        <input type="hidden" name="periode" value="{{ $selectedPeriode }}">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 text-center">Realisasi (%)</label>
                            <div class="relative">
                                <input type="number" step="any" name="real_ratio" required
                                    placeholder="8.59" min="0"
                                    class="w-full px-6 py-5 text-4xl font-black text-red-600 border-2 border-slate-200 rounded-xl bg-slate-50 text-center focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors">
                                <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none">
                                    <span class="text-slate-400 font-black text-2xl">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-center pt-2">
                            <button type="submit" id="btn-simpan" disabled
                                class="flex items-center space-x-2 bg-slate-400 cursor-not-allowed text-white font-bold text-xs px-6 py-3 rounded-xl uppercase tracking-wider shadow-md transition-all duration-200"
                                title="Pilih segment terlebih dahulu">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Simpan Realisasi</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center space-x-3">
                            <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                            <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Riwayat Aktivitas
                                Collection Ratio</h2>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('collection.cr') }}" class="grid grid-cols-5 gap-3">
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Segment</label>
                            <select name="segment" onchange="this.form.submit()"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:border-red-400 bg-white">
                                <option value="">Semua Segment</option>
                                @foreach(['Government', 'Private', 'SOE', 'SME'] as $seg)
                                    <option value="{{ $seg }}" {{ ($selectedSegment ?? '') == $seg ? 'selected' : '' }}>{{ $seg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Bulan</label>
                            <select name="bulan" onchange="this.form.submit()"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:border-red-400 bg-white">
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
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:border-red-400 bg-white">
                                <option value="">Semua Tahun</option>
                                @foreach($tahuns as $t)
                                    <option value="{{ $t }}" {{ ($selectedTahun ?? '') == $t ? 'selected' : '' }}>{{ $t }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Cari</label>
                            <input type="text" name="cari" value="{{ $selectedCari ?? '' }}"
                                placeholder="Teks atau angka..."
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:border-red-400">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 invisible">Reset</label>
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="flex-1 px-4 py-2.5 bg-slate-900 hover:bg-red-600 text-white font-bold text-xs rounded-lg transition-colors uppercase tracking-wider">
                                    Cari
                                </button>
                                <a href="{{ route('collection.cr') }}"
                                    class="flex-1 flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-lg transition-colors uppercase tracking-wider">
                                    Reset
                                </a>
                            </div>
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
                                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Segment</th>
                                <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Komitmen (%)</th>
                                <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Realisasi (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($collections as $item)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-slate-400">{{ $collections->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-500">
                                        {{ optional($item->created_at)->translatedFormat('d M Y H:i') ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-600">
                                        {{ $item->periode ? \Carbon\Carbon::parse($item->periode)->translatedFormat('F Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($item->segment)
                                            <span class="text-xs font-bold rounded-md px-2.5 py-1 text-red-700 bg-red-50 border border-red-200">{{ $item->segment }}</span>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center font-black text-slate-700">
                                        {{ $item->commitment !== null ? number_format($item->commitment, 2).'%' : '0' }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-black text-red-600">
                                        {{ $item->real_ratio !== null ? number_format($item->real_ratio, 2).'%' : '0' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-16 text-center">
                                        <svg class="mx-auto w-10 h-10 text-slate-200 mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="text-sm font-bold text-slate-400">Belum Ada Data Collection Ratio</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                 </div>

                @if($collections->hasPages())
                <div class="px-8 py-4 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-xs font-semibold text-slate-400">
                        Menampilkan {{ $collections->firstItem() }}–{{ $collections->lastItem() }} dari {{ $collections->total() }} data
                    </p>
                    <div class="flex items-center gap-1">
                        @if($collections->onFirstPage())
                            <span class="px-3 py-1.5 text-xs font-bold text-slate-300 bg-slate-50 border border-slate-200 rounded-lg cursor-not-allowed">‹</span>
                        @else
                            <a href="{{ $collections->previousPageUrl() }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">‹</a>
                        @endif
                        @foreach($collections->getUrlRange(1, $collections->lastPage()) as $page => $url)
                            @if($page == $collections->currentPage())
                                <span class="px-3 py-1.5 text-xs font-bold text-white bg-slate-900 border border-slate-900 rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($collections->hasMorePages())
                            <a href="{{ $collections->nextPageUrl() }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">›</a>
                        @else
                            <span class="px-3 py-1.5 text-xs font-bold text-slate-300 bg-slate-50 border border-slate-200 rounded-lg cursor-not-allowed">›</span>
                        @endif
                    </div>
                </div>
                @endif

            </div>

        </div>
    </div>

    <script>
        setTimeout(() => {
            const flash = document.getElementById('flash-message');
            if (flash) {
                flash.classList.add('opacity-0');
                setTimeout(() => flash.remove(), 500);
            }
        }, 5000);
        
        // ← Tambahkan ini
        function checkCommitment(segment) {
            const btn     = document.getElementById('btn-simpan');
            const warning = document.getElementById('segment-locked-warning');

            const isLocked      = segment && lockedSegments.includes(segment);
            const hasCommitment = segment && commitmentMap[segment] !== null && commitmentMap[segment] !== undefined;

            // Tampilkan warning jika locked
            if (isLocked) {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }

            // Disable button jika locked ATAU tidak ada commitment
            if (!isLocked && hasCommitment) {
                btn.disabled = false;
                btn.className = 'flex items-center space-x-2 bg-slate-900 hover:bg-red-600 text-white font-bold text-xs px-6 py-3 rounded-xl transition-all duration-200 uppercase tracking-wider shadow-md hover:shadow-lg hover:shadow-red-200 cursor-pointer';
                btn.title = '';
            } else {
                btn.disabled = true;
                btn.className = 'flex items-center space-x-2 bg-slate-400 cursor-not-allowed text-white font-bold text-xs px-6 py-3 rounded-xl uppercase tracking-wider shadow-md transition-all duration-200';
                btn.title = isLocked
                    ? 'Segment ' + segment + ' sudah dinonaktifkan untuk periode ini'
                    : (segment
                        ? 'Segment ' + segment + ' belum memiliki nilai komitmen untuk periode ini'
                        : 'Pilih segment terlebih dahulu');
            }
        }
    </script>
@endsection
