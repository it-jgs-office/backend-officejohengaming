@extends('layouts.app')
@section('body-class', 'page-admin')
@section('title', 'Peralatan Kantor')
@section('page-title', 'Data Aset > Peralatan Kantor')
@section('page-subtitle', 'Inventaris peralatan kantor milik perusahaan')
@section('sidebar-menu') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="pt-2 space-y-4 animate-fade-in">

    {{-- Stat Cards: Count --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 md:gap-3">
        @php
            $countCards = [
                ['label' => 'Total Peralatan', 'count' => $stats['total'], 'color' => '#a78bfa', 'bg' => 'rgba(124,58,237,0.12)', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['label' => 'Kondisi Baik', 'count' => $stats['kondisi_baik'], 'color' => '#34d399', 'bg' => 'rgba(16,185,129,0.12)', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Perlu Servis', 'count' => $stats['perlu_servis'], 'color' => '#fbbf24', 'bg' => 'rgba(245,158,11,0.12)', 'icon' => 'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Rusak', 'count' => $stats['rusak'], 'color' => '#ef4444', 'bg' => 'rgba(239,68,68,0.12)', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp
        @foreach($countCards as $card)
        <div class="stat-card-compact">
            <div class="stat-icon-box" style="background:{{ $card['bg'] }};box-shadow:0 0 14px {{ $card['color'] }}20;">
                <svg style="color:{{ $card['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <div>
                <div class="stat-num">{{ $card['count'] }}</div>
                <div class="stat-label-text" style="font-size:0.7rem;">{{ $card['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Stat Cards: Nominal --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 md:gap-3">
        <div class="stat-card-compact">
            <div class="stat-icon-box" style="background:rgba(59,130,246,0.12);box-shadow:0 0 14px rgba(59,130,246,0.20);">
                <svg style="color:#60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-num" style="color:#60a5fa;">Rp {{ number_format($stats['total_nilai'], 0, ',', '.') }}</div>
                <div class="stat-label-text" style="font-size:0.7rem;">Total Nilai (Pembelian)</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-box" style="background:rgba(245,158,11,0.12);box-shadow:0 0 14px rgba(245,158,11,0.20);">
                <svg style="color:#fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div>
                <div class="stat-num" style="color:#fbbf24;">Rp {{ number_format($stats['total_harga_sekarang'], 0, ',', '.') }}</div>
                <div class="stat-label-text" style="font-size:0.7rem;">Total Harga Saat Ini</div>
            </div>
        </div>
    </div>

    <div id="export-period-modal" class="modal-overlay" style="display:none;position:fixed;inset:0;z-index:1000;align-items:center;justify-content:center;padding:16px;background:var(--bg-overlay);" onclick="if(event.target===this)closeModal('export-period-modal')">
        <div class="modal-content" style="max-width:440px;width:100%;padding:24px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.25);">
            <div class="flex items-start justify-between gap-4 mb-2"><div><h2 class="text-lg font-bold" style="color:var(--text-primary);">Export Peralatan Kantor</h2><p class="text-sm mt-1" style="color:var(--text-muted);">Pilih periode tanggal pembelian yang akan diexport.</p></div><button type="button" onclick="closeModal('export-period-modal')" class="p-1.5 rounded-xl" style="color:var(--text-muted);background:none;border:none;cursor:pointer;">Tutup</button></div>
            <form method="GET" action="{{ route('admin.export') }}" class="mt-5">
                <input type="hidden" name="type" value="peralatan-kantor"><input type="hidden" name="filter" value="all">
                @if($activeTim)<input type="hidden" name="tim" value="{{ $activeTim }}">@endif
                <div class="space-y-2">
                    <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer" style="border:1px solid var(--border-color);color:var(--text-primary);"><input type="radio" name="range" value="all"><span class="text-sm font-medium">Semua data (tanpa batas periode)</span></label>
                    <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer" style="border:1px solid var(--border-color);color:var(--text-primary);"><input type="radio" name="range" value="harian"><span class="text-sm font-medium">Harian</span></label>
                    <div data-export-period="harian" class="pl-9 pb-2" hidden><label for="export-date" class="block text-xs font-medium mb-1" style="color:var(--text-muted);">Tanggal input</label><input id="export-date" type="date" name="date" value="{{ now()->toDateString() }}" style="width:100%;padding:10px 12px;border:1px solid var(--border-color);border-radius:10px;background:var(--bg-card);color:var(--text-primary);" disabled></div>
                    <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer" style="border:1px solid var(--border-color);color:var(--text-primary);"><input type="radio" name="range" value="mingguan"><span class="text-sm font-medium">Mingguan</span></label>
                    <div data-export-period="mingguan" class="pl-9 pb-2 space-y-2" hidden>
                        <div><label for="export-week-start" class="block text-xs font-medium mb-1" style="color:var(--text-muted);">Tanggal awal</label><input id="export-week-start" type="date" name="week_start" value="{{ now()->startOfWeek()->toDateString() }}" style="width:100%;padding:10px 12px;border:1px solid var(--border-color);border-radius:10px;background:var(--bg-card);color:var(--text-primary);" disabled></div>
                        <div><label for="export-week-end" class="block text-xs font-medium mb-1" style="color:var(--text-muted);">Tanggal akhir</label><input id="export-week-end" type="date" name="week_end" value="{{ now()->endOfWeek()->toDateString() }}" style="width:100%;padding:10px 12px;border:1px solid var(--border-color);border-radius:10px;background:var(--bg-card);color:var(--text-primary);" disabled></div>
                    </div>
                    <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer" style="border:1px solid var(--border-color);color:var(--text-primary);"><input type="radio" name="range" value="bulanan" checked><span class="text-sm font-medium">Bulanan</span></label>
                    <div data-export-period="bulanan" class="pl-9 pb-2"><label for="export-month" class="block text-xs font-medium mb-1" style="color:var(--text-muted);">Bulan input</label><input id="export-month" type="month" name="month" value="{{ now()->format('Y-m') }}" style="width:100%;padding:10px 12px;border:1px solid var(--border-color);border-radius:10px;background:var(--bg-card);color:var(--text-primary);"></div>
                </div>
                <script>
                    document.querySelectorAll('#export-period-modal input[name="range"]').forEach((radio) => {
                        radio.addEventListener('change', () => {
                            document.querySelectorAll('#export-period-modal [data-export-period]').forEach((field) => {
                                const active = field.dataset.exportPeriod === radio.value;
                                field.hidden = !active;
                                const input = field.querySelector('input');
                                input.disabled = !active;
                                input.required = active;
                            });
                        });
                    });
                    const weekStart = document.getElementById('export-week-start');
                    const weekEnd = document.getElementById('export-week-end');
                    weekStart.addEventListener('change', () => { weekEnd.min = weekStart.value; });
                    weekEnd.addEventListener('change', () => { weekStart.max = weekEnd.value; });
                </script>
                <div class="flex justify-end gap-2 mt-5"><button type="button" onclick="closeModal('export-period-modal')" class="btn btn-secondary">Batal</button><button type="submit" class="btn btn-primary">Download Excel</button></div>
            </form>
        </div>
    </div>
    {{-- Alert Kondisi Peralatan --}}
    @php
        $perluServisCount = $alertItems->where('kondisi', 'perlu_servis')->count();
        $rusakCount = $alertItems->where('kondisi', 'rusak')->count();
    @endphp
    @if($alertItems->isNotEmpty())
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        @if($perluServisCount > 0)
        <div style="flex:1;min-width:260px;">
            <div class="flex items-start gap-3 px-5 py-3.5 rounded-2xl" style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color:#f59e0b;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold" style="color:#f59e0b;">{{ $perluServisCount }} Perlu Servis</div>
                    <div class="text-xs mt-1" style="color:var(--text-secondary);">{{ $perluServisCount }} peralatan dengan kondisi perlu servis.</div>
                </div>
                <button type="button" onclick="showAlertPopup('warning')" style="flex-shrink:0;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:600;background:rgba(245,158,11,0.12);color:#f59e0b;border:1px solid rgba(245,158,11,0.2);cursor:pointer;white-space:nowrap;">Lihat Detail</button>
            </div>
        </div>
        @endif
        @if($rusakCount > 0)
        <div style="flex:1;min-width:260px;">
            <div class="flex items-start gap-3 px-5 py-3.5 rounded-2xl" style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color:#ef4444;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold" style="color:#ef4444;">{{ $rusakCount }} Rusak</div>
                    <div class="text-xs mt-1" style="color:var(--text-secondary);">{{ $rusakCount }} peralatan dengan kondisi rusak.</div>
                </div>
                <button type="button" onclick="showAlertPopup('danger')" style="flex-shrink:0;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:600;background:rgba(239,68,68,0.12);color:#ef4444;border:1px solid rgba(239,68,68,0.2);cursor:pointer;white-space:nowrap;">Lihat Detail</button>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Tabel --}}
    <div class="gaming-card" style="overflow:visible;">
        <div id="asset-card-header" class="px-6 py-4 flex items-center justify-between" style="border-bottom:1px solid var(--border-color);">
            <div>
                <div style="font-weight:600;font-size:0.8rem;color:var(--text-primary);">Peralatan Kantor</div>
                <div style="font-size:0.7rem;color:var(--text-muted);margin-top:2px;font-weight:400;">Inventaris peralatan kantor milik perusahaan.</div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openScanModal()" class="btn btn-secondary btn-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    Scan Barcode
                </button>
                @if(!in_array(auth()->user()->role, ['gm', 'ceo']))
                <button type="button" onclick="openCreateModal()" class="btn btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Peralatan
                </button>
                @endif
            </div>
        </div>
        <div id="asset-toolbar" class="px-5 py-2.5 flex flex-wrap items-center gap-3" style="border-bottom:1px solid var(--border-color);">
            <div class="relative flex-1 min-w-[200px] max-w-[260px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Cari nama barang, kode aset, barcode, atau PIC..." autocomplete="off" oninput="applyItemSearch()" onkeydown="if(event.key==='Enter')event.preventDefault()"
                    class="w-full pl-9 pr-3 py-1.5 rounded-lg text-xs"
                    style="background:var(--bg-surface);border:1px solid var(--border-color);color:var(--text-primary);outline:none;">
            </div>
            <div class="flex items-center gap-2" style="margin-left:auto;">
                <button type="button" onclick="openImportModal()" class="btn btn-secondary btn-sm inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
                    </svg>
                    Import Excel
                </button>
                <button type="button" onclick="openModal('export-period-modal')" class="btn btn-secondary btn-sm inline-flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2-2z"/></svg>Export</button>
                @if(auth()->user()->role === 'admin')
                <button type="button" onclick="confirmResetData()" class="btn btn-sm inline-flex items-center gap-1.5" style="color:#ef4444;border:1px solid rgba(239,68,68,0.3);background:rgba(239,68,68,0.08);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset Data
                </button>
                <form id="reset-data-form" method="POST" action="{{ route('admin.peralatan-kantor.reset') }}" style="display:none;">@csrf</form>
                @endif
                <div class="filter-dropdown-wrap" style="position:relative;">
                <button type="button" onclick="toggleFilterMenu(event)" class="filter-btn"
                    style="display:flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:500;cursor:pointer;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-primary);outline:none;white-space:nowrap;">
                    <span id="filter-label">{{ $range ? ['harian' => 'Hari Ini', 'mingguan' => 'Minggu Ini', 'bulanan' => 'Bulan Ini'][$range].' · ' : '' }}{{ $kondisi && $kondisi !== 'all' ? ucwords(str_replace('_', ' ', $kondisi)) : 'Semua Kondisi' }}</span>
                    <svg class="w-3.5 h-3.5" style="color:var(--text-muted);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="filter-menu" class="filter-menu" style="display:none;position:absolute;right:0;top:100%;z-index:40;min-width:150px;max-height:70vh;overflow-y:auto;background:var(--bg-surface);border:1px solid var(--border-color);border-radius:10px;padding:4px;box-shadow:0 8px 24px rgba(0,0,0,0.15);margin-top:4px;">
                    <button type="button" data-value="all" onclick="setFilter('all')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Semua Kondisi</button>
                    <button type="button" data-value="baik" onclick="setFilter('baik')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Kondisi Baik</button>
                    <button type="button" data-value="perlu_servis" onclick="setFilter('perlu_servis')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Perlu Servis</button>
                    <button type="button" data-value="rusak" onclick="setFilter('rusak')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Rusak</button>
                    @foreach($kondisiOptions as $opt)
                        @if(!in_array($opt, ['baik','perlu_servis','rusak']))
                        <button type="button" data-value="{{ $opt }}" onclick="setFilter('{{ $opt }}')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;{{ $kondisi === $opt ? 'color:#93c5fd;font-weight:700;' : 'color:var(--text-primary);font-weight:400;' }}border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">{{ ucwords(str_replace('_', ' ', $opt)) }}</button>
                        @endif
                    @endforeach
                    <div style="margin:5px 8px;border-top:1px solid var(--border-color);"></div>
                    <div style="padding:6px 12px 3px;font-size:10px;font-weight:700;letter-spacing:.06em;color:var(--text-muted);">WAKTU INPUT</div>
                    <button type="button" onclick="setDateRange('')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;">Semua Waktu</button>
                    <button type="button" onclick="setDateRange('harian')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;">Hari Ini</button>
                    <button type="button" onclick="setDateRange('mingguan')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;">Minggu Ini</button>
                    <button type="button" onclick="setDateRange('bulanan')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;">Bulan Ini</button>
                </div>
                </div>
                <div class="filter-dropdown-wrap" style="position:relative;">
                    <button type="button" onclick="toggleTimFilterMenu(event)" class="filter-btn"
                        style="display:flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:500;cursor:pointer;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-primary);outline:none;white-space:nowrap;">
                        <svg class="w-3.5 h-3.5" style="color:var(--text-muted);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span id="tim-filter-label">{{ $activeTim ?: 'Semua Tim' }}</span>
                        <svg class="w-3.5 h-3.5" style="color:var(--text-muted);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="tim-filter-menu" style="display:none;position:absolute;right:0;top:100%;z-index:40;min-width:180px;max-height:300px;overflow-y:auto;background:var(--bg-surface);border:1px solid var(--border-color);border-radius:10px;padding:4px;box-shadow:0 8px 24px rgba(0,0,0,0.15);margin-top:4px;">
                        <button type="button" data-tim="" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;border-radius:6px;cursor:pointer;{{ $activeTim ? 'color:var(--text-primary);font-weight:400;' : 'color:#a78bfa;font-weight:700;' }}" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Semua Tim</button>
                        @foreach($allTim as $tim)
                        @php $isTimActive = $activeTim === $tim; @endphp
                        <button type="button" data-tim="{{ $tim }}" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;border-radius:6px;cursor:pointer;{{ $isTimActive ? 'color:#a78bfa;font-weight:700;' : 'color:var(--text-primary);font-weight:400;' }}" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">{{ $tim }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="gaming-table min-w-[3200px]" id="item-table">
                <thead>
                    <tr>
                        <th style="width:40px;white-space:nowrap;font-size:0.7rem;">No</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Nama Barang</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Tim</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Jumlah</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Detail</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Keterangan</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Lokasi Unit</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Ruangan</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Pengadaan (Tahun)</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Tgl Pembelian</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Kategori Nilai</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Kategori Ukuran</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Sub-Kategori</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Milik</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Nilai (Rupiah)</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Waktu Pakai/Hari</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Estimasi Waktu</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Pengurangan/Hari</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Harga Saat Ini</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">PIC</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Jabatan PIC</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Atasan</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Jabatan Atasan</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Kode Asset</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Barcode</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Kondisi</th>
                        <th style="white-space:nowrap;font-size:0.7rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="item-tbody">
                    @forelse($items as $i)
                    @php
                        $kondisiBadge = match($i->kondisi) {
                            'baik'        => 'badge-green',
                            'perlu_servis' => 'badge-yellow',
                            'rusak'       => 'badge-red',
                            default       => 'badge-gray',
                        };
                        $kondisiLabel = match($i->kondisi) {
                            'baik'        => 'Baik',
                            'perlu_servis' => 'Perlu Servis',
                            'rusak'       => 'Rusak',
                            default       => $i->kondisi ? ucwords(str_replace('_', ' ', $i->kondisi)) : '-',
                        };
                        $masaBarang = max($i->estimasi_waktu_barang ?: 360, 1);
                        $penyusutanPerHari = $i->nilai / $masaBarang;
                        $waktuPakai = max((int) $i->waktu_pakai_per_hari, 1);
                        $penguranganHariIni = $penyusutanPerHari * $waktuPakai;
                        $nilaiSekarang = max($i->nilai - $penguranganHariIni, 0);
                    @endphp
                    <tr data-kondisi="{{ $i->kondisi }}"@if($i->barcode_ditempel) style="background:rgba(16,185,129,0.08);"@endif>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}</td>
                        <td style="color:var(--text-primary);font-weight:500;white-space:nowrap;font-size:0.75rem;">{{ $i->nama_barang }}</td>
                        <td>@if($i->tim)<span class="badge" style="background:rgba(124,58,237,0.12);color:#a78bfa;border:1px solid rgba(124,58,237,0.25);">{{ $i->tim }}</span>@else<span style="color:var(--text-muted);font-size:0.75rem;">-</span>@endif</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->jumlah }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->detail ?? '-' }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->keterangan ?? '-' }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->lokasi_unit }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->ruangan }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->pengadaan_tahun }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->tanggal_pembelian?->format('d/m/Y') ?? '-' }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->kategori_nilai }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->kategori_ukuran }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->sub_kategori }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->milik }}</td>
                        <td style="color:var(--text-primary);font-weight:500;white-space:nowrap;font-size:0.75rem;">Rp{{ number_format($i->nilai, 0, ',', '.') }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $waktuPakai }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->estimasi_waktu_barang }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ number_format($penguranganHariIni, 2, ',', '.') }}</td>
                        <td style="color:{{ $nilaiSekarang > 0 ? 'var(--text-primary)' : '#ef4444' }};font-weight:500;white-space:nowrap;font-size:0.75rem;">Rp{{ number_format($nilaiSekarang, 0, ',', '.') }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->pic }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->jabatan }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->atasan }}</td>
                        <td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">{{ $i->jabatan_atasan }}</td>
                        <td style="color:var(--color-accent);font-weight:500;font-family:monospace;font-size:0.7rem;white-space:nowrap;">{{ $i->kode_aset }}</td>
                        <td style="color:{{ $i->barcode_ditempel ? '#34d399' : 'var(--text-muted)' }};font-family:monospace;font-size:0.7rem;white-space:nowrap;">
                            {{ $i->barcode }}
                            @if($i->barcode_ditempel)
                                @if(auth()->user()->role !== 'gm' && auth()->user()->role !== 'ceo')
                                <span class="badge" onclick="toggleBarcodeDitempel({{ $i->id }})" title="Klik untuk membatalkan tanda" style="display:block;margin-top:2px;background:rgba(16,185,129,0.12);color:#34d399;border:1px solid rgba(16,185,129,0.3);font-size:0.6rem;cursor:pointer;user-select:none;">✓ Sudah Ditempel</span>
                                @else
                                <span class="badge" style="display:block;margin-top:2px;background:rgba(16,185,129,0.12);color:#34d399;border:1px solid rgba(16,185,129,0.3);font-size:0.6rem;">✓ Sudah Ditempel</span>
                                @endif
                            @endif
                        </td>
                        <td><span class="badge {{ $kondisiBadge }}">{{ $kondisiLabel }}</span></td>
                        <td>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="showDetail({{ $i->id }})" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:4px;padding:3px 6px;font-size:0.7rem;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Lihat Detail
                                </button>
                                <div class="dropdown-wrap" style="position:relative;">
                                    <button type="button" onclick="toggleDropdown(this, {{ $i->id }})" class="btn btn-secondary btn-sm" style="padding:3px 6px;font-size:0.7rem;line-height:1;">⋮</button>
                                    <div id="dropdown-{{ $i->id }}" class="dropdown-menu" style="display:none;position:absolute;top:100%;right:0;z-index:99999;min-width:130px;background:var(--bg-surface);border:1px solid var(--border-color);border-radius:10px;padding:4px;box-shadow:0 8px 24px rgba(0,0,0,0.15);margin-top:4px;">
                                        <button type="button" onclick="showDetail({{ $i->id }})" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Detail</button>
                                        @if(auth()->user()->role !== 'gm' && auth()->user()->role !== 'ceo')
                                        <button type="button" onclick="openEditModal({{ $i->id }})" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Edit</button>
                                        <button type="button" onclick="toggleBarcodeDitempel({{ $i->id }})" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:{{ $i->barcode_ditempel ? '#ef4444' : '#34d399' }};border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">{{ $i->barcode_ditempel ? 'Batalkan Tanda Barcode' : 'Tandai Sudah Ditempel' }}</button>
                                        <form method="POST" action="{{ route('admin.peralatan-kantor.destroy', $i) }}" onsubmit="confirmSubmit(event, this)" data-confirm="Hapus peralatan ini?" style="margin:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:#ef4444;border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Hapus</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="empty-row">
                        <td colspan="27" style="text-align:center;padding:2rem;color:var(--text-muted);">Belum ada data peralatan kantor.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 pt-2" style="font-size:0.72rem;color:#34d399;">
            <span>● Baris hijau = barcode sudah ditempel pada barang.</span>
        </div>
        <div class="px-5 py-2.5 flex flex-wrap items-center gap-3" style="border-top:1px solid var(--border-color);">
            <div id="table-footer-inner" style="display:contents;">
            <span style="font-size:0.75rem;color:var(--text-muted);white-space:nowrap;">
                @if(!$showAll)
                    Menampilkan {{ $items->firstItem() }}-{{ $items->lastItem() }} dari {{ $items->total() }} item
                @else
                    Menampilkan semua {{ $items->total() }} item
                @endif
            </span>
            @if(!$showAll)
                <a href="{{ route('admin.peralatan-kantor.index') }}?show_all=1" style="font-size:0.75rem;color:var(--color-accent);font-weight:500;text-decoration:none;white-space:nowrap;">Selengkapnya &rarr;</a>
            @else
                <a href="{{ route('admin.peralatan-kantor.index') }}" style="font-size:0.75rem;color:var(--color-accent);font-weight:500;text-decoration:none;white-space:nowrap;">&larr; Kembali ke Ringkasan</a>
            @endif
            <div style="margin-left:auto;">
                @if(!$showAll && $items->hasPages())
                    {{ $items->links() }}
                @endif
            </div>
            </div>
        </div>
    </div>

