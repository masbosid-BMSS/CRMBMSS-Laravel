<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#FFF0F2] text-[#FF303B] mb-1.5">
                CRM BMSS · ZAKAT
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Zakat Center</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Dashboard operasional zakat: konsultasi masuk, status wajib zakat, outstanding, dan realisasi pembayaran.
            </p>
        </div>
        <div>
            <a href="{{ route('zakat.calculator') }}" class="h-11 px-5 rounded-xl bg-[#FF303B] text-white text-xs font-extrabold flex items-center gap-2 hover:bg-red-600 transition shadow-sm">
                <span>＋</span>
                <span>Hitung Zakat Baru</span>
            </a>
        </div>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">KONSULTASI ZAKAT</div>
            <div class="text-3xl font-extrabold text-[#252B5B]">{{ number_format($totalConsultations, 0, ',', '.') }}</div>
            <div class="text-[11px] text-gray-400 mt-2">Perhitungan tersimpan</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">WAJIB ZAKAT</div>
            <div class="text-3xl font-extrabold text-[#FFC316]">{{ number_format($wajibCount, 0, ',', '.') }}</div>
            <div class="text-[11px] text-amber-600 font-semibold mt-2">Mencapai nisab & haul</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">OUTSTANDING</div>
            <div class="text-3xl font-extrabold text-[#FF303B]">Rp {{ number_format($outstandingAmount / 1000000, 1, ',', '.') }} jt</div>
            <div class="text-[11px] text-red-500 font-semibold mt-2">Rp {{ number_format($outstandingAmount, 0, ',', '.') }}</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">TERTUNAIKAN</div>
            <div class="text-3xl font-extrabold text-[#1F9D68]">Rp {{ number_format($paidAmount / 1000000, 1, ',', '.') }} jt</div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-2">Masuk transaksi zakat</div>
        </div>
    </div>

    <!-- Two Cards -->
    <div class="grid md:grid-cols-2 gap-4">
        <!-- Pipeline Zakat -->
        <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-5 shadow-bm-sm space-y-3">
            <h3 class="font-extrabold text-sm text-[#252B5B]">Pipeline Realisasi Zakat</h3>
            <div class="space-y-2.5 text-xs">
                <div class="flex items-center justify-between p-3 rounded-xl bg-[#FCFCFF] border border-[#ECEEF5]">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#343A72]"></span>
                        <div>
                            <b>Konsultasi Masuk</b>
                            <div class="text-gray-400">Total muzakki berkonsultasi</div>
                        </div>
                    </div>
                    <b class="text-sm text-[#252B5B]">{{ $totalConsultations }}</b>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-[#FCFCFF] border border-[#ECEEF5]">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#FFC316]"></span>
                        <div>
                            <b>Wajib Zakat</b>
                            <div class="text-gray-400">Harta telah capai nisab</div>
                        </div>
                    </div>
                    <b class="text-sm text-[#C98600]">{{ $wajibCount }}</b>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-[#FCFCFF] border border-[#ECEEF5]">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#FF303B]"></span>
                        <div>
                            <b>Belum Ditunaikan (Outstanding)</b>
                            <div class="text-gray-400">Memerlukan follow-up aktif</div>
                        </div>
                    </div>
                    <b class="text-sm text-[#FF303B]">Rp {{ number_format($outstandingAmount, 0, ',', '.') }}</b>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-[#FCFCFF] border border-[#ECEEF5]">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1F9D68]"></span>
                        <div>
                            <b>Sudah Tertunaikan</b>
                            <div class="text-gray-400">Lunas tercatat di transaksi</div>
                        </div>
                    </div>
                    <b class="text-sm text-[#1F9D68]">Rp {{ number_format($paidAmount, 0, ',', '.') }}</b>
                </div>
            </div>
        </div>

        <!-- Actionable Priorities -->
        <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-5 shadow-bm-sm space-y-3">
            <h3 class="font-extrabold text-sm text-[#252B5B]">Membutuhkan Tindakan Hari Ini</h3>
            <div class="space-y-2.5 text-xs">
                <a href="{{ route('contacts.index', ['issue' => 'zakat-no-followup']) }}" class="flex items-center justify-between p-3.5 rounded-xl bg-red-50/70 border border-red-100 hover:bg-red-50 transition">
                    <div>
                        <b class="text-red-900 block">{{ $overdueCalcs }} Hasil Wajib Zakat Belum Ditindaklanjuti</b>
                        <span class="text-red-600 text-[11px]">Segera jadwalkan follow-up pembayaran</span>
                    </div>
                    <span class="text-xs font-bold text-red-600">Buka →</span>
                </a>

                <a href="{{ route('followups.index') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-amber-50/70 border border-amber-100 hover:bg-amber-50 transition">
                    <div>
                        <b class="text-amber-900 block">{{ $todayFollowupCalcs }} Follow-up Zakat Hari Ini</b>
                        <span class="text-amber-700 text-[11px]">Hubungi muzakki sesuai jadwal</span>
                    </div>
                    <span class="text-xs font-bold text-amber-700">Buka →</span>
                </a>

                <a href="{{ route('zakat.history') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-indigo-50/70 border border-indigo-100 hover:bg-indigo-50 transition">
                    <div>
                        <b class="text-indigo-900 block">{{ $partialCalcs }} Donatur Pembayaran Zakat Sebagian</b>
                        <span class="text-indigo-600 text-[11px]">Kirimkan laporan penyaluran & pengingat sisa</span>
                    </div>
                    <span class="text-xs font-bold text-indigo-600">Buka →</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
