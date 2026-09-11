<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Target & KPI CS</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Atur target individual harian, target follow-up, conversion rate, dan retensi berdasarkan masa kerja/tier.
            </p>
        </div>
    </div>

    <!-- Benchmark Banner -->
    <div class="p-4 rounded-2xl bg-[#FAFBFE] border border-[#DCE1F1] text-xs text-gray-700 leading-relaxed mb-6">
        🎯 <b>Standar Acuan Tier:</b><br>
        • <b>Tier 1</b> (±1 tahun): Target <b>Rp 1.000.000 / hari</b> · Follow-up 150/hari · Conversion 8%<br>
        • <b>Tier 2</b> (2–3 tahun): Target <b>Rp 2.000.000 / hari</b> · Follow-up 180/hari · Conversion 8%<br>
        • <b>Tier 3</b> (≥3 tahun): Target <b>Rp 3.500.000 / hari</b> · Follow-up 200/hari · Conversion 9%<br>
        Kapasitas database sehat berkisar antara <b>15.000 – 30.000 kontak/CS</b>.
    </div>

    <form action="{{ route('kpi-targets.update-all') }}" method="POST">
        @csrf
        <div class="bg-white border border-[#E1E3EC] rounded-[24px] shadow-bm-sm overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-[#FBFBFE] text-gray-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-[#E1E3EC]">
                            <th class="py-3.5 px-4">Nama CS</th>
                            <th class="py-3.5 px-3">Mulai Bekerja</th>
                            <th class="py-3.5 px-3">Tier</th>
                            <th class="py-3.5 px-3">Database</th>
                            <th class="py-3.5 px-3">Target Harian (Rp)</th>
                            <th class="py-3.5 px-3">Follow-up/Hari</th>
                            <th class="py-3.5 px-3">Conversion (%)</th>
                            <th class="py-3.5 px-3">Retensi (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($admins as $cs)
                        @php
                            $target = $cs->kpiTarget;
                        @endphp
                        <tr>
                            <td class="py-3.5 px-4 font-bold text-[#252B5B]">
                                {{ $cs->name }}
                                <div class="text-[10px] text-gray-400 font-mono">{{ $cs->username }}</div>
                            </td>
                            <td class="py-3.5 px-3">
                                <input type="date" name="targets[{{ $cs->id }}][start_date]" value="{{ $target?->start_date?->format('Y-m-d') }}" class="h-9 px-2.5 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            </td>
                            <td class="py-3.5 px-3">
                                <select name="targets[{{ $cs->id }}][tier]" class="h-9 px-2.5 bg-white border border-[#E1E3EC] rounded-xl text-xs font-semibold">
                                    <option value="1" {{ ($target?->tier ?? 1) == 1 ? 'selected' : '' }}>Tier 1</option>
                                    <option value="2" {{ ($target?->tier ?? 1) == 2 ? 'selected' : '' }}>Tier 2</option>
                                    <option value="3" {{ ($target?->tier ?? 1) == 3 ? 'selected' : '' }}>Tier 3</option>
                                </select>
                            </td>
                            <td class="py-3.5 px-3 font-semibold text-gray-700">
                                {{ $cs->contacts->count() }} kontak
                            </td>
                            <td class="py-3.5 px-3">
                                <input type="number" name="targets[{{ $cs->id }}][daily_target]" value="{{ $target?->daily_target ?? 1000000 }}" class="w-32 h-9 px-2.5 bg-white border border-[#E1E3EC] rounded-xl text-xs font-bold text-[#343A72]">
                            </td>
                            <td class="py-3.5 px-3">
                                <input type="number" name="targets[{{ $cs->id }}][followup_target]" value="{{ $target?->followup_target ?? 150 }}" class="w-20 h-9 px-2.5 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            </td>
                            <td class="py-3.5 px-3">
                                <input type="number" step="0.1" name="targets[{{ $cs->id }}][conversion_target]" value="{{ $target?->conversion_target ?? 8.0 }}" class="w-20 h-9 px-2.5 bg-white border border-[#E1E3EC] rounded-xl text-xs"> %
                            </td>
                            <td class="py-3.5 px-3">
                                <input type="number" step="0.1" name="targets[{{ $cs->id }}][retention_target]" value="{{ $target?->retention_target ?? 60.0 }}" class="w-20 h-9 px-2.5 bg-white border border-[#E1E3EC] rounded-xl text-xs"> %
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-[#343A72] text-white font-extrabold rounded-xl hover:bg-[#252B5B] transition shadow-sm text-xs">
                    Simpan Seluruh Target
                </button>
            </div>
        </div>
    </form>
</x-app-layout>
