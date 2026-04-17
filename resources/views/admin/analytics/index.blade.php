@extends('layouts.admin')
@section('title', 'Analytics — ' . $survey->title)
@section('topbar-actions')
  <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-secondary btn-sm">← Survey</a>
@endsection
@section('content')

{{-- Summary row --}}
<div class="stats-row" style="margin-bottom:24px">
  <div class="stat-card"><div class="stat-label">Total Responses</div><div class="stat-value">{{ $totalResponses }}</div></div>
  <div class="stat-card"><div class="stat-label">Questions Analysed</div><div class="stat-value">{{ count($charts) }}</div></div>
  <div class="stat-card"><div class="stat-label">Survey Status</div><div class="stat-value" style="font-size:16px;padding-top:6px"><span class="badge badge-{{ $survey->status }}">{{ ucfirst($survey->status) }}</span></div></div>
</div>

{{-- Response Timeline --}}
@if(count($timeline))
<div class="card" style="margin-bottom:24px">
  <div class="card-header"><span class="card-title">Response Timeline</span></div>
  <canvas id="chart-timeline" height="80"></canvas>
</div>
@endif

{{-- Question Charts --}}
@php $statCards = collect($charts)->where('type','stat'); $barCharts = collect($charts)->where('type','bar'); @endphp

{{-- Numeric stat cards --}}
@if($statCards->isNotEmpty())
<div style="margin-bottom:24px">
  <h3 style="font-family:'Playfair Display',serif;font-size:16px;color:#550D0E;margin-bottom:14px">Numeric Summary</h3>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px">
    @foreach($statCards as $c)
    <div class="stat-card" style="border-left:3px solid #C9A84C">
      <div class="stat-label" style="font-size:10px;margin-bottom:6px">{{ $c['code'] }}</div>
      <div style="font-size:13px;font-weight:600;color:#222;margin-bottom:10px;line-height:1.3">{{ $c['label'] }}</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;text-align:center;margin-bottom:6px">
        <div style="background:#fdf6e8;border:1px solid #e2c47a;border-radius:5px;padding:8px 4px">
          <div style="font-size:9px;color:#7a5c00;text-transform:uppercase;letter-spacing:.05em">Median</div>
          <div style="font-size:18px;font-weight:700;color:#550D0E">{{ $c['median'] }}</div>
        </div>
        <div style="background:#f8f8f8;border-radius:5px;padding:8px 4px">
          <div style="font-size:9px;color:#999;text-transform:uppercase;letter-spacing:.05em">Mean</div>
          <div style="font-size:18px;font-weight:700;color:#444">{{ $c['avg'] }}</div>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;text-align:center">
        <div style="background:#f8f8f8;border-radius:5px;padding:6px 4px">
          <div style="font-size:9px;color:#999;text-transform:uppercase;letter-spacing:.05em">Min</div>
          <div style="font-size:14px;font-weight:700;color:#444">{{ $c['min'] }}</div>
        </div>
        <div style="background:#f8f8f8;border-radius:5px;padding:6px 4px">
          <div style="font-size:9px;color:#999;text-transform:uppercase;letter-spacing:.05em">Max</div>
          <div style="font-size:14px;font-weight:700;color:#444">{{ $c['max'] }}</div>
        </div>
      </div>
      <div style="font-size:11px;color:#aaa;margin-top:8px;text-align:right">n = {{ $c['n'] }}</div>
    </div>
    @endforeach
  </div>
</div>
@endif

{{-- Categorical bar charts --}}
@if($barCharts->isNotEmpty())
<h3 style="font-family:'Playfair Display',serif;font-size:16px;color:#550D0E;margin-bottom:14px">Response Distributions</h3>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(420px,1fr));gap:20px">
  @foreach($barCharts as $i => $c)
  <div class="card">
    <div class="card-header" style="margin-bottom:10px">
      <div>
        <div style="font-size:10px;font-family:monospace;color:#C9A84C;margin-bottom:2px">{{ $c['code'] }}</div>
        <span class="card-title" style="font-size:14px">{{ $c['label'] }}</span>
      </div>
      <span style="font-size:12px;color:#888">n = {{ $c['total'] }}</span>
    </div>
    <canvas id="chart-{{ $i }}" height="140"></canvas>
  </div>
  @endforeach
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const MAROON = '#7B1213', GOLD = '#C9A84C', COLORS = [
  '#7B1213','#C9A84C','#9B2224','#E2C47A','#550D0E',
  '#D4956A','#4A7C59','#6B8CAE','#8B6BA8','#C9745A'
];

// Timeline
@if(count($timeline))
new Chart(document.getElementById('chart-timeline'), {
  type: 'line',
  data: {
    labels: {!! json_encode(array_keys($timeline)) !!},
    datasets: [{
      label: 'Responses',
      data: {!! json_encode(array_values($timeline)) !!},
      borderColor: MAROON,
      backgroundColor: 'rgba(123,18,19,.08)',
      borderWidth: 2,
      fill: true,
      tension: 0.3,
      pointRadius: 3,
      pointBackgroundColor: MAROON,
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false }, ticks: { font: { size: 11 } } },
      y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } } }
    }
  }
});
@endif

// Bar charts
@foreach($barCharts as $i => $c)
new Chart(document.getElementById('chart-{{ $i }}'), {
  type: 'bar',
  data: {
    labels: {!! json_encode($c['labels']) !!},
    datasets: [{
      data: {!! json_encode($c['data']) !!},
      backgroundColor: COLORS.slice(0, {!! count($c['data']) !!}),
      borderRadius: 4,
      borderSkipped: false,
    }]
  },
  options: {
    indexAxis: 'y',
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => {
            const total = {!! $c['total'] !!};
            const pct = total > 0 ? ((ctx.parsed.x / total) * 100).toFixed(1) : 0;
            return ` ${ctx.parsed.x} (${pct}%)`;
          }
        }
      }
    },
    scales: {
      x: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } } },
      y: { ticks: { font: { size: 11 } } }
    }
  }
});
@endforeach
</script>
@endsection