</div>

{{-- Detail Modal --}}
<div id="detail-modal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:var(--bg-overlay);">
    <div class="w-full max-w-5xl rounded-[22px] shadow-2xl flex flex-col" style="max-height:90vh;background:var(--bg-surface);border:1px solid var(--border-color);" onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 flex-shrink-0" style="border-bottom:1px solid var(--border-color);">
            <button onclick="closeDetail()" style="color:var(--text-muted);background:none;border:none;cursor:pointer;padding:6px 10px;border-radius:10px;display:flex;align-items:center;gap:6px;font-size:13px;transition:all 0.15s;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7 7l-7-7 7-7"/></svg>
                Kembali
            </button>
            <div class="flex items-center gap-2">
                <button id="detail-qr-btn" onclick="downloadQrCode(currentDetailId)" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition" style="background:rgba(0,212,255,0.15);color:#00d4ff;border:1px solid rgba(0,212,255,0.3);cursor:pointer;display:inline-flex;align-items:center;gap:4px;" title="Download QR Code">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    QR
                </button>
                <button id="detail-label-btn" onclick="printLabel(currentDetailId)" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition" style="background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.3);cursor:pointer;display:inline-flex;align-items:center;gap:4px;" title="Cetak Label">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Label
                </button>
                @if(auth()->user()->role !== 'gm' && auth()->user()->role !== 'ceo')
                <button id="detail-edit-btn" onclick="openEditModal(currentDetailId)" class="px-4 py-1.5 rounded-lg text-xs font-semibold transition" style="background:linear-gradient(135deg,#6c5cff,#8b7bff);color:#fff;border:none;cursor:pointer;">Edit</button>
                <form id="detail-delete-form" method="POST" onsubmit="confirmSubmit(event, this)" data-confirm="Hapus peralatan ini?" data-action="{{ url('admin/peralatan-kantor') }}/" style="margin:0;">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-semibold transition" style="background:rgba(239,68,68,0.15);color:#ef4444;border:1px solid rgba(239,68,68,0.3);cursor:pointer;">Hapus</button>
                </form>
                @endif
                <button onclick="closeDetail()" class="p-1.5 rounded-xl transition" style="color:var(--text-muted);background:none;border:none;cursor:pointer;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        {{-- Title + Badge --}}
        <div class="px-6 pt-5 pb-2 flex-shrink-0">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold" style="color:var(--text-primary);" id="detail-title"></h3>
                    <p class="text-sm mt-1" style="color:var(--text-muted);">Detail lengkap peralatan kantor</p>
                </div>
                <span id="detail-badge" class="badge" style="font-size:0.75rem;padding:4px 14px;"></span>
            </div>
        </div>
        {{-- Foto + Barcode Side by Side --}}
        <div class="px-6 py-3 flex-shrink-0" id="detail-media-section">
            <div class="detail-media-wrap">
                {{-- Foto Kiri --}}
                <div id="detail-foto-section" class="detail-foto-col" style="display:none;">
                    <div style="border-radius:14px;overflow:hidden;border:1px solid var(--border-color);">
                        <img id="detail-foto-img" src="" alt="Foto Barang" style="width:100%;height:120px;object-fit:cover;display:block;">
                    </div>
                </div>
                {{-- Barcode Kanan --}}
                <div id="detail-barcode-section" class="detail-barcode-col" style="display:none;">
                    <div style="background:var(--bg-surface-2);border:1px solid var(--border-color);border-radius:14px;padding:14px;text-align:center;">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span style="color:var(--text-muted);font-size:0.7rem;">Kode Aset:</span>
                                <span id="detail-kode-aset" style="color:var(--color-accent);font-weight:700;font-family:monospace;font-size:0.85rem;"></span>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button type="button" onclick="printBarcode()" class="btn btn-secondary btn-sm inline-flex items-center gap-1" style="font-size:0.65rem;padding:3px 8px;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Cetak
                                </button>
                                <button type="button" onclick="downloadBarcode()" class="btn btn-secondary btn-sm inline-flex items-center gap-1" style="font-size:0.65rem;padding:3px 8px;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                                    Unduh
                                </button>
                            </div>
                        </div>
                        <div style="display:flex;gap:8px;align-items:center;justify-content:center;">
                            <div id="detail-barcode-image" style="flex:0 1 auto;min-width:0;display:flex;justify-content:center;"></div>
                            <div id="detail-qr-row" class="hidden" style="flex-shrink:0;text-align:center;">
                                <img id="detail-qr-img" src="" alt="QR Detail" style="width:56px;height:56px;">
                            </div>
                        </div>
                        <div id="detail-qr-caption" class="hidden" style="margin-top:6px;text-align:center;">
                            <p style="font-size:9px;color:var(--text-muted);">Scan QR untuk lihat detail</p>
                            <a id="detail-qr-link" href="#" target="_blank" rel="noopener" style="font-size:9px;color:var(--color-accent);word-break:break-all;text-decoration:underline;">Buka halaman detail</a>
                        </div>
                    </div>
                </div>
                {{-- Placeholder when both hidden --}}
                <div id="detail-media-placeholder" style="flex:1;text-align:center;padding:20px;color:var(--text-muted);font-size:0.8rem;">Tidak ada foto atau barcode</div>
            </div>
        </div>
        {{-- Body --}}
        <div class="px-6 py-4 overflow-y-auto flex-1" id="detail-body" style="scrollbar-width:thin;"></div>
    </div>
</div>

{{-- Label Preview Modal --}}
<div id="label-modal" style="display:none;position:fixed;inset:0;z-index:60;align-items:center;justify-content:center;padding:16px;background:var(--bg-overlay);" onclick="if(event.target===this)closeLabelModal()">
    <div class="w-full max-w-[360px] rounded-[20px] shadow-2xl flex flex-col" style="background:var(--bg-surface);border:1px solid var(--border-color);" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-5 py-3" style="border-bottom:1px solid var(--border-color);">
            <h3 class="text-sm font-bold" style="color:var(--text-primary);">Preview Label</h3>
            <button onclick="closeLabelModal()" class="p-1 rounded-lg" style="color:var(--text-muted);background:none;border:none;cursor:pointer;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="px-5 py-4 flex flex-col items-center" id="label-preview-content">
            <div id="label-card" style="width:280px;border:2px solid var(--border-color);border-radius:14px;padding:18px;text-align:center;background:var(--bg-surface);">
                <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin:0 auto 8px;">
                    <img id="label-logo" src="{{ asset('images/logo/logo johengaming.jpg') }}" alt="Logo" style="width:36px;height:36px;object-fit:contain;">
                    <p style="font-size:11px;font-weight:800;letter-spacing:0.08em;color:var(--text-primary);">JSA PERALATAN KANTOR</p>
                </div>
                <p id="label-nama" style="font-size:12px;font-weight:600;color:var(--text-primary);margin-bottom:3px;"></p>
                <p id="label-kode" style="font-size:10px;font-family:monospace;color:#7c3aed;font-weight:700;margin-bottom:10px;"></p>
                <div style="display:flex;gap:6px;align-items:center;justify-content:center;">
                    <div id="label-qr-container" style="flex-shrink:0;"></div>
                    <div id="label-barcode-container" style="flex:1;min-width:0;"></div>
                </div>
                <p id="label-url" style="font-size:7px;color:var(--text-muted);word-break:break-all;"></p>
            </div>
        </div>
        <div class="px-5 py-3 flex items-center justify-end gap-2" style="border-top:1px solid var(--border-color);">
            <button onclick="printLabelFromModal()" class="btn btn-primary btn-sm inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak
            </button>
            <button onclick="closeLabelModal()" class="btn btn-secondary btn-sm">Tutup</button>
        </div>
    </div>
</div>

