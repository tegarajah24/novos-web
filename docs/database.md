# Struktur Database Novos

## 👤 Autentikasi & Pengguna

### `roles`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| name | string | Super Admin, Manager, Admin, Design, Produksi, Customer |
| timestamps | | |

### `users`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| role_id | foreignId | relasi ke roles |
| name | string | |
| email | string, unique | |
| password | string | |
| phone | string, nullable | |
| address | text, nullable | |
| email_verified_at | timestamp, nullable | |
| remember_token | string, nullable | |
| timestamps | | |

---

## 🛍️ Produk & Katalog

### `categories`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| name | string | |
| attributes_schema | json, nullable | Struktur atribut kustomisasi dinamis per kategori |
| timestamps | | |

### `products`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| category_id | foreignId | relasi ke categories |
| name | string | |
| description | text, nullable | |
| price | decimal(10,2) | |
| image | string, nullable | |
| min_qty | integer, default 1 | minimum order |
| production_days | integer, nullable | estimasi hari produksi |
| is_active | boolean, default true | |
| theme_color | string, nullable | warna tema |
| kerah | string, nullable | jenis kerah (contoh: O-NECK V.1) - legacy |
| bahan | string, nullable | bahan jersey (contoh: MILANO PREMIUM) - legacy |
| jenis_potongan | string, nullable | jenis potongan (contoh: REGULER) - legacy |
| lengan_jahitan | string, nullable | model lengan & jahitan (contoh: REGULER OVERDECK) - legacy |
| product_attributes | json, nullable | Nilai atribut bawaan produk dinamis |
| timestamps | | |

---

## 📦 Pesanan

### `orders`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| user_id | foreignId | relasi ke users (customer) |
| order_number | string, unique | contoh: NVS-20240601-001 |
| status | enum | menunggu_validasi, menunggu_pembayaran, dikonfirmasi, disetujui, di_design, siap_cetak, diproduksi, selesai, dibatalkan |
| notes | text, nullable | catatan dari customer |
| admin_notes | text, nullable | catatan internal admin |
| total_price | decimal(10,2) | |
| confirmed_at | timestamp, nullable | waktu customer ACC |
| timestamps | | |

### `order_items`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| order_id | foreignId | relasi ke orders |
| size | string | S, M, L, XL, XXL, dll |
| qty | integer | |
| price_per_item | decimal(10,2) | |
| subtotal | decimal(10,2) | |
| timestamps | | |

### `order_status_histories`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| order_id | foreignId | relasi ke orders |
| status | string | status baru |
| changed_by | foreignId | user_id yang mengubah |
| notes | text, nullable | alasan perubahan |
| timestamps | | |

---

## 🎨 Desain

### `design_requests`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| order_id | foreignId | relasi ke orders |
| team_name | string | nama tim |
| nama_artikel | string, nullable | nama artikel |
| nama_pemesan | string, nullable | nama pemesan |
| logo | string, nullable | file path logo |
| primary_color | string | warna utama |
| secondary_color | string, nullable | warna kedua |
| motif | string, nullable | motif yang diinginkan |
| material | string, nullable | bahan jersey - legacy |
| collar_style | string, nullable | bentuk kerah - legacy |
| additional_notes | text, nullable | catatan tambahan |
| customizations | json, nullable | Data kustomisasi pesanan dinamis |
| timestamps | | |

---

## 💳 Pembayaran

### `payments`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| order_id | foreignId | relasi ke orders |
| midtrans_order_id | string, unique | ID yang dikirim ke Midtrans |
| midtrans_transaction_id | string, nullable | ID dari Midtrans |
| amount | decimal(10,2) | |
| status | enum | pending, success, failed, expired |
| payment_method | string, nullable | gopay, bca, dll |
| paid_at | timestamp, nullable | |
| timestamps | | |

---

## 🏭 Produksi

### `production_tasks`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| order_id | foreignId | relasi ke orders |
| assigned_to | foreignId | user_id pegawai produksi |
| status | enum | pending, dikerjakan, selesai |
| started_at | timestamp, nullable | |
| finished_at | timestamp, nullable | |
| notes | text, nullable | |
| timestamps | | |

---

## 💬 Chat

### `chats`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| order_id | foreignId | relasi ke orders |
| customer_id | foreignId | user_id customer |
| admin_id | foreignId, nullable | user_id admin yang handle |
| timestamps | | |

### `chat_messages`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| chat_id | foreignId | relasi ke chats |
| sender_id | foreignId | user_id pengirim |
| message | text | |
| file_path | string, nullable | path file di storage |
| file_name | string, nullable | nama asli file |
| file_size | unsignedBigInteger, nullable | ukuran file dalam bytes |
| file_type | string, nullable | MIME type file |
| is_read | boolean, default false | |
| timestamps | | |

---

## 🧠 Mental Health & Well-being

### `daily_mental_checks`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| user_id | foreignId | relasi ke users (pegawai) |
| check_date | date | tanggal check-in |
| answers | json | jawaban kuesioner (5 pertanyaan) |
| total_score | integer | total skor |
| category | string | baik, perlu_perhatian, perlu_pendampingan |
| need_help | boolean, default false | |
| help_note | text, nullable | catatan jika butuh bantuan |
| timestamps | | |

### `micro_breaks`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| user_id | foreignId | relasi ke users (pegawai) |
| check_date | date | tanggal aktivitas |
| checklist | json | 8 item checklist aktivitas |
| score | integer | skor total |
| level | string | rendah, sedang, tinggi |
| eval | json | 3 pertanyaan evaluasi diri |
| catatan_membantu | text, nullable | |
| catatan_kendala | text, nullable | |
| timestamps | | |

---

## Relasi Ringkasan

```
roles → users (hasMany)
users → orders (hasMany)
orders → order_items (hasOne)
orders → design_requests (hasOne)
orders → payments (hasOne)
orders → order_status_histories (hasMany)
orders → production_tasks (hasOne)
orders → chats (hasOne)
chats → chat_messages (hasMany)
daily_mental_checks → users (belongsTo)
micro_breaks → users (belongsTo)
```
