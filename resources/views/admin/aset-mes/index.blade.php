@extends('layouts.app')
@section('body-class', 'page-admin page-aset-mes')
@section('title', 'Aset MES')
@section('page-title', 'Data Aset > Aset MES')
@section('page-subtitle', 'Daftar aset MES Putra, Putri, Koordinator, dan Johen Streamer')
@section('sidebar-menu') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="pt-2 space-y-4 animate-fade-in">

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 md:gap-3">
        <div class="stat-card-compact">
            <div class="stat-icon-box" style="background:rgba(124,58,237,0.15);box-shadow:0 0 14px rgba(124,58,237,0.20);">
                <svg style="color:#a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3h14a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2zM3 13h18v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6z"/>
                </svg>
            </div>
            <div>
                <div class="stat-num" style="color:#a78bfa;">{{ $stats['total'] }}</div>
                <div class="stat-label-text" style="font-size:0.7rem;">Total Aset MES</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-box" style="background:rgba(37,99,235,0.15);box-shadow:0 0 14px rgba(37,99,235,0.20);">
                <svg style="color:#60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <div class="stat-num" style="color:#60a5fa;">{{ $stats['putra'] }}</div>
                <div class="stat-label-text" style="font-size:0.7rem;">Mes Putra</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-box" style="background:rgba(236,72,153,0.15);box-shadow:0 0 14px rgba(236,72,153,0.20);">
                <svg style="color:#f472b6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <div class="stat-num" style="color:#f472b6;">{{ $stats['putri'] }}</div>
                <div class="stat-label-text" style="font-size:0.7rem;">Mes Putri</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-box" style="background:rgba(16,185,129,0.15);box-shadow:0 0 14px rgba(16,185,129,0.20);">
                <svg style="color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-num" style="color:#34d399;">{{ $stats['aktif'] }}</div>
                <div class="stat-label-text" style="font-size:0.7rem;">Aset Aktif</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-box" style="background:rgba(239,68,68,0.15);box-shadow:0 0 14px rgba(239,68,68,0.20);">
                <svg style="color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-num" style="color:#ef4444;">{{ $stats['nonaktif'] }}</div>
                <div class="stat-label-text" style="font-size:0.7rem;">Aset Tidak Aktif</div>
            </div>
        </div>
    </div>

    {{-- Alert Aset Tidak Aktif --}}
    @if($alertItems->isNotEmpty())
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <div style="flex:1;min-width:260px;">
            <div class="flex items-start gap-3 px-5 py-3.5 rounded-2xl" style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color:#ef4444;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold" style="color:#ef4444;">{{ $alertItems->count() }} Aset Tidak Aktif</div>
                    <div class="text-xs mt-1" style="color:var(--text-secondary);">{{ $alertItems->count() }} aset MES berstatus tidak aktif.</div>
                </div>
                <button type="button" onclick="showAlertPopup()" style="flex-shrink:0;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:600;background:rgba(239,68,68,0.12);color:#ef4444;border:1px solid rgba(239,68,68,0.2);cursor:pointer;white-space:nowrap;">Lihat Detail</button>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== MES PUTRA ===== --}}
    <div class="gaming-card" style="overflow:visible;">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom:1px solid var(--border-color);">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:#60a5fa;"></span>
                <div>
                    <div style="font-weight:600;font-size:0.8rem;color:var(--text-primary);">Data Aset MES Putra</div>
                    <div style="font-size:0.7rem;color:var(--text-muted);margin-top:2px;font-weight:400;">Aset dan perlengkapan Mes Putra.</div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[0.65rem] font-semibold mt-1.5" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Penanggung Jawab: {{ $penanggungJawabMes['putra'] }}
                    </span>
                </div>
            </div>
            @if(!in_array(auth()->user()->role, ['gm', 'ceo']))
            <button type="button" onclick="openCreateModal('putra')" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Aset Putra
            </button>
            @endif
        </div>
        <div class="px-6 py-2.5 flex flex-wrap items-center gap-3" style="border-bottom:1px solid var(--border-color);">
            <div class="relative flex-1 min-w-[200px] max-w-[260px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <form method="GET" action="{{ route('admin.aset-mes.index') }}">
                    <input type="hidden" name="status_putra" value="{{ $statusPutra }}">
                    <input type="hidden" name="show_all_putra" value="{{ $showAllPutra ? 1 : '' }}">
                    <input type="hidden" name="search_putri" value="{{ $searchPutri }}">
                    <input type="hidden" name="status_putri" value="{{ $statusPutri }}">
                    <input type="hidden" name="show_all_putri" value="{{ $showAllPutri ? 1 : '' }}">
                    <input type="text" name="search_putra" value="{{ $searchPutra }}" placeholder="Cari..." oninput="this.form.submit()"
                        class="w-full pl-9 pr-3 py-1.5 rounded-lg text-xs"
                        style="background:var(--bg-surface);border:1px solid var(--border-color);color:var(--text-primary);outline:none;">
                </form>
            </div>
            <div class="flex items-center gap-2" style="margin-left:auto;">
                <a href="{{ route('admin.export', ['type' => 'aset-mes', 'filter' => 'putra']) }}" class="btn btn-secondary btn-sm inline-flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Export</a>
                <div class="filter-dropdown-wrap" style="position:relative;">
                    <button type="button" onclick="toggleStatusMenu(event, 'putra')" class="filter-btn" style="display:flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:500;cursor:pointer;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-primary);outline:none;white-space:nowrap;">
                        <span id="status-label-putra">{{ $statusPutra === '1' ? 'Aktif' : ($statusPutra === '0' ? 'Nonaktif' : 'Semua Status') }}</span>
                        <svg class="w-3.5 h-3.5" style="color:var(--text-muted);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="status-menu-putra" class="filter-menu" style="display:none;position:absolute;right:0;top:100%;z-index:40;min-width:150px;background:var(--bg-surface);border:1px solid var(--border-color);border-radius:10px;padding:4px;box-shadow:0 8px 24px rgba(0,0,0,0.15);margin-top:4px;">
                        <button type="button" data-value="all" onclick="setStatusFilter('all', 'putra')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Semua Status</button>
                        <button type="button" data-value="1" onclick="setStatusFilter('1', 'putra')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Aktif</button>
                        <button type="button" data-value="0" onclick="setStatusFilter('0', 'putra')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Tidak Aktif</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="gaming-table min-w-[700px]" id="aset-table-putra">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Aset</th>
                        <th>Jumlah</th>
                        <th>Penanggung Jawab</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="aset-tbody-putra">
                    @forelse($assetsPutra as $a)
                    <tr data-status="{{ $a->is_active ? '1' : '0' }}">
                        <td style="color:var(--text-muted);">{{ $assetsPutra->firstItem() + $loop->index }}</td>
                        <td style="color:var(--text-primary);font-weight:500;">{{ $a->nama_aset }}</td>
                        <td style="color:var(--text-muted);">{{ $a->jumlah }}</td>
                        <td style="color:var(--text-muted);">{{ $a->penanggungJawab?->name ?? '-' }}</td>
                        <td><span class="badge {{ $a->is_active ? 'badge-green' : 'badge-red' }}">{{ $a->is_active ? 'Aktif' : 'Tidak Aktif' }}</span></td>
                        <td style="max-width:150px;color:var(--text-muted);">{{ $a->keterangan ?? '-' }}</td>
                                                <td>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="showDetail({{ $a->id }})" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:4px;padding:3px 6px;font-size:0.7rem;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Lihat Detail
                                </button>
                                <div class="dropdown-wrap" style="position:relative;">
                                    <button type="button" onclick="toggleDropdown(this, {{ $a->id }})" class="btn btn-secondary btn-sm" style="padding:3px 6px;font-size:0.7rem;line-height:1;">⋮</button>
                                    <div id="dropdown-{{ $a->id }}" class="dropdown-menu" style="display:none;position:absolute;top:100%;right:0;z-index:99999;min-width:130px;background:var(--bg-surface);border:1px solid var(--border-color);border-radius:10px;padding:4px;box-shadow:0 8px 24px rgba(0,0,0,0.15);margin-top:4px;">
                                        <button type="button" onclick="showDetail({{ $a->id }})" style="display:block;width:100%;text-align:left;padding:6px 10px;border:none;background:none;font-size:12px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Detail</button>
                                        @if(auth()->user()->role !== 'gm' && auth()->user()->role !== 'ceo')
                                        <button type="button" onclick="openEditModal({{ $a->id }})" style="display:block;width:100%;text-align:left;padding:6px 10px;border:none;background:none;font-size:12px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Edit</button>
                                        <form method="POST" action="{{ route('admin.aset-mes.destroy', $a) }}" onsubmit="confirmSubmit(event, this)" data-confirm="Hapus aset MES ini?" style="margin:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="display:block;width:100%;text-align:left;padding:6px 10px;border:none;background:none;font-size:12px;color:#ef4444;border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Hapus</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted);">Belum ada data aset Mes Putra.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-2.5 flex flex-wrap items-center gap-3" style="border-top:1px solid var(--border-color);">
            <span style="font-size:0.75rem;color:var(--text-muted);white-space:nowrap;">
                @if($assetsPutra->firstItem())
                    @if(!$showAllPutra)
                        Menampilkan {{ $assetsPutra->firstItem() }}-{{ $assetsPutra->lastItem() }} dari {{ $assetsPutra->total() }} item
                    @else
                        Menampilkan semua {{ $assetsPutra->total() }} item
                    @endif
                @else
                    Menampilkan 0 dari {{ $assetsPutra->total() }} item
                @endif
            </span>
            @if($assetsPutra->total() > 0)
                @if(!$showAllPutra)
                    <a href="{{ route('admin.aset-mes.index') }}?show_all_putra=1" style="font-size:0.75rem;color:var(--color-accent);font-weight:500;text-decoration:none;white-space:nowrap;">Selengkapnya &rarr;</a>
                @else
                    <a href="{{ route('admin.aset-mes.index') }}" style="font-size:0.75rem;color:var(--color-accent);font-weight:500;text-decoration:none;white-space:nowrap;">&larr; Kembali ke Ringkasan</a>
                @endif
            @endif
            <div style="margin-left:auto;">
                @if(method_exists($assetsPutra, 'links') && $assetsPutra->hasPages())
                    {{ $assetsPutra->links() }}
                @endif
            </div>
        </div>
    </div>

    {{-- ===== MES PUTRI ===== --}}
    <div class="gaming-card" style="overflow:visible;">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom:1px solid var(--border-color);">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:#f472b6;"></span>
                <div>
                    <div style="font-weight:600;font-size:0.8rem;color:var(--text-primary);">Data Aset MES Putri</div>
                    <div style="font-size:0.7rem;color:var(--text-muted);margin-top:2px;font-weight:400;">Aset dan perlengkapan Mes Putri.</div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[0.65rem] font-semibold mt-1.5" style="background:#fdf2f8;color:#be185d;border:1px solid #fbcfe8;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Penanggung Jawab: {{ $penanggungJawabMes['putri'] }}
                    </span>
                </div>
            </div>
            @if(!in_array(auth()->user()->role, ['gm', 'ceo']))
            <button type="button" onclick="openCreateModal('putri')" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Aset Putri
            </button>
            @endif
        </div>
        <div class="px-6 py-2.5 flex flex-wrap items-center gap-3" style="border-bottom:1px solid var(--border-color);">
            <div class="relative flex-1 min-w-[200px] max-w-[260px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <form method="GET" action="{{ route('admin.aset-mes.index') }}">
                    <input type="hidden" name="status_putri" value="{{ $statusPutri }}">
                    <input type="hidden" name="show_all_putri" value="{{ $showAllPutri ? 1 : '' }}">
                    <input type="hidden" name="search_putra" value="{{ $searchPutra }}">
                    <input type="hidden" name="status_putra" value="{{ $statusPutra }}">
                    <input type="hidden" name="show_all_putra" value="{{ $showAllPutra ? 1 : '' }}">
                    <input type="text" name="search_putri" value="{{ $searchPutri }}" placeholder="Cari..." oninput="this.form.submit()"
                        class="w-full pl-9 pr-3 py-1.5 rounded-lg text-xs"
                        style="background:var(--bg-surface);border:1px solid var(--border-color);color:var(--text-primary);outline:none;">
                </form>
            </div>
            <div class="flex items-center gap-2" style="margin-left:auto;">
                <a href="{{ route('admin.export', ['type' => 'aset-mes', 'filter' => 'putri']) }}" class="btn btn-secondary btn-sm inline-flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Export</a>
                <div class="filter-dropdown-wrap" style="position:relative;">
                    <button type="button" onclick="toggleStatusMenu(event, 'putri')" class="filter-btn" style="display:flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:500;cursor:pointer;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-primary);outline:none;white-space:nowrap;">
                        <span id="status-label-putri">{{ $statusPutri === '1' ? 'Aktif' : ($statusPutri === '0' ? 'Nonaktif' : 'Semua Status') }}</span>
                        <svg class="w-3.5 h-3.5" style="color:var(--text-muted);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="status-menu-putri" class="filter-menu" style="display:none;position:absolute;right:0;top:100%;z-index:40;min-width:150px;background:var(--bg-surface);border:1px solid var(--border-color);border-radius:10px;padding:4px;box-shadow:0 8px 24px rgba(0,0,0,0.15);margin-top:4px;">
                        <button type="button" data-value="all" onclick="setStatusFilter('all', 'putri')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Semua Status</button>
                        <button type="button" data-value="1" onclick="setStatusFilter('1', 'putri')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Aktif</button>
                        <button type="button" data-value="0" onclick="setStatusFilter('0', 'putri')" style="display:block;width:100%;text-align:left;padding:7px 12px;border:none;background:none;font-size:13px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Tidak Aktif</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="gaming-table min-w-[700px]" id="aset-table-putri">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Aset</th>
                        <th>Jumlah</th>
                        <th>Penanggung Jawab</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="aset-tbody-putri">
                    @forelse($assetsPutri as $a)
                    <tr data-status="{{ $a->is_active ? '1' : '0' }}">
                        <td style="color:var(--text-muted);">{{ $assetsPutri->firstItem() + $loop->index }}</td>
                        <td style="color:var(--text-primary);font-weight:500;">{{ $a->nama_aset }}</td>
                        <td style="color:var(--text-muted);">{{ $a->jumlah }}</td>
                        <td style="color:var(--text-muted);">{{ $a->penanggungJawab?->name ?? '-' }}</td>
                        <td><span class="badge {{ $a->is_active ? 'badge-green' : 'badge-red' }}">{{ $a->is_active ? 'Aktif' : 'Tidak Aktif' }}</span></td>
                        <td style="max-width:150px;color:var(--text-muted);">{{ $a->keterangan ?? '-' }}</td>
                                                <td>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="showDetail({{ $a->id }})" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:4px;padding:3px 6px;font-size:0.7rem;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Lihat Detail
                                </button>
                                <div class="dropdown-wrap" style="position:relative;">
                                    <button type="button" onclick="toggleDropdown(this, {{ $a->id }})" class="btn btn-secondary btn-sm" style="padding:3px 6px;font-size:0.7rem;line-height:1;">⋮</button>
                                    <div id="dropdown-{{ $a->id }}" class="dropdown-menu" style="display:none;position:absolute;top:100%;right:0;z-index:99999;min-width:130px;background:var(--bg-surface);border:1px solid var(--border-color);border-radius:10px;padding:4px;box-shadow:0 8px 24px rgba(0,0,0,0.15);margin-top:4px;">
                                        <button type="button" onclick="showDetail({{ $a->id }})" style="display:block;width:100%;text-align:left;padding:6px 10px;border:none;background:none;font-size:12px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Detail</button>
                                        @if(auth()->user()->role !== 'gm' && auth()->user()->role !== 'ceo')
                                        <button type="button" onclick="openEditModal({{ $a->id }})" style="display:block;width:100%;text-align:left;padding:6px 10px;border:none;background:none;font-size:12px;color:var(--text-primary);border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Edit</button>
                                        <form method="POST" action="{{ route('admin.aset-mes.destroy', $a) }}" onsubmit="confirmSubmit(event, this)" data-confirm="Hapus aset MES ini?" style="margin:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="display:block;width:100%;text-align:left;padding:6px 10px;border:none;background:none;font-size:12px;color:#ef4444;border-radius:6px;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">Hapus</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted);">Belum ada data aset Mes Putri.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-2.5 flex flex-wrap items-center gap-3" style="border-top:1px solid var(--border-color);">
            <span style="font-size:0.75rem;color:var(--text-muted);white-space:nowrap;">
                @if($assetsPutri->firstItem())
                    @if(!$showAllPutri)
                        Menampilkan {{ $assetsPutri->firstItem() }}-{{ $assetsPutri->lastItem() }} dari {{ $assetsPutri->total() }} item
                    @else
                        Menampilkan semua {{ $assetsPutri->total() }} item
                    @endif
                @else
                    Menampilkan 0 dari {{ $assetsPutri->total() }} item
                @endif
            </span>
            @if($assetsPutri->total() > 0)
                @if(!$showAllPutri)
                    <a href="{{ route('admin.aset-mes.index') }}?show_all_putri=1" style="font-size:0.75rem;color:var(--color-accent);font-weight:500;text-decoration:none;white-space:nowrap;">Selengkapnya &rarr;</a>
                @else
                    <a href="{{ route('admin.aset-mes.index') }}" style="font-size:0.75rem;color:var(--color-accent);font-weight:500;text-decoration:none;white-space:nowrap;">&larr; Kembali ke Ringkasan</a>
                @endif
            @endif
            <div style="margin-left:auto;">
                @if(method_exists($assetsPutri, 'links') && $assetsPutri->hasPages())
                    {{ $assetsPutri->links() }}
                @endif
            </div>
        </div>
    </div>
    @foreach($additionalCategories as $kategori => $group)
    <div class="gaming-card" style="overflow:visible;">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom:1px solid var(--border-color);">
            <div>
                <div style="font-weight:600;font-size:0.85rem;color:var(--text-primary);">Data Aset {{ $group['label'] }}</div>
                <div style="font-size:0.7rem;color:var(--text-muted);margin-top:2px;">{{ $group['assets']->total() }} data aset</div>
            </div>
            @if(!in_array(auth()->user()->role, ['gm', 'ceo']))
            <button type="button" onclick="openCreateModal('{{ $kategori }}')" class="btn btn-primary btn-sm">Tambah Aset</button>
            @endif
        </div>
        <div class="px-6 py-2.5 flex items-center gap-3" style="border-bottom:1px solid var(--border-color);">
            <form method="GET" action="{{ route('admin.aset-mes.index') }}" class="flex-1 max-w-[260px]">
                <input type="text" name="search_{{ $kategori }}" value="{{ $group['search'] }}" placeholder="Cari aset..." class="w-full px-3 py-1.5 rounded-lg text-xs" style="background:var(--bg-surface);border:1px solid var(--border-color);color:var(--text-primary);">
            </form>
            <a href="{{ route('admin.export', ['type' => 'aset-mes', 'filter' => $kategori]) }}" class="btn btn-secondary btn-sm">Export</a>
        </div>
        <div class="table-responsive">
            <table class="gaming-table min-w-[700px]"><thead><tr><th>No</th><th>Nama Aset</th><th>Jumlah</th><th>Penanggung Jawab</th><th>Status</th><th>Keterangan</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($group['assets'] as $a)
                    <tr>
                        <td>{{ $group['assets']->firstItem() + $loop->index }}</td><td>{{ $a->nama_aset }}</td><td>{{ $a->jumlah }}</td>
                        <td>{{ $a->penanggungJawab?->name ?? $a->pic ?? '-' }}</td>
                        <td><span class="badge {{ $a->is_active ? 'badge-green' : 'badge-red' }}">{{ $a->is_active ? 'Aktif' : 'Tidak Aktif' }}</span></td>
                        <td>{{ $a->keterangan ?? '-' }}</td>
                        <td>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="showDetail({{ $a->id }})" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:4px;padding:3px 6px;font-size:0.7rem;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Lihat Detail
                                </button>
                                <div class="dropdown-wrap" style="position:relative;">
                                    <button type="button" onclick="toggleDropdown(this, {{ $a->id }})" class="btn btn-secondary btn-sm" style="padding:3px 6px;font-size:0.7rem;line-height:1;">⋮</button>
                                    <div id="dropdown-{{ $a->id }}" class="dropdown-menu" style="display:none;position:absolute;top:100%;right:0;z-index:99999;min-width:130px;background:var(--bg-surface);border:1px solid var(--border-color);border-radius:10px;padding:4px;box-shadow:0 8px 24px rgba(0,0,0,0.15);margin-top:4px;">
                                        <button type="button" onclick="showDetail({{ $a->id }})" style="display:block;width:100%;text-align:left;padding:6px 10px;border:none;background:none;font-size:12px;color:var(--text-primary);border-radius:6px;cursor:pointer;">Detail</button>
                                        @if(auth()->user()->role !== 'gm' && auth()->user()->role !== 'ceo')
                                        <button type="button" onclick="openEditModal({{ $a->id }})" style="display:block;width:100%;text-align:left;padding:6px 10px;border:none;background:none;font-size:12px;color:var(--text-primary);border-radius:6px;cursor:pointer;">Edit</button>
                                        <form method="POST" action="{{ route('admin.aset-mes.destroy', $a) }}" onsubmit="confirmSubmit(event, this)" data-confirm="Hapus aset MES ini?" style="margin:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="display:block;width:100%;text-align:left;padding:6px 10px;border:none;background:none;font-size:12px;color:#ef4444;border-radius:6px;cursor:pointer;">Hapus</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted);">Belum ada aset {{ $group['label'] }}.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($group['assets']->hasPages())<div class="px-5 py-3">{{ $group['assets']->links() }}</div>@endif
    </div>
    @endforeach
