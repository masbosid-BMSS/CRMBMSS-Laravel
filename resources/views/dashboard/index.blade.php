<x-app-layout>
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">
                Assalamu'alaikum, {{ auth()->user()->name }} 👋
            </h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                @if(auth()->user()->isMaster())
                    Pantau semua database donatur, zakat, fundraising, dan kinerja CS dari satu dashboard.
                @else
                    Fokus ke follow-up, prospek zakat, dan perolehan transaksi pada portfolio milikmu.
                @endif
            </p>
        </div>

        <!-- Filter Chips & Actions -->
        <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
            <a href="{{ route('dashboard', ['period' => 'today']) }}" class="px-3 py-1.5 rounded-full border transition {{ $period === 'today' ? 'bg-[#EEF0F8] text-[#343A72] border-[#343A72]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                Hari Ini
            </a>
            <a href="{{ route('dashboard', ['period' => '7d']) }}" class="px-3 py-1.5 rounded-full border transition {{ $period === '7d' ? 'bg-[#EEF0F8] text-[#343A72] border-[#343A72]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                7 Hari
            </a>
            <a href="{{ route('dashboard', ['period' => '30d']) }}" class="px-3 py-1.5 rounded-full border transition {{ $period === '30d' ? 'bg-[#EEF0F8] text-[#343A72] border-[#343A72]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                30 Hari
            </a>
            <a href="{{ route('dashboard', ['period' => 'year']) }}" class="px-3 py-1.5 rounded-full border transition {{ $period === 'year' ? 'bg-[#EEF0F8] text-[#343A72] border-[#343A72]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                12 Bulan
            </a>
            <a href="{{ route('reports.export-xlsx', ['period' => $period, 'start' => request('start'), 'end' => request('end')]) }}" class="px-3.5 py-1.5 rounded-full bg-[#343A72] text-white hover:bg-[#252B5B] transition flex items-center gap-1.5">
                <span>⇩</span>
                <span>Export XLSX</span>
            </a>
        </div>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5 mb-6">
        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-4 relative overflow-hidden shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">KONTAK AKTIF</div>
            <div class="text-2xl lg:text-3xl font-extrabold text-[#252B5B] leading-none">{{ number_format($totalContacts, 0, ',', '.') }}</div>
            <div class="text-[11px] text-gray-400 mt-2">Database terdaftar</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-4 relative overflow-hidden shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">KONTAK BARU</div>
            <div class="text-2xl lg:text-3xl font-extrabold text-[#252B5B] leading-none">{{ number_format($newContacts, 0, ',', '.') }}</div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-2">Periode terpilih</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-4 relative overflow-hidden shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">DANA TERCATAT</div>
            <div class="text-2xl lg:text-3xl font-extrabold text-[#C98600] leading-none">Rp {{ number_format($totalFund / 1000000, 1, ',', '.') }} jt</div>
            <div class="text-[11px] text-gray-400 mt-2">Rp {{ number_format($totalFund, 0, ',', '.') }}</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-4 relative overflow-hidden shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">OUTSTANDING ZAKAT</div>
            <div class="text-2xl lg:text-3xl font-extrabold text-[#FF303B] leading-none">Rp {{ number_format($totalOutstandingZakat / 1000000, 1, ',', '.') }} jt</div>
            <div class="text-[11px] text-red-500 font-semibold mt-2">Perlu follow-up</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-4 relative overflow-hidden shadow-bm-sm col-span-2 lg:col-span-1">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">FOLLOW-UP AKTIF</div>
            <div class="text-2xl lg:text-3xl font-extrabold text-[#1F9D68] leading-none">{{ number_format($activeFollowups, 0, ',', '.') }}</div>
            <div class="text-[11px] text-gray-400 mt-2">Belum selesai</div>
        </div>
    </div>

    <!-- Two-column Layout -->
    <div class="grid lg:grid-cols-[1.4fr_1fr] gap-4 mb-6">
        <!-- Left Column -->
        <div class="space-y-4">
            <!-- Target Card -->
            <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-5 shadow-bm-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-extrabold text-sm text-[#252B5B]">Target Fundraising Bulan Ini</h3>
                    <span class="text-xs text-gray-400 font-medium">{{ $daysRemaining }} hari tersisa</span>
                </div>
                <div class="text-2xl font-black text-[#252B5B]">
                    Rp {{ number_format($currentMonthFund, 0, ',', '.') }}
                    <span class="text-xs text-gray-400 font-normal">/ Rp {{ number_format($monthlyTarget, 0, ',', '.') }}</span>
                </div>
                <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden mt-3">
                    <div class="h-full bg-gradient-to-r from-[#343A72] to-[#5b63a4] rounded-full transition-all" style="width: {{ min(100, $monthPct) }}%"></div>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-400 mt-2 font-medium">
                    <span>{{ $monthPct }}% tercapai</span>
                    <span>Butuh Rp {{ number_format($neededDaily, 0, ',', '.') }} / hari</span>
                </div>
            </div>

            <!-- Infaq Chart Card -->
            <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-5 shadow-bm-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-extrabold text-sm text-[#252B5B]">Grafik Perolehan Infaq (7 Hari Terakhir)</h3>
                        <div class="text-xs text-gray-400">Total Rp {{ number_format(array_sum($chartData), 0, ',', '.') }}</div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-[#EAF8F1] text-[#166848]">Trend Harian</span>
                </div>
                <div class="h-52">
                    <canvas id="infaqChartCanvas"></canvas>
                </div>
            </div>

            <!-- Priority Follow-ups -->
            <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-5 shadow-bm-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-extrabold text-sm text-[#252B5B]">Follow-up Prioritas</h3>
                    <a href="{{ route('followups.index') }}" class="text-xs font-bold text-[#343A72] hover:underline">Lihat semua →</a>
                </div>
                <div class="space-y-3">
                    @forelse($priorityFollowups as $f)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-[#FCFCFF] border border-[#ECEEF5] gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-white border border-[#E1E3EC] text-[#343A72] font-black text-xs grid place-items-center flex-shrink-0">
                                {{ $f->contact?->initials ?? '?' }}
                            </div>
                            <div class="min-w-0">
                                <b class="block text-xs font-bold text-[#252B5B] truncate">{{ $f->contact?->name ?? 'Kontak' }}</b>
                                <div class="text-[11px] text-gray-400 truncate">{{ $f->reason }} · {{ $f->title }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-[11px] font-bold {{ $f->status === 'Overdue' ? 'text-red-500' : 'text-gray-400' }}">
                                {{ $f->scheduled_at?->translatedFormat('d M H:i') }}
                            </span>
                            <form action="{{ route('followups.complete', $f) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-[#EAF8F1] text-[#166848] text-xs font-extrabold rounded-lg hover:bg-[#caead8] transition">
                                    Selesai
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-xs text-gray-400">Tidak ada follow-up aktif.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-4">
            <!-- Butuh Perhatian -->
            <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-5 shadow-bm-sm">
                <h3 class="font-extrabold text-sm text-[#252B5B] mb-3">Butuh Perhatian Segera</h3>
                <div class="space-y-2.5 text-xs">
                    <a href="{{ route('contacts.index', ['issue' => 'overdue']) }}" class="flex items-center justify-between p-3 rounded-xl bg-red-50/70 border border-red-100 hover:bg-red-50 transition">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#FF303B]"></span>
                            <span class="font-bold text-red-900">{{ $overdueCount }} Follow-up Terlambat</span>
                        </div>
                        <span class="font-extrabold text-red-600">Buka →</span>
                    </a>

                    <a href="{{ route('contacts.index', ['issue' => 'zakat-outstanding']) }}" class="flex items-center justify-between p-3 rounded-xl bg-amber-50/70 border border-amber-100 hover:bg-amber-50 transition">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#FFC316]"></span>
                            <span class="font-bold text-amber-900">Zakat Belum Ditunaikan</span>
                        </div>
                        <span class="font-extrabold text-amber-600">Lihat →</span>
                    </a>

                    <a href="{{ route('contacts.index', ['issue' => 'untrust']) }}" class="flex items-center justify-between p-3 rounded-xl bg-purple-50/70 border border-purple-100 hover:bg-purple-50 transition">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#7558c9]"></span>
                            <span class="font-bold text-purple-900">{{ $untrustCount }} Donatur Status Untrust</span>
                        </div>
                        <span class="font-extrabold text-purple-600">Review →</span>
                    </a>

                    <a href="{{ route('contacts.index', ['issue' => 'bored']) }}" class="flex items-center justify-between p-3 rounded-xl bg-indigo-50/70 border border-indigo-100 hover:bg-indigo-50 transition">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#5b63a4]"></span>
                            <span class="font-bold text-indigo-900">{{ $boredCount }} Donatur Status Bosan</span>
                        </div>
                        <span class="font-extrabold text-indigo-600">Variasikan →</span>
                    </a>

                    @if($blockedCount > 0)
                    <a href="{{ route('contacts.index', ['issue' => 'blocked']) }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-100 border border-gray-200 hover:bg-gray-200 transition">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-gray-800"></span>
                            <span class="font-bold text-gray-800">{{ $blockedCount }} Kontak Blokir (Do Not Contact)</span>
                        </div>
                        <span class="font-extrabold text-gray-600">Detail →</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Leads & Program Breakdown -->
            <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-5 shadow-bm-sm">
                <h3 class="font-extrabold text-sm text-[#252B5B] mb-3">Program Teratas (Periode Terpilih)</h3>
                <div class="space-y-2.5 text-xs">
                    @forelse($programsBreakdown as $pb)
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-100">
                        <span class="font-bold text-gray-700">{{ $pb->program ?: 'Umum' }}</span>
                        <b class="text-[#343A72]">Rp {{ number_format($pb->total, 0, ',', '.') }}</b>
                    </div>
                    @empty
                    <div class="text-center py-4 text-xs text-gray-400">Belum ada transaksi di periode ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Kinerja CS & Target Table -->
    @can('is-master')
    <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-5 shadow-bm-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
                <h3 class="font-extrabold text-base text-[#252B5B]">Kinerja CS, Target & Evaluasi</h3>
                <span class="text-xs text-gray-400">Periode cut-off: {{ $window['label'] }}</span>
            </div>
            <a href="{{ route('kpi-targets.index') }}" class="text-xs font-bold text-[#343A72] hover:underline">Kelola Target Individual →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-[#FBFBFE] text-gray-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-[#E1E3EC]">
                        <th class="py-3 px-3">CS</th>
                        <th class="py-3 px-3">Tier</th>
                        <th class="py-3 px-3">Database</th>
                        <th class="py-3 px-3">Target/Hari</th>
                        <th class="py-3 px-3">Perolehan</th>
                        <th class="py-3 px-3">Capaian</th>
                        <th class="py-3 px-3">Rp/1K DB</th>
                        <th class="py-3 px-3">Kapasitas</th>
                        <th class="py-3 px-3">Saran Utama</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($csMetrics as $m)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3.5 px-3 font-bold text-[#252B5B]">{{ $m['user']->name }}</td>
                        <td class="py-3.5 px-3">Tier {{ $m['tier'] }}</td>
                        <td class="py-3.5 px-3 font-semibold">{{ number_format($m['db_count'], 0, ',', '.') }}</td>
                        <td class="py-3.5 px-3">Rp {{ number_format($m['daily_target'], 0, ',', '.') }}</td>
                        <td class="py-3.5 px-3 font-extrabold text-[#343A72]">Rp {{ number_format($m['revenue'], 0, ',', '.') }}</td>
                        <td class="py-3.5 px-3">
                            <span class="px-2 py-0.5 rounded-full font-extrabold text-[10px] {{ $m['achievement_pct'] >= 100 ? 'bg-[#EAF8F1] text-[#166848]' : ($m['achievement_pct'] >= 80 ? 'bg-[#FFF7D8] text-[#8b6500]' : 'bg-[#FFF0F2] text-[#8d2e35]') }}">
                                {{ $m['achievement_pct'] }}%
                            </span>
                        </td>
                        <td class="py-3.5 px-3">Rp {{ number_format($m['rev_per_1k_db'], 0, ',', '.') }}</td>
                        <td class="py-3.5 px-3 {{ $m['capacity_class'] }}">{{ $m['capacity_status'] }}</td>
                        <td class="py-3.5 px-3 max-w-xs">
                            <b class="text-gray-800">{{ $m['advice_area'] }}</b>
                            <p class="text-gray-500 text-[11px] leading-tight mt-0.5">{{ $m['advice_text'] }}</p>
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
                            ticks: {
                                callback: (v) => (v / 1000000) + ' jt'
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
