# Docker REST API Base - Technical Testing

## Pendahuluan
Proyek ini adalah sebuah aplikasi REST API berbasis Laravel yang dijalankan menggunakan Docker. Berikut adalah langkah-langkah untuk memulai proyek ini di lingkungan pengembangan lokal Anda.

## Prasyarat
Pastikan Anda telah menginstal software berikut di sistem Anda:
- [Git](https://git-scm.com/)
- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Langkah-langkah Instalasi

### 1. Clone Repository
Clone repository ini ke direktori lokal Anda:
```bash
git clone https://github.com/username/docker-rest-api-base.git
```

### 2. Masuk ke Directori Project
Pindah ke direktori project docker-rest-api-base:
```bash
cd docker-rest-api-base
```

### 3. Build dan Jalankan Container
Gunakan perintah berikut untuk membangun dan menjalankan container:
```bash
docker-compose up --build -d
```

### 4. Install Dependencies dengan Composer
Instal dependensi Laravel menggunakan Composer di dalam container:
```bash
docker compose run --rm composer install
```

### 5. Masuk ke Container PHP
Akses shell ke dalam container PHP untuk menjalankan perintah di dalam container:
```bash
docker compose run --rm php /bin/sh
```

### 6. Salin File .env
Salin file .env.example ke .env untuk konfigurasi environment Laravel:
```bash
cp .env.example .env
```

### 7. Edit File .env
Edit file .env untuk konfigurasi database dengan menggunakan editor nano:
```bash
nano .env
```
Sesuaikan konfigurasi database seperti berikut:
```bash
DB_CONNECTION=mysql       
DB_HOST=mysql             
DB_PORT=3306              
DB_DATABASE=laraveldb     
DB_USERNAME=laravel       
DB_PASSWORD=secret        
```

### 8. Ubah Hak Akses Direktori
Ubah hak akses direktori ke user laravel:
```bash
chown -R laravel:laravel /var/www/html
```
Setelah selesai, keluar dari container:
```bash
exit
```

### 9. Generate Application Key
Jalankan perintah untuk generate application key Laravel:
```bash
docker compose run --rm artisan key:generate
```

### 10. Migrasi Database
Jalankan migrasi database untuk mengatur skema database:
```bash
docker compose run --rm artisan migrate
```

### 11. Akses Aplikasi
Sekarang Anda dapat mengakses aplikasi melalui URL:
```bash
http://localhost:80/
```