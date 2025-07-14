# Panduan Sistem Pembayaran Travel Agency

## Gambaran Umum

Sistem pembayaran ini menggunakan Midtrans sebagai payment gateway dengan fitur-fitur:

- ✅ **Webhook Notification Handling** - Otomatis update status pembayaran
- ✅ **Status Monitoring** - Real-time monitoring dan sync dengan Midtrans
- ✅ **Error Recovery** - Otomatis deteksi dan perbaikan masalah
- ✅ **Security Protection** - Rate limiting dan IP validation
- ✅ **Audit Trail** - Complete logging untuk semua transaksi

## Flow Pembayaran

### 1. User Melakukan Booking
```
User → Pilih Paket → Isi Data → Konfirmasi Booking
```

### 2. Payment Creation
```
Booking Created → Payment Record (Status: Unpaid) → Midtrans Snap Token
```

### 3. Payment Process
```
User → Midtrans Payment Page → Payment Method → Payment Completion
```

### 4. Webhook Notification
```
Midtrans → Webhook → Validation → Status Update → Booking Confirmation
```

## Status Mapping

### Payment Status
| Midtrans Status | Internal Status | Booking Status | Keterangan |
|----------------|----------------|----------------|-----------|
| `pending` | `Unpaid` | `pending` | Menunggu pembayaran |
| `capture` | `Paid` | `confirmed` | Pembayaran berhasil (credit card) |
| `settlement` | `Paid` | `confirmed` | Pembayaran berhasil (final) |
| `deny` | `Failed` | `cancelled` | Pembayaran ditolak |
| `cancel` | `Failed` | `cancelled` | Pembayaran dibatalkan |
| `expire` | `Failed` | `cancelled` | Pembayaran expired |
| `failure` | `Failed` | `cancelled` | Pembayaran gagal |
| `refund` | `Refunded` | `refunded` | Pembayaran di-refund |

### Special Cases
| Kondisi | Status | Keterangan |
|---------|--------|------------|
| Fraud Challenge | `Unpaid` | Credit card fraud detection |
| Partial Refund | `Refunded` | Sebagian dana di-refund |

## Command Line Tools

### 1. Monitor Payment Status
```bash
# Monitor semua pending payments
php artisan payment:monitor

# Check payment tertentu
php artisan payment:monitor --payment-id=123

# Shortcut command
php artisan payment:check 123
```

### 2. Fix Status Inconsistencies
```bash
# Dry run - lihat masalah tanpa fix
php artisan payment:fix-status --dry-run

# Fix semua masalah
php artisan payment:fix-status

# Shortcut command
php artisan payment:fix-all
```

### 3. Manual Status Check
```bash
# Check via Laravel tinker
php artisan tinker

# Di dalam tinker:
$payment = App\Models\Payment::find(123);
$payment->payment_status;
$payment->gateway_status;
$payment->booking->status;
```

## Troubleshooting

### Problem: Payment sukses tapi status tetap "unpaid"

**Step 1: Check Payment Details**
```bash
php artisan payment:check [payment_id]
```

**Step 2: Check Logs**
```bash
tail -f storage/logs/laravel.log | grep -i "payment\|midtrans"
```

**Step 3: Manual Sync**
```bash
php artisan payment:monitor --payment-id=[payment_id]
```

**Step 4: Force Fix (jika diperlukan)**
```bash
php artisan tinker

# Di dalam tinker:
$payment = App\Models\Payment::find([payment_id]);
$payment->markAsPaid();
```

### Problem: Webhook tidak diterima

**Check 1: Webhook URL**
- URL: `https://yourdomain.com/api/payment/notification`
- Method: POST
- Content-Type: application/json

**Check 2: Server Logs**
```bash
# Check web server logs
tail -f /var/log/nginx/access.log | grep notification

# Check Laravel logs
tail -f storage/logs/laravel.log | grep webhook
```

**Check 3: Firewall/Security**
- Pastikan IP Midtrans tidak diblokir
- Check rate limiting settings
- Verify SSL certificate

### Problem: Duplicate notifications

