# 🍽️ Happy Pot
Happy Pot adalah aplikasi web yang berfungsi sebagai platform bagi penggemar makanan untuk menemukan, membaca, dan mengunggah resep makanan. Aplikasi ini menyediakan fitur utama untuk interaksi yang dipersonalisasi dan pengalaman berbagi kuliner.

Fitur Utama:
- Recipe Browsing: Menjelajahi berbagai resep dengan instruksi dan bahan terperinci.
- User Registration and Login: Autentikasi pengguna yang aman untuk interaksi yang dipersonalisasi.
- Upload Recipe: Pengguna dapat mengunggah resep mereka sendiri ke situs web.
- Commenting System: Menulis dan membaca komentar pada resep untuk berbagi tips dan umpan balik.

# 💻 Stack teknologi:
- Frontend: HTML, CSS, JavaScript
- Backend: PHP
- Database: MySQL (dikelola melalui Google Cloud SQL di lingkungan produksi)
- Web Server: Apache (terkonfigurasi dalam Docker)

# ⚙️ Requirements
Untuk menjalankan proyek ini secara lokal, Anda memerlukan:
- Docker Desktop: Untuk membangun dan menjalankan aplikasi serta database dalam container. Pastikan Docker Engine berjalan dengan baik di sistem Anda.
- Git: Untuk mengelola versi kode sumber dan berinteraksi dengan GitHub.
- Composer: Manajer dependensi untuk PHP.

# 🚀 Cara Setup di Lingkungan Lokal
Ikuti langkah-langkah berikut untuk menjalankan aplikasi secara lokal menggunakan Docker:

1. Clone Repository:
``` bash
git clone https://github.com/HappyPot-PSO/happypot.git
cd happypot
```

3. Navigasi ke Direktori Proyek Utama:
Asumsi Dockerfile dan file PHP utama di direktori

4. Build Image Docker:
``` bash
docker build -t simple-recipe-web .
```

5. Jalankan Container Docker (container aplikasi web (PHP + Apache) dan memetakan port.):
``` bash
docker run -p 8080:80 -d \
    --name simple-recipe-app \
    -v "direktori root proyek":/var/www/html \
    simple-recipe-web
```

6. Akses aplikasi melalui
``` bash
http://localhost:8080.
```

Catatan Penting untuk Database MySQL Lokal:
Untuk menjalankan aplikasi ini secara lokal dengan database, Anda dapat menggunakan docker-compose yang sudah disediakan di repositori Anda. docker-compose.yml akan mengatur dan menjalankan container aplikasi dan database MySQL secara bersamaan.
Di direktori yang sama dengan docker-compose.yml
``` bash
docker-compose up -d
```
Pastikan recipedb.sql Anda berada di lokasi yang benar yang direferensikan oleh docker-compose.yml untuk inisialisasi skema database.


# 🧪 Cara Menjalankan Linter / Unit Test di Lingkungan Lokal
Proyek ini menggunakan PHPUnit untuk unit testing dan Composer untuk mengelola dependensi, serta tools untuk memeriksa kualitas kode.
1. Instal Dependensi Composer:
Pastikan Anda sudah berada di direktori root proyek Anda,
composer install

2. Jalankan Validasi Composer
``` bash
composer validate --strict
```

4. Jalankan PHPUnit Tests:
./vendor/bin/phpunit

5. Jalankan PHP Code Sniffer (Linter):
./vendor/bin/phpcs --standard=PSR12 --extensions=php .

6. Jalankan PHP Mess Detector (Code Quality Analysis):
./vendor/bin/phpmd . text cleancode,codesize,controversial,design,naming,unusedcode

# ➕ Cara Menambahkan Fitur Baru
Proyek ini mengikuti alur kerja Git standar untuk pengembangan fitur. Ikuti langkah-langkah berikut untuk menambahkan fitur baru atau melakukan perbaikan:
1. Buat Branch Baru:
``` bash
git checkout main
git pull origin main # Pastikan branch main Anda terbaru
git checkout -b nama-fitur-baru-anda
```

3. Kembangkan Fitur & Lakukan Commit:
``` bash
git add .
git commit -m "feat: Menambahkan fungsionalitas baru untuk [Deskripsi Fitur]"
```

4. Dorong Branch ke GitHub:
``` bash
git push origin nama-fitur-baru-anda
```

5. Buat Pull Request (PR) dari branch baru Anda ke branch main.


6, Review Kode & Merge:
Merged pull request ke branch main. Penggabungan ke main akan memicu pipeline Continuous Deployment (CD).

# 🌐 Public URL Webnya
Aplikasi ini di-deploy ke Google Cloud Run sebagai bagian dari pipeline CI/CD. Setelah berhasil di-deploy melalui GitHub Actions, Anda dapat mengakses aplikasi di URL publik yang akan disediakan oleh Cloud Run.
URL Aplikasi: 
``` bash
https://happypot-5405j2592366-asia-southeast2.a.run.app
```
