@extends('layouts.app')

@section('title', 'UTIP Management')

@section('content')
    <div class="min-h-screen" style="background:#f1f5f9;">
        <div class="max-w-7xl mx-auto px-8 py-10">

            {{-- ══ HEADER ══ --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-10 py-7 mb-10 relative overflow-hidden">
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
                            <h1 class="text-2xl font-black tracking-tight text-slate-900 leading-none uppercase">UTIP <span
                                    class="text-red-600">Management</span></h1>
                            <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-tight">Unit Target Income
                                Performance</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('dashboard.collection') }}"
                            class="flex items-center space-x-2.5 bg-white border-2 border-slate-900 hover:bg-red-600 hover:border-red-600 text-slate-900 hover:text-white px-6 py-3 rounded-xl font-black text-xs transition-all duration-300 shadow-sm group uppercase tracking-wider">
                            <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
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
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 mb-6 rounded-xl text-sm font-semibold">
                    <p class="font-bold mb-1">Terdapat kesalahan:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ══ FORM REALISASI ══ --}}
            @php $periodeLabel = now()->translatedFormat('F Y'); @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                        <div>
                            <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Input Realisasi UTIP</h2>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">Pilih tipe UTIP lalu catat realisasi harian.</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-black tracking-widest text-red-600 bg-red-50 border border-red-100 rounded-md px-3 py-1 uppercase">
                        {{ $periodeLabel }}
                    </span>
                </div>
                <div class="p-8">
                    <form action="{{ route('collection.utip.storeRealisasi') }}" method="POST">
                        @csrf
                        @php
                            $lockedTypesMap = [];
                            foreach($utips as $utip) {
                                $lockedTypesMap[$utip->type] = false;
                            }
                            // Tambahkan tipe yang locked
                            foreach($lockedTypes as $lt) {
                                $lockedTypesMap[$lt] = true;
                            }
                        @endphp
                        <script>
                            const lockedTypes = @json($lockedTypes);
                        </script>
                        <!-- <input type="hidden" name="type" id="type-input" value="{{ $utips->first()->type ?? '' }}"> -->
                        <div class="max-w-md mx-auto space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 text-center">Tipe UTIP</label>
                                <select name="type" required onchange="checkUtipLocked(this.value)"
                                    class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors bg-white">
                                    @if($utips->isEmpty())
                                        <option value="" disabled selected>Tipe UTIP belum ditambahkan</option>
                                    @endif
                                    @foreach($utips as $utip)
                                        <option value="{{ $utip->type }}" {{ $utip->type == ($utips->first()->type ?? '') ? 'selected' : '' }}>
                                            {{ $utip->type }}
                                        </option>
                                    @endforeach
                                </select>

                                {{-- Warning locked --}}
                                <div id="utip-locked-warning" class="hidden flex items-center space-x-2 text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <p class="text-xs font-bold">Tipe UTIP ini sudah dinonaktifkan.</p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 text-center">Realisasi (Rp)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-black text-2xl">Rp</span>
                                    </div>
                                    <input type="number" step="1" name="ratio_aktual" required
                                        placeholder="0" min="0"
                                        class="w-full pl-16 pr-6 py-5 text-4xl font-black text-red-600 border-2 border-slate-200 rounded-xl bg-slate-50 text-center focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors">
                                </div>
                            </div>
                            <div class="flex items-center justify-center pt-2">
                                <button type="submit" id="btn-utip-simpan"
                                    class="flex items-center space-x-2 bg-slate-900 hover:bg-red-600 text-white font-bold text-xs px-6 py-3 rounded-xl transition-all duration-200 uppercase tracking-wider shadow-md hover:shadow-lg hover:shadow-red-200">
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

            {{-- ══ HISTORY TABLE ══ --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100">
                    <div class="flex items-center space-x-3 mb-5">
                        <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                        <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Riwayat Aktivitas UTIP</h2>
                    </div>
                    <form method="GET" action="{{ route('collection.utip') }}" class="grid grid-cols-5 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Tipe</label>
                            <select name="tipe" onchange="this.form.submit()"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:border-red-400 bg-white">
                                <option value="">Semua Tipe</option>
                                @foreach($tipes as $t)
                                    <option value="{{ $t }}" {{ $selectedTipe == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Bulan</label>
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
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Tahun</label>
                            <select name="tahun" onchange="this.form.submit()"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:border-red-400 bg-white">
                                <option value="">Semua Tahun</option>
                                @foreach($tahuns as $t)
                                    <option value="{{ $t }}" {{ ($selectedTahun ?? '') == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Cari</label>
                            <input type="text" name="cari" value="{{ $selectedCari ?? '' }}" placeholder="Teks atau angka..."
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:border-red-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 invisible">Reset</label>
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="flex-1 px-4 py-2.5 bg-slate-900 hover:bg-red-600 text-white font-bold text-xs rounded-lg transition-colors uppercase tracking-wider">
                                    Cari
                                </button>
                                <a href="{{ route('collection.utip') }}"
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
                                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipe</th>
                                <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Data Prospek</th>
                                <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Komitmen</th>
                                <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Realisasi </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($activities as $activity)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-slate-400">{{ $activities->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-500">
                                        {{ $activity->created_at->translatedFormat('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                         <span class="text-xs font-bold rounded-md px-2.5 py-1 text-red-700 bg-red-50 border border-red-200">{{ $activity->type }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-black text-slate-700">
                                        {{ $activity->plan !== null ? 'Rp'.number_format($activity->plan, 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-black text-slate-700">
                                        {{ $activity->commitment !== null ? 'Rp'.number_format($activity->commitment, 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-black text-red-600">
                                        {{ $activity->real_ratio !== null ? 'Rp'.number_format($activity->real_ratio, 0, ',', '.') : '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-16 text-center">
                                        <svg class="mx-auto w-10 h-10 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="text-sm font-bold text-slate-400">Belum Ada Data UTIP</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($activities->hasPages())
                <div class="px-8 py-4 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-xs font-semibold text-slate-400">
                        Menampilkan {{ $activities->firstItem() }}–{{ $activities->lastItem() }} dari {{ $activities->total() }} data
                    </p>
                    <div class="flex items-center gap-1">
                        @if($activities->onFirstPage())
                            <span class="px-3 py-1.5 text-xs font-bold text-slate-300 bg-slate-50 border border-slate-200 rounded-lg cursor-not-allowed">‹</span>
                        @else
                            <a href="{{ $activities->previousPageUrl() }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">‹</a>
                        @endif
                        @foreach($activities->getUrlRange(1, $activities->lastPage()) as $page => $url)
                            @if($page == $activities->currentPage())
                                <span class="px-3 py-1.5 text-xs font-bold text-white bg-slate-900 border border-slate-900 rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($activities->hasMorePages())
                            <a href="{{ $activities->nextPageUrl() }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">›</a>
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
        function checkUtipLocked(type) {
            const btn     = document.getElementById('btn-utip-simpan');
            const warning = document.getElementById('utip-locked-warning');
            const isLocked = type && lockedTypes.includes(type);

            if (isLocked) {
                warning.classList.remove('hidden');
                btn.disabled = true;
                btn.className = 'flex items-center space-x-2 bg-slate-400 cursor-not-allowed text-white font-bold text-xs px-6 py-3 rounded-xl uppercase tracking-wider shadow-md transition-all duration-200';
            } else {
                warning.classList.add('hidden');
                btn.disabled = false;
                btn.className = 'flex items-center space-x-2 bg-slate-900 hover:bg-red-600 text-white font-bold text-xs px-6 py-3 rounded-xl transition-all duration-200 uppercase tracking-wider shadow-md hover:shadow-lg hover:shadow-red-200';
            }
        }
    </script>
@endsection
