# Email Configuration Guide

## Overview

Panduan konfigurasi email untuk aplikasi Travel Agency menggunakan Gmail SMTP.

## Konfigurasi Email

### 1. Environment Variables (.env)

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Gmail Setup

#### Menggunakan App Password (Recommended)

1. **Enable 2-Factor Authentication** di akun Gmail Anda
2. **Generate App Password**:
   - Buka [Google Account Settings](https://myaccount.google.com/)
   - Pilih "Security" → "2-Step Verification"
   - Scroll ke bawah dan pilih "App passwords"
   - Pilih "Mail" dan "Other (custom name)"
   - Masukkan nama aplikasi (contoh: "Travel Agency")
   - Copy password yang dihasilkan
   - Gunakan password ini di `MAIL_PASSWORD`

#### Alternative: Less Secure Apps (Not Recommended)

1. Buka [Less secure app access](https://myaccount.google.com/lesssecureapps)
2. Turn ON "Allow less secure apps"
3. Gunakan password Gmail biasa di `MAIL_PASSWORD`

### 3. Config File (config/mail.php)

Pastikan konfigurasi SMTP sudah benar:

```php
'smtp' => [
    'transport' => 'smtp',
    'host' => env('MAIL_HOST', '127.0.0.1'),
    'port' => env('MAIL_PORT', 2525),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
    'timeout' => null,
    'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
],
```

## Testing Email

### Command Line Test

```bash
# Test dengan email tertentu
php artisan email:test your-email@gmail.com

# Test dengan prompt input
php artisan email:test
```

### Output yang Diharapkan

```
Testing Email Configuration...

=== Current Email Configuration ===
+-------------------+-------------------------------+
| Setting           | Value                         |
+-------------------+-------------------------------+
| MAIL_MAILER       | smtp                          |
| MAIL_HOST         | smtp.gmail.com                |
| MAIL_PORT         | 587                           |
| MAIL_ENCRYPTION   | tls                           |
| MAIL_USERNAME     | ***@gmail.com                 |
| MAIL_PASSWORD     | ****************              |
| MAIL_FROM_ADDRESS | noreply@sumatratourtravel.com |
| MAIL_FROM_NAME    | Sumatra Tour Travel           |
+-------------------+-------------------------------+

Sending test email...

✅ Test email sent successfully!
📧 Email sent to: your-email@gmail.com
Please check your inbox (and spam folder) for the test email.
```

## Email Features dalam Aplikasi

### 1. Payment Success Notification

- **File**: `app/Notifications/PaymentSuccessNotification.php`
- **Trigger**: Setelah payment berhasil
- **Content**: Konfirmasi pembayaran + PDF invoice attachment
- **Logging**: Comprehensive logging untuk attachment process

### 2. Payment Failed Notification

- **File**: `app/Notifications/PaymentFailedNotification.php`
- **Trigger**: Ketika payment gagal
- **Content**: Informasi kegagalan pembayaran

### 3. Payment Pending Notification

- **File**: `app/Notifications/PaymentPendingNotification.php`
- **Trigger**: Ketika payment masih pending
- **Content**: Reminder untuk menyelesaikan pembayaran

### 4. Refund Processed Notification

- **File**: `app/Notifications/RefundProcessedNotification.php`
- **Trigger**: Setelah refund diproses
- **Content**: Konfirmasi refund

## Troubleshooting

### Common Issues

#### 1. Authentication Failed

**Error**: `Authentication failed`

**Solutions**:
- Pastikan menggunakan App Password, bukan password Gmail biasa
- Verify 2FA sudah diaktifkan di Gmail
- Check username dan password di .env file

#### 2. Connection Timeout

**Error**: `Connection timeout`

**Solutions**:
- Check MAIL_HOST dan MAIL_PORT
- Pastikan server memiliki akses internet
- Verify firewall tidak memblokir port 587

#### 3. TLS/SSL Issues

**Error**: `TLS/SSL connection failed`

**Solutions**:
- Pastikan MAIL_ENCRYPTION=tls
- Check OpenSSL extension di PHP
- Verify server mendukung TLS 1.2+

#### 4. From Address Issues

**Error**: `From address not allowed`

**Solutions**:
- Pastikan MAIL_FROM_ADDRESS valid
- Check domain reputation
- Verify SPF/DKIM records jika menggunakan custom domain

### Debug Mode

#### Enable Debug Logging

```env
# .env
LOG_LEVEL=debug
MAIL_LOG_CHANNEL=stack
```

#### Check Logs

```bash
# View email logs
tail -f storage/logs/laravel.log | grep -i mail

# View specific date
tail -f storage/logs/laravel-$(date +%Y-%m-%d).log
```

### Testing Different Providers

#### Mailtrap (Development)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
```

#### SendGrid

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

#### Mailgun

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-mailgun-secret
```

## Performance Optimization

### 1. Queue Email Jobs

```bash
# Setup queue
php artisan queue:table
php artisan migrate

# Start queue worker
php artisan queue:work
```

### 2. Email Templates Caching

```bash
# Cache views
php artisan view:cache

# Clear cache when needed
php artisan view:clear
```

### 3. Batch Email Sending

Untuk mengirim email dalam batch:

```php
// Dalam notification class
public function via($notifiable)
{
    return ['mail'];
}

// Queue notification
$user->notify((new PaymentSuccessNotification($payment))->delay(now()->addMinutes(1)));
```

## Security Best Practices

### 1. Environment Security

- Jangan commit file .env ke repository
- Gunakan App Password, bukan password utama
- Rotate password secara berkala

### 2. Email Content Security

- Validate semua input sebelum mengirim email
- Sanitize HTML content
- Avoid mengirim sensitive data via email

### 3. Rate Limiting

```php
// Dalam notification class
public function shouldSend($notifiable, $channel)
{
    // Implement rate limiting logic
    return true;
}
```

## Monitoring & Alerts

### 1. Email Delivery Monitoring

```bash
# Check email logs
grep "email" storage/logs/laravel.log

# Monitor failed emails
grep "failed" storage/logs/laravel.log | grep -i mail
```

### 2. Performance Monitoring

```bash
# Check slow email sends
grep "slow" storage/logs/invoice-performance-*.log
```

### 3. Error Alerts

Setup alerts untuk email failures:

```php
// Dalam AppServiceProvider
Mail::failures(function ($data) {
    Log::error('Email failed to send', $data);
    // Send alert to admin
});
```

## Support

Jika masih mengalami masalah:

1. **Check Configuration**: Jalankan `php artisan email:test`
2. **Review Logs**: Check `storage/logs/laravel.log`
3. **Verify Credentials**: Test login manual ke Gmail
4. **Network Check**: Pastikan server bisa akses smtp.gmail.com:587
5. **Contact Support**: Dengan log error yang lengkap

## Useful Commands

```bash
# Test email configuration
php artisan email:test your-email@example.com

# Clear config cache
php artisan config:clear

# Clear view cache
php artisan view:clear

# Check queue status
php artisan queue:work --verbose

# Monitor logs
tail -f storage/logs/laravel.log
```
