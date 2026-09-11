<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6" x-data="{ txModal: false }">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Transaksi Dana</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Pencatatan real-time penerimaan zakat, infak, sedekah, dan wakaf donatur.
            </p>
        </div>
        <div>
            <button type="button" @click="txModal = true" class="h-11 px-5 rounded-xl bg-[#343A72] text-white text-xs font-extrabold flex items-center gap-2 hover:bg-[#252B5B] transition shadow-sm">
                <span>＋</span>
                <span>Input Transaksi Baru</span>
            </button>
        </div>

        <!-- Modal Tambah Transaksi -->
        <div x-show="txModal" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="txModal = false">
            <div class="bg-white rounded-[24px] border border-[#E1E3EC] shadow-bmss p-6 max-w-md w-full max-h-[90vh] overflow-y-auto">
                <h3 class="font-black text-lg text-[#252B5B]">Input Transaksi Dana</h3>
                <p class="text-xs text-gray-400 mb-3">Tercatat ke ledger internal dan menambah LTV donatur.</p>
                <form action="{{ route('transactions.store') }}" method="POST" class="space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Pilih Kontak Donatur *</label>
                        <select name="contact_id" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-medium">
                            <option value="">-- Pilih Kontak --</option>
                            @foreach(\App\Models\Contact::active()->orderBy('name')->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->niss }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Jenis Dana *</label>
                            <select name="type" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-semibold">
                                <option value="Infak">Infak</option>
                                <option value="Zakat">Zakat</option>
                                <option value="Sedekah">Sedekah</option>
                                <option value="Wakaf">Wakaf</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Nominal (Rp) *</label>
                            <input type="number" name="amount" required min="1000" step="1000" placeholder="100000" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-bold text-[#343A72]">
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Metode Pembayaran *</label>
                        <select name="payment_method" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->name }}">{{ $pm->name }} ({{ $pm->detail ?: $pm->category }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Program</label>
                        <select name="program" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            <option value="">-- Pilih Program --</option>
                            @foreach($programs as $pr)
                                <option value="{{ $pr->name }}">{{ $pr->name }} ({{ $pr->category }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Campaign Global (Opsional)</label>
                        <select name="campaign_id" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            <option value="">Tanpa Campaign Khusus</option>
                            @foreach($campaigns as $cp)
                                <option value="{{ $cp->id }}">{{ $cp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="txModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B]">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Table Transactions -->
    <div class="bg-white border border-[#E1E3EC] rounded-[24px] shadow-bm-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-[#FBFBFE] text-gray-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-[#E1E3EC]">
                        <th class="py-3.5 px-4">ID Transaksi</th>
                        <th class="py-3.5 px-3">Donatur</th>
                        <th class="py-3.5 px-3">CS Owner</th>
                        <th class="py-3.5 px-3">Jenis</th>
                        <th class="py-3.5 px-3">Program / Campaign</th>
                        <th class="py-3.5 px-3">Nominal (Rp)</th>
                        <th class="py-3.5 px-3">Metode</th>
                        <th class="py-3.5 px-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-[#343A72]">{{ $t->id }}</td>
                        <td class="py-3.5 px-3 font-bold text-[#252B5B]">
                            <a href="{{ route('contacts.show', $t->contact) }}" class="hover:underline">
                                {{ $t->contact?->name ?: '-' }}
                            </a>
                        </td>
                        <td class="py-3.5 px-3 font-medium">{{ $t->owner?->name ?: '-' }}</td>
                        <td class="py-3.5 px-3">
                            <span class="px-2 py-0.5 rounded-full font-bold text-[10px] {{ $t->type === 'Zakat' ? 'bg-[#FFF7D8] text-[#8b6500]' : 'bg-[#FFF0F2] text-[#FF303B]' }}">
                                {{ $t->type }}
                            </span>
                        </td>
                        <td class="py-3.5 px-3 font-medium text-gray-600">
                            {{ $t->program }}
                            @if($t->campaign)
                                <span class="block text-[10px] text-gray-400">◎ {{ $t->campaign->name }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-3 font-extrabold text-[#343A72]">
                            Rp {{ number_format($t->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-3 text-gray-500">{{ $t->payment_method }}</td>
                        <td class="py-3.5 px-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold s-active">
                                {{ $t->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">Belum ada data transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </div>
</x-app-layout>