{{-- Modal Tambah / Edit Peralatan Kantor (6 Step) --}}
<div id="item-modal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:var(--bg-overlay);">
    <div class="w-full max-w-3xl rounded-3xl shadow-2xl flex flex-col" style="max-height:95vh;background:var(--bg-surface);" onclick="event.stopPropagation()">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 flex-shrink-0" style="border-bottom:1px solid var(--border-color);">
            <h3 class="text-base font-bold" style="color:var(--text-primary);" id="modal-title">Tambah Peralatan</h3>
            <button type="button" onclick="closeModal('item-modal')" class="p-1.5 rounded-xl transition" style="color:var(--text-muted);background:none;border:none;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Steps Indicator --}}
        <div class="px-6 pt-4 pb-2 flex-shrink-0">
            <div class="flex items-center gap-1 text-xs font-semibold" id="step-indicator">
                <div class="step-dot active" data-step="1">1</div>
                <div class="step-line" data-step="1"></div>
                <div class="step-dot" data-step="2">2</div>
                <div class="step-line" data-step="2"></div>
                <div class="step-dot" data-step="3">3</div>
                <div class="step-line" data-step="3"></div>
                <div class="step-dot" data-step="4">4</div>
                <div class="step-line" data-step="4"></div>
                <div class="step-dot" data-step="5">5</div>
                <div class="step-line" data-step="5"></div>
                <div class="step-dot" data-step="6">6</div>
            </div>
        </div>

        {{-- Body --}}
        <div class="px-6 py-4 overflow-y-auto flex-1">
            <form id="item-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="id" id="form-id" value="">

                {{-- Step 1 --}}
                <div class="step-content" id="step-1">
                    <p class="text-sm font-bold mb-1" style="color:var(--text-primary);">Informasi Umum</p>
                    <p class="text-xs mb-4" style="color:var(--text-muted);">Data dasar barang</p>
                    <div class="space-y-4">
                        <div>
                            <label class="gaming-label">Nama Barang <span style="color:#f87171;">*</span></label>
                            <input type="text" name="nama_barang" id="f-nama_barang" required placeholder="Masukan nama barang" class="gaming-input">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="gaming-label">Jumlah <span style="color:#f87171;">*</span></label>
                                <input type="number" name="jumlah" id="f-jumlah" required placeholder="Masukan jumlah unit" class="gaming-input" min="1">
                                <p class="text-xs mt-1" style="color:var(--text-muted);">Minimal 1 unit</p>
                            </div>
                            <div>
                                <label class="gaming-label">Foto Barang</label>
                                <div class="flex items-center gap-3">
                                    <input type="file" name="foto" id="f-foto" accept="image/jpeg,image/jpg,image/png,image/webp" class="gaming-input flex-1" style="padding:6px;">
                                    <div id="f-foto-preview" class="hidden flex-shrink-0 relative">
                                        <img id="f-foto-preview-img" src="" alt="Preview" style="max-width:72px;max-height:48px;border-radius:8px;object-fit:cover;border:1px solid var(--border-color);">
                                        <button type="button" onclick="clearFotoPreview()" aria-label="Hapus foto" title="Hapus foto" style="position:absolute;top:-6px;right:-6px;width:18px;height:18px;border-radius:9999px;background:#ef4444;color:#fff;border:2px solid var(--bg-surface);cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-xs mt-1" style="color:var(--text-muted);">Format: JPG, PNG, WebP. Maks 2MB.</p>
                            </div>
                        </div>
                        <div>
                            <label class="gaming-label">Kondisi <span style="color:#f87171;">*</span></label>
                            <select id="f-kondisi" required onchange="kondisiSelectChanged()" class="gaming-input">
                                <option value="">-- Pilih Kondisi --</option>
                                <option value="baik">Baik</option>
                                <option value="perlu_servis">Perlu Servis</option>
                                <option value="rusak">Rusak</option>
                                @foreach($kondisiOptions as $opt)
                                    @if(!in_array($opt, ['baik','perlu_servis','rusak']))
                                    <option value="{{ $opt }}">{{ ucwords(str_replace('_', ' ', $opt)) }}</option>
                                    @endif
                                @endforeach
                                <option value="__custom__">Lainnya (tulis manual)...</option>
                            </select>
                            <input type="text" id="f-kondisi-custom" placeholder="Tulis kondisi lain (contoh: Dalam Perbaikan)" class="gaming-input" style="display:none;margin-top:8px;" oninput="syncKondisi()">
                            <input type="hidden" name="kondisi" id="f-kondisi-hidden" value="baik">
                        </div>
                        <div>
                            <label class="gaming-label">Detail <span style="color:#f87171;">*</span></label>
                            <textarea name="detail" id="f-detail" required placeholder="Masukan detail barang" rows="2" class="gaming-input" style="resize:vertical;"></textarea>
                        </div>
                        <div>
                            <label class="gaming-label">Keterangan</label>
                            <textarea name="keterangan" id="f-keterangan" placeholder="Masukan keterangan" rows="2" class="gaming-input" style="resize:vertical;"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="step-content hidden" id="step-2">
                    <p class="text-sm font-bold mb-1" style="color:var(--text-primary);">Lokasi</p>
                    <p class="text-xs mb-4" style="color:var(--text-muted);">Lokasi penempatan barang</p>
                    <div class="space-y-4">
                        <div>
                            <label class="gaming-label">Lokasi Unit <span style="color:#f87171;">*</span></label>
                            <input type="text" name="lokasi_unit" id="f-lokasi_unit" required placeholder="Masukan lokasi unit" class="gaming-input">
                        </div>
                        <div>
                            <label class="gaming-label">Ruangan <span style="color:#f87171;">*</span></label>
                            <input type="text" name="ruangan" id="f-ruangan" required placeholder="Masukan ruangan" class="gaming-input">
                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="step-content hidden" id="step-3">
                    <p class="text-sm font-bold mb-1" style="color:var(--text-primary);">Pengadaan & Nilai</p>
                    <p class="text-xs mb-4" style="color:var(--text-muted);">Data pengadaan, kategori, dan nilai</p>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="gaming-label">Pengadaan (in tahun) <span style="color:#f87171;">*</span></label>
                                <input type="number" name="pengadaan_tahun" id="f-pengadaan_tahun" required placeholder="Masukan tahun pengadaan" class="gaming-input" min="1900" max="{{ now()->year + 1 }}">
                            </div>
                            <div>
                                <label class="gaming-label">Tanggal Pembelian <span style="color:#f87171;">*</span></label>
                                <input type="date" name="tanggal_pembelian" id="f-tanggal_pembelian" required class="gaming-input" oninput="hitungPenyusutan()">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="gaming-label">Kategori Nilai <span style="color:#f87171;">*</span></label>
                                <select name="kategori_nilai" id="f-kategori_nilai" required class="gaming-input">
                                    <option value="Rendah">Rendah</option>
                                    <option value="Menengah">Menengah</option>
                                    <option value="Tinggi">Tinggi</option>
                                </select>
                            </div>
                            <div>
                                <label class="gaming-label">Kategori Ukuran <span style="color:#f87171;">*</span></label>
                                <select name="kategori_ukuran" id="f-kategori_ukuran" required class="gaming-input">
                                    <option value="Kecil">Kecil</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Besar">Besar</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="gaming-label">Sub-Kategori <span style="color:#f87171;">*</span></label>
                            <select name="sub_kategori" id="f-sub_kategori" required class="gaming-input">
                                <option value="Peralatan Kantor">Peralatan Kantor</option>
                                <option value="Alat Tulis">Alat Tulis</option>
                                <option value="Elektronik">Elektronik</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="gaming-label">Tim (Aset Tim)</label>
                            <select name="tim" id="f-tim" class="gaming-input">
                                <option value="">— Tanpa Tim —</option>
                                @foreach($allTim as $tim)
                                <option value="{{ $tim }}">{{ $tim }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="gaming-label">Milik <span style="color:#f87171;">*</span></label>
                            <select name="milik" id="f-milik" required class="gaming-input">
                                <option value="Milik Perusahaan">Milik Perusahaan</option>
                                <option value="Sewa">Sewa</option>
                                <option value="Pinjaman">Pinjaman</option>
                            </select>
                        </div>
                        <div>
                            <label class="gaming-label">Nilai (Rp) <span style="color:#f87171;">*</span></label>
                            <input type="number" name="nilai" id="f-nilai" required placeholder="Masukan nilai aset" class="gaming-input" min="0" step="0.01" oninput="hitungPenyusutan()">
                        </div>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="step-content hidden" id="step-4">
                    <p class="text-sm font-bold mb-1" style="color:var(--text-primary);">Penyusutan Umur Aset</p>
                    <p class="text-xs mb-4" style="color:var(--text-muted);">Perhitungan penyusutan otomatis</p>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="gaming-label">Waktu Pakai Barang Perhari Ini <span style="color:#f87171;">*</span></label>
                                <input type="number" name="waktu_pakai_per_hari" id="f-waktu_pakai_per_hari" required value="2" class="gaming-input" min="1" oninput="hitungPenyusutan()">
                            </div>
                            <div>
                                <label class="gaming-label">Estimasi Waktu Barang <span style="color:#f87171;">*</span></label>
                                <input type="number" name="estimasi_waktu_barang" id="f-estimasi_waktu_barang" required value="360" class="gaming-input" min="1" oninput="hitungPenyusutan()">
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Contoh: 360 hari (1 tahun)</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="gaming-label">Pengurangan Harga Aset Perhari (Rp)</label>
                                <div id="penyusutan-display" class="gaming-input" style="padding:8px 12px;background:var(--bg-surface-2);color:var(--text-muted);border-radius:8px;font-size:13px;cursor:default;">—</div>
                            </div>
                            <div>
                                <label class="gaming-label">Harga Barang Perhari Ini (Rp)</label>
                                <div id="nilai-sekarang-display" class="gaming-input" style="padding:8px 12px;background:var(--bg-surface-2);color:var(--text-muted);border-radius:8px;font-size:13px;cursor:default;">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 5 --}}
                <div class="step-content hidden" id="step-5">
                    <p class="text-sm font-bold mb-1" style="color:var(--text-primary);">Penanggung Jawab</p>
                    <p class="text-xs mb-4" style="color:var(--text-muted);">Data penanggung jawab</p>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="gaming-label">PIC <span style="color:#f87171;">*</span></label>
                                <select name="pic" id="f-pic" required class="gaming-input gaming-select">
                                    <option value="">— Pilih PIC —</option>
                                    @foreach(\App\Models\User::where('is_active', true)->orderBy('name')->get() as $u)
                                    <option value="{{ $u->name }}">{{ $u->name }} ({{ $u->username }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="gaming-label">Jabatan PIC <span style="color:#f87171;">*</span></label>
                                <select name="jabatan" id="f-jabatan" required class="gaming-input gaming-select">
                                    <option value="">Pilih Jabatan</option>
                                    <option value="Chief Executive Officer (CEO)">Chief Executive Officer (CEO)</option>
                                    <option value="General Manager (GM)">General Manager (GM)</option>
                                    <option value="Head of Store">Head of Store</option>
                                    <option value="Admin Master">Admin Master</option>
                                    <option value="HR">HR</option>
                                    <option value="Koordinator">Koordinator</option>
                                    <option value="Karyawan">Karyawan</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="gaming-label">Atasan <span style="color:#f87171;">*</span></label>
                                <input type="text" name="atasan" id="f-atasan" required placeholder="Masukan nama atasan" class="gaming-input">
                            </div>
                            <div>
                                <label class="gaming-label">Jabatan Atasan <span style="color:#f87171;">*</span></label>
                                <select name="jabatan_atasan" id="f-jabatan_atasan" required class="gaming-input gaming-select">
                                    <option value="">Pilih Jabatan</option>
                                    <option value="Chief Executive Officer (CEO)">Chief Executive Officer (CEO)</option>
                                    <option value="General Manager (GM)">General Manager (GM)</option>
                                    <option value="Head of Store">Head of Store</option>
                                    <option value="Admin Master">Admin Master</option>
                                    <option value="HR">HR</option>
                                    <option value="Koordinator">Koordinator</option>
                                    <option value="Karyawan">Karyawan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 6 Preview --}}
                <div class="step-content hidden" id="step-6">
                    <p class="text-sm font-bold mb-1" style="color:var(--text-primary);">Pratinjau Data</p>
                    <p class="text-xs mb-4" style="color:var(--text-muted);">Periksa kembali data sebelum menyimpan</p>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3" id="preview-content">
                        {{-- Card 1: Informasi Umum --}}
                        <div style="background:var(--bg-surface-2);border:1px solid var(--border-color);border-radius:14px;padding:14px;">
                            <p style="color:var(--color-accent);font-size:0.75rem;font-weight:700;letter-spacing:0.05em;margin-bottom:10px;text-transform:uppercase;">Informasi Umum</p>
                            <div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Nama Barang</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-nama_barang">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Jumlah</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-jumlah">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Detail</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-detail">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Kondisi</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-kondisi">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Keterangan</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-keterangan">-</span>
                                </div>
                            </div>
                        </div>
                        {{-- Card 2: Lokasi --}}
                        <div style="background:var(--bg-surface-2);border:1px solid var(--border-color);border-radius:14px;padding:14px;">
                            <p style="color:var(--color-accent);font-size:0.75rem;font-weight:700;letter-spacing:0.05em;margin-bottom:10px;text-transform:uppercase;">Lokasi</p>
                            <div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Lokasi Unit</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-lokasi_unit">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Ruangan</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-ruangan">-</span>
                                </div>
                            </div>
                        </div>
                        {{-- Card 3: Pengadaan & Nilai --}}
                        <div style="background:var(--bg-surface-2);border:1px solid var(--border-color);border-radius:14px;padding:14px;">
                            <p style="color:var(--color-accent);font-size:0.75rem;font-weight:700;letter-spacing:0.05em;margin-bottom:10px;text-transform:uppercase;">Pengadaan &amp; Nilai</p>
                            <div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Pengadaan (in tahun)</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-pengadaan_tahun">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Tanggal Pembelian</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-tanggal_pembelian">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Kategori Nilai</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-kategori_nilai">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Kategori Ukuran</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-kategori_ukuran">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Sub-Kategori</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-sub_kategori">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Milik</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-milik">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Nilai (Rp)</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-nilai">-</span>
                                </div>
                            </div>
                        </div>
                        {{-- Card 4: Penyusutan Umur Aset --}}
                        <div style="background:var(--bg-surface-2);border:1px solid var(--border-color);border-radius:14px;padding:14px;">
                            <p style="color:var(--color-accent);font-size:0.75rem;font-weight:700;letter-spacing:0.05em;margin-bottom:10px;text-transform:uppercase;">Penyusutan Umur Aset</p>
                            <div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Waktu Pakai Barang Perhari Ini</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-waktu_pakai_per_hari">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Estimasi Waktu Barang</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-estimasi_waktu_barang">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Hari Terpakai</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-hari_terpakai">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Pengurangan Harga Aset Perhari</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-pengurangan_harga_per_hari">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Nilai Awal</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-nilai_awal">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Harga Barang Perhari Ini</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-harga_per_hari_ini">-</span>
                                </div>
                            </div>
                        </div>
                        {{-- Card 5: Penanggung Jawab --}}
                        <div style="background:var(--bg-surface-2);border:1px solid var(--border-color);border-radius:14px;padding:14px;">
                            <p style="color:var(--color-accent);font-size:0.75rem;font-weight:700;letter-spacing:0.05em;margin-bottom:10px;text-transform:uppercase;">Penanggung Jawab</p>
                            <div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">PIC</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-pic">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Jabatan PIC</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-jabatan">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border-color);">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Atasan</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-atasan">-</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;">
                                    <span style="color:var(--text-muted);font-size:0.75rem;">Jabatan Atasan</span>
                                    <span style="color:var(--text-primary);font-size:0.8rem;font-weight:600;text-align:right;margin-left:8px;" id="pv-jabatan_atasan">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <div class="flex items-center pt-4 mt-4" style="border-top:1px solid var(--border-color);">
                    <div class="flex gap-3">
                        <button type="button" id="prev-btn" onclick="prevStep()" class="btn btn-secondary" style="display:none;">Sebelumnya</button>
                    </div>
                    <div class="flex gap-3 ml-auto">
                        <button type="button" onclick="closeModal('item-modal')" class="btn btn-secondary" id="cancel-btn">Batal</button>
                        <button type="button" id="next-btn" onclick="nextStep()" class="btn btn-primary">Selanjutnya</button>
                        <button type="button" id="submit-btn" class="btn btn-primary" style="display:none;" onclick="submitForm()">Simpan</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

