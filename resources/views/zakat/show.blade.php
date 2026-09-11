<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('zakat.history') }}" class="text-xs font-bold text-[#343A72] hover:underline mb-2 inline-block">← Kembali ke Riwayat Zakat</a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Detail Perhitungan Zakat</h1>
            </div>
            <button type="button" onclick="window.print()" class="h-10 px-4 rounded-xl bg-white border border-[#E1E3EC] text-[#343A72] text-xs font-extrabold hover:bg-gray-50 transition">
                Print / Simpan PDF
            </button>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 md:p-8 shadow-bm-sm space-y-6">
            <!-- Header Result Card -->
            <div class="p-6 rounded-2xl bg-gradient-to-r from-[#FFC316] to-[#ffd24d] text-[#3b2c00]">
                <div class="text-xs opacity-80 font-medium">
                    {{ $calculation->contact?->name ?: '-' }} · {{ $calculation->type }} · {{ $calculation->calculation_date?->format('d M Y') }}
                </div>
                <div class="text-3xl md:text-4xl font-black mt-2">
                    Rp {{ number_format($calculation->zakat_amount, 0, ',', '.') }}
                </div>
                <div class="text-xs font-bold mt-1">
                    {{ $calculation->zakat_amount > 0 ? 'Wajib Zakat (Telah Mencapai Nisab & Haul)' : 'Belum Wajib Zakat' }}
                </div>
            </div>

            <!-- Breakdown Key Values -->
            <div class="text-xs space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Nomor Referensi Hitung</span>
                    <b class="font-mono text-gray-800">{{ $calculation->ref_no }}</b>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Muzakki</span>
                    <a href="{{ route('contacts.show', $calculation->contact) }}" class="font-bold text-[#343A72] hover:underline">
                        {{ $calculation->contact?->name ?: '-' }} ({{ $calculation->contact?->niss }})
                    </a>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">CS Pendamping (Owner)</span>
                    <b class="text-gray-800">{{ $calculation->owner?->name ?: '-' }}</b>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Total Harta Terdata</span>
                    <b class="text-gray-800">Rp {{ number_format($calculation->assets_total, 0, ',', '.') }}</b>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Total Pengurang (Hutang Segera)</span>
                    <b class="text-red-600">Rp {{ number_format($calculation->deductions_total, 0, ',', '.') }}</b>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Harta Bersih</span>
                    <b class="text-[#343A72]">Rp {{ number_format($calculation->net_amount, 0, ',', '.') }}</b>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Nisab Emas (85g)</span>
                    <b class="text-gray-800">Rp {{ number_format($calculation->nisab_amount, 0, ',', '.') }}</b>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Tarif Zakat</span>
                    <b class="text-gray-800">{{ number_format($calculation->rate * 100, 4, ',', '.') }}%</b>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Zakat Terbayar</span>
                    <b class="text-emerald-600">Rp {{ number_format($calculation->paid_amount, 0, ',', '.') }}</b>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Sisa Kewajiban</span>
                    <b class="text-[#FF303B]">Rp {{ number_format($calculation->remaining_amount, 0, ',', '.') }}</b>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500">Status Kewajiban</span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold
                        {{ $calculation->status === 'Lunas' ? 's-active' : ($calculation->status === 'Outstanding' ? 's-red' : ($calculation->status === 'Sebagian' ? 's-risk' : 's-gray')) }}">
                        {{ $calculation->status }}
                    </span>
                </div>
            </div>

            <!-- Explanation Box -->
            <div class="p-4 rounded-2xl bg-[#FBFBFE] border border-[#ECEEF5] text-xs">
                <b class="block text-gray-800 mb-1">Dasar & Penjelasan Perhitungan:</b>
                <p class="text-gray-600 leading-relaxed">{{ $explanation }}</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('contacts.show', $calculation->contact) }}" class="px-5 py-2.5 bg-white border border-[#E1E3EC] text-[#343A72] font-extrabold rounded-xl hover:bg-gray-50 text-xs">
                    Buka Profil Donatur
                </a>
                @if($calculation->remaining_amount > 0 && auth()->user()->can('update', $calculation->contact))
                <a href="{{ route('contacts.show', $calculation->contact) }}#payment" class="px-5 py-2.5 bg-[#FF303B] text-white font-extrabold rounded-xl hover:bg-red-600 text-xs shadow-sm">
                    Catat Pembayaran
                </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
