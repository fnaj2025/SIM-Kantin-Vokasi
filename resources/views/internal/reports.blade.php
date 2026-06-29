@extends('layouts.internal')
@section('title', 'Laporan & Analitik')

@section('content')
<style>
/* ── Reports Premium Style ── */
.report-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
.report-card { 
    background: white; border-radius: 1.5rem; padding: 2rem; 
    border: 1px solid #E2E8F0; position: relative; overflow: hidden; 
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); transition: 0.3s;
}
.report-card:hover { transform: translateY(-4px); box-shadow: 0 12px 20px -5px rgba(0,0,0,0.05); }

.report-icon-bg { 
    position: absolute; right: -1rem; bottom: -1rem; opacity: 0.05; 
    width: 120px; height: 120px; color: #0F172A; 
}
.report-label { font-size: 0.85rem; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
.report-val { font-size: 2.25rem; font-weight: 900; color: #0F172A; letter-spacing: -0.02em; }

.panel-premium { 
    background: #FFF; border-radius: 1.5rem; border: 1px solid #E2E8F0; 
    padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); 
}
.panel-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
.panel-title { font-size: 1.25rem; font-weight: 900; color: #0F172A; display: flex; align-items: center; gap: 0.75rem; }

.ai-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
@media(max-width: 900px) { .ai-grid { grid-template-columns: 1fr; } }

.ai-insight-box { 
    background: #F8FAFC; border-radius: 1.25rem; padding: 1.5rem; 
    border: 1.5px solid #E2E8F0; transition: 0.2s;
}
.ai-insight-box:hover { border-color: #EA580C; background: white; }

.ai-title { 
    font-size: 1rem; font-weight: 800; color: #0F172A; margin-bottom: 1.25rem; 
    display: flex; align-items: center; gap: 0.6rem; 
}
.ai-list { display: flex; flex-direction: column; gap: 1rem; }
.ai-item { display: flex; gap: 0.75rem; align-items: flex-start; }
.ai-dot { 
    width: 24px; height: 24px; border-radius: 50%; display: flex; 
    align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;
}
.ai-text { font-size: 0.9rem; font-weight: 500; color: #475569; line-height: 1.5; }

/* Chart Mockup Bar */
.chart-container { 
    display: flex; align-items: flex-end; gap: 0.75rem; height: 240px; 
    margin-top: 1rem; padding-bottom: 2rem; border-bottom: 1px dashed #E2E8F0; 
    position: relative; 
}
.bar-wrapper { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.75rem; position: relative; }
.bar-fill { 
    width: 100%; max-width: 44px; background: #E2E8F0; border-radius: 0.75rem 0.75rem 0 0; 
    min-height: 8px; transition: 1s cubic-bezier(0.4, 0, 0.2, 1); position: relative;
}
.bar-fill.active { background: linear-gradient(180deg, #EA580C, #C2410C); box-shadow: 0 4px 12px rgba(234, 88, 12, 0.2); }
.bar-label { position: absolute; bottom: -1.75rem; font-size: 0.75rem; font-weight: 700; color: #94A3B8; }
.bar-value { font-size: 0.8rem; font-weight: 800; color: #0F172A; }
</style>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; flex-wrap:wrap; gap:1rem">
    <div>
        <h1 style="font-size:1.75rem; font-weight:900; letter-spacing:-0.03em; color:#0F172A">Laporan & Analitik</h1>
        <p style="font-size:0.95rem; color:#64748B; font-weight:500">Performa bisnis dan rekomendasi Agentic AI</p>
    </div>
    <button onclick="window.print()" style="background:white; border:1.5px solid #E2E8F0; color:#0F172A; padding:0.875rem 1.5rem; border-radius:1rem; font-weight:800; font-size:0.9rem; cursor:pointer; display:flex; align-items:center; gap:0.6rem">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        CETAK LAPORAN
    </button>
</div>

<div class="report-stats">
    <div class="report-card">
        <svg class="report-icon-bg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="22"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        <div class="report-label">Pendapatan Kotor</div>
        <div class="report-val">Rp {{ number_format($totalRevenue,0,',','.') }}</div>
    </div>
    <div class="report-card">
        <svg class="report-icon-bg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
        <div class="report-label">Total Pesanan</div>
        <div class="report-val">{{ $totalOrders }}</div>
    </div>
    <div class="report-card">
        <svg class="report-icon-bg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <div class="report-label">Selesai</div>
        <div class="report-val">{{ $completedOrders }}</div>
    </div>
</div>

<div class="panel-premium">
    <div class="panel-header">
        <div class="panel-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#EA580C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8V4H8"></path><rect width="16" height="12" x="4" y="8" rx="2"></rect><path d="M2 14h2"></path><path d="M20 14h2"></path><path d="M15 13v2"></path><path d="M9 13v2"></path></svg>
            Wawasan Agentic AI
        </div>
        <span style="font-size:0.8rem; font-weight:800; color:#EA580C; background:#FFF7ED; padding:0.4rem 0.8rem; border-radius:0.75rem">LIVE ANALYSIS</span>
    </div>
    
    <div class="ai-grid">
        {{-- Inventory AI --}}
        <div class="ai-insight-box">
            <div class="ai-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg>
                Analisis Inventori
            </div>
            <div class="ai-list">
                @php $invAgent = (new \App\Services\Agents\InventoryIntelligenceAgent())->analyze(); @endphp
                @forelse($invAgent['alerts'] as $alert)
                    <div class="ai-item">
                        <div class="ai-dot" style="background:#FEE2E2; color:#EF4444">!</div>
                        <div class="ai-text">{{ $alert['message'] }}</div>
                    </div>
                @empty
                    <div class="ai-item">
                        <div class="ai-dot" style="background:#ECFDF5; color:#10B981">✓</div>
                        <div class="ai-text">Stok bahan baku terpantau aman dan stabil.</div>
                    </div>
                @endforelse
                @foreach($invAgent['recommendations'] as $rec)
                    <div class="ai-item">
                        <div class="ai-dot" style="background:#EFF6FF; color:#3B82F6">i</div>
                        <div class="ai-text">{{ $rec }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Kitchen AI --}}
        <div class="ai-insight-box">
            <div class="ai-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v2"/><path d="M12 18v2"/><path d="M4.93 4.93l1.41 1.41"/><path d="M17.66 17.66l1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="M6.34 17.66l-1.41 1.41"/><path d="M19.07 4.93l-1.41 1.41"/></svg>
                Optimalisasi Dapur
            </div>
            <div class="ai-list">
                @php $kitAgent = (new \App\Services\Agents\KitchenWorkflowAgent())->analyze(); @endphp
                @forelse($kitAgent['alerts'] as $alert)
                    <div class="ai-item">
                        <div class="ai-dot" style="background:#FEE2E2; color:#EF4444">!</div>
                        <div class="ai-text">{{ $alert['message'] }}</div>
                    </div>
                @empty
                    <div class="ai-item">
                        <div class="ai-dot" style="background:#ECFDF5; color:#10B981">✓</div>
                        <div class="ai-text">Workflow dapur berjalan optimal tanpa kendala.</div>
                    </div>
                @endforelse
                @foreach($kitAgent['recommendations'] as $rec)
                    <div class="ai-item">
                        <div class="ai-dot" style="background:#EFF6FF; color:#3B82F6">i</div>
                        <div class="ai-text">{{ $rec }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        
        {{-- Finance AI --}}
        <div class="ai-insight-box">
            <div class="ai-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                Efisiensi Keuangan
            </div>
            <div class="ai-list">
                @php $finAgent = (new \App\Services\Agents\FinancialMonitoringAgent())->analyze(); @endphp
                @forelse($finAgent['alerts'] as $alert)
                    <div class="ai-item">
                        <div class="ai-dot" style="background:#FFF7ED; color:#EA580C">!</div>
                        <div class="ai-text">{{ $alert['message'] }}</div>
                    </div>
                @empty
                    <div class="ai-item">
                        <div class="ai-dot" style="background:#ECFDF5; color:#10B981">✓</div>
                        <div class="ai-text">Arus kas operasional terpantau sehat.</div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Trends AI --}}
        <div class="ai-insight-box">
            <div class="ai-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 7L13.5 15.5L8.5 10.5L2 17"></path><polyline points="16 7 22 7 22 13"></polyline></svg>
                Tren & Operasional
            </div>
            <div class="ai-list">
                @php $opsAgent = (new \App\Services\Agents\OperationalAnalyticsAgent())->analyze(); @endphp
                @forelse($opsAgent['insights'] as $ins)
                    <div class="ai-item">
                        <div class="ai-dot" style="background:#EFF6FF; color:#3B82F6">i</div>
                        <div class="ai-text">{{ $ins['message'] }}</div>
                    </div>
                @empty
                    <div class="ai-item">
                        <div class="ai-dot" style="background:#F1F5F9; color:#94A3B8">...</div>
                        <div class="ai-text">Menunggu data operasional lebih lanjut untuk analisis tren.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@if(!empty($opsAgent['hourly_data']))
<div class="panel-premium">
    <div class="panel-title" style="margin-bottom:2rem">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#EA580C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
        Distribusi Pesanan Per Jam
    </div>
    @php $maxVal = max(array_column($opsAgent['hourly_data'], 'count') ?: [1]); @endphp
    <div class="chart-container">
        @foreach($opsAgent['hourly_data'] as $point)
        @php $h = $maxVal > 0 ? max(10, round($point['count'] / $maxVal * 180)) : 10; @endphp
        <div class="bar-wrapper">
            <div class="bar-value" style="opacity:{{ $point['count']>0?1:0 }}">{{ $point['count'] }}</div>
            <div class="bar-fill {{ $point['count']==$maxVal && $maxVal>0 ? 'active' : '' }}" style="height:{{ $h }}px"></div>
            <div class="bar-label">{{ $point['hour'] }}</div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
