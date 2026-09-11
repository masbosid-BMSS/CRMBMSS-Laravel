# CRM BMSS — Baitulmaal Sejuta Santri (Laravel Production-Ready)

Aplikasi web CRM BMSS dibangun dengan arsitektur **Laravel 11**, **MySQL/MariaDB** (dan SQLite ready untuk testing/local), **Blade Components**, **Tailwind CSS**, **Alpine.js**, dan **Chart.js** yang mempertahankan 100% identitas visual, alur kerja, dan logika bisnis functional prototype aslinya.

---

## Fitur & Modul Utama

1. **Authentication & Role Policy**:
   - Master Admin (`bmssmanfaat` / `bismillah100`): Full access manajemen user, transfer database, KPI/target CS, multi-WA management, payment methods, campaign/program global, settings, export laporan.
   - Admin / CS (`nisa.cs`, `rani.cs`, dll / `admin12345`): Data isolation untuk portfolio miliknya (read/write), view-only pada database rekan CS.
2. **NISS Generator**:
   - Format standar `NISS-00000001` dengan atomic database locking (`niss_sequences`) untuk mengeliminasi race-condition pada transaksi bersamaan.
3. **Zakat Center & Calculator Terintegrasi**:
   - Jenis zakat: Zakat Maal, Perdagangan, Tabungan, Emas/Perak, Penghasilan, Investasi.
   - Perhitungan nisab otomatis (85 gram emas) dengan harga acuan dinamis.
   - Pilihan haul Hijriyah (2,5%) vs Masehi (2,5775%).
   - Simpan kalkulasi, pembuatan status kewajiban (Outstanding, Sebagian, Lunas, Belum Wajib), dan integrasi langsung ke pencatatan transaksi pembayaran.
4. **Fundraising & Transaksi Dana**:
   - Ledger transaksi (Zakat, Infak, Sedekah, Wakaf) dengan update otomatis LTV donatur dan sisa zakat.
   - Pilihan metode pembayaran yang dikelola Master Admin (BCA, Mandiri, QRIS, Tunai, BSI).
   - Campaign Global dan Program Penyaluran.
5. **Lead & Pipeline (Kanban)**:
   - Tahapan: `Lead Baru`, `Contacted`, `Interested`, `Follow-up`, `Donasi`.
6. **Follow-up Management**:
   - Status otomatis `Today`, `Overdue`, `Scheduled`, `Completed`.
   - Opsi auto-create follow-up pengingat langsung saat menambah kontak baru.
7. **CS & Nomor WhatsApp Management**:
   - Maksimal 5 slot nomor WhatsApp per CS dengan pelacakan kapasitas (3.000–5.000 database).
   - Generator link WhatsApp wa.me dengan validasi status relasi (`Blokir` otomatis dicegah).
8. **Dashboard & Cutoff Report (15:31 – 15:30)**:
   - Menghitung cut-off operasional harian (15:31 kemarin s.d. 15:30 hari ini, khusus Senin mencakup Sabtu 15:31).
   - KPI CS individual: perolehan infaq, capaian target %, efisiensi per 1K database, evaluasi kapasitas, dan saran otomatis.
   - Export Excel XLSX 6 Sheet: `RINGKASAN`, `KINERJA CS`, `SARAN & EVALUASI`, `PROGRAM`, `TRANSAKSI`, `LEADS`.

---

## Kredensial Bawaan (Default Demo & Production Baseline)

| Akun | Username | Password | Role |
| :--- | :--- | :--- | :--- |
| **Master Admin** | `bmssmanfaat` | `bismillah100` | Master Admin (Akses Penuh) |
| **CS Nisa** | `nisa.cs` | `admin12345` | Admin / CS (Tier 1) |
| **CS Rani** | `rani.cs` | `admin12345` | Admin / CS (Tier 2) |
| **CS Fikri** | `fikri.cs` | `admin12345` | Admin / CS (Tier 3) |
| **CS Dina** | `dina.cs` | `admin12345` | Admin / CS (Tier 3) |
| **CS Yusuf** | `yusuf.cs` | `admin12345` | Admin / CS (Tier 2) |

---

## Cara Menjalankan Aplikasi

### 1. Menjalankan via Helper Docker (Langsung Siap Pakai)
Jika di host lokal belum terpasang PHP 8.2+:
```bash
# Jalankan container backend (atau via helper ./artisan-docker)
./artisan-docker serve --host=0.0.0.0 --port=8000
```
Buka browser di `http://localhost:8000`.

### 2. Menjalankan di Server / Environment PHP Native
```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### 3. Import Dataset Demo Skala Penuh
Untuk memasukkan 100+ kontak realistis, riwayat zakat, 150+ transaksi dana, dan follow-up:
```bash
./artisan-docker bmss:import-demo
# atau jika native:
# php artisan bmss:import-demo
```

### 4. Menjalankan Automated Test Suite
```bash
./artisan-docker test
# atau
# php artisan test
```
