@extends('layouts.internal')
@section('title', 'Kitchen Display System (KDS)')

@section('content')
<style>
/* ── KDS Premium Layout ── */
.kds-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1.5rem;
    height: calc(100vh - 120px);
}

@media(max-width: 1200px) {
    .kds-layout { grid-template-columns: 1fr; }
}

/* Kanban Board */
.kanban-board {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
    height: 100%;
}

.kanban-col {
    background: #F8FAFC;
    border-radius: 1.25rem;
    display: flex;
    flex-direction: column;
    border: 1px solid #E2E8F0;
    overflow: hidden;
}

.kanban-header {
    padding: 1.25rem 1.5rem;
    font-weight: 800;
    font-size: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid rgba(0,0,0,0.05);
}

.kanban-header-menunggu { background: #FFF7ED; color: #9A3412; border-bottom-color: #FED7AA; }
.kanban-header-diproses { background: #EFF6FF; color: #1E40AF; border-bottom-color: #BFDBFE; }
.kanban-header-siap     { background: #ECFDF5; color: #065F46; border-bottom-color: #A7F3D0; }

.kanban-count {
    background: rgba(255, 255, 255, 0.9);
    padding: 0.25rem 0.75rem;
    border-radius: 0.75rem;
    font-size: 0.85rem;
    font-weight: 800;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.kanban-body {
    padding: 1rem;
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* Ticket Cards */
.ticket-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    border: 1px solid #E2E8F0;
    position: relative;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.ticket-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.ticket-priority-indicator {
    position: absolute;
    top: 0; left: 0; bottom: 0;
    width: 6px;
    border-radius: 1rem 0 0 1rem;
}
.priority-3 { background: #EF4444; }
.priority-2 { background: #F59E0B; }
.priority-1 { background: #3B82F6; }

.ticket-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px dashed #E2E8F0;
}

.ticket-number { font-weight: 900; color: #0F172A; font-size: 1.1rem; }
.ticket-time { font-size: 0.75rem; color: #64748B; font-weight: 700; display: flex; align-items: center; gap: 0.35rem; }

.ticket-items {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.item-row {
    display: flex;
    gap: 1rem;
    font-size: 0.95rem;
    align-items: center;
}

.item-qty {
    font-weight: 800;
    color: #EA580C;
    background: #FFEDD5;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
    flex-shrink: 0;
}

.item-name {
    font-weight: 700;
    color: #1E293B;
}

/* Ticket meta info (tipe, sumber) */
.ticket-meta {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}
.meta-chip {
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.2rem 0.6rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.chip-dine  { background: #EFF6FF; color: #1E40AF; }
.chip-take  { background: #F0FDF4; color: #166534; }
.chip-online { background: #FDF4FF; color: #7E22CE; }
.chip-pos   { background: #FFF7ED; color: #9A3412; }

.ticket-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

/* Baris tombol sekunder (cek stok + batalkan) */
.ticket-actions-secondary {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}

.btn-ticket {
    padding: 0.75rem;
    border: none;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 800;
    cursor: pointer;
    transition: 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.02em;
}

.btn-process { background: #EA580C; color: white; box-shadow: 0 4px 6px -1px rgba(234, 88, 12, 0.2); }
.btn-process:hover { background: #C2410C; transform: scale(1.02); }

.btn-ready { background: #10B981; color: white; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); }
.btn-ready:hover { background: #059669; transform: scale(1.02); }

.btn-finish { background: #3B82F6; color: white; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2); }
.btn-finish:hover { background: #2563EB; transform: scale(1.02); }

/* Tombol Cek Stok */
.btn-check-stock {
    background: #F0F9FF;
    color: #0369A1;
    border: 1.5px solid #BAE6FD;
    font-size: 0.8rem;
    padding: 0.75rem 0.5rem;
    font-weight: 800;
    letter-spacing: 0.01em;
}
.btn-check-stock:hover {
    background: #0369A1;
    color: white;
    border-color: #0369A1;
    transform: scale(1.02);
}

/* Tombol Batalkan (di kolom pending) */
.btn-cancel-sm {
    background: #FEF2F2;
    color: #DC2626;
    border: 1.5px solid #FECACA;
    font-size: 0.8rem;
    padding: 0.75rem 0.5rem;
    font-weight: 800;
    letter-spacing: 0.01em;
}
.btn-cancel-sm:hover {
    background: #DC2626;
    color: white;
    border-color: #DC2626;
    transform: scale(1.02);
}

/* Tombol Batalkan (di kolom diproses) */
.btn-cancel { background: transparent; color: #EF4444; border: 1.5px solid #FEE2E2; }
.btn-cancel:hover { background: #FEE2E2; }

/* Sidebar Premium */
.kitchen-sidebar {
    background: white;
    border-radius: 1.25rem;
    border: 1px solid #E2E8F0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
}

.widget-header {
    padding: 1.5rem;
    font-weight: 800;
    border-bottom: 1px solid #F1F5F9;
    background: #FAFAFA;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #0F172A;
}

.widget-body {
    padding: 1.5rem;
    flex: 1;
    overflow-y: auto;
}

.stock-alert {
    padding: 1rem;
    border-radius: 0.75rem;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    margin-bottom: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: 0.2s;
}

.stock-alert.empty { border-left: 4px solid #EF4444; background: #FEF2F2; }
.stock-alert.low { border-left: 4px solid #F59E0B; background: #FFFBEB; }

.stock-name { font-weight: 700; font-size: 0.9rem; color: #1E293B; }
.stock-val { font-size: 0.75rem; font-weight: 800; padding: 0.25rem 0.6rem; border-radius: 0.5rem; }
.stock-val.danger { background: #EF4444; color: white; }
.stock-val.warn { background: #F59E0B; color: white; }

.btn-sidebar-action {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
    padding: 1rem;
    background: #0F172A;
    color: white;
    border-radius: 1rem;
    text-decoration: none;
    font-weight: 800;
    font-size: 0.9rem;
    transition: 0.3s;
}
.btn-sidebar-action:hover { background: #1E293B; transform: translateY(-2px); }

/* Modal */
.modal-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,0.4); backdrop-filter: blur(8px); z-index: 1000;
    display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.modal-overlay.active { opacity: 1; pointer-events: all; }
.modal-card { 
    background: white; width: 100%; max-width: 480px; border-radius: 1.5rem; padding: 2rem; 
    transform: scale(0.9) translateY(20px); transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15); 
}
.modal-overlay.active .modal-card { transform: scale(1) translateY(0); }

.form-select, .form-textarea {
    width: 100%; padding: 0.875rem 1rem; border: 1.5px solid #E2E8F0; border-radius: 0.75rem; font-family: inherit; font-size: 0.95rem; transition: 0.2s;
}
.form-select:focus, .form-textarea:focus { outline: none; border-color: #EA580C; box-shadow: 0 0 0 4px #FFEDD5; }

/* Stock check result */
.stock-result-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}
.stock-result-ok   { background: #F0FDF4; border: 1px solid #BBF7D0; }
.stock-result-fail { background: #FEF2F2; border: 1px solid #FECACA; }
</style>

<div class="kds-layout">

    <!-- Kanban Board -->
    <div class="kanban-board">

        {{-- ── Kolom 1: PESANAN MASUK (pending) ─────────────────────────── --}}
        <div class="kanban-col">
            <div class="kanban-header kanban-header-menunggu">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    PESANAN MASUK
                </div>
                <span class="kanban-count">{{ count($queues['pending'] ?? []) }}</span>
            </div>
            <div class="kanban-body">
                @foreach($queues['pending'] ?? [] as $q)
                <div class="ticket-card">
                    <div class="ticket-priority-indicator priority-{{ $q->priority ?? 1 }}"></div>

                    <div class="ticket-header">
                        <div class="ticket-number">#{{ $q->order->order_number }}</div>
                        <div class="ticket-time">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            {{ $q->created_at->diffForHumans() }}
                        </div>
                    </div>

                    {{-- Tipe & Sumber --}}
                    <div class="ticket-meta">
                        <span class="meta-chip {{ $q->order->order_type === 'dine_in' ? 'chip-dine' : 'chip-take' }}">
                            @if($q->order->order_type === 'dine_in')
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/></svg>
                                Makan di Tempat
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                Bungkus
                            @endif
                        </span>
                        <span class="meta-chip {{ $q->order->source === 'online' ? 'chip-online' : 'chip-pos' }}">
                            @if($q->order->source === 'online')
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                                Online
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                Kasir
                            @endif
                        </span>
                    </div>

                    <div class="ticket-items">
                        @foreach($q->order->items as $item)
                        <div class="item-row">
                            <span class="item-qty">{{ $item->quantity }}</span>
                            <span class="item-name">{{ $item->name }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="ticket-actions">
                        {{-- Tombol utama: Mulai Masak --}}
                        <form action="{{ route('internal.kitchen.update-status', $q->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="preparing">
                            <button class="btn-ticket btn-process" style="width: 100%;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v2"/><path d="M12 18v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                                MULAI MASAK
                            </button>
                        </form>

                        {{-- Divider --}}
                        <div style="display:flex; align-items:center; gap:0.5rem; margin: 0.125rem 0;">
                            <div style="flex:1; height:1px; background:#F1F5F9;"></div>
                            <span style="font-size:0.65rem; color:#CBD5E1; font-weight:700; letter-spacing:0.05em; white-space:nowrap;">ATAU</span>
                            <div style="flex:1; height:1px; background:#F1F5F9;"></div>
                        </div>

                        {{-- Tombol sekunder: Cek Stok + Batalkan --}}
                        <div class="ticket-actions-secondary">
                            {{-- CEK STOK --}}
                            <button
                                class="btn-ticket btn-check-stock"
                                onclick="checkStock({{ $q->id }}, this)"
                                title="Cek apakah bahan mencukupi untuk pesanan ini">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                                CEK STOK
                            </button>
                            {{-- BATALKAN --}}
                            <button
                                class="btn-ticket btn-cancel-sm"
                                onclick="openCancelModal({{ $q->id }})"
                                title="Batalkan pesanan ini">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                BATALKAN
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach

                @if(empty($queues['pending']))
                <div style="text-align:center; color:#94A3B8; padding:3rem 0; font-size:0.875rem; font-weight:600;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 1rem; color:#E2E8F0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Tidak ada pesanan masuk
                </div>
                @endif
            </div>
        </div>

        {{-- ── Kolom 2: SEDANG DIMASAK (preparing + cooking) ─────────────── --}}
        <div class="kanban-col">
            <div class="kanban-header kanban-header-diproses">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v2"/><path d="M12 18v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                    SEDANG DIMASAK
                </div>
                <span class="kanban-count">{{ count($queues['preparing'] ?? []) + count($queues['cooking'] ?? []) }}</span>
            </div>
            <div class="kanban-body">
                @php $activeQueues = collect($queues['preparing'] ?? [])->merge($queues['cooking'] ?? []); @endphp
                @foreach($activeQueues as $q)
                <div class="ticket-card">
                    <div class="ticket-priority-indicator priority-{{ $q->priority ?? 1 }}"></div>
                    <div class="ticket-header">
                        <div class="ticket-number">#{{ $q->order->order_number }}</div>
                        <div class="ticket-time" style="color: #3B82F6;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            AKTIF {{ $q->started_at ? $q->started_at->diffForHumans() : 'BARU SAJA' }}
                        </div>
                    </div>
                    <div class="ticket-items">
                        @foreach($q->order->items as $item)
                        <div class="item-row">
                            <span class="item-qty">{{ $item->quantity }}</span>
                            <span class="item-name">{{ $item->name }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="ticket-actions">
                        <form action="{{ route('internal.kitchen.update-status', $q->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="ready">
                            <button class="btn-ticket btn-ready" style="width: 100%;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                SELESAI DIMASAK
                            </button>
                        </form>
                        <button type="button" class="btn-ticket btn-cancel" onclick="openCancelModal({{ $q->id }})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            BATALKAN
                        </button>
                    </div>
                </div>
                @endforeach

                @if($activeQueues->isEmpty())
                <div style="text-align:center; color:#94A3B8; padding:3rem 0; font-size:0.875rem; font-weight:600;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 1rem; color:#E2E8F0;"><path d="M12 2v2"/><circle cx="12" cy="12" r="8"/></svg>
                    Dapur sedang kosong
                </div>
                @endif
            </div>
        </div>

        {{-- ── Kolom 3: SIAP DISAJIKAN (ready) ────────────────────────────── --}}
        <div class="kanban-col">
            <div class="kanban-header kanban-header-siap">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                    SIAP DISAJIKAN
                </div>
                <span class="kanban-count">{{ count($queues['ready'] ?? []) }}</span>
            </div>
            <div class="kanban-body">
                @foreach($queues['ready'] ?? [] as $q)
                <div class="ticket-card" style="background: #F0FDF4;">
                    <div class="ticket-priority-indicator" style="background: #10B981;"></div>
                    <div class="ticket-header">
                        <div class="ticket-number">#{{ $q->order->order_number }}</div>
                        <div class="ticket-time" style="color: #059669;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            MENUNGGU PELANGGAN
                        </div>
                    </div>
                    <div class="ticket-items">
                        @foreach($q->order->items as $item)
                        <div class="item-row">
                            <span class="item-qty" style="background: #D1FAE5; color: #065F46;">{{ $item->quantity }}</span>
                            <span class="item-name">{{ $item->name }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="ticket-actions">
                        <form action="{{ route('internal.kitchen.update-status', $q->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="delivered">
                            <button class="btn-ticket btn-finish" style="width: 100%;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                                SUDAH DIAMBIL
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach

                @if(empty($queues['ready']))
                <div style="text-align:center; color:#94A3B8; padding:3rem 0; font-size:0.875rem; font-weight:600;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 1rem; color:#E2E8F0;"><path d="M20 6 9 17l-5-5"/></svg>
                    Belum ada pesanan siap
                </div>
                @endif
            </div>
        </div>

    </div>{{-- end kanban-board --}}

    <!-- ── Sidebar Widget ────────────────────────────────────────────────── -->
    <div class="kitchen-sidebar">
        <div class="widget-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #EF4444;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>
            Peringatan Stok
        </div>
        <div class="widget-body">
            @if(count($emptyStockItems) == 0 && count($lowStockItems) == 0)
                <div style="text-align: center; color: #94A3B8; padding: 3rem 0; font-size: 0.875rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 1rem; color: #E2E8F0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Semua stok aman
                </div>
            @endif

            @foreach($emptyStockItems as $item)
                <div class="stock-alert empty">
                    <div class="stock-name">{{ $item->name }}</div>
                    <div class="stock-val danger">HABIS</div>
                </div>
            @endforeach

            @foreach($lowStockItems as $item)
                <div class="stock-alert low">
                    <div class="stock-name">{{ $item->name }}</div>
                    <div class="stock-val warn">{{ $item->stock }} {{ $item->unit }}</div>
                </div>
            @endforeach

            <a href="{{ route('internal.kitchen.menus') }}" class="btn-sidebar-action">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                KELOLA MENU
            </a>
        </div>
    </div>

</div>{{-- end kds-layout --}}

{{-- ── Modal: Batalkan Pesanan ──────────────────────────────────────────── --}}
<div class="modal-overlay" id="cancelModal">
    <div class="modal-card">
        <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; color: #0F172A;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #EF4444;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            Batalkan Pesanan
        </h3>
        <form id="cancelForm" method="POST">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="cancelled">

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.625rem; color: #475569;">Alasan Pembatalan</label>
                <select name="cancellation_reason" class="form-select" required>
                    <option value="">Pilih alasan...</option>
                    <option value="stock_habis">Stok Habis</option>
                    <option value="bahan_tidak_cukup">Bahan Tidak Cukup</option>
                    <option value="kitchen_overload">Terlalu Banyak Pesanan</option>
                    <option value="bahan_expired">Bahan Expired</option>
                    <option value="pelanggan_cancel">Pelanggan Membatalkan</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.625rem; color: #475569;">Catatan (Opsional)</label>
                <textarea name="cancellation_notes" class="form-textarea" rows="3" placeholder="Tulis catatan tambahan jika perlu..."></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <button type="button" onclick="closeCancelModal()" style="padding: 1rem; border: 1.5px solid #E2E8F0; border-radius: 1rem; background: white; cursor: pointer; font-weight: 800; color: #64748B;">KEMBALI</button>
                <button type="submit" style="padding: 1rem; border: none; border-radius: 1rem; background: #EF4444; color: white; cursor: pointer; font-weight: 800; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);">KONFIRMASI BATAL</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal: Hasil Cek Stok ────────────────────────────────────────────── --}}
<div class="modal-overlay" id="stockModal">
    <div class="modal-card">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem;">
            <h3 style="font-size:1.25rem; font-weight:800; color:#0F172A; display:flex; align-items:center; gap:0.75rem; margin:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0369A1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                Pengecekan Stok
            </h3>
            <button onclick="closeStockModal()" style="background:#F1F5F9; border:none; width:32px; height:32px; border-radius:50%; cursor:pointer; font-size:1.2rem; display:flex; align-items:center; justify-content:center; color:#64748B;">&times;</button>
        </div>

        {{-- Loading state --}}
        <div id="stockLoading" style="text-align:center; padding:2rem;">
            <div style="width:40px; height:40px; border:3px solid #E2E8F0; border-top-color:#0369A1; border-radius:50%; animation:spin 0.8s linear infinite; margin:0 auto 1rem;"></div>
            <p style="color:#64748B; font-weight:600; font-size:0.9rem;">Mengecek ketersediaan bahan...</p>
        </div>

        {{-- Result --}}
        <div id="stockResult" style="display:none;">
            <div id="stockSummary" style="padding:1rem; border-radius:0.875rem; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.75rem; font-weight:700;"></div>
            <div id="stockItems"></div>
            <button onclick="closeStockModal()" style="width:100%; margin-top:1.5rem; padding:0.875rem; background:#0F172A; color:white; border:none; border-radius:1rem; font-weight:800; cursor:pointer;">TUTUP</button>
        </div>
    </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<script>
// ── Cancel Modal ──────────────────────────────────────────────────────────────
function openCancelModal(id) {
    document.getElementById('cancelForm').action = `/internal/kitchen/${id}/status`;
    document.getElementById('cancelModal').classList.add('active');
}
function closeCancelModal() {
    document.getElementById('cancelModal').classList.remove('active');
}

// ── Stock Check Modal ─────────────────────────────────────────────────────────
async function checkStock(queueId, btn) {
    // Buka modal & tunjukkan loading
    document.getElementById('stockLoading').style.display = 'block';
    document.getElementById('stockResult').style.display  = 'none';
    document.getElementById('stockModal').classList.add('active');

    // Disable tombol sementara
    btn.disabled = true;
    const origText = btn.innerHTML;
    btn.innerHTML = '<svg style="animation:spin 0.8s linear infinite" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> MENGECEK...';

    try {
        const res  = await fetch(`/internal/kitchen/${queueId}/check-stock`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await res.json();

        // Render hasil
        const summary = document.getElementById('stockSummary');
        const items   = document.getElementById('stockItems');

        if (data.sufficient) {
            summary.style.background = '#F0FDF4';
            summary.style.color      = '#166534';
            summary.style.border     = '1px solid #BBF7D0';
            summary.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Stok mencukupi — pesanan bisa diproses!`;
            items.innerHTML = '';
        } else {
            summary.style.background = '#FEF2F2';
            summary.style.color      = '#991B1B';
            summary.style.border     = '1px solid #FECACA';
            summary.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                Stok tidak mencukupi — ${data.issues.length} bahan kurang!`;

            items.innerHTML = data.issues.map(issue => `
                <div class="stock-result-item stock-result-fail">
                    <div>
                        <div style="font-weight:700; color:#1E293B; font-size:0.9rem;">${issue.item}</div>
                        <div style="font-size:0.75rem; color:#64748B; margin-top:0.2rem;">Dibutuhkan: <b>${issue.required} ${issue.unit ?? ''}</b> &bull; Tersedia: <b>${issue.available} ${issue.unit ?? ''}</b></div>
                    </div>
                    <span style="background:#EF4444; color:white; font-size:0.7rem; font-weight:800; padding:0.2rem 0.6rem; border-radius:0.5rem;">KURANG</span>
                </div>
            `).join('');
        }

        document.getElementById('stockLoading').style.display = 'none';
        document.getElementById('stockResult').style.display  = 'block';

    } catch (e) {
        document.getElementById('stockLoading').innerHTML = `
            <p style="color:#EF4444; font-weight:700;">Gagal mengecek stok: ${e.message}</p>
            <button onclick="closeStockModal()" style="margin-top:1rem; padding:0.75rem 1.5rem; background:#0F172A; color:white; border:none; border-radius:0.75rem; font-weight:800; cursor:pointer;">TUTUP</button>`;
    } finally {
        btn.disabled  = false;
        btn.innerHTML = origText;
    }
}

function closeStockModal() {
    document.getElementById('stockModal').classList.remove('active');
}

// Auto-reload setiap 60 detik
setTimeout(() => { location.reload(); }, 60000);
</script>
@endsection