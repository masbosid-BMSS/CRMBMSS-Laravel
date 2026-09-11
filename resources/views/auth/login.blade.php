<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — CRM BMSS Baitulmaal Sejuta Santri</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-tr from-[#fafbff] to-[#fff7f8] font-sans antialiased text-[#242842]">
    <div class="min-h-screen grid lg:grid-cols-[1fr_420px]">
        <!-- Hero Left Side -->
        <div class="p-8 md:p-14 flex items-center justify-center">
            <div class="max-w-2xl w-full">
                <!-- Brand Title -->
                <div class="flex items-center gap-5 mb-8">
                    <div class="w-20 h-20 rounded-3xl bg-[#343A72] text-white flex flex-col items-center justify-center font-black shadow-bmss">
                        <span class="text-2xl leading-none">BM</span>
                        <span class="text-[10px] tracking-widest text-[#FFC316]">SS</span>
                    </div>
                    <div>
                        <h1 class="text-4xl md:text-5xl font-extrabold text-[#343A72] tracking-tight leading-none">
                            CRM <span class="text-[#FF303B]">BMSS</span>
                        </h1>
                        <p class="text-sm md:text-base text-gray-500 mt-2 font-medium">
                            Baitulmaal Sejuta Santri · Sistem Manajemen Hubungan Donatur, Zakat, & Fundraising
                        </p>
                    </div>
                </div>

                <!-- Hero Feature Cards -->
                <div class="grid sm:grid-cols-2 gap-4 mt-6">
                    <div class="bg-white/80 backdrop-blur-md border border-[#343A72]/10 p-5 rounded-2xl shadow-bm-sm">
                        <b class="text-sm font-bold text-[#252B5B]">Dashboard Role-Based</b>
                        <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                            Master Admin memantau seluruh database. Admin/CS fokus ke portfolio kontak dan target miliknya.
                        </p>
                    </div>
                    <div class="bg-white/80 backdrop-blur-md border border-[#343A72]/10 p-5 rounded-2xl shadow-bm-sm">
                        <b class="text-sm font-bold text-[#252B5B]">Penghitung Zakat Terintegrasi</b>
                        <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                            Kalkulasi nisab & haul terhubung langsung ke profil muzakki dan pencatatan transaksi dana.
                        </p>
                    </div>
                    <div class="bg-white/80 backdrop-blur-md border border-[#343A72]/10 p-5 rounded-2xl shadow-bm-sm">
                        <b class="text-sm font-bold text-[#252B5B]">Data Isolation & NISS</b>
                        <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                            Hak akses mutlak di backend. NISS menjamin nomor identitas tunggal bebas race condition.
                        </p>
                    </div>
                    <div class="bg-white/80 backdrop-blur-md border border-[#343A72]/10 p-5 rounded-2xl shadow-bm-sm">
                        <b class="text-sm font-bold text-[#252B5B]">Multi-WA & Capacity Slot</b>
                        <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                            Maksimal 5 nomor WhatsApp per CS dengan batas kapasitas 3.000–5.000 database.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Card Right Side -->
        <div class="bg-white/60 backdrop-blur-md border-t lg:border-t-0 lg:border-l border-[#343A72]/10 flex items-center justify-center p-6 md:p-10">
            <div class="bg-white border border-[#E1E3EC] rounded-[24px] shadow-bmss p-7 w-full max-w-sm">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-xl bg-[#343A72] text-white font-extrabold grid place-items-center text-sm">
                        BM
                    </div>
                    <div>
                        <b class="block text-sm font-bold text-[#252B5B]">Internal BMSS</b>
                        <span class="text-xs text-gray-400">Silakan login untuk melanjutkan</span>
                    </div>
                </div>

                @if($errors->any())
                <div class="p-3 mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-semibold">
                    {{ $errors->first() }}
                </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="username" class="block text-xs font-extrabold text-[#252B5B] mb-1.5">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus placeholder="Masukkan username" class="w-full h-11 px-3.5 bg-white border border-[#E1E3EC] rounded-xl text-sm focus:border-[#343A72] focus:ring-0 transition">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-extrabold text-[#252B5B] mb-1.5">Password</label>
                        <input type="password" name="password" id="password" required placeholder="Masukkan password" class="w-full h-11 px-3.5 bg-white border border-[#E1E3EC] rounded-xl text-sm focus:border-[#343A72] focus:ring-0 transition">
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center gap-2 text-gray-600 font-medium">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#343A72] focus:ring-[#343A72] border-gray-300">
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full h-11 rounded-xl bg-[#343A72] text-white font-extrabold text-sm hover:bg-[#252B5B] transition shadow-bm-sm">
                        Masuk
                    </button>
                </form>

                <div class="mt-6 p-4 rounded-2xl bg-gradient-to-tr from-[#EEF0F8] to-[#f9faff] border border-[#dce2f2] text-xs text-gray-500">
                    <b>Akun Bawaan Demo:</b><br>
                    • Master Admin: <code class="text-[#343A72] font-bold">bmssmanfaat</code> / <code class="text-[#343A72] font-bold">bismillah100</code><br>
                    • CS Nisa: <code class="text-[#343A72] font-bold">nisa.cs</code> / <code class="text-[#343A72] font-bold">admin12345</code>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
