@extends('layouts.internal')
@section('title', 'Manajemen Pesanan')

@push('styles')
<style>
.detail-panel {
    position: fixed; top: 0; right: 0;
    width: 420px; height: 100vh;
    background: var(--bg-card);
    border-left: 1px solid var(--border);
    box-shadow: -8px 0 32px rgba(0,0,0,0.08);
    z-index: 200;
    display: flex; flex-direction: column;
    transform: translateX(100%);
    transition: transform 0.3s cubic-bezier(0.16,1,0.3,1);
    overflow: hidden;
}
.detail-panel.open { transform: translateX(0); }

.detail-overlay {
    position: fixed; inset: 0;
    background: rgba(15,23,42,0.25);
    z-index: 199; backdrop-filter: blur(2px);
    opacity: 0; pointer-events: none; transition: opacity 0.25s;
}
.detail-overlay.open { opacity: 1; pointer-events: all; }

.detail-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-light);
    display: flex; align-items: center; justify-content: space-between;
    background: #FAFAFA; flex-shrink: 0;
}
.detail-body { flex: 1; overflow-y: auto; padding: 1.5rem; }
.detail-body::-webkit-scrollbar { width: 4px; }
.detail-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

.detail-section { margin-bottom: 1.5rem; }
.detail-section-title {
    font-size: 0.65rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-faint); margin-bottom: 0.75rem;
    padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-light);
}
.detail-row {
    display: flex; justify-content: space-between;
    align-items: baseline; margin-bottom: 0.625rem; font-size: 0.875rem;
}
.detail-label { color: var(--text-muted); font-weight: 500; }
.detail-value { font-weight: 700; color: var(--text-dark); text-align: right; max-width: 60%; }

.detail-item-row {
    display: flex; align-items: center; gap: 0.875rem;
    padding: 0.875rem 0; border-bottom: 1px solid var(--border-light);
}
.detail-item-row:last-child { border-bottom: none; }
.detail-item-icon {
    width: 36px; height: 36px; background: var(--primary-light);
    border-radius: var(--radius-sm); display: flex; align-items: center;
    justify-content: center; flex-shrink: 0; color: var(--primary);
    font-weight: 900; font-size: 0.8rem;
}

/* status pill tanpa emoji */
.status-pill {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.25rem 0.7rem; border-radius: var(--radius-full);
    font-size: 0.7rem; font-weight: 700; white-space: nowrap;
}
.status-pill.menunggu   { background: var(--warning-bg);  color: var(--warning-text); }
.status-pill.diproses   { background: var(--info-bg);     color: var(--info-text); }
.status-pill.siap       { background: var(--success-bg);  color: var(--success-text); }
.status-pill.selesai    { background: var(--bg-subtle);   color: var(--text-muted); }
.status-pill.dibatalkan { background: var(--danger-bg);   color: var(--danger-text); }

.close-panel-btn {
    width: 30px; height: 30px; background: var(--bg-subtle);
    border: none; border-radius: 50%; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-muted); transition: 0.15s;
}
.close-panel-btn:hover { background: var(--border); color: var(--text-dark); }

.order-row:hover td { background: #FFF7F0 !important; }

/* select tanpa emoji */
.status-select {
    padding: 0.3rem 1.75rem 0.3rem 0.625rem;
    border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    font-family: inherit; font-size: 0.8rem; font-weight: 700;
    background: var(--bg-card); color: var(--text-dark);
    cursor: pointer; appearance: none; -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 0.4rem center;
    transition: border-color 0.15s;
}
.status-select:focus { outline: none; border-color: var(--primary); }

@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div x-data="ordersApp()">

{{-- Header --}}
<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.75rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 class="page-title">Manajemen Pesanan</h1>
        <p class="page-sub">Monitor dan kelola seluruh pesanan masuk secara realtime</p>
    </div>
    <div style="display:flex; align-items:center; gap:0.75rem;">
        <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-md); padding:0.5rem 0.875rem; font-size:0.8rem; font-weight:600; color:var(--text-muted); display:flex; align-items:center; gap:0.4rem;">
            <span style="width:7px;height:7px;background:var(--success);border-radius:50%;display:inline-block;"></span>
            Live Update Aktif
        </div>
        <span style="font-size:0.8rem;color:var(--text-faint);font-weight:600;">{{ $orders->total() }} pesanan</span>
    </div>
</div>