</div>

{{-- Detail Modal --}}
<div id="detail-modal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:var(--bg-overlay);">
    <div class="w-full max-w-[460px] rounded-3xl shadow-2xl flex flex-col" style="max-height:65vh;background:var(--bg-surface);" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 flex-shrink-0" style="border-bottom:1px solid var(--border-color);">
            <h3 class="text-base font-bold" style="color:var(--text-primary);" id="detail-title">Detail Aset MES</h3>
            <button type="button" onclick="closeDetail()" class="p-1.5 rounded-xl transition" style="color:var(--text-muted);background:none;border:none;cursor:pointer;" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='none'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="px-6 py-5 overflow-y-auto flex-1" id="detail-body"></div>
        <div class="px-6 py-4 flex-shrink-0 flex justify-between items-center" style="border-top:1px solid var(--border-color);">
            <button type="button" onclick="closeDetail()" class="px-5 py-2 rounded-xl text-sm font-medium transition" style="color:var(--text-primary);border:1px solid var(--border-color);background:var(--bg-surface);" onmouseover="this.style.background='var(--bg-surface-2)'" onmouseout="this.style.background='var(--bg-surface)'">Tutup</button>
        </div>
    </div>
</div>

{{-- Modal Tambah / Edit --}}
<div id="aset-modal" class="modal-modern" onclick="if(event.target===this)closeModal('aset-modal')">
    <div class="modal-modern-panel md" onclick="event.stopPropagation()">
        <div class="modal-modern-header">
            <h3 id="modal-title">Tambah Aset MES</h3>
            <button type="button" onclick="closeModal('aset-modal')" class="modal-modern-close">&times;</button>
        </div>
        <form id="aset-form" method="POST">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            <input type="hidden" name="id" id="form-id" value="">
            <div class="modal-modern-body">
                <div class="form-grid-2">
                    <div class="field-group">
                        <label class="gaming-label">Kategori <span class="field-req">*</span></label>
                        <select name="kategori" id="f-kategori" required class="gaming-input gaming-select">
                            <option value="putra">Mes Putra</option>
                            <option value="putri">Mes Putri</option>
                            <option value="koordinator">MES Koordinator</option>
                            <option value="johen_streamer">MES Johen Streamer</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="gaming-label">Jumlah</label>
                        <input type="number" name="jumlah" id="f-jumlah" placeholder="Jumlah" min="1" class="gaming-input">
                    </div>
                    <div class="field-group">
                        <label class="gaming-label">Nama Aset <span class="field-req">*</span></label>
                        <input type="text" name="nama_aset" id="f-nama_aset" required placeholder="Masukan nama aset" class="gaming-input">
                    </div>
                    <div class="field-group">
                        <label class="gaming-label">Penanggung Jawab</label>
                        <select name="penanggung_jawab" id="f-penanggung_jawab" class="gaming-input gaming-select">
                            <option value="">— Pilih Koordinator —</option>
                            @foreach(\App\Models\User::where('role', 'koordinator')->orWhere('role', 'admin_ga')->orderBy('name')->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->username }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="gaming-label">PIC</label>
                        <select name="pic" id="f-pic" class="gaming-input gaming-select">
                            <option value="">— Pilih PIC —</option>
                            @foreach(\App\Models\User::where('is_active', true)->orderBy('name')->get() as $u)
                            <option value="{{ $u->name }}">{{ $u->name }} ({{ $u->username }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="gaming-label">Jabatan</label>
                        <input type="text" name="jabatan" id="f-jabatan" placeholder="Jabatan PIC" class="gaming-input">
                    </div>
                    <div class="field-group" style="grid-column:1/-1;">
                        <label class="gaming-label">Keterangan</label>
                        <textarea name="keterangan" id="f-keterangan" placeholder="Keterangan" rows="2" class="gaming-input" style="resize:vertical;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-modern-footer gap-2">
                <button type="button" onclick="closeModal('aset-modal')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary" id="form-submit-btn">Tambah</button>
            </div>
        </form>
    </div>
</div>

{{-- Popup Alert Aset MES --}}
<div id="alert-overlay" style="display:none;position:fixed;inset:0;z-index:9999;background:var(--bg-overlay);align-items:center;justify-content:center;padding:16px;" onclick="if(event.target===this)closeAlertPopup()">
    <div style="background:var(--bg-surface);border-radius:16px;padding:24px;width:90%;max-width:460px;max-height:65vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <div id="alert-popup-title" style="font-weight:700;font-size:16px;color:var(--text-primary);">Aset MES Tidak Aktif</div>
            <button type="button" onclick="closeAlertPopup()" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:20px;line-height:1;">&times;</button>
        </div>
        <div id="alert-popup-body"></div>
    </div>
</div>

@endsection

@push('styles')
<style>
.gaming-table tbody td { padding: 0.75rem 1.125rem; vertical-align: middle; font-size:0.8rem; }
.gaming-table thead th { padding: 0.625rem 1.125rem; font-size:0.65rem; letter-spacing:0.03em; }
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; margin-bottom: 16px; }
@media (max-width: 640px) { .form-grid-2 { grid-template-columns: 1fr; } }
.field-group { display: flex; flex-direction: column; gap: 6px; }
.field-req { color: #f87171; }
.page-admin.page-aset-mes .btn-primary {
    background: #6d5ef9;
    box-shadow: 0 2px 8px rgba(109,94,249,0.25);
}
.page-admin.page-aset-mes .btn-primary:hover {
    background: #5a4be0;
    box-shadow: 0 4px 14px rgba(109,94,249,0.35);
}
</style>
@endpush

@push('scripts')
<script>
const assets = @json($assetsJson);
const alertData = @json($alertJson);

function kategoriLabel(kategori) {
    return ({putra:'Mes Putra', putri:'Mes Putri', koordinator:'MES Koordinator', johen_streamer:'MES Johen Streamer'})[kategori] || kategori;
}

function showAlertPopup() {
    const title = document.getElementById('alert-popup-title');
    const body = document.getElementById('alert-popup-body');
    var color = '#ef4444';

    title.textContent = 'Aset MES Tidak Aktif';
    const items = alertData.filter(function(a) { return !a.is_active; });

    if (items.length === 0) {
        body.innerHTML = '<p style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ada aset tidak aktif.</p>';
    } else {
        body.innerHTML = items.map(function(a) {
            return '<div class="flex items-center gap-3 px-4 py-3.5 rounded-xl" style="border:1px solid var(--border-color);margin-bottom:8px;cursor:pointer;transition:all 0.15s;background:var(--bg-surface-2);" onclick="openEditModal(' + a.id + ')" onmouseover="this.style.borderColor=\'' + color + '\'" onmouseout="this.style.borderColor=\'var(--border-color)\'">' +
                '<div class="flex-1 min-w-0">' +
                    '<p style="font-weight:600;font-size:14px;color:var(--text-primary);margin:0;">' + a.nama_aset + '</p>' +
                    '<p style="font-size:12px;color:var(--text-muted);margin:2px 0 0;">' + kategoriLabel(a.kategori) + ' — PIC: ' + (a.pic || '-') + '</p>' +
                    '<p style="font-size:11px;color:#ef4444;margin:4px 0 0;font-weight:600;">Tidak Aktif</p>' +
                '</div>' +
                '<span onclick="event.stopPropagation()"><button onclick="openEditModal(' + a.id + ')" style="flex-shrink:0;padding:8px 16px;border-radius:10px;font-size:12px;font-weight:700;border:none;cursor:pointer;transition:all 0.2s;background:' + color + ';color:#fff;box-shadow:0 4px 12px rgba(239,68,68,0.3);" onmouseover="this.style.opacity=\'0.85\'" onmouseout="this.style.opacity=\'1\'">Perbaiki</button></span>' +
            '</div>';
        }).join('');
    }

    document.getElementById('alert-overlay').style.display = 'flex';
}

function closeAlertPopup() {
    document.getElementById('alert-overlay').style.display = 'none';
}

function toggleStatusMenu(e, kategori) {
    e.stopPropagation();
    const menu = document.getElementById('status-menu-' + kategori);
    document.querySelectorAll('.filter-menu').forEach(m => { if (m.id !== 'status-menu-' + kategori) m.style.display = 'none'; });
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function setStatusFilter(value, kategori) {
    const params = new URLSearchParams(window.location.search);
    params.delete('page_putra');
    params.delete('page_putri');
    if (value === 'all') params.delete('status_' + kategori);
    else params.set('status_' + kategori, value);
    window.location.search = params.toString();
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.filter-dropdown-wrap')) {
        document.querySelectorAll('.filter-menu').forEach(m => m.style.display = 'none');
    }
});

function toggleDropdown(btn, id) {
    const menu = document.getElementById('dropdown-' + id);
    document.querySelectorAll('.dropdown-menu').forEach(m => { if (m.id !== 'dropdown-' + id) m.style.display = 'none'; });
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown-wrap')) {
        document.querySelectorAll('.dropdown-menu').forEach(m => m.style.display = 'none');
    }
});

