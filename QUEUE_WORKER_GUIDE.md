# Queue Worker Guide

## Masalah Email Verifikasi

Email verifikasi tidak terkirim karena Laravel menggunakan queue system untuk mengirim email, tetapi queue worker tidak berjalan secara otomatis.

## Solusi

### 1. Menjalankan Queue Worker Manual
```bash
php artisan queue:work
```

### 2. Menggunakan Script Otomatis
Jalankan file `start-queue.bat` yang telah dibuat:
```bash
./start-queue.bat
```

### 3. Untuk Development
Untuk development lokal, Anda bisa mengubah konfigurasi queue menjadi `sync` di file `.env`:
```
QUEUE_CONNECTION=sync
```

Dengan `sync`, email akan langsung terkirim tanpa perlu queue worker.

## Konfigurasi Email

Pastikan konfigurasi email di `.env` sudah benar:
- `MAIL_MAILER=smtp`
- `MAIL_HOST=smtp.gmail.com`
- `MAIL_PORT=587`
- `MAIL_USERNAME=` (email Gmail Anda)
- `MAIL_PASSWORD=` (App Password Gmail)
- `MAIL_ENCRYPTION=tls`

## Troubleshooting

1. **Cek job yang tertunda:**
   ```bash
   php artisan tinker --execute="echo 'Jobs: ' . DB::table('jobs')->count();"
   ```

2. **Proses job manual:**
   ```bash
   php artisan queue:work --once
   ```

3. **Cek log error:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

## Catatan Penting

- Queue worker harus tetap berjalan untuk memproses email
- Untuk production, gunakan supervisor atau systemd untuk menjalankan queue worker
- Jangan lupa restart queue worker setelah deploy kode baru