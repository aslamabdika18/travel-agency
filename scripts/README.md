# Scripts Directory

Kumpulan script utilitas untuk membantu development dan deployment aplikasi Travel Booking System.

## Daftar Script

### 1. Midtrans Configuration Testing

#### `test-midtrans-config.php`
Script untuk menguji konfigurasi Midtrans di berbagai environment.

**Penggunaan:**
```bash
# Test environment local
php scripts/test-midtrans-config.php local

# Test environment staging
php scripts/test-midtrans-config.php staging

# Test environment production
php scripts/test-midtrans-config.php production
```

**Fitur:**
- Memuat konfigurasi environment yang sesuai
- Mengatur SSL verification berdasarkan environment
- Menguji koneksi ke API Midtrans
- Membuat sample transaction untuk verifikasi
- Memberikan feedback detail tentang status konfigurasi

### 2. Laragon SSL Configuration

#### `setup-laragon-ssl.bat`
Script batch untuk setup SSL certificate di Laragon (Windows).

**Penggunaan:**
```cmd
# Jalankan sebagai Administrator
scripts\setup-laragon-ssl.bat
```

**Fitur:**
- Download CA certificate bundle terbaru
- Setup environment variables
- Update konfigurasi PHP.ini
- Konfigurasi Composer
- Membuat test script SSL
- Backup file konfigurasi sebelum diubah

#### `setup-laragon-ssl.ps1`
Script PowerShell yang lebih advanced untuk setup SSL certificate.

**Penggunaan:**
```powershell
# Basic usage (jalankan sebagai Administrator)
.\scripts\setup-laragon-ssl.ps1

# Dengan custom Laragon path
.\scripts\setup-laragon-ssl.ps1 -LaragonPath "C:\laragon"

# Skip konfigurasi Composer
.\scripts\setup-laragon-ssl.ps1 -SkipComposer

# Force overwrite existing certificate
.\scripts\setup-laragon-ssl.ps1 -Force

# Verbose output
.\scripts\setup-laragon-ssl.ps1 -Verbose
```

**Parameter:**
- `-LaragonPath`: Path custom ke instalasi Laragon
- `-Force`: Overwrite certificate yang sudah ada
- `-SkipComposer`: Skip konfigurasi Composer
- `-Verbose`: Output detail

**Fitur:**
- Error handling yang lebih baik
- Validasi input dan output
- Backup otomatis file konfigurasi
- Test koneksi internet
- Verifikasi ukuran file certificate
- Interactive prompts
- Colored output untuk readability

## Persyaratan

### Untuk Script Midtrans
- PHP 7.4+
- Composer dependencies terinstall
- File `.env` dengan konfigurasi Midtrans
- Koneksi internet untuk API testing

### Untuk Script SSL Laragon
- Windows OS
- Laragon terinstall
- Administrator privileges
- PowerShell 5.0+ (untuk script .ps1)
- Koneksi internet untuk download certificate

## Troubleshooting

### Script Midtrans

**Error: "Class 'Midtrans\Config' not found"**
```bash
# Install dependencies
composer install
```

**Error: "SSL certificate problem"**
```bash
# Jalankan setup SSL Laragon terlebih dahulu
scripts\setup-laragon-ssl.bat
```

**Error: "Unauthorized"**
- Periksa Server Key dan Client Key di file `.env`
- Pastikan menggunakan key yang sesuai dengan environment (sandbox/production)

### Script SSL Laragon

**Error: "Access denied"**
- Pastikan menjalankan script sebagai Administrator
- Disable antivirus sementara jika diperlukan

**Error: "Failed to download certificate"**
- Periksa koneksi internet
- Coba download manual dari https://curl.se/ca/cacert.pem
- Periksa firewall settings

**Error: "PHP installations not found"**
- Periksa path Laragon sudah benar
- Pastikan PHP terinstall di Laragon
- Gunakan parameter `-LaragonPath` untuk custom path

## Best Practices

1. **Selalu backup** file konfigurasi sebelum menjalankan script
2. **Test di environment development** sebelum production
3. **Jalankan script SSL setup** setelah install/update Laragon
4. **Update certificate** secara berkala (minimal 6 bulan sekali)
5. **Dokumentasikan perubahan** yang dilakukan oleh script

## Kontribusi

Jika ingin menambah script baru atau memperbaiki yang ada:

1. Buat script di direktori `scripts/`
2. Tambahkan dokumentasi di README ini
3. Sertakan error handling yang memadai
4. Test di berbagai environment
5. Update dokumentasi utama jika diperlukan

## Lihat Juga

- [Laragon SSL Configuration Guide](../docs/laragon-ssl-configuration.md)
- [Midtrans Production Deployment Guide](../docs/midtrans-production-deployment.md)
- [Main README](../README.md)