function closeModal(id) { document.getElementById(id).style.display = 'none'; document.body.style.overflow = ''; }
document.querySelectorAll('[id$="-modal"]').forEach(m => {
    m.addEventListener('click', function(e) { if (e.target === this) { this.style.display = 'none'; document.body.style.overflow = ''; } });
});

function showDetail(id) {
    const a = assets.find(x => x.id === id);
    if (!a) return;
    document.getElementById('detail-title').textContent = a.nama_aset;

    const kategoriStyle = a.kategori === 'putri'
        ? 'background:#fdf2f8;color:#be185d;border:1px solid #fbcfe8;'
        : 'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;';
    const kategoriBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold" style="' + kategoriStyle + '">' + kategoriLabel(a.kategori) + '</span>';

    const rows = [
        { label: 'Nama Aset', value: a.nama_aset },
        { label: 'Kategori', value: kategoriLabel(a.kategori) },
        { label: 'Jumlah', value: a.jumlah },
        { label: 'Penanggung Jawab', value: a.penanggung_jawab_nama },
        { label: 'PIC', value: a.pic || '-' },
        { label: 'Jabatan', value: a.jabatan || '-' },
    ];

    document.getElementById('detail-body').innerHTML = `
        <div class="space-y-1">
            ${rows.map((r, i) => `
                <div class="flex items-center justify-between py-2.5" ${i < rows.length - 1 ? 'style="border-bottom:1px solid var(--border-color);"' : ''}>
                    <p class="text-sm" style="color:var(--text-muted);">${r.label}</p>
                    <p class="text-sm font-semibold text-right" style="color:var(--text-primary);max-width:55%;">${r.label === 'Kategori' ? kategoriBadge : r.value}</p>
                </div>
            `).join('')}
            <div class="flex items-center justify-between py-2.5" style="border-bottom:1px solid var(--border-color);">
                <p class="text-sm" style="color:var(--text-muted);">Status</p>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold" style="background:${a.is_active ? '#ecfdf5' : '#fef2f2'};color:${a.is_active ? '#059669' : '#dc2626'};border:1px solid ${a.is_active ? '#a7f3d0' : '#fecaca'};">${a.is_active ? 'Aktif' : 'Tidak Aktif'}</span>
            </div>
            <div class="flex items-center justify-between py-2.5">
                <p class="text-sm" style="color:var(--text-muted);">Keterangan</p>
                <p class="text-sm font-semibold text-right" style="color:var(--text-primary);max-width:55%;">${a.keterangan || '-'}</p>
            </div>
        </div>
    `;
    openModal('detail-modal');
}
function closeDetail() { closeModal('detail-modal'); }
document.getElementById('detail-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDetail();
});

