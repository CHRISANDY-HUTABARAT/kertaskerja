@extends('layouts.app')

@section('title', 'SOE')

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
                                LOP Qualified
                                <span class="text-red-600">— SOE</span>
                            </h1>
                            <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-tight">Scaling Management
                                System</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.scalling.soe') }}"
                            class="flex items-center space-x-2.5 bg-white border-2 border-slate-900 hover:bg-slate-900 text-slate-900 hover:text-white px-6 py-3 rounded-xl font-black text-xs transition-all duration-300 shadow-sm uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Back</span>
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

            {{-- ══ ACTION BUTTONS ══ --}}
            <div class="grid grid-cols gap-4 mb-8">
                <a href="{{ route('admin.progress', ['segment' => 'soe', 'type' => 'qualified']) }}"
                    class="group bg-white rounded-xl border border-slate-200 hover:border-red-300 hover:shadow-md transition-all duration-200 px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                            style="background:#fff1f2; border:1.5px solid #fecdd3;">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-black text-slate-900 text-sm">Lihat Progress</p>
                            <p class="text-xs text-slate-400 font-medium mt-0.5">Progress yang diupdate user</p>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-red-500 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols gap-4 mb-8">
                <a onclick="document.getElementById('modalAddLop').classList.remove('hidden')"
                    class="group bg-white rounded-xl border border-slate-200 hover:border-red-300 hover:shadow-md transition-all duration-200 px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                            style="background:#fff1f2; border:1.5px solid #fecdd3;">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-black text-slate-900 text-sm">Tambah Project</p>
                            <p class="text-xs text-slate-400 font-medium mt-0.5">Tambah data project baru</p>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-red-500 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            {{-- ══ UPLOAD FILE ══ --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 mb-8">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-1 h-6 bg-red-600 rounded-full"></div>
                    <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Upload File</h2>
                </div>

                <form action="{{ route('admin.scalling.soe.qualified.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                        <input type="hidden" name="type" value="qualified">
                        <input type="hidden" name="segment" value="soe">
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Periode</label>
                            <input type="month" name="periode" required value="{{ date('Y-m') }}"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors">
                            @error('periode')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">File
                                Excel</label>
                            <label
                                class="flex items-center space-x-3 w-full px-4 py-2.5 border border-dashed border-slate-300 hover:border-red-400 rounded-lg cursor-pointer transition-colors">
                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <span id="file-name-text" class="text-sm font-semibold text-slate-400">Pilih file (.xlsx,
                                    .xls, .csv)</span>
                                <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="sr-only"
                                    onchange="displayFileName(this)">
                            </label>
                            @error('excel_file')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="flex items-center space-x-2 bg-slate-900 hover:bg-red-600 text-white font-bold text-xs px-6 py-2.5 rounded-lg transition-all duration-200 uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span>Upload</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- ══ TABEL RIWAYAT ══ --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-1 h-6 bg-red-600 rounded-full"></div>
                        <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">Riwayat Upload</h2>
                    </div>
                    <span
                        class="text-xs font-bold text-slate-400 bg-slate-50 border border-slate-200 rounded-full px-3 py-1">
                        {{ $logs->total() }} uploads
                    </span>
                </div>

                @if($logs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        File Name</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Admin</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Periode</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Rows</th>
                                    <th
                                        class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($logs as $data)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span
                                                    class="text-sm font-semibold text-slate-700">{{ $data->original_filename }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-slate-600">{{ $data->uploaded_by }}</td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="text-xs font-bold text-red-600 bg-red-50 border border-red-100 rounded-md px-2.5 py-1">
                                                {{ $data->periode ? \Carbon\Carbon::parse($data->periode)->format('M Y') : '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php $isActive = ($data->status ?? 'active') === 'active'; @endphp
                                            @if($isActive)
                                                <span
                                                    class="text-xs font-bold text-green-700 bg-green-50 border border-green-200 rounded-md px-2.5 py-1">
                                                    Active
                                                </span>
                                            @else
                                                <span
                                                    class="text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 rounded-md px-2.5 py-1">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs font-bold text-slate-500 bg-slate-100 rounded-md px-2.5 py-1">
                                                {{ $data->total_rows_imported ?? 0 }} rows
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center space-x-2">
                                                {{-- Toggle Status --}}
                                                <form action="{{ route('admin.scalling.toggle-status', $data->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        onclick="return confirm('{{ $isActive ? 'Nonaktifkan file ini? User tidak akan bisa mengedit data.' : 'Aktifkan kembali file ini?' }}')"
                                                        class="inline-flex items-center space-x-1.5 text-xs font-bold px-3 py-1.5 rounded-lg border transition-all duration-200
                                                            {{ $isActive
                                                                ? 'text-amber-700 hover:text-white border-amber-200 hover:border-amber-500 bg-amber-50 hover:bg-amber-500'
                                                                : 'text-green-700 hover:text-white border-green-200 hover:border-green-500 bg-green-50 hover:bg-green-500' }}">
                                                        @if($isActive)
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        </svg>
                                                        <span>Nonaktifkan</span>
                                                        @else
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span>Aktifkan</span>
                                                        @endif
                                                    </button>
                                                </form>

                                                {{-- Preview --}}
                                                <button onclick="toggleFilePreview({{ $data->id }})"
                                                    class="inline-flex items-center space-x-1.5 text-xs font-bold text-slate-600 hover:text-red-600 border border-slate-200 hover:border-red-300 bg-white hover:bg-red-50 px-3 py-1.5 rounded-lg transition-all duration-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    <span>Preview</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- Preview Row --}}
                                    <tr id="preview-{{ $data->id }}" class="hidden">
                                        <td colspan="6" class="px-6 py-4 bg-slate-50">
                                            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                                                <div class="px-5 py-3 border-b border-slate-100 flex items-center space-x-2">
                                                    <div class="w-1 h-4 bg-red-500 rounded-full"></div>
                                                    <span class="text-xs font-black text-slate-600 uppercase tracking-widest">File
                                                        Preview</span>
                                                </div>
                                                @php
                                                    $previewRows = $data->scallingData()->limit(20)->get();
                                                    $hasPreview = $previewRows->isNotEmpty();
                                                @endphp

                                                @if($hasPreview)
                                                    <div class="overflow-x-auto">
                                                        <table class="min-w-full text-xs">
                                                            <thead class="bg-slate-50 border-b border-slate-100">
                                                                <tr>
                                                                    @php
                                                                        $first = $previewRows->first()->toArray();
                                                                        $excluded = ['id', 'imports_log_id', 'created_at', 'updated_at', 'is_manual']; // tambahkan kolom lain kalau perlu
                                                                        $headers = array_diff(array_keys($first), $excluded);
                                                                    @endphp
                                                                    @foreach($headers as $header)
                                                                        <th
                                                                            class="px-4 py-2.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                                            {{ $header }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-slate-100">
                                                                @foreach($previewRows as $row)
                                                                    <tr class="hover:bg-slate-50">
                                                                        @foreach($headers as $field)
                                                                            @if($field === 'est_nilai_bc')
                                                                            <td class="px-4 py-2.5 text-slate-700 font-bold">
                                                                                {{ number_format($row->est_nilai_bc, 0, ',', '.') }}
                                                                            </td>
                                                                            @else
                                                                                <td class="px-4 py-2.5 text-slate-700">{{ $row->{$field} }}</td>
                                                                            @endif
                                                                        @endforeach
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @if($previewRows->count() > 5)
                                                        <p
                                                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center py-3 border-t border-slate-100">
                                                            Menampilkan {{ $data->scallingData()->count() }} baris
                                                        </p>
                                                    @endif
                                                @else
                                                    <p class="text-sm text-slate-400 text-center py-6">No data available</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($projects->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100">
                            {{ $logs->links() }}
                        </div>
                    @endif

                @else
                    <div class="text-center py-16">
                        <svg class="mx-auto w-10 h-10 text-slate-200 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm font-bold text-slate-400">Belum ada upload</p>
                        <p class="text-xs text-slate-300 mt-1">Upload file Excel untuk memulai</p>
                    </div>
                @endif
            </div>
        </div>

    {{-- Modal Tambah LOP --}}
    <div id="modalAddLop" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background: rgba(0,0,0,0.5);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl relative overflow-hidden">

            {{-- Modal header --}}
            <div class="absolute top-0 left-0 right-0 h-1" style="background: linear-gradient(90deg, #dc2626, #ef4444, #dc2626);"></div>
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-black text-slate-900 uppercase tracking-wide">Tambah LOP Qualified</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">SOE — Scalling Qualified</p>
                </div>
                <button onclick="document.getElementById('modalAddLop').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-red-100 hover:text-red-600 text-slate-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal body --}}
            <form action="{{ route('admin.scalling.soe.qualified.storeData') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="qualified">
                <input type="hidden" name="status" value="active">
                <input type="hidden" name="segment" value="soe">
                {{-- Kirim periode yang sedang aktif --}}
                <input type="hidden" name="periode" value="{{ $currentPeriode }}">

                <div class="px-8 py-6 grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[60vh] overflow-y-auto">

                    {{-- Project --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Project Name <span class="text-red-500">*</span></label>
                        <input type="text" name="project" value="{{ old('project') }}" placeholder="cth: Project ABC"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors" required>
                    </div>

                    {{-- Periode --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Periode <span class="text-red-500">*</span></label>
                        <input type="month" name="periode" value="{{ date('Y-m') }}" placeholder="cth: 2023-01" required
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors" required>
                    </div>

                    {{-- ID LOP --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">ID LOP</label>
                        <input type="text" name="id_lop" value="{{ old('id_lop') }}" placeholder="cth: LOP-001"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors" >
                    </div>

                    {{-- CC --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">CC <span class="text-red-500">*</span></label>
                        <input type="text" name="cc" value="{{ old('cc') }}" placeholder="cth: Provsu"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors" required>
                    </div>

                    {{-- NIPNAS --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">NIPNAS</label>
                        <input type="text" name="nipnas" value="{{ old('nipnas') }}" placeholder="cth: 1234567890"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors" >
                    </div>

                    {{-- AM --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">AM <span class="text-red-500">*</span></label>
                        <input type="text" name="am" value="{{ old('am') }}" placeholder="cth: Frengky Hutajulu"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors" required>
                    </div>

                    {{-- Mitra --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Mitra</label>
                        <input type="text" name="mitra" value="{{ old('mitra') }}" placeholder="cth: Dengan Mitra"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors">
                    </div>

                    {{-- Plan Bulan --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Plan Bulan Bill Comp <span class="text-red-500">*</span></label>
                        <input type="number" name="plan_bulan_billcomp_2025" value="{{ old('plan_bulan_billcomp_2025') }}"
                            placeholder="1-12" min="1" max="12"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors" required>
                    </div>

                    {{-- Est Nilai BC --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Estimasi Nilai BC <span class="text-red-500">*</span></label>
                        <input type="number" name="est_nilai_bc" value="{{ old('est_nilai_bc') }}"
                            placeholder="cth: 1000000" min="0"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition-colors" required>
                    </div>

                </div>

                {{-- Modal footer --}}
                <div class="px-8 py-5 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-xs text-slate-400 font-medium">
                        <span class="font-black text-slate-600"></span>
                    </p>
                    <div class="flex items-center space-x-3">
                        <button type="button"
                            onclick="document.getElementById('modalAddLop').classList.add('hidden')"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-xs rounded-xl transition-all uppercase tracking-wider">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex items-center space-x-2 px-6 py-2.5 bg-slate-900 hover:bg-red-600 text-white font-black text-xs rounded-xl transition-all uppercase tracking-wider shadow-md hover:shadow-red-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Simpan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tutup modal saat klik backdrop --}}
    <script>
    document.getElementById('modalAddLop').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
    </script>
    </div>

    <script>
        function displayFileName(input) {
            const fileName = input.files[0]?.name;
            if (fileName) document.getElementById('file-name-text').textContent = fileName;
        }
        function toggleFilePreview(id) {
            document.getElementById(`preview-${id}`).classList.toggle('hidden');
        }
    </script>
@endsection
