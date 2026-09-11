<x-app-layout>
    <div class="mb-6">
        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#FFF7D8] text-[#8b6500] mb-1.5">
            PENGHITUNG ZAKAT
        </div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Kalkulator Zakat Terintegrasi</h1>
        <p class="text-xs md:text-sm text-gray-500 mt-1">
            Hitung nisab, haul, dan estimasi zakat donatur secara presisi dengan formula terverifikasi.
        </p>
    </div>

    <!-- Stepper indicator -->
    <div class="flex flex-wrap items-center gap-2 mb-6 text-xs font-bold">
        <span class="px-3.5 py-1.5 rounded-full bg-[#343A72] text-white">1. Pilih Muzakki</span>
        <span class="px-3.5 py-1.5 rounded-full bg-[#343A72] text-white">2. Jenis Zakat</span>
        <span class="px-3.5 py-1.5 rounded-full bg-[#343A72] text-white">3. Harta & Pengurang</span>
        <span class="px-3.5 py-1.5 rounded-full bg-[#343A72] text-white">4. Nisab & Haul</span>
        <span class="px-3.5 py-1.5 rounded-full bg-[#343A72] text-white">5. Finalisasi</span>
    </div>

    <!-- Calculator Engine (Alpine.js) -->
    <div x-data="zakatCalculator()" x-init="init()" class="grid lg:grid-cols-[1.2fr_0.8fr] gap-5 items-start">
        <!-- Form Left -->
        <div class="space-y-4">
            <!-- 1. Muzakki -->
            <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 shadow-bm-sm">
                <h3 class="font-extrabold text-sm text-[#252B5B] mb-3">1. Pilih Muzakki</h3>
                <div class="grid sm:grid-cols-2 gap-3 text-xs">
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-gray-700 mb-1">Kontak Donatur *</label>
                        <select x-model="contactId" class="w-full h-11 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-medium">
                            <option value="">-- Pilih Kontak Donatur --</option>
                            @foreach($contacts as $c)
                                <option value="{{ $c->id }}" {{ ($selectedContact?->id === $c->id) ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->niss }}) · {{ $c->phone }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Tanggal Perhitungan</label>
                        <input type="date" x-model="calcDate" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Admin / CS</label>
                        <input type="text" value="{{ auth()->user()->name }}" disabled class="w-full h-10 px-3 bg-gray-50 border border-[#E1E3EC] rounded-xl text-xs text-gray-500">
                    </div>
                </div>
            </div>

            <!-- 2. Jenis Zakat -->
            <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 shadow-bm-sm">
                <h3 class="font-extrabold text-sm text-[#252B5B] mb-3">2. Jenis Zakat & Metode Haul</h3>
                <div class="grid sm:grid-cols-2 gap-3 text-xs">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Jenis Zakat</label>
                        <select x-model="zakatType" @change="recalc()" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-medium">
                            <option value="Zakat Maal">Zakat Maal</option>
                            <option value="Zakat Perdagangan">Zakat Perdagangan</option>
                            <option value="Zakat Tabungan">Zakat Tabungan</option>
                            <option value="Zakat Emas & Perak">Zakat Emas & Perak</option>
                            <option value="Zakat Penghasilan">Zakat Penghasilan</option>
                            <option value="Zakat Investasi">Zakat Investasi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Metode Tahun</label>
                        <select x-model="yearMethod" @change="recalc()" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-medium">
                            <option value="h">Tahun Hijriyah — 2,5%</option>
                            <option value="m">Tahun Masehi — 2,5775%</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 3. Harta & Pengurang (Accordion) -->
            <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 shadow-bm-sm space-y-3">
                <h3 class="font-extrabold text-sm text-[#252B5B] mb-2">3. Harta & Pengurang yang Relevan</h3>

                <!-- Aset Likuid -->
                <div class="border border-[#ECEEF5] rounded-2xl overflow-hidden" x-data="{ open: true }">
                    <button type="button" @click="open = !open" class="w-full p-3.5 bg-[#FBFBFE] font-bold text-xs text-[#252B5B] flex justify-between items-center">
                        <span>Aset Likuid & Simpanan</span>
                        <span x-text="open ? '▲' : '▼'"></span>
                    </button>
                    <div x-show="open" class="p-4 bg-white grid sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block text-gray-500 font-semibold mb-1">Uang Tunai (Rp)</label>
                            <input type="number" x-model.number="items.cash" @input="recalc()" placeholder="0" class="w-full h-10 px-3 border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-gray-500 font-semibold mb-1">Saldo Rekening Bank (Rp)</label>
                            <input type="number" x-model.number="items.bank" @input="recalc()" placeholder="0" class="w-full h-10 px-3 border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-gray-500 font-semibold mb-1">E-Wallet / Saldo Digital (Rp)</label>
                            <input type="number" x-model.number="items.ewallet" @input="recalc()" placeholder="0" class="w-full h-10 px-3 border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-gray-500 font-semibold mb-1">Tabungan / Deposito (Rp)</label>
                            <input type="number" x-model.number="items.saving" @input="recalc()" placeholder="0" class="w-full h-10 px-3 border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                    </div>
                </div>

                <!-- Usaha & Piutang -->
                <div class="border border-[#ECEEF5] rounded-2xl overflow-hidden" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="w-full p-3.5 bg-[#FBFBFE] font-bold text-xs text-[#252B5B] flex justify-between items-center">
                        <span>Usaha & Piutang Tertagih</span>
                        <span x-text="open ? '▲' : '▼'"></span>
                    </button>
                    <div x-show="open" class="p-4 bg-white grid sm:grid-cols-2 gap-3 text-xs" x-cloak>
                        <div>
                            <label class="block text-gray-500 font-semibold mb-1">Kas & Rekening Usaha (Rp)</label>
                            <input type="number" x-model.number="items.business" @input="recalc()" placeholder="0" class="w-full h-10 px-3 border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-gray-500 font-semibold mb-1">Nilai Stok Dagangan (Rp)</label>
                            <input type="number" x-model.number="items.stock" @input="recalc()" placeholder="0" class="w-full h-10 px-3 border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-gray-500 font-semibold mb-1">Piutang yang Berpotensi Tertagih (Rp)</label>
                            <input type="number" x-model.number="items.receivable" @input="recalc()" placeholder="0" class="w-full h-10 px-3 border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                    </div>
                </div>

                <!-- Emas & Investasi -->
                <div class="border border-[#ECEEF5] rounded-2xl overflow-hidden" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="w-full p-3.5 bg-[#FBFBFE] font-bold text-xs text-[#252B5B] flex justify-between items-center">
                        <span>Emas, Perak, & Investasi</span>
                        <span x-text="open ? '▲' : '▼'"></span>
                    </button>
                    <div x-show="open" class="p-4 bg-white grid sm:grid-cols-2 gap-3 text-xs" x-cloak>
                        <div>
                            <label class="block text-gray-500 font-semibold mb-1">Nilai Emas (Rp)</label>
                            <input type="number" x-model.number="items.goldVal" @input="recalc()" placeholder="0" class="w-full h-10 px-3 border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-gray-500 font-semibold mb-1">Nilai Perak (Rp)</label>
                            <input type="number" x-model.number="items.silverVal" @input="recalc()" placeholder="0" class="w-full h-10 px-3 border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-gray-500 font-semibold mb-1">Nilai Investasi / Saham / Reksadana (Rp)</label>
                            <input type="number" x-model.number="items.invest" @input="recalc()" placeholder="0" class="w-full h-10 px-3 border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                    </div>
                </div>

                <!-- Pengurang (Hutang) -->
                <div class="border border-[#ECEEF5] rounded-2xl overflow-hidden" x-data="{ open: true }">
                    <button type="button" @click="open = !open" class="w-full p-3.5 bg-red-50/50 font-bold text-xs text-red-900 flex justify-between items-center">
                        <span>Kewajiban / Hutang Jatuh Tempo (Pengurang)</span>
                        <span x-text="open ? '▲' : '▼'"></span>
                    </button>
                    <div x-show="open" class="p-4 bg-white grid sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block text-gray-500 font-semibold mb-1">Hutang Segera Jatuh Tempo (Rp)</label>
                            <input type="number" x-model.number="items.debt" @input="recalc()" placeholder="0" class="w-full h-10 px-3 border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-gray-500 font-semibold mb-1">Kewajiban Jangka Pendek (Rp)</label>
                            <input type="number" x-model.number="items.shortObligation" @input="recalc()" placeholder="0" class="w-full h-10 px-3 border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Nisab & Haul -->
            <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 shadow-bm-sm">
                <h3 class="font-extrabold text-sm text-[#252B5B] mb-3">4. Nisab & Haul</h3>
                <div class="grid sm:grid-cols-2 gap-3 text-xs mb-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Harga Emas / Gram (Rp)</label>
                        <input type="number" x-model.number="goldPrice" @input="recalc()" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-bold text-[#343A72]">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nisab Otomatis (85g Emas)</label>
                        <input type="text" :value="formatMoney(nisab)" disabled class="w-full h-10 px-3 bg-gray-50 border border-[#E1E3EC] rounded-xl text-xs font-extrabold text-gray-700">
                    </div>
                </div>

                <div class="text-xs">
                    <label class="block font-bold text-gray-700 mb-2">Status Haul (Telah Dimiliki 1 Tahun)</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="haulStatus = 'yes'; recalc()" :class="haulStatus === 'yes' ? 'bg-[#343A72] text-white' : 'bg-gray-100 text-gray-600'" class="px-3.5 py-1.5 rounded-full font-bold transition">
                            Sudah Memenuhi
                        </button>
                        <button type="button" @click="haulStatus = 'no'; recalc()" :class="haulStatus === 'no' ? 'bg-[#343A72] text-white' : 'bg-gray-100 text-gray-600'" class="px-3.5 py-1.5 rounded-full font-bold transition">
                            Belum Memenuhi
                        </button>
                        <button type="button" @click="haulStatus = 'na'; recalc()" :class="haulStatus === 'na' ? 'bg-[#343A72] text-white' : 'bg-gray-100 text-gray-600'" class="px-3.5 py-1.5 rounded-full font-bold transition">
                            Tidak Berlaku / Khusus
                        </button>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-bold text-gray-700 text-xs mb-1">Catatan Dasar Perhitungan</label>
                    <textarea x-model="calcNote" rows="2" placeholder="Catatan opsional untuk arsip muzakki" class="w-full p-2.5 bg-white border border-[#E1E3EC] rounded-xl text-xs"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 mt-5 pt-4 border-t border-gray-100">
                    <button type="button" @click="saveCalc(true)" :disabled="isSaving" class="h-11 px-6 bg-[#FF303B] text-white font-extrabold rounded-xl hover:bg-red-600 transition shadow-sm text-xs">
                        Finalisasi Hasil
                    </button>
                    <button type="button" @click="saveCalc(false)" :disabled="isSaving" class="h-11 px-5 bg-white border border-[#E1E3EC] text-[#343A72] font-extrabold rounded-xl hover:bg-gray-50 transition text-xs">
                        Simpan Draft
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Sticky Right Panel -->
        <div class="sticky top-[84px] space-y-4">
            <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 shadow-bm-sm">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#FFF7D8] text-[#8b6500] mb-2">
                    RINGKASAN REAL-TIME
                </span>
                <h3 class="text-lg font-black text-[#252B5B]" x-text="zakatType"></h3>

                <!-- Summary Breakdown -->
                <div class="p-4 rounded-2xl bg-gradient-to-b from-[#343A72] to-[#252B5B] text-white text-xs space-y-2 mt-3">
                    <div class="flex justify-between py-1 border-b border-white/10">
                        <span class="text-white/70">Total Harta</span>
                        <b x-text="formatMoney(totalAssets)"></b>
                    </div>
                    <div class="flex justify-between py-1 border-b border-white/10">
                        <span class="text-white/70">Total Pengurang</span>
                        <b x-text="formatMoney(totalDeductions)"></b>
                    </div>
                    <div class="flex justify-between py-1 border-b border-white/10">
                        <span class="text-white/70">Harta Bersih</span>
                        <b x-text="formatMoney(netAmount)"></b>
                    </div>
                    <div class="flex justify-between py-1 border-b border-white/10">
                        <span class="text-white/70">Nisab (85g)</span>
                        <b x-text="formatMoney(nisab)"></b>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-white/70">Tarif Zakat</span>
                        <b x-text="ratePercent"></b>
                    </div>
                </div>

                <!-- Status alert box -->
                <div class="p-3.5 rounded-xl text-xs font-semibold mt-3" :class="isWajib ? 'bg-[#EAF8F1] text-[#166848] border border-[#caead8]' : 'bg-[#FFF7D8] text-[#836100] border border-[#f8de8b]'" x-text="statusText">
                </div>

                <!-- Final Zakat Amount Box -->
                <div class="p-4 rounded-2xl bg-gradient-to-b from-[#FF303B] to-[#ff5c64] text-white mt-3">
                    <div class="text-[11px] text-white/80 font-medium">Estimasi Zakat yang Perlu Ditunaikan:</div>
                    <div class="text-3xl font-black mt-1" x-text="formatMoney(zakatAmount)"></div>
                    <div class="text-[10px] text-white/70 mt-1" x-text="formulaText"></div>
                </div>

                <div class="p-3 bg-gray-50 rounded-xl text-[11px] text-gray-500 leading-relaxed mt-3">
                    Perhitungan otomatis ini mengacu pada ketentuan nisab 85 gram emas murni serta haul 1 tahun Hijriyah / Masehi.
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function zakatCalculator() {
            return {
                contactId: '{{ $selectedContact?->id ?? "" }}',
                calcDate: '{{ now()->toDateString() }}',
                zakatType: 'Zakat Maal',
                yearMethod: 'h',
                haulStatus: 'yes',
                goldPrice: {{ $defaultGoldPrice }},
                calcNote: '',
                isSaving: false,
                items: {
                    cash: 0,
                    bank: 0,
                    ewallet: 0,
                    saving: 0,
                    business: 0,
                    stock: 0,
                    receivable: 0,
                    goldVal: 0,
                    silverVal: 0,
                    invest: 0,
                    debt: 0,
                    shortObligation: 0
                },
                totalAssets: 0,
                totalDeductions: 0,
                netAmount: 0,
                nisab: 0,
                rate: 0.025,
                isWajib: false,
                zakatAmount: 0,
                statusText: '',
                formulaText: '—',

                init() {
                    this.recalc();
                },

                formatMoney(amount) {
                    return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
                },

                get ratePercent() {
                    return (this.rate * 100).toLocaleString('id-ID', { maximumFractionDigits: 4 }) + '%';
                },

                recalc() {
                    const it = this.items;
                    this.totalAssets = Number(it.cash || 0) + Number(it.bank || 0) + Number(it.ewallet || 0) + Number(it.saving || 0) +
                                      Number(it.business || 0) + Number(it.stock || 0) + Number(it.receivable || 0) +
                                      Number(it.goldVal || 0) + Number(it.silverVal || 0) + Number(it.invest || 0);

                    this.totalDeductions = Number(it.debt || 0) + Number(it.shortObligation || 0);
                    this.netAmount = Math.max(0, this.totalAssets - this.totalDeductions);

                    this.nisab = Number(this.goldPrice || 0) * 85.0;
                    this.rate = this.yearMethod === 'm' ? 0.025775 : 0.025;

                    const isNisabMet = this.nisab > 0 && this.netAmount >= this.nisab;
                    const isHaulMet = this.haulStatus === 'yes' || this.haulStatus === 'na';

                    this.isWajib = isNisabMet && isHaulMet;
                    this.zakatAmount = this.isWajib ? Math.round(this.netAmount * this.rate) : 0;

                    if (this.nisab <= 0) {
                        this.statusText = 'Harga emas belum diisi, nisab belum ditentukan.';
                    } else if (!isNisabMet) {
                        this.statusText = 'Belum wajib: Harta bersih belum mencapai nisab (85g emas).';
                    } else if (!isHaulMet) {
                        this.statusText = 'Belum wajib: Harta mencapai nisab, tetapi syarat haul belum terpenuhi.';
                    } else {
                        this.statusText = 'Wajib zakat: Harta mencapai nisab dan syarat haul terpenuhi.';
                    }

                    this.formulaText = this.zakatAmount > 0
                        ? `${this.formatMoney(this.netAmount)} × ${this.ratePercent}`
                        : '—';
                },

                async saveCalc(finalize) {
                    if (!this.contactId) {
                        alert('Silakan pilih muzakki terlebih dahulu.');
                        return;
                    }

                    this.isSaving = true;

                    try {
                        const response = await fetch('{{ route("zakat.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                contact_id: this.contactId,
                                type: this.zakatType,
                                calculation_date: this.calcDate,
                                gold_price: this.goldPrice,
                                year_method: this.yearMethod,
                                haul_status: this.haulStatus,
                                assets_total: this.totalAssets,
                                deductions_total: this.totalDeductions,
                                note: this.calcNote,
                                items: this.items,
                                finalize: finalize
                            })
                        });

                        const res = await response.json();
                        if (res.success) {
                            window.location.href = '{{ route("zakat.history") }}';
                        } else {
                            alert(res.message || 'Gagal menyimpan kalkulasi.');
                        }
                    } catch (e) {
                        alert('Terjadi kesalahan jaringan.');
                    } finally {
                        this.isSaving = false;
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
