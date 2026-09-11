<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('contacts.index') }}" class="text-xs font-bold text-[#343A72] hover:underline mb-2 inline-block">← Kembali ke Semua Kontak</a>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Tambah Kontak Donatur</h1>
            <p class="text-xs text-gray-500 mt-1">NISS akan dibuat otomatis secara atomic untuk mencegah duplikasi.</p>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 md:p-8 shadow-bm-sm">
            <form action="{{ route('contacts.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nama Lengkap Donatur *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Bpk. Ahmad Fauzi" class="w-full h-11 px-3.5 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">WhatsApp / No. Telepon *</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 081234567890" class="w-full h-11 px-3.5 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kota Domisili</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="Contoh: Bandung" class="w-full h-11 px-3.5 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Sumber Kontak</label>
                        <input type="text" name="source" value="{{ old('source') }}" placeholder="Contoh: Ngalamat / Meta Ads / Referral" class="w-full h-11 px-3.5 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Status Donatur</label>
                        <select name="status" class="w-full h-11 px-3.5 bg-white border border-[#E1E3EC] rounded-xl text-xs font-semibold">
                            <option value="Baru" {{ old('status') === 'Baru' ? 'selected' : '' }}>Baru</option>
                            <option value="Aktif" {{ old('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Loyal" {{ old('status') === 'Loyal' ? 'selected' : '' }}>Loyal</option>
                            <option value="At Risk" {{ old('status') === 'At Risk' ? 'selected' : '' }}>At Risk</option>
                            <option value="Dormant" {{ old('status') === 'Dormant' ? 'selected' : '' }}>Dormant</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Status Relasi</label>
                        <select name="relation_status" class="w-full h-11 px-3.5 bg-white border border-[#E1E3EC] rounded-xl text-xs font-semibold">
                            <option value="Normal" {{ old('relation_status') === 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="Blokir" {{ old('relation_status') === 'Blokir' ? 'selected' : '' }}>Blokir (Do Not Contact)</option>
                            <option value="Untrust" {{ old('relation_status') === 'Untrust' ? 'selected' : '' }}>Untrust (Kepercayaan Menurun)</option>
                            <option value="Bosan" {{ old('relation_status') === 'Bosan' ? 'selected' : '' }}>Bosan (Komunikasi Berulang)</option>
                        </select>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Program Favorit / Minat</label>
                        <select name="program" class="w-full h-11 px-3.5 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            <option value="">Pilih Program</option>
                            @foreach(\App\Models\Program::where('status', 'Aktif')->get() as $pr)
                                <option value="{{ $pr->name }}" {{ old('program') === $pr->name ? 'selected' : '' }}>{{ $pr->name }} ({{ $pr->category }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">NISS Kustom (Opsional)</label>
                        <input type="text" name="niss" value="{{ old('niss') }}" placeholder="Kosongkan untuk NISS otomatis" class="w-full h-11 px-3.5 bg-white border border-[#E1E3EC] rounded-xl text-xs font-mono">
                    </div>
                </div>

                @can('is-master')
                <div class="grid sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-200">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Tugaskan ke CS (Owner)</label>
                        <select name="owner_id" class="w-full h-11 px-3.5 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            @foreach(\App\Models\User::where('role', 'admin')->where('status', 'active')->get() as $cs)
                                <option value="{{ $cs->id }}" {{ old('owner_id', auth()->id()) === $cs->id ? 'selected' : '' }}>{{ $cs->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Slot Nomor WhatsApp CS</label>
                        <select name="wa_account_id" class="w-full h-11 px-3.5 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            <option value="">Pilih nomor WhatsApp CS</option>
                            @foreach(\App\Models\WaAccount::where('status', 'Aktif')->with('user')->get() as $wa)
                                <option value="{{ $wa->id }}" {{ old('wa_account_id') === $wa->id ? 'selected' : '' }}>
                                    {{ $wa->user?->name }} · {{ $wa->label }} ({{ $wa->phone ?: 'belum diisi' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endcan

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Catatan Relasi / Donatur</label>
                    <textarea name="relationship_note" rows="2" placeholder="Catatan kondisi donatur, waktu respon yang disukai, dll." class="w-full p-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">{{ old('relationship_note') }}</textarea>
                </div>

                <!-- Follow-up Auto Creator -->
                <div class="p-4 rounded-2xl bg-[#EEF0F8] border border-[#dce2f2]" x-data="{ makeFu: true }">
                    <label class="flex items-center gap-2 font-bold text-[#343A72] text-xs cursor-pointer">
                        <input type="checkbox" name="make_followup" value="1" x-model="makeFu" class="w-4 h-4 rounded text-[#343A72] focus:ring-[#343A72] border-gray-300">
                        <span>Buat jadwal follow-up otomatis setelah kontak disimpan</span>
                    </label>

                    <div x-show="makeFu" class="grid sm:grid-cols-2 gap-3 mt-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Waktu Follow-up</label>
                            <input type="datetime-local" name="followup_date" value="{{ now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i') }}" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Judul / Alasan</label>
                            <input type="text" name="followup_title" value="Follow-up sapa kontak baru dan perkenalkan BMSS" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('contacts.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-[#343A72] text-white font-extrabold rounded-xl hover:bg-[#252B5B] transition shadow-sm">
                        Simpan Kontak
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