{{-- Modal Import Excel --}}
<div id="import-modal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:var(--bg-overlay);">
    <div class="w-full max-w-lg rounded-3xl shadow-2xl flex flex-col" style="background:var(--bg-surface);" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 flex-shrink-0" style="border-bottom:1px solid var(--border-color);">
            <h3 class="text-base font-bold" style="color:var(--text-primary);">Import Excel Peralatan Kantor</h3>
            <button type="button" onclick="closeModal('import-modal')" class="p-1.5 rounded-xl transition" style="color:var(--text-muted);background:none;border:none;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="px-6 py-5">
            <p class="text-sm mb-3" style="color:var(--text-muted);">Download template terlebih dahulu, lalu isi data sesuai format.</p>
            <a href="{{ route('admin.peralatan-kantor.template') }}" class="btn btn-secondary btn-sm inline-flex items-center gap-1.5 mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
                </svg>
                Download Template
            </a>
            <form method="POST" action="{{ route('admin.peralatan-kantor.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="field-group" style="margin-bottom:16px;">
                    <label class="gaming-label">Pilih File Excel <span class="field-req">*</span></label>
                    <input type="file" name="file" id="import-file" accept=".xlsx,.xls,.csv" required
                        class="gaming-input" style="padding:8px 12px;">
                    <p style="font-size:11px;color:var(--text-muted);margin-top:4px;">Format: xlsx, xls, csv. Maksimal 5 MB.</p>
                </div>
                <div class="form-footer" style="padding-top:0;border:none;">
                    <button type="button" onclick="closeModal('import-modal')" class="btn-form btn-form-batal">Batal</button>
                    <button type="submit" class="btn-form btn-form-simpan" id="import-submit-btn">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Scan Barcode --}}
<div id="scan-modal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:var(--bg-overlay);">
    <div class="w-full max-w-lg rounded-3xl shadow-2xl flex flex-col" style="max-height:90vh;background:var(--bg-surface);border:1px solid var(--border-color);" onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 flex-shrink-0" style="border-bottom:1px solid var(--border-color);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(124,58,237,0.15);">
                    <svg class="w-5 h-5" style="color:#a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold" style="color:var(--text-primary);">Scan Barcode</h3>
                    <p class="text-xs" style="color:var(--text-muted);">Arahkan kamera ke barcode aset</p>
                </div>
            </div>
            <button type="button" onclick="closeScanModal()" class="p-1.5 rounded-xl transition" style="color:var(--text-muted);background:none;border:none;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Camera View --}}
        <div class="px-6 py-4 flex-1 overflow-y-auto">
            {{-- Status Indicator --}}
            <div id="scan-status" class="mb-4 text-center">
                <div id="scan-status-idle" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold" style="background:rgba(124,58,237,0.15);color:#a78bfa;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Siap Memindai
                </div>
                <div id="scan-status-scanning" class="hidden inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold" style="background:rgba(59,130,246,0.15);color:#60a5fa;">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                    Memindai...
                </div>
                <div id="scan-status-success" class="hidden inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold" style="background:rgba(16,185,129,0.15);color:#34d399;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Barcode Dikenali!
                </div>
                <div id="scan-status-error" class="hidden inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold" style="background:rgba(239,68,68,0.15);color:#f87171;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span id="scan-status-error-text">Data aset tidak ditemukan.</span>
                </div>
            </div>

            {{-- Camera Container --}}
            <div id="camera-container" class="relative overflow-hidden rounded-2xl mb-4" style="background:#000;aspect-ratio:4/3;">
                <video id="camera-video" autoplay playsinline muted style="width:100%;height:100%;object-fit:cover;"></video>
                <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center" style="background:rgba(0,0,0,0.6);">
                    <svg class="w-16 h-16 mb-3" style="color:rgba(255,255,255,0.3);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-xs" style="color:rgba(255,255,255,0.5);">Klik "Mulai Scan" untuk mengaktifkan kamera</p>
                </div>
                {{-- Scan Line Animation --}}
                <div id="scan-line" class="hidden" style="position:absolute;left:10%;right:10%;height:2px;background:linear-gradient(90deg,transparent,#a78bfa,transparent);box-shadow:0 0 12px rgba(124,58,237,0.6);animation:scanLine 2s ease-in-out infinite;"></div>
            </div>

            {{-- Manual Input --}}
            <div class="mb-4">
                <p class="text-xs font-semibold mb-2" style="color:var(--text-muted);">Atau masukkan kode secara manual:</p>
                <div class="flex gap-2">
                    <input type="text" id="manual-code-input" placeholder="Masukkan kode aset atau barcode" class="gaming-input flex-1" style="font-family:monospace;font-size:0.85rem;" onkeypress="if(event.key==='Enter'){manualScan();}">
                    <button type="button" onclick="manualScan()" class="btn btn-primary btn-sm whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cari
                    </button>
                </div>
            </div>

            {{-- No Camera Warning --}}
            <div id="no-camera-warning" class="hidden mb-4 p-3 rounded-xl text-xs" style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);color:#fbbf24;">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="font-semibold">Kamera tidak tersedia</p>
                        <p class="mt-1" style="color:var(--text-muted);">Gunakan input manual di bawah untuk memasukkan kode aset atau barcode.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="px-6 py-4 flex-shrink-0 flex items-center justify-between" style="border-top:1px solid var(--border-color);">
            <button type="button" id="scan-toggle-btn" onclick="toggleScan()" class="btn btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span id="scan-toggle-text">Mulai Scan</span>
            </button>
            <button type="button" onclick="closeScanModal()" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</div>

@if(session('import_errors'))
<div class="pt-2">
    <div class="gaming-card p-4" style="border-left:4px solid #f59e0b;">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color:#f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold" style="color:var(--text-primary);">
                    Import Selesai: {{ session('import_success_count') }} berhasil, {{ session('import_error_count') }} gagal.
                </p>
                @if(session('import_errors'))
                <div class="mt-2 max-h-[200px] overflow-y-auto" style="scrollbar-width:thin;">
                    <ul style="list-style:none;padding:0;margin:0;">
                        @foreach(session('import_errors') as $error)
                        <li style="font-size:12px;color:#ef4444;padding:2px 0;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            <button type="button" onclick="this.closest('.gaming-card').remove()" class="ml-auto p-1" style="background:none;border:none;cursor:pointer;color:var(--text-muted);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>
@endif
{{-- Popup Alert Kondisi Peralatan --}}
<div id="alert-overlay" style="display:none;position:fixed;inset:0;z-index:9999;background:var(--bg-overlay);align-items:center;justify-content:center;padding:16px;" onclick="if(event.target===this)closeAlertPopup()">
    <div style="background:var(--bg-surface);border-radius:16px;padding:24px;width:90%;max-width:460px;max-height:65vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <div id="alert-popup-title" style="font-weight:700;font-size:16px;color:var(--text-primary);">Detail Kondisi Peralatan</div>
            <button type="button" onclick="closeAlertPopup()" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:20px;line-height:1;">&times;</button>
        </div>
        <div id="alert-popup-body"></div>
    </div>
</div>

@endsection

@push('styles')
<style>
.detail-media-wrap {
    display: flex;
    gap: 10px;
    align-items: flex-start;
}
.detail-media-wrap .detail-foto-col {
    flex: 0 0 38%;
    max-width: 38%;
}
.detail-media-wrap .detail-barcode-col {
    flex: 1;
    min-width: 0;
}
#detail-barcode-image svg {
    max-width: 100%;
    height: auto;
}
@media (max-width: 640px) {
    #asset-card-header {
        align-items: stretch !important;
        flex-direction: column;
        gap: 12px;
        padding: 16px !important;
    }
    #asset-card-header > div:last-child {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }
    #asset-card-header > div:last-child button {
        justify-content: center;
        min-width: 0;
        white-space: normal;
    }
    #asset-toolbar {
        align-items: stretch;
        gap: 8px;
        padding: 12px !important;
    }
    #asset-toolbar > .relative {
        flex: 1 1 100%;
        max-width: none !important;
        min-width: 0;
    }
    #asset-toolbar > .flex.items-center {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        width: 100%;
        margin-left: 0 !important;
    }
    #asset-toolbar > .flex.items-center > button,
    #asset-toolbar > .flex.items-center > .filter-dropdown-wrap > button {
        width: 100%;
        min-width: 0;
        justify-content: center;
        padding-left: 8px !important;
        padding-right: 8px !important;
    }
    #asset-toolbar .filter-dropdown-wrap {
        min-width: 0;
    }
    #asset-toolbar .filter-dropdown-wrap > div[id$="-menu"] {
        left: 0;
        right: auto !important;
        min-width: min(220px, calc(100vw - 40px)) !important;
    }
    #item-table {
        min-width: 2200px !important;
    }
    #export-period-modal {
        padding: 12px !important;
        overflow-y: auto;
    }
    #export-period-modal .modal-content {
        padding: 18px !important;
        max-height: calc(100dvh - 24px);
        overflow-y: auto;
    }
    #export-period-modal form > div:last-child {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
    #export-period-modal form > div:last-child button {
        width: 100%;
        justify-content: center;
    }
    .detail-media-wrap {
        flex-direction: column;
    }
    .detail-media-wrap .detail-foto-col,
    .detail-media-wrap .detail-barcode-col {
        flex: none;
        max-width: 100%;
        width: 100%;
    }
}
.step-dot {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; flex-shrink: 0;
    background: var(--bg-surface-2); color: var(--text-muted);
    border: 2px solid var(--border-color); transition: all 0.3s;
}
.step-dot.active {
    background: var(--color-accent); color: #fff;
    border-color: var(--color-accent);
    box-shadow: 0 0 12px rgba(124,58,237,0.4);
}
.step-dot.done {
    background: #10b981; color: #fff;
    border-color: #10b981;
}
.step-line {
    flex: 1; height: 2px;
    background: var(--border-color); transition: all 0.3s;
    margin: 0 4px; border-radius: 2px;
}
.step-line.done { background: #10b981; }
#step-indicator { display: flex; align-items: center; }
.form-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding-top: 16px;
    margin-top: 8px;
    border-top: 1px solid var(--border-color);
}
.btn-form {
    padding: 8px 22px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}
.btn-form-batal {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.15);
    color: rgba(255,255,255,0.7);
}
.btn-form-batal:hover {
    border-color: rgba(255,255,255,0.3);
    color: #fff;
}
.btn-form-simpan {
    background: linear-gradient(135deg, #6c5cff, #8b7bff);
    color: #fff;
    box-shadow: 0 4px 15px rgba(108,92,255,0.3);
}
.btn-form-simpan:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(108,92,255,0.4);
}
.gaming-table tbody td { padding: 0.75rem 1.125rem; vertical-align: middle; font-size:0.8rem; }
.gaming-table thead th { padding: 0.625rem 1.125rem; font-size:0.65rem; letter-spacing:0.03em; }
@keyframes scanLine {
    0% { top: 10%; }
    50% { top: 85%; }
    100% { top: 10%; }
}
#camera-container { position: relative; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const itemsData = @json($itemsJson);
const timKoordinators = @json($timKoordinators);

