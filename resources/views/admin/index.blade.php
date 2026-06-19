@extends('layouts.app')

@section('title', 'Admin Dashboard - Management')

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
                            <h1 class="text-2xl font-black tracking-tight text-slate-900 leading-none uppercase">Admin <span
                                    class="text-red-600">Dashboard</span></h1>
                            <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-tight">Kertas Kerja
                                Management System</p>
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

            {{-- ══ 1. SCALING ══ --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center space-x-3">
                        <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                        <div>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">Scaling Management</h2>
                        </div>
                    </div>
                    <span
                        class="text-xs font-bold text-slate-500 bg-white border border-slate-200 rounded-full px-4 py-1.5 shadow-sm">Project
                        by Segment</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @php
                        $scalingCards = [
                            [
                                'label' => 'Government',
                                'sub' => 'Scaling Government',
                                'badge' => 'GOV',
                                'route' => route('admin.scalling.gov'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M3 10h18M3 7l9-4 9 4M4 10h1v11H4zm6 0h1v11h-1zm5 0h1v11h-1zm5 0h1v11h-1z"/>'
                            ],
                            [
                                'label' => 'Private',
                                'sub' => 'Scaling Private',
                                'badge' => 'PVT',
                                'route' => route('admin.scalling.private'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'
                            ],
                            [
                                'label' => 'SOE',
                                'sub' => 'Scaling SOE',
                                'badge' => 'SOE',
                                'route' => route('admin.scalling.soe'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'
                            ],
                            [
                                'label' => 'SME',
                                'sub' => 'Scaling SME',
                                'badge' => 'SME',
                                'route' => route('admin.scalling.sme'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>'
                            ],
                            [
                                'label' => 'HSI Agency',
                                'sub' => 'Scalling HSI Agency',
                                'badge' => 'HSI',
                                'route' => route('admin.hsi-agency'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'
                            ],
                            [
                                'label' => 'Upselling HSI',
                                'sub'   => 'Scalling Upselling HSI',
                                'badge' => 'UP-HSI',
                                'route' => route('admin.upselling-hsi.index'),
                                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'
                            ],
                            [
                                'label' => 'Telda',
                                'sub' => 'Scalling Telda (9 Wilayah)',
                                'badge' => 'Telda',
                                'route' => route('admin.telda.index'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>'
                            ],
                        ];
                    @endphp
                    @foreach($scalingCards as $card)
                        <a href="{{ $card['route'] }}"
                            class="group bg-white rounded-2xl border-2 border-slate-100 hover:border-red-200 shadow-sm hover:shadow-xl hover:shadow-red-500/10 transition-all duration-300 hover:-translate-y-1.5 overflow-hidden relative">
                            {{-- top accent bar --}}
                            <div
                                class="h-1 w-full bg-gradient-to-r from-red-600 to-red-400 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            </div>
                            {{-- subtle bg pattern --}}
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                style="background: radial-gradient(ellipse at top right, #fff1f2 0%, transparent 60%);"></div>
                            <div class="p-6 relative">
                                <div class="flex items-start justify-between mb-5">
                                    <div class="w-13 h-13 rounded-xl flex items-center justify-center shadow-sm border-2"
                                        style="background: linear-gradient(135deg, #fff1f2, #ffe4e6); border-color: #fecdd3; width:52px; height:52px;">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {!! $card['icon'] !!}
                                        </svg>
                                    </div>
                                    <div class="flex flex-col items-end space-y-1">
                                        <span
                                            class="text-[10px] font-black tracking-widest text-red-600 bg-red-50 border border-red-100 rounded-md px-2 py-0.5">{{ $card['badge'] }}</span>
                                    </div>
                                </div>
                                <h3 class="text-lg font-black text-slate-900 tracking-tight mb-1">{{ $card['label'] }}</h3>
                                <p class="text-sm text-slate-500 font-medium mb-5 leading-relaxed">{{ $card['sub'] }}</p>
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-xs font-black text-slate-400 group-hover:text-red-600 uppercase tracking-widest transition-colors duration-200">Manage
                                        Data</span>
                                    <div
                                        class="w-7 h-7 rounded-lg bg-slate-100 group-hover:bg-red-600 flex items-center justify-center transition-all duration-200">
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-white transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ══ 2. COLLECTION ══ --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center space-x-3">
                        <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                        <div>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">Collection Management</h2>
                        </div>
                    </div>
                    <span
                        class="text-xs font-bold text-slate-500 bg-white border border-slate-200 rounded-full px-4 py-1.5 shadow-sm">Monitor
                        Activities</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @php
                        $collectionCards = [
                            [
                                'label' => 'C3MR',
                                'sub' => 'Collection Management',
                                'badge' => 'C3MR',
                                'route' => route('admin.c3mr'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'
                            ],
                            [
                                'label' => 'Billing Perdana',
                                'sub' => 'Billing Management',
                                'badge' => 'BILL',
                                'route' => route('admin.billing'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'
                            ],
                            [
                                'label' => 'Collection Ratio',
                                'sub' => 'CR Tracking',
                                'badge' => 'CR',
                                'route' => route('admin.collection-ratio'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'
                            ],
                            [
                                'label' => 'CYC',
                                'sub' => 'CYC Management',
                                'badge' => 'CYC',
                                'route' => route('admin.cyc'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>'
                            ],
                            [
                                'label' => 'UTIP',
                                'sub' => 'UTIP Management',
                                'badge' => 'UTIP',
                                'route' => route('admin.utip'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'
                            ],
                            [
                                'label' => 'AR',
                                'sub' => 'AR Management',
                                'badge' => 'AR',
                                'route' => route('admin.ar'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>'
                           ],
                        ];
                    @endphp
                    @foreach($collectionCards as $card)
                        <a href="{{ $card['route'] }}"
                            class="group bg-white rounded-2xl border-2 border-slate-100 hover:border-red-200 shadow-sm hover:shadow-xl hover:shadow-red-500/10 transition-all duration-300 hover:-translate-y-1.5 overflow-hidden relative">
                            <div
                                class="h-1 w-full bg-gradient-to-r from-red-600 to-red-400 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            </div>
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
                                    <span
                                        class="text-[10px] font-black tracking-widest text-red-600 bg-red-50 border border-red-100 rounded-md px-2 py-0.5">{{ $card['badge'] }}</span>
                                </div>
                                <h3 class="text-lg font-black text-slate-900 tracking-tight mb-1">{{ $card['label'] }}</h3>
                                <p class="text-sm text-slate-500 font-medium mb-5">{{ $card['sub'] }}</p>
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-xs font-black text-slate-400 group-hover:text-red-600 uppercase tracking-widest transition-colors duration-200">View
                                        Data</span>
                                    <div
                                        class="w-7 h-7 rounded-lg bg-slate-100 group-hover:bg-red-600 flex items-center justify-center transition-all duration-200">
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-white transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ══ 3. CTC ══ --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center space-x-3">
                        <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                        <div>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">Combat The Churn</h2>
                        </div>
                    </div>
                    <span
                        class="text-xs font-bold text-slate-500 bg-white border border-slate-200 rounded-full px-4 py-1.5 shadow-sm">CTC
                        Monitoring</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @php
                        $ctcCards = [
                            [
                                'label' => 'Paid Pra CT0',
                                'sub' => 'Payment Tracking & Management',
                                'badge' => 'CT0',
                                'route' => route('admin.ct0'),
                                'cta' => 'Track Payments',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'
                            ],
                            [
                                'label' => 'Combat The Churn',
                                'sub' => 'Churn Analysis & Reports',
                                'badge' => 'CTC',
                                'route' => route('admin.ctc'),
                                'cta' => 'View Reports',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'
                            ],
                        ];
                    @endphp
                    @foreach($ctcCards as $card)
                        <a href="{{ $card['route'] }}"
                            class="group bg-white rounded-2xl border-2 border-slate-100 hover:border-red-200 shadow-sm hover:shadow-xl hover:shadow-red-500/10 transition-all duration-300 hover:-translate-y-1.5 overflow-hidden relative">
                            <div
                                class="h-1 w-full bg-gradient-to-r from-red-600 to-red-400 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            </div>
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                style="background: radial-gradient(ellipse at top right, #fff1f2 0%, transparent 60%);"></div>
                            <div class="p-7 flex items-center space-x-6 relative">
                                <div class="rounded-2xl flex items-center justify-center shadow-sm border-2 flex-shrink-0"
                                    style="background: linear-gradient(135deg, #fff1f2, #ffe4e6); border-color: #fecdd3; width:64px; height:64px;">
                                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $card['icon'] !!}
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <span
                                        class="text-[10px] font-black tracking-widest text-red-600 bg-red-50 border border-red-100 rounded-md px-2 py-0.5">{{ $card['badge'] }}</span>
                                    <h3 class="text-xl font-black text-slate-900 tracking-tight mt-2 mb-1">{{ $card['label'] }}
                                    </h3>
                                    <p class="text-sm text-slate-500 font-medium mb-3">{{ $card['sub'] }}</p>
                                    <div
                                        class="flex items-center text-xs font-black text-slate-400 group-hover:text-red-600 uppercase tracking-widest transition-colors duration-200">
                                        {{ $card['cta'] }}
                                        <svg class="w-3.5 h-3.5 ml-1.5 group-hover:translate-x-1 transition-transform"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div
                                    class="w-8 h-8 rounded-xl bg-slate-100 group-hover:bg-red-600 flex items-center justify-center transition-all duration-200 flex-shrink-0">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-white transition-colors" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ══ 4. RISING STAR ══ --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center space-x-3">
                        <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                        <div>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">Rising Star Program</h2>
                        </div>
                    </div>
                    <span
                        class="text-xs font-bold text-slate-500 bg-white border border-slate-200 rounded-full px-4 py-1.5 shadow-sm">Performance
                        by Level</span>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                    @for($i = 1; $i <= 4; $i++)
                        <a href="{{ route('admin.rising-star'.$i) }}"
                            class="group bg-white rounded-2xl border-2 border-slate-100 hover:border-red-200 shadow-sm hover:shadow-xl hover:shadow-red-500/10 transition-all duration-300 hover:-translate-y-1.5 overflow-hidden relative">
                            <div
                                class="h-1 w-full bg-gradient-to-r from-red-600 to-red-400 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            </div>
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                style="background: radial-gradient(ellipse at top right, #fff1f2 0%, transparent 60%);"></div>
                            <div class="p-6 relative">
                                <div class="rounded-xl flex items-center justify-center shadow-sm border-2 mb-4"
                                    style="background: linear-gradient(135deg, #fff1f2, #ffe4e6); border-color: #fecdd3; width:52px; height:52px;">
                                    <svg class="w-6 h-6 text-red-600 fill-red-600" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                                <div class="flex mb-3">
                                    @for($s = 0; $s < $i; $s++)
                                        <svg class="w-4 h-4 fill-red-500" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                                <h3 class="text-lg font-black text-slate-900 tracking-tight mb-1">Bintang {{ $i }}</h3>
                                @php
                                $levelLabels = [1 => 'Visiting Management', 2 => 'Profiling Management', 3 => 'Kecukupan LOP', 4 => 'Aosodomoro Terpadu'];
                                @endphp
                                <p class="text-sm text-slate-500 font-medium mb-5">{{ $levelLabels[$i] }}</p>
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-xs font-black text-slate-400 group-hover:text-red-600 uppercase tracking-widest transition-colors duration-200">Track
                                        Progress</span>
                                    <div
                                        class="w-7 h-7 rounded-lg bg-slate-100 group-hover:bg-red-600 flex items-center justify-center transition-all duration-200">
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-white transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endfor
                </div>
            </div>

            {{-- ══ 5. PSAK ══ --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center space-x-3">
                        <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                        <div>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">PSAK Management</h2>
                        </div>
                    </div>
                    <span
                        class="text-xs font-bold text-slate-500 bg-white border border-slate-200 rounded-full px-4 py-1.5 shadow-sm">Admin
                        Input Commitment</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @php
                        $psakCards = [
                            [
                                'label' => 'PSAK Gov',
                                'sub' => 'Government PSAK Data',
                                'badge' => 'GOV',
                                'route' => route('admin.psak.gov'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                            ],
                            [
                                'label' => 'PSAK Private',
                                'sub' => 'Private PSAK Data',
                                'badge' => 'PVT',
                                'route' => route('admin.psak.private'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                            ],
                            [
                                'label' => 'PSAK SOE',
                                'sub' => 'SOE PSAK Data',
                                'badge' => 'SOE',
                                'route' => route('admin.psak.soe'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                            ],
                            [
                                'label' => 'PSAK SME',
                                'sub' => 'SME PSAK Data',
                                'badge' => 'SME',
                                'route' => route('admin.psak.sme'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                            ],
                        ];
                    @endphp
                    @foreach($psakCards as $card)
                        <a href="{{ $card['route'] }}"
                            class="group bg-white rounded-2xl border-2 border-slate-100 hover:border-red-200 shadow-sm hover:shadow-xl hover:shadow-red-500/10 transition-all duration-300 hover:-translate-y-1.5 overflow-hidden relative">
                            <div
                                class="h-1 w-full bg-gradient-to-r from-red-600 to-red-400 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            </div>
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
                                    <span
                                        class="text-[10px] font-black tracking-widest text-red-600 bg-red-50 border border-red-100 rounded-md px-2 py-0.5">{{ $card['badge'] }}</span>
                                </div>
                                <h3 class="text-lg font-black text-slate-900 tracking-tight mb-1">{{ $card['label'] }}</h3>
                                <p class="text-sm text-slate-500 font-medium mb-5">{{ $card['sub'] }}</p>
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-xs font-black text-slate-400 group-hover:text-red-600 uppercase tracking-widest transition-colors duration-200">Manage
                                        Data</span>
                                    <div
                                        class="w-7 h-7 rounded-lg bg-slate-100 group-hover:bg-red-600 flex items-center justify-center transition-all duration-200">
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-white transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ══ 6. NGTMA ══ --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center space-x-3">
                        <div class="w-1.5 h-8 bg-red-600 rounded-full"></div>
                        <div>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">NGTMA Management</h2>
                        </div>
                    </div>
                    <span
                        class="text-xs font-bold text-slate-500 bg-white border border-slate-200 rounded-full px-4 py-1.5 shadow-sm">Project by Segment</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @php
                        $ngtmaCards = [
                            [
                                'key' => 'gov',
                                'label' => 'Government',
                                'sub' => 'New GTMA Government',
                                'badge' => 'GOV',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M3 10h18M3 7l9-4 9 4M4 10h1v11H4zm6 0h1v11h-1zm5 0h1v11h-1zm5 0h1v11h-1z"/>'
                            ],
                            [
                                'key' => 'private',
                                'label' => 'Private',
                                'sub' => 'New GTMA Private',
                                'badge' => 'PVT',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'
                            ],
                            [
                                'key' => 'soe',
                                'label' => 'SOE',
                                'sub' => 'New GTMA SOE',
                                'badge' => 'SOE',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'
                            ],
                            [
                                'key' => 'sme',
                                'label' => 'SME',
                                'sub' => 'New GTMA SME',
                                'badge' => 'SME',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>'
                            ],
                        ];
                    @endphp
                    @foreach($ngtmaCards as $card)
                        <a href="{{ route('admin.ngtma.index', ['segment' => $card['key']]) }}"
                            class="group bg-white rounded-2xl border-2 border-slate-100 hover:border-red-200 shadow-sm hover:shadow-xl hover:shadow-red-500/10 transition-all duration-300 hover:-translate-y-1.5 overflow-hidden relative">
                            <div
                                class="h-1 w-full bg-gradient-to-r from-red-600 to-red-400 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            </div>
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
                                    <span
                                        class="text-[10px] font-black tracking-widest text-red-600 bg-red-50 border border-red-100 rounded-md px-2 py-0.5">{{ $card['badge'] }}</span>
                                </div>
                                <h3 class="text-lg font-black text-slate-900 tracking-tight mb-1">{{ $card['label'] }}</h3>
                                <p class="text-sm text-slate-500 font-medium mb-5">{{ $card['sub'] }}</p>
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-xs font-black text-slate-400 group-hover:text-red-600 uppercase tracking-widest transition-colors duration-200">Manage
                                        Data</span>
                                    <div
                                        class="w-7 h-7 rounded-lg bg-slate-100 group-hover:bg-red-600 flex items-center justify-center transition-all duration-200">
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-white transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
@endsection
