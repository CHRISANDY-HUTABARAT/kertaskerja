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
                    <a href="{{ route('admin.ngtma.index', ['segment' => $segment, 'type' => 'ngtma']) }}"
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
                    Progress Data
                </h2>
            </div>
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari Project, ID LOP, AM, CC..."
                        class="text-xs font-semibold text-slate-700 bg-white border-2 border-slate-200 hover:border-red-400 focus:border-red-400 focus:outline-none rounded-lg pl-8 pr-3 py-1.5 shadow-sm transition-colors w-64">
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
                            <td class="px-3 py-2.5 whitespace-nowrap font-bold text-slate-700 border-r border-slate-100 text-center sticky-col">{{ $row->no }}</td>
                            <td class="px-4 py-2.5 text-slate-600 border-r border-slate-100 font-medium sticky-col">{{ $row->project }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-700 border-r border-slate-100 bg-emerald-50 font-bold sticky-col">{{ $row->id_lop }}</td>
                            <td class="px-3 py-2.5 whitespace-nowrap text-slate-600 border-r border-slate-100 sticky-col">{{ $row->cc }}</td>
                            <td class="px-4 py-2.5 text-slate-600 border-r border-slate-100">{{ $row->am }}</td>
                            <td class="px-4 py-2.5 text-slate-700 border-r border-slate-100 bg-emerald-50 font-semibold">{{ $row->mitra }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 border-r border-slate-100 text-center">{{ $row->plan_bulan_billcomp_2025 }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap font-black text-right text-slate-800 border-r border-slate-100">{{ number_format($row->est_nilai_bc, 0, ',', '.') }}</td>
                            {{-- F0 --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-blue-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-blue-600 rounded cursor-pointer"
                                    data-field="f0_inisiasi_solusi" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}"
                                    {{ $checked('f0_inisiasi_solusi') ? 'checked' : '' }}>
                            </td>
                            {{-- F1 --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-purple-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-purple-600 rounded cursor-pointer"
                                    data-field="f1_tech_budget" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}"
                                    {{ $checked('f1_tech_budget') ? 'checked' : '' }}>
                            </td>
                            {{-- F2 --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_p0_p1" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f2_p0_p1') ? 'checked' : '' }}>

                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_p2" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f2_p2') ? 'checked' : '' }}>

                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_p3" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f2_p3') ? 'checked' : '' }}>

                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_p4" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f2_p4') ? 'checked' : '' }}>

                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_offering" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f2_offering') ? 'checked' : '' }}>

                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_p5" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f2_p5') ? 'checked' : '' }}>

                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-pink-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-pink-600 rounded cursor-pointer" data-field="f2_proposal" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f2_proposal') ? 'checked' : '' }}>
                            </td>
                            {{-- F3 --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-orange-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-orange-600 rounded cursor-pointer" data-field="f3_p6" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f3_p6') ? 'checked' : '' }}>

                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-orange-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-orange-600 rounded cursor-pointer" data-field="f3_p7" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f3_p7') ? 'checked' : '' }}>

                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-orange-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-orange-600 rounded cursor-pointer" data-field="f3_submit" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f3_submit') ? 'checked' : '' }}>
                            </td>
                            {{-- F4 --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-teal-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-teal-600 rounded cursor-pointer" data-field="f4_negosiasi" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f4_negosiasi') ? 'checked' : '' }}>
                            </td>
                            {{-- F5 --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-green-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-green-600 rounded cursor-pointer" data-field="f5_sk_mitra" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f5_sk_mitra') ? 'checked' : '' }}>

                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-green-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-green-600 rounded cursor-pointer" data-field="f5_ttd_kontrak" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f5_ttd_kontrak') ? 'checked' : '' }}>
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-green-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-green-600 rounded cursor-pointer" data-field="f5_p8" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('f5_p8') ? 'checked' : '' }}>

                            </td>
                            {{-- DELIVERY --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-emerald-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-emerald-600 rounded cursor-pointer" data-field="delivery_kontrak" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('delivery_kontrak') ? 'checked' : '' }}>
                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-emerald-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-emerald-600 rounded cursor-pointer" data-field="delivery_baut_bast" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('delivery_baut_bast') ? 'checked' : '' }}>

                            </td>
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-emerald-50">
                                <input type="checkbox" class="funnel-checkbox w-4 h-4 text-emerald-600 rounded cursor-pointer" data-field="delivery_baso" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}" {{ $checked('delivery_baso') ? 'checked' : '' }}>

                            </td>
                            {{-- BILLING COMPLETE --}}
                            <td class="px-2 py-2.5 text-center border-r border-slate-100 bg-indigo-50">
                                <input type="checkbox" class="billing-checkbox w-4 h-4 text-indigo-600 rounded cursor-pointer"
                                    data-field="delivery_billing_complete" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}"
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
                                    data-field="cancel" data-data-type="{{ $type }}" data-ngtma-id="{{ $row->id }}"
                                    {{ $checked('cancel') ? 'checked' : '' }}>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="sticky bottom-0 z-10">
                        <tr class="border-t-2 border-red-200 bg-slate-50">
                            <td colspan="7" class="px-4 py-3 text-right text-xs font-black text-slate-700 uppercase tracking-widest border-r border-slate-100">TOTAL:</td>
                            <td class="px-4 py-3 text-right font-black text-emerald-700 border-r border-slate-100 bg-emerald-50">
                                {{ number_format($dataRows->sum(fn($r) => floatval($r->est_nilai_bc ?? 0)), 0, ',', '.') }}
                            </td>
                            <td colspan="20" class="border-r border-slate-100"></td>
                            <td class="px-4 py-3 text-right font-black text-violet-700 bg-violet-50" id="total-nilai-billcomp">
                                @php
                                    $totalBillComp = $funnelMap->filter(fn($f) => $f && $f->delivery_billing_complete)
                                        ->sum(function($f) use ($dataRows) {
                                            $nilai = $f->delivery_nilai_billcomp;
                                            if (!$nilai) {
                                                $row = $dataRows->firstWhere('id', $f->ngtma_id);
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) return;

    const updateUrl = '{{ route("admin.ngtma.progress.funnel.update", ["segment" => $segment]) }}';

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
            const rowId     = this.dataset.ngtmaId;
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
                body: JSON.stringify({ data_type: '{{ $type }}', ngtma_id: rowId, field: 'cancel', value: cancelled })
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
            saveChange(this.dataset.ngtmaId, this.dataset.dataType, this.dataset.field, this.checked, null, this.parentElement);
            if (this.checked) cascadeBackwards(this);
        });
    });

    // ── BILLING CHECKBOXES ──
    document.querySelectorAll('.billing-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const row = this.closest('tr');
            if (row && row.dataset.cancelled === 'true') { this.checked = !this.checked; return; }
            this.parentElement.classList.add('bg-yellow-100');
            saveChange(this.dataset.ngtmaId, this.dataset.dataType, this.dataset.field, this.checked, this.dataset.estNilai || '0', this.parentElement);
            // Cascade backward dari billing juga (tanpa cancel)
            if (this.checked) cascadeBackwards(this);
        });
    });

    // ── CASCADE BACKWARDS ──
    function cascadeBackwards(changedCb) {
        const row = changedCb.closest('tr');
        if (!row) return;
        const boxes = Array.from(row.querySelectorAll('.funnel-checkbox, .billing-checkbox'));
        const idx = boxes.indexOf(changedCb);
        if (idx === -1) return;
        boxes.slice(0, idx).forEach(cb => {
            if (cb.dataset.field === 'cancel') return;
            if (!cb.checked) {
                cb.checked = true;
                console.log('cascade:', cb.dataset.ngtmaId, cb.dataset.dataType, cb.dataset.field);
                saveChange(cb.dataset.ngtmaId, cb.dataset.dataType, cb.dataset.field, true, null, cb.parentElement);
            }
        });
    }

    // ── SAVE CHANGE ──
    function saveChange(rowId, dataType, field, value, estNilaiBc, container) {
        const payload = { data_type: dataType, ngtma_id: rowId, field, value };
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
                    const fi        = row?.querySelector('input[data-ngtma-id]');
                    if (fi) {
                        fields.forEach(fld => {
                            if (fld === 'cancel') return; // ← KUNCI: cancel tidak boleh disentuh auto_fields
                            const sib = document.querySelector(
                                `.funnel-checkbox[data-field="${fld}"][data-ngtma-id="${fi.dataset.ngtmaId}"][data-data-type="${fi.dataset.dataType}"]`
                            );
                            if (sib && sib.checked !== targetVal) {
                                sib.checked = targetVal;
                                saveChange(fi.dataset.ngtmaId, fi.dataset.dataType, fld, targetVal, null, sib.parentElement);
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
});
</script>
@endif

<style>
.sticky-col-header { position:sticky; z-index:3; }
.sticky-col { position:sticky; z-index:1; background:white; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── SORT ROWS BY AM (kolom index 4) ──────────────────────────────────
    (function sortByAM() {
        const tbody = document.querySelector('tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr[data-row-id]'));
        rows.sort(function (a, b) {
            const amA = (a.querySelectorAll('td')[4]?.textContent.trim() || '').toLowerCase();
            const amB = (b.querySelectorAll('td')[4]?.textContent.trim() || '').toLowerCase();
            return amA.localeCompare(amB, 'id');
        });
        rows.forEach(function (row) { tbody.appendChild(row); });

        // Re-number kolom NO setelah sort
        rows.forEach(function (row, idx) {
            const noCell = row.querySelectorAll('td')[0];
            if (noCell) noCell.textContent = idx + 1;
        });
    })();

    // ── STICKY COLUMNS ───────────────────────────────────────────────────
    const stickyCount = 4;
    const table = document.querySelector('table');
    if (!table) return;

    const firstRow = table.querySelector('tbody tr:first-child');
    if (!firstRow) return;
    const cells = firstRow.querySelectorAll('td');

    let pos = 0;
    for (let i = 0; i < stickyCount; i++) {
        const width = cells[i].offsetWidth;

        table.querySelectorAll('tbody tr td:nth-child(' + (i+1) + ')').forEach(td => {
            td.style.left = pos + 'px';
        });

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
