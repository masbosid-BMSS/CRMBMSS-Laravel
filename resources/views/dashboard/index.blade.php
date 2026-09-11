<x-app-layout>
  <div class="ph">
    <div>
      <h1 class="pt">Assalamu'alaikum, {{ auth()->user()->name }} 👋</h1>
      <p class="ps">
        @if(auth()->user()->isMaster())
          Pantau semua database, zakat, fundraising, dan aktivitas admin dari satu dashboard.
        @else
          Fokus ke follow-up, prospek zakat, dan transaksi pada portfolio milikmu.
        @endif
      </p>
    </div>
    <div class="chips">
      <a href="{{ route('dashboard', ['period' => 'today']) }}" class="chip {{ $period === 'today' ? 'a' : '' }}">Hari Ini</a>
      <a href="{{ route('dashboard', ['period' => '7d']) }}" class="chip {{ $period === '7d' ? 'a' : '' }}">7 Hari</a>
      <a href="{{ route('dashboard', ['period' => '30d']) }}" class="chip {{ $period === '30d' ? 'a' : '' }}">30 Hari</a>
      <a href="{{ route('dashboard', ['period' => 'year']) }}" class="chip {{ $period === 'year' ? 'a' : '' }}">12 Bulan</a>
      <a href="{{ route('reports.export-xlsx', ['period' => $period, 'start' => request('start'), 'end' => request('end')]) }}" class="btn btn-p" style="min-height:36px;font-size:12px">⇩ Export Hasil</a>
    </div>
  </div>

  <div class="grid kpi-grid">
    <div class="card kpi">
      <div class="l">DONATUR AKTIF</div>
      <div class="v">{{ number_format($totalContacts, 0, ',', '.') }}</div>
      <div class="m">Seluruh database</div>
    </div>
    <div class="card kpi">
      <div class="l">KONTAK BARU</div>
      <div class="v">{{ number_format($newContacts, 0, ',', '.') }}</div>
      <div class="m">Periode terpilih</div>
    </div>
    <div class="card kpi gold">
      <div class="l">DANA BULAN INI</div>
      <div class="v">Rp {{ number_format($totalFund / 1000000, 1, ',', '.') }} jt</div>
      <div class="m">Semua jenis dana</div>
    </div>
    <div class="card kpi red">
      <div class="l">OUTSTANDING ZAKAT</div>
      <div class="v">Rp {{ number_format($totalOutstandingZakat / 1000000, 1, ',', '.') }} jt</div>
      <div class="m">Perlu follow-up</div>
    </div>
    <div class="card kpi green">
      <div class="l">FOLLOW-UP AKTIF</div>
      <div class="v">{{ number_format($activeFollowups, 0, ',', '.') }}</div>
      <div class="m">{{ auth()->user()->isMaster() ? 'Semua admin' : 'Portfolio saya' }}</div>
    </div>
  </div>

  <div class="two">
    <div class="grid">
      <!-- Target Fundraising -->
      <div class="card sec">
        <div class="sh">
          <h3>Target Bulan Ini</h3>
          <span>{{ $daysRemaining }} hari tersisa</span>
        </div>
        <div style="font-size:26px;font-weight:800">
          Rp {{ number_format($currentMonthFund, 0, ',', '.') }}
          <span class="small muted" style="font-weight:600">/ Rp {{ number_format($monthlyTarget, 0, ',', '.') }}</span>
        </div>
        <div class="prog" style="margin-top:12px">
          <div class="bar" style="width:{{ min(100, $monthPct) }}%"></div>
        </div>
        <div class="small muted" style="display:flex;justify-content:space-between;margin-top:9px">
          <span>{{ $monthPct }}% tercapai</span>
          <span>Butuh Rp {{ number_format($neededDaily, 0, ',', '.') }} / hari</span>
        </div>
      </div>

      <!-- Infaq Chart -->
      <div class="card sec">
        <div class="sh">
          <h3>Grafik Perolehan Infaq</h3>
          <span>7 Hari Terakhir</span>
        </div>
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:10px">
          <div>
            <div class="small muted">Total infaq periode ini</div>
            <div style="font-size:24px;font-weight:800">Rp {{ number_format(array_sum($chartData), 0, ',', '.') }}</div>
          </div>
          <span class="status s-active">7 hari</span>
        </div>
        <div style="min-height:210px">
          <canvas id="infaqChartCanvas"></canvas>
        </div>
      </div>

      <!-- Follow-up Prioritas -->
      <div class="card sec">
        <div class="sh">
          <h3>{{ auth()->user()->isMaster() ? 'Follow-up Prioritas' : 'Follow-up Milik Saya' }}</h3>
          <a href="{{ route('followups.index') }}" class="btn btn-s" style="min-height:34px;font-size:11px">Lihat semua</a>
        </div>
        <div class="schedule">
          @forelse($priorityFollowups as $f)
          <div class="sch">
            <div class="time">
              {{ $f->scheduled_at?->format('d M') }}<br>
              <span class="small muted">{{ $f->scheduled_at?->format('H:i') }}</span>
            </div>
            <div class="person">
              <div class="ava">{{ $f->contact?->initials ?? '?' }}</div>
              <div>
                <b>{{ $f->contact?->name ?? 'Kontak' }}</b>
                <div class="small muted">{{ $f->reason }} · {{ $f->title }}</div>
              </div>
            </div>
            <div class="q">
              @if($f->contact && $f->contact->relation_status !== 'Blokir')
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $f->contact->phone) }}" target="_blank" rel="noopener" class="mini p">WA</a>
              @endif
              <form action="{{ route('followups.complete', $f) }}" method="POST">
                @csrf
                <button type="submit" class="mini g">Selesai</button>
              </form>
            </div>
          </div>
          @empty
          <div class="empty">Tidak ada follow-up aktif.</div>
          @endforelse
        </div>
      </div>
    </div>

    <!-- Right Side -->
    <div class="grid">
      <!-- Butuh Perhatian -->
      <div class="card sec">
        <div class="sh">
          <h3>Butuh Perhatian</h3>
          <span>Prioritas</span>
        </div>
        <div class="list">
          <div class="item">
            <span class="dot" style="background:var(--red)"></span>
            <div style="flex:1">
              <b>{{ $overdueCount }} follow-up terlambat</b>
              <div class="small muted">Segera ditindaklanjuti</div>
            </div>
            <a href="{{ route('contacts.index', ['issue' => 'overdue']) }}" class="mini p">Lihat</a>
          </div>
          <div class="item">
            <span class="dot" style="background:var(--gold)"></span>
            <div style="flex:1">
              <b>Zakat belum ditunaikan</b>
              <div class="small muted">Perlu follow-up pembayaran</div>
            </div>
            <a href="{{ route('contacts.index', ['issue' => 'zakat-outstanding']) }}" class="mini y">Lihat</a>
          </div>
          <div class="item">
            <span class="dot" style="background:#7558c9"></span>
            <div style="flex:1">
              <b>{{ $untrustCount }} donatur status Untrust</b>
              <div class="small muted">Perlu pendekatan pemulihan</div>
            </div>
            <a href="{{ route('contacts.index', ['issue' => 'untrust']) }}" class="mini">Review</a>
          </div>
          <div class="item">
            <span class="dot" style="background:var(--navy)"></span>
            <div style="flex:1">
              <b>{{ $boredCount }} donatur status Bosan</b>
              <div class="small muted">Tawarkan program baru</div>
            </div>
            <a href="{{ route('contacts.index', ['issue' => 'bored']) }}" class="mini">Detail</a>
          </div>
        </div>
      </div>

      <!-- Program Breakdown -->
      <div class="card sec">
        <div class="sh">
          <h3>Program Teratas</h3>
          <span>Periode terpilih</span>
        </div>
        <div class="list">
          @forelse($programsBreakdown as $pb)
          <div class="item">
            <span class="dot" style="background:var(--navy)"></span>
            <div style="flex:1">
              <b>{{ $pb->program ?: 'Umum' }}</b>
            </div>
            <b>Rp {{ number_format($pb->total, 0, ',', '.') }}</b>
          </div>
          @empty
          <div class="empty">Belum ada transaksi di periode ini.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  @can('is-master')
  <!-- Kinerja CS & Target -->
  <div class="card sec" style="margin-top:16px">
    <div class="sh">
      <h3>Kinerja CS & Target</h3>
      <span>Target individual · kapasitas DB · saran</span>
    </div>
    <div class="tw">
      <table>
        <thead>
          <tr>
            <th>CS</th>
            <th>Tier</th>
            <th>Database</th>
            <th>Target/Hari</th>
            <th>Perolehan</th>
            <th>Capaian</th>
            <th>Rp/1K DB</th>
            <th>Status</th>
            <th>Saran Utama</th>
          </tr>
        </thead>
        <tbody>
          @foreach($csMetrics as $m)
          <tr>
            <td><b>{{ $m['user']->name }}</b></td>
            <td>Tier {{ $m['tier'] }}</td>
            <td>{{ number_format($m['db_count'], 0, ',', '.') }}<div class="small muted">{{ $m['capacity_status'] }}</div></td>
            <td>Rp {{ number_format($m['daily_target'], 0, ',', '.') }}</td>
            <td><b>Rp {{ number_format($m['revenue'], 0, ',', '.') }}</b></td>
            <td>
              <span class="status {{ $m['achievement_pct'] >= 100 ? 's-active' : ($m['achievement_pct'] >= 80 ? 's-gold' : 's-red') }}">
                {{ $m['achievement_pct'] }}%
              </span>
            </td>
            <td>Rp {{ number_format($m['rev_per_1k_db'], 0, ',', '.') }}</td>
            <td>{{ $m['achievement_pct'] >= 100 ? 'Tercapai' : ($m['achievement_pct'] >= 80 ? 'Perlu dorongan' : 'Perlu evaluasi') }}</td>
            <td class="advice">
              <b>{{ $m['advice_area'] }}</b>
              <div class="small muted">{{ $m['advice_text'] }}</div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endcan

  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const ctx = document.getElementById('infaqChartCanvas');
      if (!ctx) return;
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: @json($chartLabels),
          datasets: [{
            label: 'Infaq (Rp)',
            data: @json($chartData),
            borderColor: '#343A72',
            backgroundColor: 'rgba(52, 58, 114, 0.08)',
            borderWidth: 3,
            fill: true,
            tension: 0.35,
            pointBackgroundColor: '#FF303B',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 5
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: (ctx) => 'Rp ' + Number(ctx.raw).toLocaleString('id-ID')
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { color: '#EDF0F6' },
              ticks: { callback: (v) => (v / 1000000) + ' jt' }
            },
            x: { grid: { display: false } }
          }
        }
      });
    });
  </script>
  @endpush
</x-app-layout>
