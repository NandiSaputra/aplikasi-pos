# 🖥️ Instalasi Lokal Aplikasi POS Laravel

Ikuti panduan di bawah ini untuk menginstal dan menjalankan aplikasi POS Laravel + Filament + Midtrans di laptop Anda (Windows).

---

## 1️⃣ Persyaratan Sistem

Pastikan sistem Anda telah terinstal:

-   PHP ≥ 8.2
-   Composer
-   Node.js & NPM
-   Git
-   Ngrok
-   MySQL / MariaDB
-   Web Server: **Laragon** atau **XAMPP**

---

## 2️⃣ Clone Project

Clone dari GitHub:

```bash
git clone https://github.com/NandiSaputra/aplikasi-pos.git
cd aplikasi-pos
```

Atau download dan ekstrak secara manual ke folder seperti `C:/laragon/www/aplikasi-pos`.

---

## 3️⃣ Install Dependencies

```bash
composer install
npm install && npm run dev
```

---

## 4️⃣ Setup Database

1. Jalankan XAMPP atau Laragon.
2. Buat database baru dengan nama `db_pos`.
3. Import file `db_pos.sql` ke database tersebut (bisa lewat phpMyAdmin).
4. Ubah file `.env`:
    - Salin `.env.example` ke `.env`
    - Atur konfigurasi database:

```env
DB_DATABASE=db_pos
DB_USERNAME=root
DB_PASSWORD=
```

---

## 5️⃣ Instalasi Ngrok

1. Download dan install Ngrok: [https://ngrok.com/downloads/windows](https://ngrok.com/downloads/windows)
2. Jalankan Ngrok di terminal:

```bash
tempat kamu simpan ngrok/ngrok http 8000
```

Salin link Forwarding `https://xxxx.ngrok.io` yang muncul.

---

## 6️⃣ Konfigurasi Midtrans

1. Daftar atau login di: [https://midtrans.com/](https://midtrans.com/)
2. Pilih **Sandbox Environment**
3. Masuk ke **Settings → Access Key**
4. Salin Server Key dan Client Key, lalu ubah di file `.env`:

```env
MIDTRANS_SERVER_KEY=Mid-server-xxx
MIDTRANS_CLIENT_KEY=Mid-client-xxx
MIDTRANS_IS_PRODUCTION=false
```

5. Di menu **Settings → Snap Settings → Payment Notification URL**, ubah jadi:

```
https://{LINK_NGROK_ANDA}/api/midtrans/webhook
```

Lalu klik **Save** dan lanjutkan.

---

## 7️⃣ Setup Public Folder

Jalankan perintah berikut untuk membuat symbolic link ke `storage`:

```bash
php artisan storage:link
php artisan key:generate
```

---

## 8️⃣ Jalankan Aplikasi

```bash
php artisan serve
```

> Buka browser: `http://127.0.0.1:8000`
> Buka browser untuk halaman admin: `http://127.0.0.1:8000/admin`

Lalu, buka terminal baru:

```bash
npm run dev
```

---

## ✅ Default Login

```
login admin
Email: admin@example.com
Password: password

login kasir
Email: nansa@gmail.com
Password: admin12345
```
