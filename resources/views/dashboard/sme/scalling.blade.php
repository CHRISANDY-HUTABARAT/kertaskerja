@extends('layouts.app')

@section('title', 'SME Dashboard')

@section('content')
<div class="min-h-screen" style="background:#f1f5f9;">
    <div class="max-w-7xl mx-auto px-8 py-10">

        {{-- ══ HEADER ══ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-10 py-7 mb-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1.5"
                style="background: linear-gradient(90deg, #dc2626, #ef4444, #dc2626);"></div>
            <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full opacity-[0.04]" style="background: #dc2626;"></div>
            <div class="relative flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <img src="{{ asset('img/Telkom.png') }}" alt="Telkom" class="h-12 w-auto">
                    <div class="w-px h-12 bg-slate-200"></div>
                    <div>
                        <p class="text-[10px] font-black tracking-[0.3em] text-red-600 uppercase mb-1">Witel Sumut</p>
                        <h1 class="text-2xl font-black tracking-tight text-slate-900 leading-none uppercase">SME <span class="text-red-600">Dashboard</span></h1>
                        <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-tight">Kertas Kerja Management System</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                        <a href="{{ route('report.index') }}"
                            class="flex items-center space-x-2.5 bg-white border-2 border-slate-900 hover:bg-red-600 hover:border-red-600 text-slate-900 hover:text-white px-6 py-3 rounded-xl font-black text-xs transition-all duration-300 shadow-sm group uppercase tracking-wider">
                            <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>View Report</span>
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

        {{-- ══ 1. SCALLING TABLE ══ --}}
        <div class="mb-10">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center space-x-3">
                    <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">Scalling Management</h2>
                    </div>
                </div>
                <span class="text-xs font-bold text-slate-500 bg-white border border-slate-200 rounded-full px-4 py-1.5 shadow-sm">LOP SME</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @php
                $scallingCards = [
                    [
                        'key'   => 'on-hand',
                        'label' => 'LOP On Hand',
                        'sub'   => 'Data LOP On Hand SME',
                        'badge' => 'ON-HAND',
                        'route' => route('dashboard.sme.lop-on-hand'),
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
                    ],
                    [
                        'key'   => 'qualified',
                        'label' => 'LOP Qualified',
                        'sub'   => 'Data LOP Qualified SME',
                        'badge' => 'QUALIFIED',
                        'route' => route('dashboard.sme.lop-qualified'),
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>',
                    ],
                    [
                        'key'   => 'initiate',
                        'label' => 'LOP Initiate',
                        'sub'   => 'Data LOP Initiate SME',
                        'badge' => 'INITIATE',
                        'route' => route('dashboard.sme.initiate'),
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                    ],
                    
                ];
                @endphp

                @foreach($scallingCards as $card)
                <a href="{{ $card['route'] }}"
                    class="group bg-white rounded-2xl border-2 border-slate-100 hover:border-red-200 shadow-sm hover:shadow-xl hover:shadow-red-500/10 transition-all duration-300 hover:-translate-y-1.5 overflow-hidden relative">
                    <div class="h-1 w-full bg-gradient-to-r from-red-600 to-red-400 opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        style="background: radial-gradient(ellipse at top right, #fff1f2 0%, transparent 60%);"></div>
                    <div class="p-6 relative">
                        <div class="flex items-start justify-between mb-5">
                            <div class="rounded-xl flex items-center justify-center shadow-sm border-2"
                                style="background: linear-gradient(135deg, #fff1f2, #ffe4e6); border-color: #fecdd3; width:52px; height:52px;">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $card['icon'] !!}
                                </svg>
                            </div>
                            <span class="text-[10px] font-black tracking-widest text-red-600 bg-red-50 border border-red-100 rounded-md px-2 py-0.5">{{ $card['badge'] }}</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight mb-1">{{ $card['label'] }}</h3>
                        <p class="text-sm text-slate-500 font-medium mb-5">{{ $card['sub'] }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-400 group-hover:text-red-600 uppercase tracking-widest transition-colors duration-200">open data</span>
                            <div class="w-7 h-7 rounded-lg bg-slate-100 group-hover:bg-red-600 flex items-center justify-center transition-all duration-200">
                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        {{-- ══ 2. RISING STAR — BINTANG 4 ══ --}}
        <div class="mb-10">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center space-x-3">
                    <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">Scalling — Upselling HSI</h2>
                    </div>
                </div>
                <span class="text-xs font-bold text-slate-500 bg-white border border-slate-200 rounded-full px-4 py-1.5 shadow-sm">Scalling Management</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Upselling HSI --}}
                <a href="{{ route('dashboard.sme.upselling') }}"
                    class="group bg-white rounded-2xl border-2 border-slate-100 hover:border-red-200 shadow-sm hover:shadow-xl hover:shadow-red-500/10 transition-all duration-300 hover:-translate-y-1.5 overflow-hidden relative">
                    <div class="h-1 w-full bg-gradient-to-r from-red-600 to-red-400 opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        style="background: radial-gradient(ellipse at top right, #fff1f2 0%, transparent 60%);"></div>
                    <div class="p-7 flex items-center space-x-6 relative">
                        <div class="rounded-2xl flex items-center justify-center shadow-sm border-2 flex-shrink-0"
                            style="background: linear-gradient(135deg, #fff1f2, #ffe4e6); border-color: #fecdd3; width:64px; height:64px;">
                            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <!-- <span class="text-[10px] font-black tracking-widest text-red-600 bg-red-50 border border-red-100 rounded-md px-2 py-0.5"></span> -->
                            <h3 class="text-xl font-black text-slate-900 tracking-tight mt-2 mb-1">Upselling HSI</h3>
                            <p class="text-sm text-slate-500 font-medium mb-3">Input realisasi data Upselling HSI</p>
                            <div class="flex items-center text-xs font-black text-slate-400 group-hover:text-red-600 uppercase tracking-widest transition-colors duration-200">
                                Input Data
                                <svg class="w-3.5 h-3.5 ml-1.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-slate-100 group-hover:bg-red-600 flex items-center justify-center transition-all duration-200 flex-shrink-0">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- ══ 2. RISING STAR — BINTANG 4 ══ --}}
        <div class="mb-10">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center space-x-3">
                    <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">Rising Star — Bintang 4</h2>
                    </div>
                </div>
                <span class="text-xs font-bold text-slate-500 bg-white border border-slate-200 rounded-full px-4 py-1.5 shadow-sm">Asodomoro Management</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Asodomoro 0-3 Bulan --}}
                <a href="{{ route('dashboard.sme.aosodomoro-0-3-bulan') }}"
                    class="group bg-white rounded-2xl border-2 border-slate-100 hover:border-red-200 shadow-sm hover:shadow-xl hover:shadow-red-500/10 transition-all duration-300 hover:-translate-y-1.5 overflow-hidden relative">
                    <div class="h-1 w-full bg-gradient-to-r from-red-600 to-red-400 opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        style="background: radial-gradient(ellipse at top right, #fff1f2 0%, transparent 60%);"></div>
                    <div class="p-7 flex items-center space-x-6 relative">
                        <div class="rounded-2xl flex items-center justify-center shadow-sm border-2 flex-shrink-0"
                            style="background: linear-gradient(135deg, #fff1f2, #ffe4e6); border-color: #fecdd3; width:64px; height:64px;">
                            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <span class="text-[10px] font-black tracking-widest text-red-600 bg-red-50 border border-red-100 rounded-md px-2 py-0.5">0-3 BLN</span>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight mt-2 mb-1">Aosodomoro 0-3 Bulan</h3>
                            <p class="text-sm text-slate-500 font-medium mb-3">Input realisasi data Aosodomoro untuk periode 0-3 bulan</p>
                            <div class="flex items-center text-xs font-black text-slate-400 group-hover:text-red-600 uppercase tracking-widest transition-colors duration-200">
                                Input Data
                                <svg class="w-3.5 h-3.5 ml-1.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-slate-100 group-hover:bg-red-600 flex items-center justify-center transition-all duration-200 flex-shrink-0">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>

                {{-- Asodomoro >3 Bulan --}}
                <a href="{{ route('dashboard.sme.aosodomoro-above-3-bulan') }}"
                    class="group bg-white rounded-2xl border-2 border-slate-100 hover:border-red-200 shadow-sm hover:shadow-xl hover:shadow-red-500/10 transition-all duration-300 hover:-translate-y-1.5 overflow-hidden relative">
                    <div class="h-1 w-full bg-gradient-to-r from-red-600 to-red-400 opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        style="background: radial-gradient(ellipse at top right, #fff1f2 0%, transparent 60%);"></div>
                    <div class="p-7 flex items-center space-x-6 relative">
                        <div class="rounded-2xl flex items-center justify-center shadow-sm border-2 flex-shrink-0"
                            style="background: linear-gradient(135deg, #fff1f2, #ffe4e6); border-color: #fecdd3; width:64px; height:64px;">
                            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <span class="text-[10px] font-black tracking-widest text-red-600 bg-red-50 border border-red-100 rounded-md px-2 py-0.5">&gt;3 BLN</span>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight mt-2 mb-1">Aosodomoro &gt;3 Bulan</h3>
                            <p class="text-sm text-slate-500 font-medium mb-3">Input realisasi data Aosodomoro untuk periode lebih dari 3 bulan</p>
                            <div class="flex items-center text-xs font-black text-slate-400 group-hover:text-red-600 uppercase tracking-widest transition-colors duration-200">
                                Input Data Realisasi
                                <svg class="w-3.5 h-3.5 ml-1.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-slate-100 group-hover:bg-red-600 flex items-center justify-center transition-all duration-200 flex-shrink-0">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