function openCreateModal(kategori) {
    kategori = kategori || 'putra';
    document.getElementById('modal-title').textContent = 'Tambah Aset ' + kategoriLabel(kategori);
    document.getElementById('form-method').value = 'POST';
    document.getElementById('form-id').value = '';
    document.getElementById('form-submit-btn').textContent = 'Tambah';
    document.getElementById('aset-form').action = '{{ route("admin.aset-mes.index") }}';
    document.getElementById('f-kategori').value = kategori;
    document.getElementById('f-nama_aset').value = '';
    document.getElementById('f-jumlah').value = '';
    document.getElementById('f-penanggung_jawab').value = '';
    document.getElementById('f-pic').value = '';
    document.getElementById('f-jabatan').value = '';
    document.getElementById('f-keterangan').value = '';
    document.getElementById('aset-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function openEditModal(id) {
    const a = assets.find(x => x.id === id);
    if (!a) return;
    document.getElementById('modal-title').textContent = 'Edit Aset MES';
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('form-id').value = a.id;
    document.getElementById('form-submit-btn').textContent = 'Simpan';
    document.getElementById('aset-form').action = '{{ url("admin/aset-mes") }}/' + a.id;
    document.getElementById('f-kategori').value = a.kategori || 'putra';
    document.getElementById('f-nama_aset').value = a.nama_aset;
    document.getElementById('f-jumlah').value = a.jumlah || '';
    document.getElementById('f-penanggung_jawab').value = a.penanggung_jawab || '';
    document.getElementById('f-pic').value = a.pic || '';
    document.getElementById('f-jabatan').value = a.jabatan || '';
    document.getElementById('f-keterangan').value = a.keterangan || '';
    document.getElementById('aset-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
</script>
@endpush