function isiPicDariTim(timValue, paksa) {
    const info = timKoordinators[timValue];
    if (!info) return;
    const picEl = document.getElementById('f-pic');
    if (paksa || !picEl.value) {
        picEl.value = info.pic;
        const jabatanEl = document.getElementById('f-jabatan');
        if ([...jabatanEl.options].some(o => o.value === 'Koordinator')) {
            jabatanEl.value = 'Koordinator';
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const fTim = document.getElementById('f-tim');
    if (fTim) {
        fTim.addEventListener('change', function () {
            isiPicDariTim(this.value, true);
        });
    }
});
const alertData = @json($alertJson);

const csrfToken = '{{ csrf_token() }}';
const itemBaseUrl = '{{ url('admin/peralatan-kantor') }}';
const userRole = '{{ auth()->user()->role }}';
const canEditRow = userRole !== 'gm' && userRole !== 'ceo';
const originalTbodyHtml = document.getElementById('item-tbody') ? document.getElementById('item-tbody').innerHTML : '';
const originalFooterHtml = document.getElementById('table-footer-inner') ? document.getElementById('table-footer-inner').innerHTML : '';

function kondisiBadgeInfo(k) {
    if (k === 'baik') return '<span class="badge badge-green">Baik</span>';
    if (k === 'perlu_servis') return '<span class="badge badge-yellow">Perlu Servis</span>';
    if (k === 'rusak') return '<span class="badge badge-red">Rusak</span>';
    return '<span class="badge badge-gray">' + (k ? String(k).replace(/_/g, ' ') : '-') + '</span>';
}

function itemSearchText(i) {
    if (i.__search === undefined) {
        i.__search = [i.nama_barang, i.kode_aset, i.barcode, i.pic, i.jabatan, i.atasan, i.jabatan_atasan, i.lokasi_unit, i.ruangan, i.tim, i.detail, i.keterangan, i.sub_kategori, i.milik, i.kondisi]
            .map(s => (s == null ? '' : String(s))).join(' ').toLowerCase();
    }
    return i.__search;
}

function rowHtml(i, idx) {
    const fmtRp = v => 'Rp' + Number(v || 0).toLocaleString('id-ID');
    const fmtRp2 = v => Number(v || 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const fmtTgl = v => v ? String(v).split('-').reverse().join('/') : '-';
    const green = i.barcode_ditempel ? ' style="background:rgba(16,185,129,0.08);"' : '';
    const timBadge = i.tim
        ? '<span class="badge" style="background:rgba(124,58,237,0.12);color:#a78bfa;border:1px solid rgba(124,58,237,0.25);">' + i.tim + '</span>'
        : '<span style="color:var(--text-muted);font-size:0.75rem;">-</span>';
    const barcodeBadge = i.barcode_ditempel
        ? (canEditRow
            ? '<span class="badge" onclick="toggleBarcodeDitempel(' + i.id + ')" title="Klik untuk membatalkan tanda" style="display:block;margin-top:2px;background:rgba(16,185,129,0.12);color:#34d399;border:1px solid rgba(16,185,129,0.3);font-size:0.6rem;cursor:pointer;user-select:none;">✓ Sudah Ditempel</span>'
            : '<span class="badge" style="display:block;margin-top:2px;background:rgba(16,185,129,0.12);color:#34d399;border:1px solid rgba(16,185,129,0.3);font-size:0.6rem;">✓ Sudah Ditempel</span>')
        : '';
    const barcodeCell = '<td style="color:' + (i.barcode_ditempel ? '#34d399' : 'var(--text-muted)') + ';font-family:monospace;font-size:0.7rem;white-space:nowrap;">' + (i.barcode || '') + barcodeBadge + '</td>';
    const hargaSekarang = parseFloat(i.harga_per_hari_ini) || 0;
    let aksi = '<td><div class="flex items-center gap-1">' +
        '<button type="button" onclick="showDetail(' + i.id + ')" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:4px;padding:3px 6px;font-size:0.7rem;">' +
        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Lihat Detail</button>' +
        '<div class="dropdown-wrap" style="position:relative;">' +
        '<button type="button" onclick="toggleDropdown(this, ' + i.id + ')" class="btn btn-secondary btn-sm" style="padding:3px 6px;font-size:0.7rem;line-height:1;">⋮</button>' +
        '<div id="dropdown-' + i.id + '" class="dropdown-menu" style="display:none;position:absolute;top:100%;right:0;z-index:99999;min-width:150px;background:var(--bg-surface);border:1px solid var(--border-color);border-radius:10px;padding:4px;box-shadow:0 8px 24px rgba(0,0,0,0.15);margin-top:4px;">' +
        '<button type="button" onclick="showDetail(' + i.id + ')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background=\'var(--bg-surface-2)\'" onmouseout="this.style.background=\'none\'">Detail</button>';
    if (canEditRow) {
        aksi += '<button type="button" onclick="openEditModal(' + i.id + ')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background=\'var(--bg-surface-2)\'" onmouseout="this.style.background=\'none\'">Edit</button>' +
            '<button type="button" onclick="toggleBarcodeDitempel(' + i.id + ')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:' + (i.barcode_ditempel ? '#ef4444' : '#34d399') + ';border-radius:6px;cursor:pointer;" onmouseover="this.style.background=\'var(--bg-surface-2)\'" onmouseout="this.style.background=\'none\'">' + (i.barcode_ditempel ? 'Batalkan Tanda Barcode' : 'Tandai Sudah Ditempel') + '</button>' +
            '<form method="POST" action="' + itemBaseUrl + '/' + i.id + '" onsubmit="confirmSubmit(event, this)" data-confirm="Hapus peralatan ini?" style="margin:0;">' +
            '<input type="hidden" name="_token" value="' + csrfToken + '"><input type="hidden" name="_method" value="DELETE">' +
            '<button type="submit" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:#ef4444;border-radius:6px;cursor:pointer;" onmouseover="this.style.background=\'var(--bg-surface-2)\'" onmouseout="this.style.background=\'none\'">Hapus</button></form>';
    }
    aksi += '</div></div></div></td>';

    return '<tr id="row-' + i.id + '" data-kondisi="' + (i.kondisi || '') + '"' + green + '>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + idx + '</td>' +
        '<td style="color:var(--text-primary);font-weight:500;white-space:nowrap;font-size:0.75rem;">' + (i.nama_barang || '') + '</td>' +
        '<td>' + timBadge + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.jumlah || '') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.detail || '-') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.keterangan || '-') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.lokasi_unit || '') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.ruangan || '') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.pengadaan_tahun || '') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + fmtTgl(i.tanggal_pembelian) + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.kategori_nilai || '') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.kategori_ukuran || '') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.sub_kategori || '') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.milik || '') + '</td>' +
        '<td style="color:var(--text-primary);font-weight:500;white-space:nowrap;font-size:0.75rem;">' + fmtRp(i.nilai) + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.waktu_pakai_per_hari || '') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.estimasi_waktu_barang || '') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + fmtRp2(i.pengurangan_harga_per_hari) + '</td>' +
        '<td style="color:' + (hargaSekarang > 0 ? 'var(--text-primary)' : '#ef4444') + ';font-weight:500;white-space:nowrap;font-size:0.75rem;">' + fmtRp(i.harga_per_hari_ini) + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.pic || '') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.jabatan || '') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.atasan || '') + '</td>' +
        '<td style="color:var(--text-muted);white-space:nowrap;font-size:0.75rem;">' + (i.jabatan_atasan || '') + '</td>' +
        '<td style="color:var(--color-accent);font-weight:500;font-family:monospace;font-size:0.7rem;white-space:nowrap;">' + (i.kode_aset || '') + '</td>' +
        barcodeCell +
        '<td>' + kondisiBadgeInfo(i.kondisi) + '</td>' +
        aksi +
        '</tr>';
}

function applyItemSearch() {
    const input = document.getElementById('search-input');
    const tbody = document.getElementById('item-tbody');
    const footer = document.getElementById('table-footer-inner');
    if (!input || !tbody || !footer || !originalFooterHtml) return;

    const q = (input.value || '').trim();

    if (q === '') {
        tbody.innerHTML = originalTbodyHtml;
        footer.innerHTML = originalFooterHtml;
        return;
    }

    const results = itemsData.filter(i => itemSearchText(i).includes(q.toLowerCase()));

    if (results.length === 0) {
        tbody.innerHTML = '<tr id="empty-row"><td colspan="27" style="text-align:center;padding:2rem;color:var(--text-muted);">Tidak ada hasil untuk "' + q + '".</td></tr>';
    } else {
        tbody.innerHTML = results.map(function (i, idx) { return rowHtml(i, idx + 1); }).join('');
    }
    footer.innerHTML = '<span style="font-size:0.75rem;color:var(--text-muted);white-space:nowrap;">Menampilkan ' + results.length + ' item untuk pencarian "' + q + '"</span>';
}

function showAlertPopup(type) {
    const title = document.getElementById('alert-popup-title');
    const body = document.getElementById('alert-popup-body');
    var color, items, titleText;

    if (type === 'danger') {
        color = '#ef4444';
        items = alertData.filter(function(i) { return i.kondisi === 'rusak'; });
        titleText = 'Peralatan Kondisi Rusak';
    } else {
        color = '#f59e0b';
        items = alertData.filter(function(i) { return i.kondisi === 'perlu_servis'; });
        titleText = 'Peralatan Perlu Servis';
    }

    title.textContent = titleText;

    if (items.length === 0) {
        body.innerHTML = '<p style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ada peralatan.</p>';
    } else {
        body.innerHTML = items.map(function(i) {
            var kondisiLabel = i.kondisi === 'rusak'
                ? '<span style="color:#ef4444;font-weight:600;">Rusak</span>'
                : '<span style="color:#f59e0b;font-weight:600;">Perlu Servis</span>';
            return '<div class="flex items-center gap-3 px-4 py-3.5 rounded-xl" style="border:1px solid var(--border-color);margin-bottom:8px;cursor:pointer;transition:all 0.15s;background:var(--bg-surface-2);" onclick="openEditModal(' + i.id + ')" onmouseover="this.style.borderColor=\'' + color + '\'" onmouseout="this.style.borderColor=\'var(--border-color)\'">' +
                '<div class="flex-1 min-w-0">' +
                    '<p style="font-weight:600;font-size:14px;color:var(--text-primary);margin:0;">' + i.nama_barang + '</p>' +
                    '<p style="font-size:12px;color:var(--text-muted);margin:2px 0 0;">' + (i.kode_aset || '-') + ' — Lokasi: ' + (i.lokasi_unit || '-') + '</p>' +
                    '<p style="font-size:11px;color:var(--text-secondary);margin:4px 0 0;">Kondisi: ' + kondisiLabel + '</p>' +
                '</div>' +
                '<span onclick="event.stopPropagation()"><button onclick="openEditModal(' + i.id + ')" style="flex-shrink:0;padding:8px 16px;border-radius:10px;font-size:12px;font-weight:700;border:none;cursor:pointer;transition:all 0.2s;background:' + color + ';color:#fff;box-shadow:0 4px 12px ' + (type === 'danger' ? 'rgba(239,68,68,0.3)' : 'rgba(245,158,11,0.3)') + ';" onmouseover="this.style.opacity=\'0.85\'" onmouseout="this.style.opacity=\'1\'">Perbaiki</button></span>' +
            '</div>';
        }).join('');
    }

    document.getElementById('alert-overlay').style.display = 'flex';
}

function closeAlertPopup() {
    document.getElementById('alert-overlay').style.display = 'none';
}
const scanUrl = '{{ route("admin.peralatan-kantor.scan") }}';
const showAll = @json($showAll);
const currentRole = '{{ auth()->user()->role }}';
let currentStep = 1;
const totalSteps = 6;
let currentDetailId = null;
let currentPage = 1;
const perPage = 10;
let filteredRows = [];

function openCreateModal() {
    document.getElementById('modal-title').textContent = 'Tambah Peralatan';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('form-id').value = '';
    document.getElementById('item-form').action = '{{ route('admin.peralatan-kantor.store') }}';
    document.getElementById('submit-btn').textContent = 'Simpan';
    document.getElementById('item-form').querySelectorAll('input, textarea, select').forEach(el => {
        if (el.type !== 'hidden' && el.name !== '_token' && el.name !== '_method') {
            if (el.name === 'waktu_pakai_per_hari' || el.name === 'estimasi_waktu_barang' ||
                el.name === 'pengurangan_harga_per_hari' || el.name === 'harga_per_hari_ini') {
                el.value = '2';
            } else {
                el.value = '';
            }
        }
    });
    document.getElementById('f-sub_kategori').value = 'Peralatan Kantor';
    const fTim = document.getElementById('f-tim');
    fTim.value = '{{ $activeTim }}';
    if (fTim.value !== '{{ $activeTim }}') fTim.value = '';
    isiPicDariTim(fTim.value, false);
    document.getElementById('f-milik').value = 'Milik Perusahaan';
    document.getElementById('f-kategori_nilai').value = 'Rendah';
    document.getElementById('f-kategori_ukuran').value = 'Kecil';
    const kSel = document.getElementById('f-kondisi');
    if (kSel) {
        kSel.value = 'baik';
        const kCustom = document.getElementById('f-kondisi-custom');
        kCustom.value = '';
        kCustom.style.display = 'none';
        kCustom.required = false;
        syncKondisi();
    }
    document.getElementById('f-foto-preview').classList.add('hidden');
    document.getElementById('f-foto').value = '';
    hitungPenyusutan();
    currentStep = 1;
    showStep(currentStep);
    showModal();
}

function renderDetailCards(i) {
    const fmtRp = (v) => v ? 'Rp' + Number(v).toLocaleString('id-ID') : '-';
    const fmtTgl = (v) => v || '-';

    const cards = [
        {
            title: 'Informasi Umum',
            rows: [
                { label: 'Nama Barang', value: i.nama_barang },
                { label: 'Tim', value: i.tim || '-' },
                { label: 'Jumlah', value: i.jumlah },
                { label: 'Detail', value: i.detail || '-' },
                { label: 'Keterangan', value: i.keterangan || '-' },
            ]
        },
        {
            title: 'Lokasi',
            rows: [
                { label: 'Lokasi Unit', value: i.lokasi_unit },
                { label: 'Ruangan', value: i.ruangan },
            ]
        },
        {
            title: 'Pengadaan & Nilai',
            rows: [
                { label: 'Pengadaan (in tahun)', value: i.pengadaan_tahun },
                { label: 'Tanggal Pembelian', value: fmtTgl(i.tanggal_pembelian) },
                { label: 'Kategori Nilai', value: i.kategori_nilai },
                { label: 'Kategori Ukuran', value: i.kategori_ukuran },
                { label: 'Sub-Kategori', value: i.sub_kategori },
                { label: 'Milik', value: i.milik },
                { label: 'Nilai (Rp)', value: fmtRp(i.nilai) },
            ]
        },
        {
            title: 'Penyusutan Umur Aset',
            rows: [
                { label: 'Waktu Pakai Barang Perhari Ini', value: (i.waktu_pakai_per_hari || 0) },
                { label: 'Estimasi Waktu Barang', value: (i.estimasi_waktu_barang || 0) + ' Hari' },
                { label: 'Hari Terpakai', value: (i.hari_terpakai || 0) + ' Hari' },
                { label: 'Pengurangan Harga Aset Perhari', value: fmtRp(i.penyusutan_per_hari) },
                { label: 'Nilai Awal', value: fmtRp(i.nilai) },
                { label: 'Harga Barang Perhari Ini', value: fmtRp(i.nilai_sekarang) },
            ]
        },
        {
            title: 'Penanggung Jawab',
            rows: [
                { label: 'PIC', value: i.pic },
                { label: 'Jabatan PIC', value: i.jabatan },
                { label: 'Atasan', value: i.atasan || '-' },
                { label: 'Jabatan Atasan', value: i.jabatan_atasan || '-' },
            ]
        },
    ];

    let html = '<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">';
    cards.forEach(function (card) {
        html += '<div style="background:var(--bg-surface-2);border:1px solid var(--border-color);border-radius:14px;padding:18px;">';
        html += '<p style="color:var(--color-accent);font-size:0.75rem;font-weight:700;letter-spacing:0.05em;margin-bottom:12px;text-transform:uppercase;">' + card.title + '</p>';
        html += '<div>';
        for (var r = 0; r < card.rows.length; r++) {
            var row = card.rows[r];
            var borderStyle = r < card.rows.length - 1 ? 'border-bottom:1px solid var(--border-color);' : '';
            html += '<div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;' + borderStyle + '">';
            html += '<span style="color:var(--text-muted);font-size:0.8rem;">' + row.label + '</span>';
            html += '<span style="color:var(--text-primary);font-size:0.85rem;font-weight:600;text-align:right;margin-left:12px;">' + row.value + '</span>';
            html += '</div>';
        }
        html += '</div></div>';
    });
    html += '</div>';
    return html;
}

function getBarcodeColor() {
    return document.body.classList.contains('dark') ? '#ffffff' : '#000000';
}

function renderBarcode(containerId, code, encodeUrl) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    const qrRow = document.getElementById('detail-qr-row');
    const qrCaption = document.getElementById('detail-qr-caption');
    if (!code) {
        document.getElementById('detail-barcode-section').style.display = 'none';
        if (qrRow) qrRow.classList.add('hidden');
        if (qrCaption) qrCaption.classList.add('hidden');
        return;
    }
    document.getElementById('detail-barcode-section').style.display = '';
    document.getElementById('detail-kode-aset').textContent = code;
    if (qrRow) {
        const qrImg = document.getElementById('detail-qr-img');
        const qrLink = document.getElementById('detail-qr-link');
        if (encodeUrl && qrImg && qrLink) {
            qrImg.src = encodeUrl + '/qr';
            qrLink.href = encodeUrl;
            qrLink.textContent = encodeUrl;
            qrRow.classList.remove('hidden');
            if (qrCaption) qrCaption.classList.remove('hidden');
        } else {
            qrRow.classList.add('hidden');
            if (qrCaption) qrCaption.classList.add('hidden');
        }
    }
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.id = 'barcode-svg';
    container.appendChild(svg);
    try {
        const color = getBarcodeColor();
        JsBarcode(svg, code, {
            format: 'CODE128',
            text: code,
            width: 1.2,
            height: 35,
            displayValue: true,
            fontSize: 11,
            font: 'monospace',
            margin: 5,
            background: 'transparent',
            lineColor: color,
            textColor: color,
        });
    } catch (e) {
        container.innerHTML = '<p style="color:var(--text-muted);font-size:12px;">Gagal render barcode</p>';
    }
}