**Diagnosis:**
```bash
grep "Duplicate notification" storage/logs/laravel.log
```

**Solution:**
Sistem otomatis handle duplicate notifications dengan cache lock selama 60 detik.

### Problem: Payment stuck di "pending"

**Check Midtrans Status:**
```bash
php artisan tinker

# Di dalam tinker:
$service = app(App\Services\MidtransService::class);
$status = $service->getTransactionStatus('[transaction_id]');
dd($status);
```

**Manual Update:**
```bash
php artisan payment:monitor --payment-id=[payment_id]
```

## Development & Testing

### Sandbox Testing

1. **Setup Environment**
```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxx
MIDTRANS_IS_PRODUCTION=false
```

2. **Test Payment Flow**
- Gunakan test credit card numbers dari Midtrans
- Monitor logs selama testing
- Verify webhook notifications

3. **Test Webhook Locally**
```bash
# Install ngrok
npm install -g ngrok

# Expose local server
ngrok http 8000

# Update webhook URL di Midtrans dashboard
# https://xxx.ngrok.io/api/payment/notification
```

### Production Deployment

1. **Environment Setup**
```env
MIDTRANS_SERVER_KEY=Mid-server-xxx
MIDTRANS_CLIENT_KEY=Mid-client-xxx
MIDTRANS_IS_PRODUCTION=true
LOG_LEVEL=info
```

2. **Webhook Configuration**
- URL: `https://yourdomain.com/api/payment/notification`
- Enable all notification types
- Set proper timeout (30 seconds)

3. **Monitoring Setup**
```bash
# Add to crontab
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Security Best Practices

### 1. Webhook Security
- ✅ IP validation (production only)
- ✅ Rate limiting per order_id
- ✅ Request validation
- ✅ HTTPS only

### 2. Data Protection
- ✅ Sensitive data encryption
- ✅ Audit trail logging
- ✅ Access control
- ✅ Regular security updates

### 3. Monitoring
- ✅ Real-time status monitoring
- ✅ Error alerting
- ✅ Performance tracking
- ✅ Fraud detection

## API Endpoints

### Public Endpoints

**Webhook Notification**
```
POST /api/payment/notification
Content-Type: application/json

# Midtrans akan kirim data otomatis
```

**Payment Status Check**
```
GET /api/payment/status?order_id=BOOK-123-1234567890

Response:
{
  "booking_status": "confirmed",
  "payment_status": "Paid",
  "payment": {
    "id": 123,
    "payment_status": "Paid",
    "gateway_status": "settlement",
    "total_price": 1500000
  }
}
```

### Protected Endpoints

**Create Payment**
```
POST /api/payment/create-snap-redirect-url
Authorization: Bearer [token]

{
  "booking_id": 123
}

Response:
{
  "redirect_url": "https://app.sandbox.midtrans.com/snap/v2/vtweb/xxx",
  "payment_reference": "PAY-123-1234567890"
}
```

## Maintenance

### Daily Tasks
- ✅ Otomatis via scheduler: `payment:fix-status --dry-run`
- ✅ Otomatis via scheduler: `payment:monitor`

### Weekly Tasks
```bash
# Manual review inconsistencies
php artisan payment:fix-status --dry-run

# Check failed payments
php artisan tinker
App\Models\Payment::failed()->count();
```

### Monthly Tasks
```bash
# Database cleanup (optional)
php artisan tinker

# Archive old completed payments
App\Models\Payment::where('payment_status', 'Paid')
    ->where('created_at', '<', now()->subMonths(6))
    ->count();
```

## Support & Contact

Jika mengalami masalah:

1. **Check dokumentasi** ini terlebih dahulu
2. **Run diagnostic commands** untuk identifikasi masalah
3. **Check logs** untuk error details
4. **Contact developer** dengan informasi lengkap:
   - Payment ID
   - Error logs
   - Steps to reproduce
   - Expected vs actual behavior

---

**Last Updated**: January 2025  
**Version**: 2.0  
**Compatibility**: Laravel 11, Midtrans v2