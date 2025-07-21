# Panduan Testing Pembayaran Midtrans

Panduan lengkap untuk melakukan testing pembayaran Midtrans di aplikasi Travel Booking System.

## 📋 Daftar Isi

1. [Persiapan Testing](#persiapan-testing)
2. [Testing Konfigurasi](#testing-konfigurasi)
3. [Testing API Connection](#testing-api-connection)
4. [Testing Payment Flow](#testing-payment-flow)
5. [Testing Notifications](#testing-notifications)
6. [Testing dengan Simulator](#testing-dengan-simulator)
7. [Troubleshooting](#troubleshooting)

## 🚀 Persiapan Testing

### 1. Konfigurasi Environment

Pastikan file `.env` sudah dikonfigurasi dengan benar:

```env
# Midtrans Configuration
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxx  # Sandbox Server Key
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxx  # Sandbox Client Key
MIDTRANS_IS_PRODUCTION=false                  # Set false untuk testing

# Email Configuration (untuk testing notifikasi)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Travel Agency"
```

### 2. Mendapatkan Sandbox Credentials

1. Daftar di [Midtrans Dashboard](https://dashboard.midtrans.com/)
2. Pilih environment **Sandbox**
3. Copy **Server Key** dan **Client Key** dari Settings > Access Keys
4. Masukkan ke file `.env`

## 🔧 Testing Konfigurasi

### 1. Test Konfigurasi Dasar

Jalankan script test konfigurasi:

```bash
# Test environment local
php scripts/test-midtrans-config.php local

# Test environment staging
php scripts/test-midtrans-config.php staging

# Test environment production
php scripts/test-midtrans-config.php production
```

**Output yang diharapkan:**
```
=== MIDTRANS CONFIGURATION TEST ===
Environment: local
✓ Environment file loaded
✓ Midtrans keys configured
✓ SSL verification disabled for development
✓ API connection successful!
✓ Snap token created
✓ Environment verification passed
=== TEST COMPLETED SUCCESSFULLY ===
```

### 2. Test Sederhana dengan PHP

Jalankan test sederhana:

```bash
php tests/test_midtrans.php
```

## 🌐 Testing API Connection

### 1. Test Manual dengan cURL

```bash
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1tMmxfdEJPajRxWHJTTkFSYjNsR3VXVFE6' \
  -d '{
    "transaction_details": {
      "order_id": "TEST-" + Date.now(),
      "gross_amount": 100000
    },
    "customer_details": {
      "first_name": "Test User",
      "email": "test@example.com",
      "phone": "+62812345678"
    }
  }'
```

### 2. Test Connection Script

Gunakan script yang sudah disediakan:

```bash
php tests/test-midtrans-connection.php
```

## 💳 Testing Payment Flow

### 1. Testing melalui Web Interface

1. **Akses aplikasi**: `http://localhost:8000`
2. **Pilih paket wisata** dan lakukan booking
3. **Isi form pembayaran** dengan data test
4. **Klik "Bayar Sekarang"** untuk membuka Snap
5. **Gunakan test credentials** untuk simulasi pembayaran

### 2. Test Credentials untuk Sandbox

#### Kartu Kredit Test:
```
# Visa - Success
Card Number: 4811 1111 1111 1114
Expiry: 01/25
CVV: 123

# Mastercard - Success  
Card Number: 5573 3811 1111 1115
Expiry: 01/25
CVV: 123

# Visa - Failed
Card Number: 4911 1111 1111 1113
Expiry: 01/25
CVV: 123
```

#### Bank Transfer Test:
```
# BCA Virtual Account
VA Number: 12345678901

# BNI Virtual Account  
VA Number: 12345678901

# Mandiri Bill Payment
Bill Key: 12345678901
Biller Code: 70012
```

#### E-Wallet Test:
```
# GoPay
Phone: +62812345678
OTP: 123456

# ShopeePay
Phone: +62812345678
OTP: 123456
```

### 3. Testing Different Payment Status

#### Success Payment:
- Gunakan kartu kredit valid
- Ikuti flow pembayaran sampai selesai
- Cek notifikasi email dan database

#### Pending Payment:
- Gunakan Bank Transfer
- Jangan selesaikan pembayaran
- Cek status pending di dashboard

#### Failed Payment:
- Gunakan kartu kredit yang gagal
- Atau batalkan pembayaran di tengah jalan
- Cek notifikasi kegagalan

## 📧 Testing Notifications

### 1. Unit Testing Notifications

Jalankan test notifikasi:

```bash
# Test semua notifikasi
php artisan test tests/Feature/PaymentNotificationTest.php

# Test spesifik success notification
php artisan test --filter=it_sends_success_notification_for_settlement_status

# Test spesifik pending notification
php artisan test --filter=it_sends_pending_notification_for_pending_status

# Test spesifik failed notification
php artisan test --filter=it_sends_failed_notification_for_deny_status
```

### 2. Manual Testing Notifications

#### Test Success Notification:
```php
// Simulasi di Tinker
php artisan tinker

$user = User::first();
$payment = Payment::first();

// Kirim notifikasi success
$user->notify(new \App\Notifications\PaymentSuccessNotification($payment));
```

#### Test Pending Notification:
```php
// Simulasi di Tinker
$user->notify(new \App\Notifications\PaymentPendingNotification($payment));
```

#### Test Failed Notification:
```php
// Simulasi di Tinker
$user->notify(new \App\Notifications\PaymentFailedNotification($payment, 'deny'));
```

### 3. Testing Email Templates

Untuk melihat preview email:

```bash
# Generate preview email
php artisan make:mail PaymentTestMail --markdown=emails.payment-test
```

## 🎮 Testing dengan Simulator

### 1. Midtrans Simulator

Midtrans menyediakan simulator untuk testing:

1. **Akses Simulator**: [https://simulator.sandbox.midtrans.com/](https://simulator.sandbox.midtrans.com/)
2. **Input Order ID** dari transaksi yang ingin disimulasikan
3. **Pilih status** yang ingin disimulasikan:
   - `settlement` - Pembayaran berhasil
   - `pending` - Pembayaran pending
   - `deny` - Pembayaran ditolak
   - `cancel` - Pembayaran dibatalkan
   - `expire` - Pembayaran kedaluwarsa

### 2. Testing Webhook/Callback

#### Setup Ngrok untuk Local Testing:
```bash
# Install ngrok
npm install -g ngrok

# Expose local server
ngrok http 8000

# Update webhook URL di Midtrans Dashboard
# Settings > Configuration > Payment Notification URL
# https://your-ngrok-url.ngrok.io/api/midtrans/callback
```

#### Manual Webhook Testing:
```bash
# Test webhook dengan curl
curl -X POST http://localhost:8000/api/midtrans/callback \
  -H "Content-Type: application/json" \
  -d '{
    "transaction_status": "settlement",
    "order_id": "BOOK-1-1234567890",
    "transaction_id": "test-transaction-123",
    "gross_amount": "100000.00",
    "payment_type": "credit_card",
    "transaction_time": "2024-01-01 12:00:00",
    "fraud_status": "accept"
  }'
```

## 🔍 Testing Scenarios

### 1. Happy Path Testing

```bash
# 1. User melakukan booking
# 2. Sistem generate payment
# 3. User bayar via Snap
# 4. Midtrans kirim notification
# 5. Sistem update status
# 6. User terima email konfirmasi
```

### 2. Error Scenarios Testing

#### Invalid Configuration:
```bash
# Test dengan server key salah
MIDTRANS_SERVER_KEY=invalid-key php scripts/test-midtrans-config.php
```

#### Network Issues:
```bash
# Test dengan SSL verification enabled di development
# Akan menghasilkan SSL error
```

#### Invalid Payment Data:
```bash
# Test dengan order_id yang sudah ada
# Test dengan gross_amount negatif
# Test dengan customer_details kosong
```

## 🛠 Troubleshooting

### 1. SSL Certificate Issues

**Problem**: SSL verification failed

**Solution**:
```bash
# Development - disable SSL verification
MIDTRANS_SSL_VERIFY=false

# Production - update CA bundle
wget https://curl.se/ca/cacert.pem -O /path/to/cacert.pem
```

### 2. Authentication Issues

**Problem**: Access denied (401)

**Solution**:
- Periksa Server Key di `.env`
- Pastikan menggunakan Sandbox key untuk testing
- Periksa format Authorization header

### 3. Webhook Not Received

**Problem**: Callback tidak diterima

**Solution**:
- Periksa URL webhook di Midtrans Dashboard
- Pastikan server dapat diakses dari internet
- Gunakan ngrok untuk local testing
- Periksa firewall settings

### 4. Email Notifications Not Sent

**Problem**: Email tidak terkirim

**Solution**:
```bash
# Test email configuration
php artisan tinker
Mail::raw('Test email', function($msg) {
    $msg->to('test@example.com')->subject('Test');
});

# Check mail logs
tail -f storage/logs/laravel.log | grep mail
```

### 5. Database Issues

**Problem**: Payment status tidak update

**Solution**:
```bash
# Check database connection
php artisan migrate:status

# Check payment records
php artisan tinker
Payment::where('gateway_status', 'pending')->get();

# Manual status update
$payment = Payment::find(1);
$payment->update(['payment_status' => 'Paid', 'gateway_status' => 'settlement']);
```

## 📊 Monitoring & Logging

### 1. Check Application Logs

```bash
# Monitor real-time logs
tail -f storage/logs/laravel.log

# Filter Midtrans logs
grep -i midtrans storage/logs/laravel.log

# Filter payment logs
grep -i payment storage/logs/laravel.log
```

### 2. Database Monitoring

```sql
-- Check payment status distribution
SELECT payment_status, COUNT(*) as count 
FROM payments 
GROUP BY payment_status;

-- Check recent payments
SELECT * FROM payments 
ORDER BY created_at DESC 
LIMIT 10;

-- Check failed payments
SELECT * FROM payments 
WHERE payment_status = 'Failed' 
ORDER BY created_at DESC;
```

### 3. Midtrans Dashboard Monitoring

1. **Login ke Midtrans Dashboard**
2. **Pilih Sandbox environment**
3. **Check Transactions** untuk melihat semua transaksi
4. **Check Logs** untuk melihat webhook delivery

## 🎯 Best Practices

### 1. Testing Strategy

- **Selalu test di Sandbox** sebelum production
- **Test semua payment methods** yang didukung
- **Test semua status scenarios** (success, pending, failed)
- **Test notification delivery** dan email templates
- **Test webhook reliability** dengan berbagai network conditions

### 2. Security Testing

- **Validate webhook signature** untuk memastikan request dari Midtrans
- **Test dengan invalid data** untuk memastikan validation bekerja
- **Test rate limiting** untuk mencegah abuse
- **Test CSRF protection** pada payment forms

### 3. Performance Testing

- **Test concurrent payments** untuk memastikan tidak ada race condition
- **Test dengan large transaction amounts** 
- **Monitor response times** untuk API calls
- **Test database performance** dengan banyak payment records

## 📚 Resources

- [Midtrans Documentation](https://docs.midtrans.com/)
- [Midtrans Sandbox](https://dashboard.sandbox.midtrans.com/)
- [Midtrans Simulator](https://simulator.sandbox.midtrans.com/)
- [Laravel Testing Documentation](https://laravel.com/docs/testing)

---

**Catatan**: Panduan ini dibuat berdasarkan implementasi Travel Booking System dan mengikuti best practices Midtrans dan Laravel.