async function showDetail(id) {
    let i = itemsData.find(x => x.id === id);
    try {
        const res = await fetch(itemBaseUrl + '/' + id + '/json', { headers: { 'Accept': 'application/json' } });
        if (res.ok) {
            const fresh = await res.json();
            const idx = itemsData.findIndex(x => x.id === id);
            if (idx >= 0) itemsData[idx] = fresh; else itemsData.push(fresh);
            i = fresh;
        }
    } catch (e) {}
    if (!i) return;
    currentDetailId = id;

    document.getElementById('detail-title').textContent = i.nama_barang;

    const kondisiMap = {
        baik: { label: 'Baik', bg: '#ecfdf5', text: '#059669', border: '#a7f3d0' },
        perlu_servis: { label: 'Perlu Servis', bg: '#fff7ed', text: '#c2410c', border: '#fed7aa' },
        rusak: { label: 'Rusak', bg: '#fef2f2', text: '#dc2626', border: '#fecaca' },
    };
    const k = kondisiMap[i.kondisi] || { label: i.kondisi || 'Kondisi', bg: 'var(--bg-surface-2)', text: 'var(--text-muted)', border: 'var(--border-color)' };
    const badgeEl = document.getElementById('detail-badge');
    badgeEl.textContent = k.label;
    badgeEl.style.background = k.bg;
    badgeEl.style.color = k.text;
    badgeEl.style.border = '1px solid ' + k.border;

    const delForm = document.getElementById('detail-delete-form');
    delForm.action = delForm.dataset.action + id;

    const fotoSection = document.getElementById('detail-foto-section');
    const fotoImg = document.getElementById('detail-foto-img');
    if (i.foto) {
        fotoImg.src = i.foto;
        fotoSection.style.display = '';
    } else {
        fotoSection.style.display = 'none';
    }

    renderBarcode('detail-barcode-image', i.barcode || i.kode_aset, '{{ url("/aset") }}/' + encodeURIComponent(i.kode_aset));
    document.getElementById('detail-body').innerHTML = renderDetailCards(i);
    document.getElementById('detail-media-placeholder').style.display = (!i.foto && !i.barcode && !i.kode_aset) ? '' : 'none';
    openModal('detail-modal');
}

function closeDetail() {
    closeModal('detail-modal');
}

function downloadQrCode(id) {
    const i = itemsData.find(x => x.id === id);
    if (!i) return;
    const qrUrl = '{{ url("/aset") }}/' + encodeURIComponent(i.kode_aset) + '/qr';
    const publicUrl = '{{ url("/aset") }}/' + encodeURIComponent(i.kode_aset);

    document.getElementById('label-nama').textContent = i.nama_barang;
    document.getElementById('label-kode').textContent = i.kode_aset;
    document.getElementById('label-url').textContent = publicUrl;
    document.getElementById('label-qr-container').innerHTML = '<img src="' + qrUrl + '" alt="QR Code" style="width:56px;height:56px;">';

    const bcSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    try {
        JsBarcode(bcSvg, i.barcode || i.kode_aset, {
            format: 'CODE128',
            text: i.barcode || i.kode_aset,
            width: 1.4,
            height: 40,
            displayValue: true,
            fontSize: 9,
            font: 'monospace',
            fontOptions: 'bold',
            margin: 4,
            background: 'transparent',
            lineColor: '#000000',
            textColor: '#000000',
        });
        bcSvg.setAttribute('style', 'width:100%;height:auto;display:block;');
        document.getElementById('label-barcode-container').innerHTML = new XMLSerializer().serializeToString(bcSvg);
    } catch (e) {
        document.getElementById('label-barcode-container').innerHTML = '';
    }

    openModal('label-modal');
}

function closeLabelModal() {
    closeModal('label-modal');
}

function printLabelFromModal() {
    const card = document.getElementById('label-card');
    const win = window.open('', '_blank', 'width=400,height=520');
    win.document.write('<!DOCTYPE html><html><head><title>Cetak Label</title><style>@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap");@page{size:50mm 30mm;margin:0;}*{box-sizing:border-box;margin:0;padding:0;}body{font-family:"Poppins",sans-serif;display:flex;justify-content:center;align-items:flex-start;min-height:100vh;padding:0;background:#fff;}#label-card{width:50mm !important;height:30mm !important;padding:2mm !important;display:flex;flex-direction:column;justify-content:flex-start;overflow:hidden;}@media print{body{padding:0;}#label-card{border-color:#1e1e2e;}}</style></head><body>' + card.outerHTML + '<script>setTimeout(function(){window.print();},500);<\/script></body></html>');
    win.document.close();
}

function printLabel(id) {
    const i = itemsData.find(x => x.id === id);
    if (!i) return;
    const qrUrl = '{{ url("/aset") }}/' + encodeURIComponent(i.kode_aset) + '/qr';
    const publicUrl = '{{ url("/aset") }}/' + encodeURIComponent(i.kode_aset);
    const logoUrl = '{{ asset("images/logo/logo johengaming.jpg") }}';
    let bcHtml = '';
    try {
        const bcSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        JsBarcode(bcSvg, i.barcode || i.kode_aset, {
            format: 'CODE128',
            text: i.barcode || i.kode_aset,
            width: 1.4,
            height: 40,
            displayValue: true,
            fontSize: 9,
            font: 'monospace',
            fontOptions: 'bold',
            margin: 4,
            background: 'transparent',
            lineColor: '#000000',
            textColor: '#000000',
        });
        bcSvg.setAttribute('style', 'width:100%;height:auto;display:block;');
        bcHtml = new XMLSerializer().serializeToString(bcSvg);
    } catch (e) {
        bcHtml = '';
    }
    const win = window.open('', '_blank', 'width=400,height=500');
    win.document.write(`
        <!DOCTYPE html>
        <html><head><title>Cetak Label - ${i.kode_aset}</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');
            @page { size: 50mm 30mm; margin: 0; }
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body { font-family: 'Poppins', sans-serif; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; padding: 0; }
            .label { width: 50mm; height: 30mm; border: 1px solid #1e1e2e; border-radius: 3px; padding: 2mm; text-align: center; display: flex; flex-direction: column; justify-content: flex-start; overflow: hidden; }
            .head-line { display: flex; align-items: center; justify-content: center; gap: 2mm; margin-bottom: 1.5mm; }
            .logo { width: 6mm; height: 6mm; object-fit: contain; }
            .brand { font-size: 2.2mm; font-weight: 800; letter-spacing: 0.05em; color: #1e1e2e; white-space: nowrap; }
            .name { font-size: 2mm; font-weight: 600; color: #0f172a; margin-bottom: 0.8mm; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .code { font-size: 2mm; font-family: monospace; color: #7c3aed; font-weight: 700; margin-bottom: 1.2mm; }
            .media-row { display: flex; align-items: center; justify-content: center; gap: 2mm; margin: 0 auto 1mm; }
            .qr { flex-shrink: 0; }
            .qr img { width: 13mm; height: 13mm; display: block; }
            .barcode { flex: 1; min-width: 0; }
            .barcode svg { width: 100%; height: auto; display: block; }
            .url { font-size: 1.4mm; color: #94a3b8; word-break: break-all; line-height: 1.2; }
            @media print { body { padding: 0; } .label { border-width: 1px; } }
        </style></head><body>
        <div class="label">
            <div class="head-line">
                <img src="${logoUrl}" class="logo" alt="Logo">
                <div class="brand">JSA PERALATAN KANTOR</div>
            </div>
            <div class="name">${i.nama_barang}</div>
            <div class="code">${i.kode_aset}</div>
            <div class="media-row">
                <div class="qr"><img src="${qrUrl}" alt="QR Code"></div>
                <div class="barcode">${bcHtml}</div>
            </div>
            <div class="url">${publicUrl}</div>
        </div>
        <script>setTimeout(function(){window.print();},800);<\/script>
        </body></html>
    `);
    win.document.close();
}

document.getElementById('detail-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDetail();
});

function toggleDropdown(btn, id) {
    const all = document.querySelectorAll('.dropdown-menu');
    all.forEach(el => { if (el.id !== 'dropdown-' + id) el.style.display = 'none'; });
    const menu = document.getElementById('dropdown-' + id);
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function toggleBarcodeDitempel(id) {
    fetch('{{ url('admin/peralatan-kantor') }}/' + id + '/barcode-ditempel', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.success) location.reload();
        else alert('Gagal mengubah tanda barcode.' + (data && data.message ? ' ' + data.message : ''));
    })
    .catch(err => alert('Gagal mengubah tanda barcode. Periksa koneksi atau muat ulang halaman. (' + err + ')'));
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown-wrap')) {
        document.querySelectorAll('.dropdown-menu').forEach(el => el.style.display = 'none');
    }
});

function openEditModal(id) {
    closeDetail();
    const i = itemsData.find(x => x.id === id);
    if (!i) return;

    document.getElementById('modal-title').textContent = 'Edit Peralatan';
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('form-id').value = i.id;
    document.getElementById('item-form').action = '{{ url('admin/peralatan-kantor') }}/' + i.id;
    document.getElementById('submit-btn').textContent = 'Simpan Perubahan';

    document.getElementById('f-nama_barang').value = i.nama_barang;
    document.getElementById('f-jumlah').value = i.jumlah;
    document.getElementById('f-detail').value = i.detail || '';
    document.getElementById('f-sub_kategori').value = i.sub_kategori;
    const fTim = document.getElementById('f-tim');
    fTim.value = i.tim || '';
    if (i.tim && fTim.value !== i.tim) {
        const opt = document.createElement('option');
        opt.value = i.tim; opt.textContent = i.tim;
        fTim.appendChild(opt);
        fTim.value = i.tim;
    }
    document.getElementById('f-keterangan').value = i.keterangan || '';
    document.getElementById('f-lokasi_unit').value = i.lokasi_unit;
    document.getElementById('f-ruangan').value = i.ruangan;
    document.getElementById('f-milik').value = i.milik;
    document.getElementById('f-pengadaan_tahun').value = i.pengadaan_tahun;
    document.getElementById('f-tanggal_pembelian').value = i.tanggal_pembelian;
    document.getElementById('f-kategori_nilai').value = i.kategori_nilai;
    document.getElementById('f-kategori_ukuran').value = i.kategori_ukuran;
    document.getElementById('f-nilai').value = i.nilai;
    document.getElementById('f-waktu_pakai_per_hari').value = i.waktu_pakai_per_hari;
    document.getElementById('f-estimasi_waktu_barang').value = i.estimasi_waktu_barang;
    document.getElementById('f-pic').value = i.pic;
    document.getElementById('f-jabatan').value = i.jabatan;
    document.getElementById('f-atasan').value = i.atasan;
    document.getElementById('f-jabatan_atasan').value = i.jabatan_atasan;
    const kSel = document.getElementById('f-kondisi');
    if (kSel) {
        const kCustom = document.getElementById('f-kondisi-custom');
        const optionExists = [...kSel.options].some(o => o.value === i.kondisi);
        if (optionExists) {
            kSel.value = i.kondisi;
            kCustom.value = '';
            kCustom.style.display = 'none';
            kCustom.required = false;
        } else {
            kSel.value = '__custom__';
            kCustom.value = i.kondisi || '';
            kCustom.style.display = '';
            kCustom.required = true;
        }
        syncKondisi();
    }

    const fotoPreview = document.getElementById('f-foto-preview');
    const fotoImg = document.getElementById('f-foto-preview-img');
    document.getElementById('f-foto').value = '';
    if (i.foto) {
        fotoImg.src = i.foto;
        fotoPreview.classList.remove('hidden');
    } else {
        fotoPreview.classList.add('hidden');
    }

    hitungPenyusutan();
    currentStep = 1;
    showStep(currentStep);
    showModal();
}

function showStep(n) {
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('step-' + n).classList.remove('hidden');

    document.querySelectorAll('.step-dot').forEach(el => {
        const s = parseInt(el.dataset.step);
        el.classList.remove('active', 'done');
        if (s === n) el.classList.add('active');
        else if (s < n) el.classList.add('done');
    });
    document.querySelectorAll('.step-line').forEach(el => {
        const s = parseInt(el.dataset.step);
        el.classList.toggle('done', s < n);
    });

    document.getElementById('prev-btn').style.display = n > 1 ? '' : 'none';
    document.getElementById('next-btn').style.display = n < totalSteps ? '' : 'none';
    document.getElementById('submit-btn').style.display = n === totalSteps ? '' : 'none';

    if (n === totalSteps) updatePreview();
    document.getElementById('item-form').querySelector('.step-content:not(.hidden)');
}

function updatePreview() {
    const map = {
        'pv-nama_barang': 'f-nama_barang',
        'pv-jumlah': 'f-jumlah',
        'pv-detail': 'f-detail',
        'pv-keterangan': 'f-keterangan',
        'pv-lokasi_unit': 'f-lokasi_unit',
        'pv-ruangan': 'f-ruangan',
        'pv-pengadaan_tahun': 'f-pengadaan_tahun',
        'pv-tanggal_pembelian': 'f-tanggal_pembelian',
        'pv-kategori_nilai': 'f-kategori_nilai',
        'pv-kategori_ukuran': 'f-kategori_ukuran',
        'pv-sub_kategori': 'f-sub_kategori',
        'pv-milik': 'f-milik',
        'pv-nilai': 'f-nilai',
        'pv-waktu_pakai_per_hari': 'f-waktu_pakai_per_hari',
        'pv-estimasi_waktu_barang': 'f-estimasi_waktu_barang',
        'pv-pic': 'f-pic',
        'pv-jabatan': 'f-jabatan',
        'pv-atasan': 'f-atasan',
        'pv-jabatan_atasan': 'f-jabatan_atasan',
    };
    for (const [pvId, inputName] of Object.entries(map)) {
        const el = document.getElementById(pvId);
        const inputEl = document.getElementById(inputName);
        if (el && inputEl) {
            el.textContent = inputEl.value || '-';
        }
    }
    const kondisiSel = document.getElementById('f-kondisi');
    const pvKondisi = document.getElementById('pv-kondisi');
    if (kondisiSel && pvKondisi) {
        let kondisiVal = kondisiSel.value === '__custom__'
            ? document.getElementById('f-kondisi-custom').value
            : kondisiSel.value;
        pvKondisi.textContent = kondisiVal || '-';
    }
    const nilai = parseFloat(document.getElementById('f-nilai').value) || 0;
    const masaBarang = parseInt(document.getElementById('f-estimasi_waktu_barang').value) || 1;
    const waktuPakai = parseInt(document.getElementById('f-waktu_pakai_per_hari').value) || 1;
    const tglBeli = document.getElementById('f-tanggal_pembelian').value;
    const penyusutan = (nilai / masaBarang) * waktuPakai;
    let hariTerpakai = 0;
    if (tglBeli) {
        const now = new Date();
        const beli = new Date(tglBeli);
        hariTerpakai = Math.max(Math.floor(Math.abs(now - beli) / (1000 * 60 * 60 * 24)), 0);
    }
    const nilaiSekarang = Math.max(nilai - penyusutan, 0);
    document.getElementById('pv-pengurangan_harga_per_hari').textContent = 'Rp' + Math.round(penyusutan).toLocaleString('id-ID');
    document.getElementById('pv-harga_per_hari_ini').textContent = 'Rp' + Math.round(nilaiSekarang).toLocaleString('id-ID');
    document.getElementById('pv-hari_terpakai').textContent = hariTerpakai + ' Hari';
    document.getElementById('pv-nilai_awal').textContent = 'Rp' + Math.round(nilai).toLocaleString('id-ID');
}

