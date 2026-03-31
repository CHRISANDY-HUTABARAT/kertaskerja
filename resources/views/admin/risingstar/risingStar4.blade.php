@extends('layouts.app')

@section('title', 'Admin - Rising Star 4')

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
                            <span class="text-red-600">Rising</span> Star 4
                        </h1>
                        <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-tight">Kelola data Aosodomoro — Government</p>
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

        {{-- ══ FLASH MESSAGE ══ --}}
        @if(session('success'))
        <div class="flex items-center space-x-3 bg-green-50 border border-green-200 text-green-800 px-5 py-3.5 mb-6 rounded-xl text-sm font-semibold">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        {{-- ══ TAB SWITCHER ══ --}}
        <div class="flex space-x-1 bg-slate-100 p-1 rounded-xl mb-6 w-fit">
            <button onclick="switchTab('input')" id="tab-input"
                class="px-5 py-2 text-xs font-black uppercase tracking-wider rounded-lg transition-all duration-200 bg-white text-slate-900 shadow-sm">
                Input Data
            </button>
            <button onclick="switchTab('ringkasan')" id="tab-ringkasan"
                class="px-5 py-2 text-xs font-black uppercase tracking-wider rounded-lg transition-all duration-200 text-slate-500 hover:text-slate-700">
                Status Terbaru
            </button>
        </div>

        {{-- ══ INPUT FORM ══ --}}
        <div id="panel-input">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                <div class="px-8 py-5 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                        <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Input  Commitment</h2>
                    </div>
                </div>
                <div class="p-8">
                    <form action="{{ route('admin.rising-star.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">

                            {{-- Segment --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Segment</label>
                                <select name="segment" required
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors bg-white">
                                    <option value="">— Pilih Segment —</option>
                                    <option value="gov" {{ old('segment') === 'gov' ? 'selected' : '' }}>Government</option>
                                    <option value="sme" {{ old('segment') === 'sme' ? 'selected' : '' }}>SME</option>
                                </select>
                                @error('segment')
                                    <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Periode --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">
                                    Periode
                                </label>
                                <input type="month" name="periode" value="{{ old('periode', now()->format('Y-m')) }}"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors">
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Status</label>
                                <select name="status" required
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors bg-white">
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- Kategori / Type --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Kategori</label>
                                <select name="type_id" required
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors bg-white">
                                    <option value="">— Pilih Tipe —</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type_id')
                                    <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Commitment --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Komitmen (%)</label>
                                <input type="number" name="commitment" step="any" min="0"
                                    value="{{ old('commitment') }}"
                                    placeholder="cth: 98.50"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors">
                            </div>

                            {{-- Realisasi --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Realisasi (%)</label>
                                <input type="number" step="any" name="real_ratio" value="{{ old('real_ratio') }}" placeholder="cth: 95.50"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors">
                            </div>

                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="reset"
                                class="flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs px-5 py-2.5 rounded-xl transition-all duration-200 uppercase tracking-wider">
                                <span>Reset</span>
                            </button>
                            <button type="submit"
                                class="flex items-center space-x-2 bg-slate-900 hover:bg-red-600 text-white font-bold text-xs px-6 py-2.5 rounded-xl transition-all duration-200 uppercase tracking-wider shadow-md hover:shadow-lg hover:shadow-red-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Simpan / Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ══ PANEL RINGKASAN ══ --}}
        <div id="panel-ringkasan" class="hidden mb-8">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                        <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Status Terbaru Rising Star 4</h2>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Periode</th>
                                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">User</th>
                                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori</th>
                                <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Komitmen (%)</th>
                                <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Realisasi (%)</th>
                                <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($ringkasanAll as $item)
                                @php $isActive = ($item->status ?? 'active') === 'active'; @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-100 rounded-md px-2.5 py-1">
                                            {{ $item->periode ? \Carbon\Carbon::parse($item->periode)->translatedFormat('F Y') : '—' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                                                style="background:#fff1f2; border:1px solid #fecdd3;">
                                                <span class="text-[10px] font-black text-red-600">{{ substr($item->user->name ?? 'U', 0, 1) }}</span>
                                            </div>
                                            <span class="text-sm font-semibold text-slate-700">{{ $item->user->name ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-bold rounded-md px-2.5 py-1 text-red-700 bg-red-50 border border-red-200">
                                            {{ $item->type->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-black text-slate-700">
                                        {{ $item->commitment !== null ? number_format($item->commitment, 2) : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-black text-red-600">
                                        {{ $item->real_ratio !== null ? number_format($item->real_ratio, 2) : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($isActive)
                                            <span class="text-xs font-bold text-green-700 bg-green-50 border border-green-200 rounded-md px-2.5 py-1">Active</span>
                                        @else
                                            <span class="text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 rounded-md px-2.5 py-1">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('admin.rising-star.toggleStatus', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                onclick="return confirm('{{ $isActive ? 'Nonaktifkan data ini?' : 'Aktifkan kembali data ini?' }}')"
                                                class="inline-flex items-center space-x-1.5 text-xs font-bold px-3 py-1.5 rounded-lg border transition-all duration-200
                                                    {{ $isActive
                                                        ? 'text-amber-700 hover:text-white border-amber-200 hover:border-amber-500 bg-amber-50 hover:bg-amber-500'
                                                        : 'text-green-700 hover:text-white border-green-200 hover:border-green-500 bg-green-50 hover:bg-green-500' }}">
                                                {{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-16 text-center">
                                        <p class="text-sm font-bold text-slate-400">Belum ada data</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ══ DATA TABLE ══ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-8 py-5 border-b border-slate-100">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center space-x-3">
                    <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                    <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Data Rising Star 4 — Aosodomoro</h2>
                </div>
                <span class="text-xs font-bold text-slate-400 bg-slate-50 border border-slate-200 rounded-full px-3 py-1">
                    {{ $rstars->total() }} records
                </span>
            </div>
            <form method="GET" action="{{ route('admin.rising-star4') }}" class="grid grid-cols-4 gap-3">
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
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Kategori</label>
                    <select name="type_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:border-red-400 bg-white">
                        <option value="">Semua Kategori</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ ($selectedType ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 invisible">Reset</label>
                    <a href="{{ route('admin.rising-star4') }}"
                        class="flex items-center justify-center w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-lg transition-colors uppercase tracking-wider">
                        Reset Filter
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto bg-white">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">No</th>
                        <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal Input</th>
                        <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">User</th>
                        <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori</th>
                        <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Periode</th>
                        <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Komitmen (%)</th>
                        <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Realisasi (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rstars as $item)
                    <tr class="bg-white hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-slate-400">{{ $rstars->firstItem() + $loop->index }}</td>
                        <td class="px-6 py-4 text-sm text-slate-400 whitespace-nowrap">{{ $item->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                                    style="background:#fff1f2; border:1px solid #fecdd3;">
                                    <span class="text-[10px] font-black text-red-600">{{ substr($item->user->name ?? 'U', 0, 1) }}</span>
                                </div>
                                <span class="text-sm font-semibold text-slate-700">{{ $item->user->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold rounded-md px-2.5 py-1 text-red-700 bg-red-50 border border-red-200">
                                {{ $item->type->name ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-100 rounded-md px-2.5 py-1">
                                {{ $item->periode ? \Carbon\Carbon::parse($item->periode)->format('M Y') : '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->commitment !== null)
                                <span class="text-sm font-black text-slate-800">{{ number_format($item->commitment, 2) }}</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->real_ratio !== null)
                                <span class="text-sm font-black text-red-600">{{ number_format($item->real_ratio, 2) }}</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-16 text-center bg-white">
                            <svg class="mx-auto w-10 h-10 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm font-bold text-slate-400">Belum ada data Rising Star</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rstars->hasPages())
        <div class="px-8 py-4 border-t border-slate-100 flex items-center justify-between bg-white">
            <p class="text-xs font-semibold text-slate-400">
                Menampilkan {{ $rstars->firstItem() }}–{{ $rstars->lastItem() }} dari {{ $rstars->total() }} data
            </p>
            <div class="flex items-center gap-1">
                @if($rstars->onFirstPage())
                    <span class="px-3 py-1.5 text-xs font-bold text-slate-300 bg-slate-50 border border-slate-200 rounded-lg cursor-not-allowed">‹</span>
                @else
                    <a href="{{ $rstars->previousPageUrl() }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">‹</a>
                @endif
                @foreach($rstars->getUrlRange(1, $rstars->lastPage()) as $page => $url)
                    @if($page == $rstars->currentPage())
                        <span class="px-3 py-1.5 text-xs font-bold text-white bg-slate-900 border border-slate-900 rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">{{ $page }}</a>
                    @endif
                @endforeach
                @if($rstars->hasMorePages())
                    <a href="{{ $rstars->nextPageUrl() }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">›</a>
                @else
                    <span class="px-3 py-1.5 text-xs font-bold text-slate-300 bg-slate-50 border border-slate-200 rounded-lg cursor-not-allowed">›</span>
                @endif
            </div>
        </div>
        @endif

</div>

<script>
    function switchTab(tab) {
        const panels = ['input', 'ringkasan'];
        panels.forEach(function(p) {
            document.getElementById('panel-' + p).classList.toggle('hidden', p !== tab);
            const btn = document.getElementById('tab-' + p);
            if (p === tab) {
                btn.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
                btn.classList.remove('text-slate-500');
            } else {
                btn.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
                btn.classList.add('text-slate-500');
            }
        });
    }
</script>

@endsection