{{-- Table --}}
<div class="table-wrap">
    {{-- Toolbar --}}
    <div style="padding:0.875rem 1.5rem; border-bottom:1px solid var(--border-light); background:#FAFAFA; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;">
        <span class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary)"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Daftar Pesanan
        </span>
        <div style="display:flex; gap:0.4rem; flex-wrap:wrap;">
            <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}" class="badge {{ !request('status') ? 'badge-primary' : 'badge-muted' }}" style="cursor:pointer;text-decoration:none;padding:0.3rem 0.75rem;">Semua</a>
            @foreach(['menunggu'=>'Menunggu','diproses'=>'Diproses','siap'=>'Siap','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'] as $val => $label)
            <a href="{{ request()->fullUrlWithQuery(['status' => $val]) }}" class="badge {{ request('status') === $val ? 'badge-primary' : 'badge-muted' }}" style="cursor:pointer;text-decoration:none;padding:0.3rem 0.75rem;">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Sumber</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="order-row" style="cursor:pointer;" @click="openDetail({{ $order->id }})">

                    <td>
                        <span style="font-weight:800;font-size:0.875rem;letter-spacing:-0.01em;">{{ $order->order_number }}</span>
                    </td>

                    <td>
                        <div style="font-weight:700;font-size:0.875rem;">{{ $order->customer_name }}</div>
                        @if($order->customer_phone)
                        <div style="font-size:0.7rem;color:var(--text-faint);">{{ $order->customer_phone }}</div>
                        @endif
                    </td>

                    {{-- Sumber: ganti emoji dengan badge + icon SVG --}}
                    <td>
                        @if($order->source === 'online')
                            <span class="badge badge-info" style="font-size:0.65rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                Online
                            </span>
                        @else
                            <span class="badge badge-muted" style="font-size:0.65rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                Kasir
                            </span>
                        @endif
                        <div style="font-size:0.7rem;color:var(--text-faint);margin-top:2px;">
                            {{ $order->order_type === 'dine_in' ? 'Makan di tempat' : 'Bawa pulang' }}
                        </div>
                    </td>

                    <td>
                        <span style="font-weight:800;">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        <div style="font-size:0.7rem;color:var(--text-faint);">{{ $order->items->count() }} item</div>
                    </td>

                    <td @click.stop>
                        @if($order->payment_status === 'sudah_bayar')
                            <span class="badge badge-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Lunas
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                Belum Bayar
                            </span>
                        @endif
                    </td>

                    {{-- Status select — tanpa emoji --}}
                    <td @click.stop>
                        <form action="{{ route('internal.orders.update', $order->id) }}" method="POST" id="sf-{{ $order->id }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="payment_status" value="{{ $order->payment_status }}">
                            <select name="status" class="status-select"
                                    onchange="document.getElementById('sf-{{ $order->id }}').submit()">
                                <option value="menunggu"   {{ $order->status==='menunggu'   ? 'selected' : '' }}>Menunggu</option>
                                <option value="diproses"   {{ $order->status==='diproses'   ? 'selected' : '' }}>Diproses</option>
                                <option value="siap"       {{ $order->status==='siap'       ? 'selected' : '' }}>Siap</option>
                                <option value="selesai"    {{ $order->status==='selesai'    ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ $order->status==='dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </form>
                    </td>

                    <td>
                        <span style="font-size:0.8rem;color:var(--text-muted);font-weight:600;">{{ $order->created_at->format('H:i') }}</span>
                        <div style="font-size:0.7rem;color:var(--text-faint);">{{ $order->created_at->format('d M Y') }}</div>
                    </td>

                    {{-- Aksi --}}
                    <td style="text-align:right;" @click.stop>
                        <div style="display:flex;gap:0.4rem;justify-content:flex-end;flex-wrap:wrap;align-items:center;">

                            {{-- Konfirmasi bayar --}}
                            @if($order->payment_status === 'belum_bayar')
                            <form action="{{ route('internal.orders.update', $order->id) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="{{ $order->status }}">
                                <input type="hidden" name="payment_status" value="sudah_bayar">
                                <button type="submit" class="btn btn-success btn-sm" title="Konfirmasi Pembayaran">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    Konfirmasi Bayar
                                </button>
                            </form>
                            @endif

                            {{-- Kirim dapur --}}
                            @if($order->status === 'menunggu')
                            <form action="{{ route('internal.orders.update', $order->id) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="diproses">
                                <input type="hidden" name="payment_status" value="{{ $order->payment_status }}">
                                <button type="submit" class="btn btn-sm" style="background:var(--info-bg);color:var(--info-text);border:1.5px solid #BFDBFE;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/></svg>
                                    Kirim Dapur
                                </button>
                            </form>
                            @endif

                            {{-- Detail --}}
                            <button @click.stop="openDetail({{ $order->id }})" class="btn btn-sm btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                Detail
                            </button>

                            {{-- Hapus — hanya untuk pesanan dibatalkan --}}
                            @if($order->status === 'dibatalkan')
                            <form action="{{ route('internal.orders.destroy', $order->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus pesanan {{ $order->order_number }}? Tindakan ini permanen.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                    Hapus
                                </button>
                            </form>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:5rem 2rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--border);margin:0 auto 1rem;display:block;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <div style="font-weight:700;color:var(--text-muted);">Belum ada pesanan</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid var(--border-light);background:#FAFAFA;">
        {{ $orders->links() }}
    </div>
    @endif
</div>

{{-- Overlay & Detail Panel --}}
<div class="detail-overlay" :class="{ open: open }" @click="close()"></div>

<div class="detail-panel" :class="{ open: open }">
    <div class="detail-header">
        <div>
            <div style="font-weight:900;font-size:1rem;color:var(--text-dark);" x-text="d.order_number || 'Detail Pesanan'"></div>
            <div style="font-size:0.75rem;color:var(--text-faint);margin-top:2px;" x-text="d.date || ''"></div>
        </div>
        <button class="close-panel-btn" @click="close()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
    </div>

    <div class="detail-body">
        <template x-if="loading">
            <div style="display:flex;align-items:center;justify-content:center;padding:4rem;color:var(--text-faint);">
                <div style="text-align:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite;margin:0 auto 0.75rem;display:block;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    <div style="font-size:0.875rem;font-weight:600;">Memuat...</div>
                </div>
            </div>
        </template>

        <template x-if="!loading && d.id">
            <div>
                {{-- Status badges --}}
                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1.5rem;">
                    <span class="status-pill" :class="d.status" x-text="statusLabel(d.status)"></span>
                    <span class="badge" :class="d.payment_status === 'sudah_bayar' ? 'badge-success' : 'badge-warning'"
                          x-text="d.payment_status === 'sudah_bayar' ? 'Lunas' : 'Belum Bayar'"></span>
                    <span class="badge badge-muted" x-text="d.source === 'online' ? 'Online' : 'Kasir'"></span>
                </div>

                {{-- Info umum --}}
                <div class="detail-section">
                    <div class="detail-section-title">Informasi Pesanan</div>
                    <div class="detail-row"><span class="detail-label">Pelanggan</span><span class="detail-value" x-text="d.customer_name"></span></div>
                    <div class="detail-row"><span class="detail-label">No. Telepon</span><span class="detail-value" x-text="d.customer_phone || '—'"></span></div>
                    <div class="detail-row"><span class="detail-label">Tipe Pesanan</span><span class="detail-value" x-text="d.order_type === 'dine_in' ? 'Makan di Tempat' : 'Bawa Pulang'"></span></div>
                    <div class="detail-row"><span class="detail-label">Metode Bayar</span><span class="detail-value" x-text="d.payment_method === 'tunai' ? 'Tunai' : 'QRIS'"></span></div>
                    <template x-if="d.notes">
                        <div class="detail-row">
                            <span class="detail-label">Catatan</span>
                            <span class="detail-value" style="color:var(--warning-text);" x-text="d.notes"></span>
                        </div>
                    </template>
                </div>

                {{-- Item pesanan --}}
                <div class="detail-section">
                    <div class="detail-section-title">Item Pesanan</div>
                    <template x-for="item in d.items" :key="item.id">
                        <div class="detail-item-row">
                            <div class="detail-item-icon" x-text="item.quantity + 'x'"></div>
                            <div style="flex:1;">
                                <div style="font-weight:700;font-size:0.875rem;" x-text="item.name"></div>
                                <div style="font-size:0.8rem;color:var(--text-muted);" x-text="'@ Rp ' + parseInt(item.price).toLocaleString('id-ID')"></div>
                                <template x-if="item.notes">
                                    <div style="font-size:0.75rem;color:var(--text-muted);font-style:italic;margin-top:2px;" x-text="'Catatan: ' + item.notes"></div>
                                </template>
                            </div>
                            <div style="font-weight:800;font-size:0.875rem;" x-text="'Rp ' + parseInt(item.subtotal).toLocaleString('id-ID')"></div>
                        </div>
                    </template>
                </div>

                {{-- Total --}}
                <div class="detail-section">
                    <div class="detail-section-title">Ringkasan Pembayaran</div>
                    <div class="detail-row">
                        <span class="detail-label">Subtotal</span>
                        <span class="detail-value" x-text="'Rp ' + parseInt(d.subtotal).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="detail-row" style="border-top:1.5px solid var(--border);padding-top:0.625rem;margin-top:0.25rem;">
                        <span style="font-weight:800;font-size:0.9rem;">Total</span>
                        <span style="font-weight:900;font-size:1rem;color:var(--primary);" x-text="'Rp ' + parseInt(d.total).toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

</div>{{-- end Alpine --}}

@push('scripts')
<script>
function ordersApp() {
    return {
        open: false, loading: false, d: {},

        async openDetail(id) {
            this.open    = true;
            this.loading = true;
            this.d       = {};
            try {
                const res = await fetch(`/internal/orders/${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                // Format tanggal untuk header panel
                data.date = new Date(data.created_at)
                    .toLocaleDateString('id-ID', { weekday:'long', day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit' });
                this.d = data;
            } catch(e) { console.error(e); }
            finally { this.loading = false; }
        },

        close() { this.open = false; this.d = {}; },

        statusLabel(s) {
            return { menunggu:'Menunggu', diproses:'Diproses', siap:'Siap Diambil', selesai:'Selesai', dibatalkan:'Dibatalkan' }[s] || s;
        },
    };
}

/* Pusher realtime */
const pusher = new Pusher('{{ config("broadcasting.connections.reverb.key") }}', {
    wsHost: window.location.hostname,
    wsPort: {{ config("broadcasting.connections.reverb.options.port", 8080) }},
    forceTLS: false, disableStats: true, cluster: 'mt1',
});
pusher.subscribe('admin-updates').bind('kitchen.status.updated', () => location.reload());
</script>
@endpush

@endsection