function validateStep(step) {
    const stepEl = document.getElementById('step-' + step);
    const requiredInputs = stepEl.querySelectorAll('[required]');
    let valid = true;
    requiredInputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#ef4444';
            input.style.boxShadow = '0 0 0 2px rgba(239,68,68,0.2)';
            valid = false;
        } else {
            input.style.borderColor = '';
            input.style.boxShadow = '';
        }
    });
    if (!valid) {
        showAlertModal('Harap isi semua field yang wajib diisi.');
    }
    return valid;
}

function nextStep() {
    if (!validateStep(currentStep)) return;
    if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
    }
}

function hitungPenyusutan() {
    const nilai = parseFloat(document.getElementById('f-nilai').value) || 0;
    const masaBarang = parseInt(document.getElementById('f-estimasi_waktu_barang').value) || 1;
    const waktuPakai = parseInt(document.getElementById('f-waktu_pakai_per_hari').value) || 1;
    const penyusutan = (nilai / masaBarang) * waktuPakai;
    const nilaiSekarang = Math.max(nilai - penyusutan, 0);
    document.getElementById('penyusutan-display').textContent = 'Rp' + Math.round(penyusutan).toLocaleString('id-ID');
    document.getElementById('nilai-sekarang-display').textContent = 'Rp' + Math.round(nilaiSekarang).toLocaleString('id-ID');
}

function kondisiSelectChanged() {
    const s = document.getElementById('f-kondisi');
    const t = document.getElementById('f-kondisi-custom');
    const isCustom = s.value === '__custom__';
    t.style.display = isCustom ? '' : 'none';
    t.required = isCustom;
    if (!isCustom) t.value = '';
    syncKondisi();
}

function syncKondisi() {
    const s = document.getElementById('f-kondisi');
    const h = document.getElementById('f-kondisi-hidden');
    if (!s || !h) return;
    if (s.value === '__custom__') {
        h.value = document.getElementById('f-kondisi-custom').value.trim();
    } else {
        h.value = s.value;
    }
}

function submitForm() {
    syncKondisi();
    for (let s = 1; s < totalSteps; s++) {
        if (!validateStep(s)) {
            const firstInvalid = document.querySelector('#step-' + s + ' [required]');
            if (firstInvalid) {
                currentStep = s;
                showStep(currentStep);
                firstInvalid.focus();
            }
            return;
        }
    }
    document.getElementById('item-form').submit();
}

function showModal() { openModal('item-modal'); }

document.getElementById('item-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal('item-modal');
});

(function() {
    let lastTheme = document.body.classList.contains('dark') ? 'dark' : 'light';
    new MutationObserver(function() {
        const current = document.body.classList.contains('dark') ? 'dark' : 'light';
        if (current !== lastTheme) {
            lastTheme = current;
            const svg = document.getElementById('barcode-svg');
            if (svg && currentDetailId) {
                const item = itemsData.find(x => x.id === currentDetailId);
                if (item) renderBarcode('detail-barcode-image', item.barcode || item.kode_aset, '{{ url("/aset") }}/' + encodeURIComponent(item.kode_aset));
            }
        }
    }).observe(document.body, { attributes: true, attributeFilter: ['class'] });
})();

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeDetail(); closeModal('item-modal'); closeScanModal(); closeLabelModal(); }
});

function prepareBlackBarcode() {
    const svg = document.getElementById('barcode-svg');
    if (!svg) return null;
    const clone = svg.cloneNode(true);

    clone.setAttribute('xmlns', 'http://www.w3.org/2000/svg');

    if (!clone.getAttribute('width') || !clone.getAttribute('height')) {
        const w = clone.getAttribute('width') || svg.getBBox().width + 10 || 200;
        const h = clone.getAttribute('height') || svg.getBBox().height + 10 || 80;
        clone.setAttribute('width', w);
        clone.setAttribute('height', h);
    }

    clone.querySelectorAll('*').forEach(el => {
        const tag = el.tagName.toLowerCase();

        if (tag === 'rect' || tag === 'path') {
            const fill = el.getAttribute('fill');
            if (fill && fill !== 'none' && fill !== 'transparent') {
                el.setAttribute('fill', '#000000');
            }
            const stroke = el.getAttribute('stroke');
            if (stroke && stroke !== 'none') {
                el.setAttribute('stroke', '#000000');
            }
        }

        if (tag === 'text') {
            el.setAttribute('fill', '#000000');
            el.setAttribute('stroke', 'none');
            el.setAttribute('font-family', 'monospace');
            if (!el.getAttribute('font-size')) {
                el.setAttribute('font-size', '14');
            }
        }

        if (el.hasAttribute('style')) {
            let s = el.getAttribute('style');
            s = s.replace(/fill\s*:\s*(?!none|transparent)[^;]+/gi, 'fill:#000000');
            s = s.replace(/stroke\s*:\s*(?!none|transparent)[^;]+/gi, 'stroke:#000000');
            s = s.replace(/color\s*:\s*(?!none|transparent)[^;]+/gi, 'color:#000000');
            el.setAttribute('style', s);
        }
    });

    const bg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    bg.setAttribute('x', '0');
    bg.setAttribute('y', '0');
    bg.setAttribute('width', '100%');
    bg.setAttribute('height', '100%');
    bg.setAttribute('fill', '#ffffff');
    clone.insertBefore(bg, clone.firstChild);

    clone.setAttribute('style', 'color:#000000;');
    return clone;
}

function getBarcodeSvgDataUrl(clone) {
    const svgData = new XMLSerializer().serializeToString(clone);
    return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgData);
}

function printBarcode() {
    const clone = prepareBlackBarcode();
    if (!clone) return;
    const svgData = new XMLSerializer().serializeToString(clone);
    const qrEl = document.getElementById('detail-qr-img');
    const qrRow = document.getElementById('detail-qr-row');
    const hasQr = qrEl && qrRow && !qrRow.classList.contains('hidden') && qrEl.src && qrEl.complete && qrEl.naturalWidth > 0;
    const logoUrl = '{{ asset("images/logo/logo johengaming.jpg") }}';
    const qrHtml = hasQr ? '<img src="' + qrEl.src + '" alt="QR" style="width:56px;height:56px;flex-shrink:0;">' : '';
    const w = clone.getAttribute('width') || 300;
    const h = clone.getAttribute('height') || 120;
    const win = window.open('', '_blank', 'width=' + w + ',height=' + h);
    win.document.write('<!DOCTYPE html><html><head><title>Cetak Barcode</title><style>@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap");@page{size:50mm 30mm;margin:0;}*{box-sizing:border-box;margin:0;padding:0;}body{display:flex;justify-content:center;align-items:flex-start;min-height:100vh;padding:0;font-family:monospace;background:#fff;}.wrap{width:50mm;height:30mm;margin:0 auto;text-align:center;background:#fff;border:1px solid #ddd;border-radius:3px;padding:2mm;display:flex;flex-direction:column;justify-content:flex-start;overflow:hidden;}.head{display:flex;align-items:center;justify-content:center;gap:2mm;margin-bottom:1.5mm;}.head img{width:6mm;height:6mm;object-fit:contain;}.head .title{font-family:"Poppins",sans-serif;font-size:2.2mm;font-weight:800;letter-spacing:0.05em;color:#000;white-space:nowrap;}.row{display:flex;align-items:center;justify-content:center;gap:2mm;}.row>div{min-width:0;}.row svg{height:auto;display:block;max-width:100%;}.row img{width:13mm;height:13mm;flex-shrink:0;display:block;}@media print{body{padding:0;}.wrap{border:none;}}</style></head><body><div class="wrap"><div class="head"><img src="' + logoUrl + '" alt="Logo"><div class="title">JSA PERALATAN KANTOR</div></div><div class="row">' + qrHtml + '<div>' + svgData + '</div></div></div><script>window.onload=function(){setTimeout(function(){window.print();},300);}<\/script></body></html>');
    win.document.close();
}

function roundedRectPath(ctx, x, y, w, h, r) {
    r = Math.min(r, w / 2, h / 2);
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.lineTo(x + w - r, y);
    ctx.arcTo(x + w, y, x + w, y + r, r);
    ctx.lineTo(x + w, y + h - r);
    ctx.arcTo(x + w, y + h, x + w - r, y + h, r);
    ctx.lineTo(x + r, y + h);
    ctx.arcTo(x, y + h, x, y + h - r, r);
    ctx.lineTo(x, y + r);
    ctx.arcTo(x, y, x + r, y, r);
    ctx.closePath();
}

function downloadBarcode() {
    const detailId = currentDetailId || 'aset';
    const i = itemsData.find(function(x) { return x.id === detailId; });
    const qrEl = document.getElementById('detail-qr-img');
    const qrRow = document.getElementById('detail-qr-row');
    const hasQr = qrEl && qrRow && !qrRow.classList.contains('hidden') && qrEl.src && qrEl.complete && qrEl.naturalWidth > 0;

    const logoUrl = '{{ asset("images/logo/logo johengaming.jpg") }}';
    const qrUrl = '{{ url("/aset") }}/' + encodeURIComponent(i ? (i.kode_aset || detailId) : detailId) + '/qr';
    const title = 'JSA PERALATAN KANTOR';

    // Buat salinan SVG barcode hitam pekat agar tajam saat digambar ke canvas
    const clone = prepareBlackBarcode();
    if (!clone) return;
    const svgData = new XMLSerializer().serializeToString(clone);

    // Ukuran canvas mengikuti rasio 50x30 mm (5:3), resolusi tinggi agar tajam
    const canvasW = 1600;
    const canvasH = 960;
    const logo = new Image();
    const bcImg = new Image();
    const qrImg = new Image();
    let logoOk = false;
    let logoReady = false;
    let bcOk = false;
    let fontReady = false;

    // Barcode SVG -> data URL lalu jadi img (didukung canvas; html2canvas tidak bisa render SVG)
    const bcblob = new Blob([svgData], { type: 'image/svg+xml' });
    const bcDataUrl = URL.createObjectURL(bcblob);

    function ensurePoppinsLoaded() {
        return new Promise(function(resolve) {
            if (!document.getElementById('poppins-font')) {
                var fl = document.createElement('link');
                fl.id = 'poppins-font';
                fl.rel = 'stylesheet';
                fl.href = 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap';
                document.head.appendChild(fl);
            }
            var done = false;
            var finish = function() { if (!done) { done = true; resolve(); } };
            var timer = setTimeout(finish, 2500);
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(function() {
                    if (document.fonts.load) {
                        document.fonts.load('800 36px Poppins').then(finish).catch(finish);
                    } else {
                        finish();
                    }
                }).catch(finish);
            } else {
                finish();
            }
        });
    }
    ensurePoppinsLoaded().then(function() { fontReady = true; build(); });

    function build() {
        if (!logoOk || !bcOk || !fontReady) return;

        const canvas = document.createElement('canvas');
        canvas.width = canvasW;
        canvas.height = canvasH;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvasW, canvasH);

        // Rasio 1mm = 32px pada canvas 1600x960 (50mm x 30mm)
        const GAP = 64;           // 2mm
        const PAD = 96;           // 3mm padding nyaman di sisi label
        const QR_MIN = 416;       // ukuran QR minimal

        // ---- Header: logo + judul di-center sebagai satu grup ----
        const logoSize = 176;      // 5.5mm logo
        ctx.font = '800 64px Poppins, Arial, sans-serif'; // 2mm judul
        const titleW = ctx.measureText(title).width;
        const headW = logoSize + GAP + titleW;
        const headX = (canvasW - headW) / 2;
        const headY = 72;         // posisi vertikal header

        if (logoReady) {
            ctx.drawImage(logo, headX, headY, logoSize, logoSize);
        }
        ctx.fillStyle = '#000000';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        ctx.fillText(title, headX + logoSize + GAP, headY + logoSize / 2);

        // ---- Konten: barcode + QR mengisi ruang vertikal di tengah ----
        const urlZone = 76;                                  // tinggi area URL di bawah
        const contentTop = headY + logoSize + Math.max(GAP / 2, 64);
        const contentBottom = canvasH - urlZone - 64;
        const availH = Math.max(QR_MIN, contentBottom - contentTop);

        const bcRatio = (bcImg.width > 0 && bcImg.height > 0) ? (bcImg.width / bcImg.height) : 5;

        // ukur konten: barcode setinggi availH (lebar sesuai rasio), QR square = availH
        let bcH = availH;
        let bcW = Math.round(bcH * bcRatio);
        let qrSize = availH;

        // ruang horizontal yang tersedia setelah padding
        const availW = canvasW - PAD * 2;
        const mediaW = bcW + (hasQr ? GAP + qrSize : 0);
        if (mediaW > availW) {
            const shrink = availW / mediaW;
            bcH = Math.round(bcH * shrink);
            bcW = Math.round(bcW * shrink);
            qrSize = Math.round(qrSize * shrink);
        }

        const mediaH = Math.max(bcH, qrSize);
        const mediaW2 = bcW + (hasQr ? GAP + qrSize : 0);
        // posisikan area konten secara vertikal di tengah ruang tersedia
        const mediaY = contentTop + (availH - mediaH) / 2;
        const mediaX = (canvasW - mediaW2) / 2;

        ctx.drawImage(bcImg, mediaX, mediaY, bcW, bcH);
        if (hasQr && qrReady()) {
            ctx.drawImage(qrImg, mediaX + bcW + GAP, mediaY, qrSize, qrSize);
        }

        // ---- URL kecil di bawah ----
        var url = '{{ url("/aset") }}/' + encodeURIComponent(i ? (i.kode_aset || detailId) : detailId);
        ctx.font = '600 34px Poppins, Arial, sans-serif';
        ctx.fillStyle = '#94a3b8';
        ctx.textAlign = 'center';
        ctx.fillText(url, canvasW / 2, canvasH - 80);

        const a = document.createElement('a');
        a.download = 'barcode-qr-' + detailId + '.png';
        a.href = canvas.toDataURL('image/png');
        a.click();
        URL.revokeObjectURL(bcDataUrl);
    }

    function qrReady() {
        return qrEl && qrEl.complete && qrEl.naturalWidth > 0;
    }

    logo.onload = function() { logoOk = true; logoReady = true; build(); };
    logo.onerror = function() { logoOk = true; build(); };
    bcImg.onload = function() { bcOk = true; build(); };
    bcImg.onerror = function() { bcOk = true; build(); };
    logo.src = '{{ asset("images/logo/logo johengaming.jpg") }}';
    bcImg.src = bcDataUrl;
    if (hasQr) {
        qrImg.onload = function() { build(); };
        qrImg.src = qrEl.src;
    }
}

