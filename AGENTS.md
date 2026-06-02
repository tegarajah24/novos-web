# 🤖 AGENTS.md — Panduan AI Agent untuk Project Novos

---

## 🎯 Tentang Project Ini

Nama project: **Novos Web**
Jenis bisnis: Konveksi / Custom Jersey
Tujuan: Sistem pemesanan jersey custom online lengkap dengan manajemen produksi internal.

---

## 🛠️ Stack Teknologi

| Layer | Teknologi |
|---|---|
| Framework | Laravel (PHP) |
| Frontend | Blade + Tailwind CSS + DaisyUI |
| Icons | Lucide |
| Alert / Dialog | SweetAlert2 |
| Admin Panel | Filament |
| Payment | Midtrans |
| Database | MySQL |
| Auth | Laravel Breeze |

> Jangan mengganti stack ini tanpa instruksi eksplisit.

---

## 📁 Struktur Dokumentasi

Sebelum mengerjakan tugas apapun, baca file berikut sesuai konteks:

| File | Kapan Dibaca |
|---|---|
| `docs/fitur.md` | Saat mengerjakan fitur baru |
| `docs/flow-pesanan.md` | Saat mengerjakan apapun terkait pesanan |
| `docs/database.md` | Saat membuat/mengubah migration, model, atau query |
| `docs/role-user.md` | Saat mengerjakan auth, middleware, atau akses halaman |
| `docs/status-pesanan.md` | Saat mengubah/menampilkan status pesanan |
| `docs/catatan-client.md` | Saat ada hal yang belum jelas dari requirement |

---

## 🗄️ Aturan Database

- Struktur database ada di `docs/database.md` — ikuti kolom yang sudah ditentukan
- Jangan tambah kolom baru tanpa instruksi eksplisit
- Setiap perubahan status pesanan **wajib** dicatat di `order_status_histories`
- Gunakan `enum` untuk kolom status, bukan string bebas
- Semua foreign key wajib pakai `constrained()->cascadeOnDelete()` kecuali ada alasan lain

---

## 👤 Aturan Role & Auth

- Role tersimpan di tabel `roles`, relasi via `role_id` di tabel `users`
- Middleware role ada di `App\Http\Middleware\RoleMiddleware`
- Penggunaan di route: `middleware(['auth', 'role:Admin,Super Admin'])`
- Daftar role: `Super Admin`, `Manager`, `Admin`, `Design`, `Produksi`, `Customer`
- Jangan hardcode nama role di logika bisnis — selalu ambil dari relasi `user->role->name`

---

## 📦 Aturan Pesanan

- Flow lengkap ada di `docs/flow-pesanan.md`
- Status pesanan hanya boleh berubah sesuai alur yang sudah ditentukan
- Nomor pesanan format: `NVS-YYYYMMDD-XXX` (contoh: `NVS-20240601-001`)
- Setiap pesanan custom selalu punya satu `design_request` terkait
- Harga diinput manual oleh Admin, bukan otomatis

---

## 🎨 Aturan Frontend

- Gunakan komponen DaisyUI sebisa mungkin sebelum membuat custom CSS
- Warna badge status pesanan sudah ditentukan di `docs/status-pesanan.md`
- Semua konfirmasi aksi penting (hapus, batalkan, ACC) gunakan SweetAlert2
- Gunakan Lucide untuk semua icon
- Layout admin dan customer **harus terpisah** (layout berbeda)

---

## 🏗️ Aturan Arsitektur Laravel

- Gunakan pola MVC — logika bisnis di Controller atau Service, bukan di Blade
- Pisahkan route berdasarkan role di file terpisah:
  ```
  routes/
  ├── web.php         ← route umum & auth
  ├── customer.php    ← route khusus customer
  ├── admin.php       ← route khusus admin & internal
  ```
- Validasi semua input menggunakan Form Request (`php artisan make:request`)
- Gunakan Eloquent relationship, jangan query manual kalau bisa dihindari

---

## 🔐 Aturan Keamanan

- Semua route selain login/register wajib pakai middleware `auth`
- Route per role wajib pakai middleware `role:`
- Jangan expose data user lain ke customer
- Semua file upload (logo desain) disimpan di `storage/app/public` dan divalidasi tipenya
- Gunakan `.env` untuk semua kredensial (Midtrans, DB, dll)

---

## 🧠 Cara Menerima Instruksi

Saat menerima tugas:
1. Baca file dokumentasi yang relevan di `docs/`
2. Cek file yang sudah ada — jangan buat ulang yang sudah ada
3. Kerjakan sekecil dan sesederhana mungkin
4. Ikuti konvensi yang sudah dipakai di project ini
5. Jangan install package baru tanpa konfirmasi

---

## 🚫 Yang Tidak Boleh Dilakukan

- Mengganti stack teknologi
- Mengubah struktur tabel yang sudah ada tanpa instruksi
- Membuat halaman admin tanpa middleware role
- Hardcode status pesanan sebagai string bebas
- Membuat logika bisnis langsung di file Blade
- Mengabaikan relasi Eloquent yang sudah didefinisikan di model

---

## ✅ Definisi "Selesai"

Sebuah fitur dianggap selesai jika:
- Berfungsi sesuai flow di `docs/flow-pesanan.md`
- Hak akses sudah benar sesuai `docs/role-user.md`
- Tidak ada error di console / log Laravel
- Tampilan menggunakan komponen DaisyUI yang konsisten
