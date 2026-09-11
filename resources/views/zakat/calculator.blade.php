<x-app-layout>
  <div class="ph">
    <div>
      <div class="chip gold" style="margin-bottom:8px">PENGHITUNG ZAKAT</div>
      <h1 class="pt">Hitung Zakat</h1>
      <p class="ps">Pilih muzakki → isi harta → cek nisab & haul → simpan hasil.</p>
    </div>
    <div>
      <button class="btn btn-s" onclick="location.reload()">Reset</button>
    </div>
  </div>

  <div class="steps">
    <div class="step a">1. Muzakki</div>
    <div class="step a">2. Jenis</div>
    <div class="step a">3. Harta</div>
    <div class="step a">4. Nisab & Haul</div>
    <div class="step a">5. Hasil</div>
  </div>

  <div class="calc" x-data="zakatCalculator()" x-init="init()">
    <div>
      <!-- 1. Muzakki -->
      <div class="card sec">
        <div class="sh">
          <h3>1. Pilih Muzakki</h3>
          <span>Terhubung ke CRM</span>
        </div>
        <div class="fg">
          <div class="field fulls">
            <label>Pilih Kontak</label>
            <select class="input" x-model="contactId" @change="syncContact()">
              <option value="">-- Pilih Donatur / Muzakki --</option>
              @foreach($contacts as $c)
                <option value="{{ $c->id }}" data-phone="{{ $c->phone }}" {{ ($selectedContact?->id === $c->id) ? 'selected' : '' }}>
                  {{ $c->name }} · {{ $c->phone }} · {{ $c->niss }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label>Tanggal Perhitungan</label>
            <input class="input" type="date" x-model="calcDate">
          </div>
          <div class="field">
            <label>Admin / CS</label>
            <input class="input" value="{{ auth()->user()->name }}" disabled>
          </div>
          <div class="field fulls">
            <label>No. WA & NISS</label>
            <input class="input" x-model="contactPhone" disabled placeholder="Otomatis terisi saat kontak dipilih">
          </div>
        </div>
      </div>

      <!-- 2. Jenis Zakat -->
      <div class="card sec" style="margin-top:16px">
        <div class="sh">
          <h3>2. Jenis Zakat</h3>
          <span>Pilih engine</span>
        </div>
        <div class="fg">
          <div class="field">
            <label>Jenis Perhitungan</label>
            <select class="input" x-model="zakatType" @change="recalc()">
              <option>Zakat Maal</option>
              <option>Zakat Perdagangan</option>
              <option>Zakat Tabungan</option>
              <option>Zakat Emas & Perak</option>
              <option>Zakat Penghasilan</option>
              <option>Zakat Investasi</option>
            </select>
          </div>
          <div class="field">
            <label>Metode Tahun</label>
            <select class="input" x-model="yearMethod" @change="recalc()">
              <option value="h">Hijriyah — 2,5%</option>
              <option value="m">Masehi — 2,5775%</option>
            </select>
          </div>
        </div>
      </div>

      <!-- 3. Harta -->
      <div class="card sec" style="margin-top:16px">
        <div class="sh">
          <h3>3. Harta yang Diperhitungkan</h3>
          <span>Accordion</span>
        </div>

        <div class="acc" x-data="{ open: true }">
          <button type="button" @click="open = !open">Aset Likuid & Simpanan <span x-text="open ? '▲' : '⌄'"></span></button>
          <div class="body" x-show="open">
            <div class="fg">
              <div class="field">
                <label>Uang Tunai</label>
                <div class="money"><span>Rp</span><input type="number" x-model.number="items.cash" @input="recalc()"></div>
              </div>
              <div class="field">
                <label>Saldo Bank</label>
                <div class="money"><span>Rp</span><input type="number" x-model.number="items.bank" @input="recalc()"></div>
              </div>
              <div class="field">
                <label>E-Wallet</label>
                <div class="money"><span>Rp</span><input type="number" x-model.number="items.ewallet" @input="recalc()"></div>
              </div>
              <div class="field">
                <label>Tabungan / Deposito</label>
                <div class="money"><span>Rp</span><input type="number" x-model.number="items.saving" @input="recalc()"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="acc" x-data="{ open: false }">
          <button type="button" @click="open = !open">Usaha & Piutang <span x-text="open ? '▲' : '⌄'"></span></button>
          <div class="body" x-show="open" x-cloak>
            <div class="fg">
              <div class="field">
                <label>Kas Usaha</label>
                <div class="money"><span>Rp</span><input type="number" x-model.number="items.business" @input="recalc()"></div>
              </div>
              <div class="field">
                <label>Stok Dagangan</label>
                <div class="money"><span>Rp</span><input type="number" x-model.number="items.stock" @input="recalc()"></div>
              </div>
              <div class="field fulls">
                <label>Piutang Tertagih</label>
                <div class="money"><span>Rp</span><input type="number" x-model.number="items.receivable" @input="recalc()"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="acc" x-data="{ open: false }">
          <button type="button" @click="open = !open">Emas, Perak & Investasi <span x-text="open ? '▲' : '⌄'"></span></button>
          <div class="body" x-show="open" x-cloak>
            <div class="fg">
              <div class="field">
                <label>Nilai Emas</label>
                <div class="money"><span>Rp</span><input type="number" x-model.number="items.goldVal" @input="recalc()"></div>
              </div>
              <div class="field">
                <label>Nilai Perak</label>
                <div class="money"><span>Rp</span><input type="number" x-model.number="items.silverVal" @input="recalc()"></div>
              </div>
              <div class="field fulls">
                <label>Nilai Investasi</label>
                <div class="money"><span>Rp</span><input type="number" x-model.number="items.invest" @input="recalc()"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="acc" x-data="{ open: true }">
          <button type="button" @click="open = !open">Pengurang yang Relevan <span x-text="open ? '▲' : '⌄'"></span></button>
          <div class="body" x-show="open">
            <div class="fg">
              <div class="field">
                <label>Utang Jatuh Tempo</label>
                <div class="money"><span>Rp</span><input type="number" x-model.number="items.debt" @input="recalc()"></div>
              </div>
              <div class="field">
                <label>Kewajiban Jangka Pendek</label>
                <div class="money"><span>Rp</span><input type="number" x-model.number="items.shortObligation" @input="recalc()"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 4. Nisab & Haul -->
      <div class="card sec" style="margin-top:16px">
        <div class="sh">
          <h3>4. Nisab & Haul</h3>
          <span>Validasi akhir</span>
        </div>
        <div class="fg">
          <div class="field">
            <label>Harga Emas / Gram</label>
            <div class="money"><span>Rp</span><input type="number" x-model.number="goldPrice" @input="recalc()"></div>
          </div>
          <div class="field">
            <label>Nisab Otomatis</label>
            <input class="input" :value="formatMoney(nisab)" disabled>
          </div>
          <div class="field fulls">
            <label>Status Haul</label>
            <div class="chips">
              <button type="button" class="chip" :class="haulStatus === 'yes' ? 'a' : ''" @click="haulStatus = 'yes'; recalc()">Sudah memenuhi</button>
              <button type="button" class="chip" :class="haulStatus === 'no' ? 'a' : ''" @click="haulStatus = 'no'; recalc()">Belum memenuhi</button>
              <button type="button" class="chip" :class="haulStatus === 'na' ? 'a' : ''" @click="haulStatus = 'na'; recalc()">Tidak berlaku / khusus</button>
            </div>
          </div>
          <div class="field fulls">
            <label>Catatan Dasar Perhitungan</label>
            <textarea class="input" x-model="calcNote" placeholder="Contoh: Harta telah mencapai nisab dan haul."></textarea>
          </div>
        </div>
        <div class="q" style="margin-top:14px">
          <button type="button" class="btn btn-r" @click="saveCalc(true)" :disabled="isSaving">Finalisasi Hasil</button>
          <button type="button" class="btn btn-s" @click="saveCalc(false)" :disabled="isSaving">Simpan Draft</button>
        </div>
      </div>
    </div>

    <!-- Right Side Summary -->
    <div class="card sec">
      <div class="chip gold">Ringkasan Real-Time</div>
      <div style="font-size:20px;font-weight:800;margin-top:10px" x-text="zakatType"></div>
      <div class="sum" style="margin-top:12px">
        <div class="srow"><span>Total Harta</span><b x-text="formatMoney(totalAssets)"></b></div>
        <div class="srow"><span>Total Pengurang</span><b x-text="formatMoney(totalDeductions)"></b></div>
        <div class="srow"><span>Harta Bersih</span><b x-text="formatMoney(netAmount)"></b></div>
        <div class="srow"><span>Nisab</span><b x-text="formatMoney(nisab)"></b></div>
        <div class="srow"><span>Tarif</span><b x-text="ratePercent"></b></div>
      </div>
      <div class="box" :class="isWajib ? 'ok' : ''" x-text="statusText"></div>
      <div class="sumr">
        <div class="small" style="opacity:.76">Estimasi zakat yang perlu ditunaikan</div>
        <div style="font-size:30px;font-weight:800;margin:6px 0 2px" x-text="formatMoney(zakatAmount)"></div>
        <div class="small" x-text="formulaText"></div>
      </div>
      <div class="footnote">
        Perhitungan otomatis ini mengacu pada ketentuan nisab 85 gram emas murni serta haul 1 tahun Hijriyah / Masehi.
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
    function zakatCalculator() {
      return {
        contactId: '{{ $selectedContact?->id ?? "" }}',
        contactPhone: '{{ $selectedContact ? $selectedContact->phone . " · " . $selectedContact->niss : "" }}',
        calcDate: '{{ now()->toDateString() }}',
        zakatType: 'Zakat Maal',
        yearMethod: 'h',
        haulStatus: 'yes',
        goldPrice: {{ $defaultGoldPrice }},
        calcNote: '',
        isSaving: false,
        items: { cash: 0, bank: 0, ewallet: 0, saving: 0, business: 0, stock: 0, receivable: 0, goldVal: 0, silverVal: 0, invest: 0, debt: 0, shortObligation: 0 },
        totalAssets: 0, totalDeductions: 0, netAmount: 0, nisab: 0, rate: 0.025, isWajib: false, zakatAmount: 0, statusText: '', formulaText: '—',

        init() { this.recalc(); },
        formatMoney(amount) { return 'Rp ' + Number(amount || 0).toLocaleString('id-ID'); },
        get ratePercent() { return (this.rate * 100).toLocaleString('id-ID', { maximumFractionDigits: 4 }) + '%'; },

        syncContact() {
          const select = document.querySelector('select[x-model="contactId"]');
          const opt = select.options[select.selectedIndex];
          this.contactPhone = opt && opt.value ? opt.text : '';
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
            this.statusText = 'Harga emas belum diisi, sehingga nisab belum dapat ditentukan.';
          } else if (!isNisabMet) {
            this.statusText = 'Belum wajib: harta bersih belum mencapai nisab.';
          } else if (!isHaulMet) {
            this.statusText = 'Belum wajib: harta mencapai nisab, tetapi haul belum terpenuhi.';
          } else {
            this.statusText = 'Wajib zakat: harta mencapai nisab dan syarat haul terpenuhi / tidak berlaku.';
          }

          this.formulaText = this.zakatAmount > 0
            ? `${this.formatMoney(this.netAmount)} × ${this.ratePercent}`
            : '—';
        },

        async saveCalc(finalize) {
          if (!this.contactId) {
            alert('Pilih muzakki terlebih dahulu');
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
            alert('Terjadi kesalahan koneksi.');
          } finally {
            this.isSaving = false;
          }
        }
      };
    }
  </script>
  @endpush
</x-app-layout>