/* ===== SCAN BARCODE ===== */
let scanStream = null;
let scanInterval = null;
let scanActive = false;
let html5QrScanner = null;
let scanInProgress = false;

function openScanModal() {
    document.getElementById('scan-status-idle').classList.remove('hidden');
    document.getElementById('scan-status-scanning').classList.add('hidden');
    document.getElementById('scan-status-success').classList.add('hidden');
    document.getElementById('scan-status-error').classList.add('hidden');
    document.getElementById('scan-line').classList.add('hidden');
    document.getElementById('manual-code-input').value = '';
    document.getElementById('no-camera-warning').classList.add('hidden');
    document.getElementById('camera-placeholder').style.display = '';
    document.getElementById('scan-toggle-text').textContent = 'Mulai Scan';
    scanActive = false;
    openModal('scan-modal');
    checkCamera();
    if (!window.BarcodeDetector && !window.Html5Qrcode) {
        const warning = document.getElementById('no-camera-warning');
        warning.innerHTML = '<div class="flex items-start gap-2"><svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><div><p class="font-semibold">Pemindaian barcode tidak didukung</p><p class="mt-1" style="color:var(--text-muted);">Browser Anda tidak mendukung pemindaian barcode otomatis. Gunakan Chrome atau Edge, atau masukkan kode secara manual di bawah.</p></div></div>';
        warning.classList.remove('hidden');
    }
}

function closeScanModal() {
    stopScan();
    closeModal('scan-modal');
}

document.getElementById('scan-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeScanModal();
});

async function checkCamera() {
    try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter(d => d.kind === 'videoinput');
        if (videoDevices.length === 0) {
            document.getElementById('no-camera-warning').classList.remove('hidden');
        }
    } catch (e) {
        document.getElementById('no-camera-warning').classList.remove('hidden');
    }
}

function toggleScan() {
    if (!window.BarcodeDetector && !window.Html5Qrcode) {
        showScanStatus('error', 'Pemindaian barcode tidak didukung di browser ini. Gunakan input manual.');
        return;
    }
    if (scanActive) {
        stopScan();
    } else {
        startScan();
    }
}

async function startScan() {
    const video = document.getElementById('camera-video');
    try {
        scanStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
        });
        video.srcObject = scanStream;
        await video.play();
        document.getElementById('camera-placeholder').style.display = 'none';
        document.getElementById('scan-line').classList.remove('hidden');
        document.getElementById('scan-toggle-text').textContent = 'Hentikan Scan';
        document.getElementById('scan-status-idle').classList.add('hidden');
        document.getElementById('scan-status-scanning').classList.remove('hidden');
        scanActive = true;
        scanInterval = setInterval(captureAndDecode, 1500);
    } catch (err) {
        document.getElementById('no-camera-warning').classList.remove('hidden');
        console.warn('Camera access denied:', err);
    }
}

function stopScan() {
    scanActive = false;
    scanInProgress = false;
    if (scanInterval) { clearInterval(scanInterval); scanInterval = null; }
    if (html5QrScanner) {
        try { html5QrScanner.clear(); } catch(e) {}
        html5QrScanner = null;
    }
    if (scanStream) {
        scanStream.getTracks().forEach(t => t.stop());
        scanStream = null;
    }
    const video = document.getElementById('camera-video');
    video.srcObject = null;
    document.getElementById('scan-line').classList.add('hidden');
    document.getElementById('camera-placeholder').style.display = '';
    document.getElementById('scan-toggle-text').textContent = 'Mulai Scan';
    document.getElementById('scan-status-idle').classList.remove('hidden');
    document.getElementById('scan-status-scanning').classList.add('hidden');
}

async function captureAndDecode() {
    if (!scanActive || scanInProgress) return;
    const video = document.getElementById('camera-video');
    if (video.readyState < video.HAVE_ENOUGH_DATA) return;

    scanInProgress = true;
    try {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);

        // Method 1: Native BarcodeDetector API
        if (window.BarcodeDetector) {
            try {
                const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.8));
                const barcodes = await new BarcodeDetector({ formats: ['code_128', 'ean_13', 'ean_8', 'qr_code', 'code_39'] }).detect(blob);
                if (barcodes.length > 0) {
                    handleScanResult(barcodes[0].rawValue);
                    return;
                }
            } catch (e) {}
        }

        // Method 2: html5-qrcode fallback
        if (window.Html5Qrcode) {
            try {
                if (!html5QrScanner) {
                    let el = document.getElementById('html5qr-region');
                    if (!el) {
                        el = document.createElement('div');
                        el.id = 'html5qr-region';
                        el.style.cssText = 'position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;';
                        document.body.appendChild(el);
                    }
                    html5QrScanner = new Html5Qrcode(el.id);
                }
                const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.8));
                const file = new File([blob], 'scan-frame.jpg', { type: 'image/jpeg', lastModified: Date.now() });
                const result = await html5QrScanner.scanFile(file, false);
                if (result) {
                    handleScanResult(result);
                    return;
                }
            } catch (e) {}
        }
    } catch (e) {
        // No barcode detected in this frame
    } finally {
        scanInProgress = false;
    }
}

function manualScan() {
    const code = document.getElementById('manual-code-input').value.trim();
    if (!code) {
        showScanStatus('error', 'Masukkan kode aset atau barcode.');
        return;
    }
    handleScanResult(code);
}

function extractKodeAset(raw) {
    if (!raw) return raw;
    try {
        var u = new URL(raw);
        var parts = u.pathname.replace(/\/+$/, '').split('/');
        var idx = parts.indexOf('aset');
        if (idx !== -1 && parts[idx + 1]) return decodeURIComponent(parts[idx + 1]);
    } catch(e) {}
    var m = raw.match(/\/aset\/([^\/\?#]+)/);
    if (m) return decodeURIComponent(m[1]);
    return raw;
}

function handleScanResult(code) {
    stopScan();
    showScanStatus('scanning', 'Mencari data...');

    var parsedCode = extractKodeAset(code);

    fetch(scanUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ code: parsedCode }),
    })
    .then(res => {
        if (!res.ok) return res.json().then(d => { throw new Error(d.message || 'Data aset tidak ditemukan.'); });
        return res.json();
    })
    .then(data => {
        showScanStatus('success', 'Barcode dikenali! Menampilkan detail...');
        setTimeout(() => {
            closeScanModal();
            const item = itemsData.find(x => x.id === data.id);
            if (item) {
                showDetail(data.id);
            } else {
                showScannedDetail(data);
            }
        }, 1000);
    })
    .catch(err => {
        showScanStatus('error', err.message || 'Data aset tidak ditemukan.');
    });
}

function showScannedDetail(i) {
    document.getElementById('detail-title').textContent = i.nama_barang;

    const kondisiMap = {
        baik: { label: 'Baik', bg: '#ecfdf5', text: '#059669', border: '#a7f3d0' },
        perlu_servis: { label: 'Perlu Servis', bg: '#fff7ed', text: '#c2410c', border: '#fed7aa' },
        rusak: { label: 'Rusak', bg: '#fef2f2', text: '#dc2626', border: '#fecaca' },
    };
    const k = kondisiMap[i.kondisi] || kondisiMap.baik;
    const badgeEl = document.getElementById('detail-badge');
    badgeEl.textContent = k.label;
    badgeEl.style.background = k.bg;
    badgeEl.style.color = k.text;
    badgeEl.style.border = '1px solid ' + k.border;

    currentDetailId = i.id;

    var fotoSection = document.getElementById('detail-foto-section');
    var fotoImg = document.getElementById('detail-foto-img');
    if (i.foto) {
        fotoImg.src = i.foto;
        fotoSection.style.display = '';
    } else {
        fotoSection.style.display = 'none';
    }

    renderBarcode('detail-barcode-image', i.barcode || i.kode_aset, '{{ url("/aset") }}/' + encodeURIComponent(i.kode_aset));
    document.getElementById('detail-body').innerHTML = renderDetailCards(i);
    document.getElementById('detail-media-placeholder').style.display = (!i.foto && !i.barcode && !i.kode_aset) ? '' : 'none';
    openModal('detail-modal');
}

function showScanStatus(type, message) {
    ['idle', 'scanning', 'success', 'error'].forEach(s => {
        const el = document.getElementById('scan-status-' + s);
        if (el) el.classList.add('hidden');
    });
    const el = document.getElementById('scan-status-' + type);
    if (el) {
        el.classList.remove('hidden');
        if (type === 'error') {
            const msgEl = document.getElementById('scan-status-error-text');
            if (msgEl) msgEl.textContent = message;
        }
    }
}

function showToast(type, message) {
    const existing = document.querySelector('.toast-notif');
    if (existing) existing.remove();

    const isSuccess = type === 'success';
    const bg = isSuccess ? '#10b981' : '#ef4444';
    const borderColor = isSuccess ? 'rgba(16,185,129,0.4)' : 'rgba(239,68,68,0.4)';

    const toast = document.createElement('div');
    toast.className = 'toast-notif';
    toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;padding:14px 22px;border-radius:12px;font-size:14px;font-weight:500;color:#fff;background:' + bg + ';border:1px solid ' + borderColor + ';box-shadow:0 8px 32px rgba(0,0,0,0.25);display:flex;align-items:center;gap:10px;transform:translateX(120%);transition:transform 0.35s cubic-bezier(0.34,1.56,0.64,1);max-width:400px;';
    toast.innerHTML = (isSuccess
        ? '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        : '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>')
        + '<span>' + message + '</span>';
    document.body.appendChild(toast);
    requestAnimationFrame(() => toast.style.transform = 'translateX(0)');
    setTimeout(() => { toast.style.transform = 'translateX(120%)'; setTimeout(() => toast.remove(), 350); }, 4000);
}

function openImportModal() {
    document.getElementById('import-file').value = '';
    document.getElementById('import-submit-btn').disabled = false;
    document.getElementById('import-submit-btn').textContent = 'Import';
    openModal('import-modal');
}

function confirmResetData() {
    showConfirmModal(
        'Semua data peralatan kantor (' + itemsData.length + ' item) akan dihapus permanen. Setelah itu, silakan import ulang dari file Excel. Lanjutkan?',
        function() {
            document.getElementById('reset-data-form').submit();
        },
        { buttonText: 'Ya, Hapus Semua' }
    );
}

document.getElementById('import-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal('import-modal');
});

document.getElementById('import-file')?.addEventListener('change', function() {
    const btn = document.getElementById('import-submit-btn');
    btn.disabled = !this.files.length;
});

function toggleFilterMenu(e) {
    e.stopPropagation();
    const menu = document.getElementById('filter-menu');
    const timMenu = document.getElementById('tim-filter-menu');
    if (timMenu) timMenu.style.display = 'none';
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function setFilter(value) {
    const url = new URL(window.location.href);
    if (value === 'all') url.searchParams.delete('kondisi');
    else url.searchParams.set('kondisi', value);
    window.location.href = url.toString();
}

function setDateRange(value) {
    const url = new URL(window.location.href);
    if (value) url.searchParams.set('range', value);
    else url.searchParams.delete('range');
    window.location.href = url.toString();
}

function toggleTimFilterMenu(e) {
    e.stopPropagation();
    const menu = document.getElementById('tim-filter-menu');
    document.getElementById('filter-menu').style.display = 'none';
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function setTimFilter(value) {
    const url = new URL(window.location.href);
    if (value) {
        url.searchParams.set('tim', value);
    } else {
        url.searchParams.delete('tim');
    }
    window.location.href = url.toString();
}

document.querySelectorAll('#tim-filter-menu button[data-tim]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        setTimFilter(btn.dataset.tim);
    });
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('.filter-dropdown-wrap')) {
        document.getElementById('filter-menu').style.display = 'none';
        const timMenu = document.getElementById('tim-filter-menu');
        if (timMenu) timMenu.style.display = 'none';
    }
});

function renderPaginatedRows() {
    const allRows = document.querySelectorAll('#item-tbody tr:not(#empty-row)');
    allRows.forEach(row => row.style.display = 'none');
    const start = (currentPage - 1) * perPage;
    const end = start + perPage;
    const pageRows = filteredRows.slice(start, end);
    pageRows.forEach(row => row.style.display = '');
    renderPagination();
}

function renderPagination() {
    const total = filteredRows.length;
    const totalPages = Math.max(Math.ceil(total / perPage), 1);
    const infoEl = document.getElementById('pagination-text');
    const controlsEl = document.getElementById('pagination-controls');
    if (!infoEl || !controlsEl) return;
    if (total === 0) {
        infoEl.textContent = 'Tidak ada data';
        controlsEl.innerHTML = '';
        return;
    }
    const start = (currentPage - 1) * perPage + 1;
    const end = Math.min(currentPage * perPage, total);
    infoEl.textContent = 'Menampilkan ' + start + '-' + end + ' dari ' + total + ' item';
    let html = '';
    html += '<button onclick="goToPage(' + (currentPage - 1) + ')" ' + (currentPage <= 1 ? 'disabled' : '') + ' class="btn btn-secondary btn-sm" style="padding:4px 10px;font-size:0.7rem;opacity:' + (currentPage <= 1 ? '0.4' : '1') + ';">&lsaquo; Sebelumnya</button>';
    for (let p = 1; p <= totalPages; p++) {
        if (totalPages > 7 && p > 3 && p < totalPages - 1 && Math.abs(p - currentPage) > 1) {
            if (p === 4 || p === totalPages - 2) html += '<span style="color:var(--text-muted);padding:0 4px;">...</span>';
            continue;
        }
        html += '<button onclick="goToPage(' + p + ')" class="btn btn-sm" style="padding:4px 9px;font-size:0.7rem;' + (p === currentPage ? 'background:var(--color-accent);color:#fff;' : 'background:var(--bg-surface);color:var(--text-primary);border:1px solid var(--border-color);') + '">' + p + '</button>';
    }
    html += '<button onclick="goToPage(' + (currentPage + 1) + ')" ' + (currentPage >= totalPages ? 'disabled' : '') + ' class="btn btn-secondary btn-sm" style="padding:4px 10px;font-size:0.7rem;opacity:' + (currentPage >= totalPages ? '0.4' : '1') + ';">Berikutnya &rsaquo;</button>';
    controlsEl.innerHTML = html;
}

function goToPage(page) {
    const totalPages = Math.max(Math.ceil(filteredRows.length / perPage), 1);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderPaginatedRows();
}

document.getElementById('f-foto')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('f-foto-preview');
    const img = document.getElementById('f-foto-preview-img');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) { img.src = ev.target.result; preview.classList.remove('hidden'); };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
});

function clearFotoPreview() {
    document.getElementById('f-foto').value = '';
    document.getElementById('f-foto-preview').classList.add('hidden');
    document.getElementById('f-foto-preview-img').src = '';
}

</script>
@endpush
