# Blog App

Blog App adalah aplikasi blog sederhana yang dibangun dengan Laravel. Aplikasi ini memungkinkan pengguna untuk membuat, mengedit, dan menghapus posting blog, dengan fitur khusus: kategori hanya bisa dikelola admin dan notifikasi email untuk post baru.

## Fitur

* Autentikasi pengguna (registrasi, login, logout)
* CRUD untuk posting blog
* **Kategori hanya bisa dibuat/diedit oleh admin**
* Pencarian posting berdasarkan judul dan konten
* Pengelolaan komentar
* **Email notifikasi saat dibuat post baru**

## Prasyarat

* PHP 8.0+
* Composer
* Database (MySQL, SQLite, atau lainnya)
* Mail server untuk notifikasi email (SMTP, Mailtrap, Gmail, dll.)
* Redis (opsional, untuk queue, direkomendasikan)

## Instalasi & Setup

1. **Clone repositori**

   ```bash
   git clone https://github.com/Koongh/blog-app.git
   cd blog-app
   ```

2. **Instal dependensi**

   ```bash
   composer install
   ```

3. **Salin file environment**

   ```bash
   cp .env.example .env
   ```

4. **Generate key aplikasi**

   ```bash
   php artisan key:generate
   ```

5. **Konfigurasi database di `.env`**

   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Jalankan migrasi dan seeder (jika ada)**

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Konfigurasi Mail/SMTP di `.env`**

   ```ini
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io      # ganti sesuai provider Anda
   MAIL_PORT=2525                  # ganti sesuai provider
   MAIL_USERNAME=your_username
   MAIL_PASSWORD=your_password
   MAIL_ENCRYPTION=tls             # atau sesuai provider
   MAIL_FROM_ADDRESS=no-reply@blogapp.com
   MAIL_FROM_NAME="Blog App"
   ```

8. **Aktifkan queue untuk notifikasi email**

   * Pastikan `QUEUE_CONNECTION` di `.env` diatur ke `database` atau `redis`:

     ```ini
     QUEUE_CONNECTION=database
     ```

   * Buat tabel queue (jika menggunakan database):

     ```bash
     php artisan queue:table
     php artisan migrate
     ```

   * Jalankan worker queue:

     ```bash
     php artisan queue:work
     ```

9. **Jalankan server pengembangan**

   ```bash
   php artisan serve
   ```

   Akses aplikasi di [http://localhost:8000](http://localhost:8000).

## Alur Aplikasi

* **Kategori:** Hanya admin yang bisa membuat atau mengubah kategori.
* **Post:** Saat pengguna membuat post baru, sistem akan otomatis mengirim email notifikasi ke admin atau subscriber melalui queue.
