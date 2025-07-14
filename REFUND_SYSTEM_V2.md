# Sistem Refund Travel Agency - Versi 2

## Overview
Sistem refund versi 2 adalah implementasi lengkap untuk mengelola pembatalan dan pengembalian dana booking travel package dengan kebijakan yang ketat dan otomatisasi penuh.

## Fitur Utama

### 1. Kebijakan Refund Otomatis
- **30+ hari sebelum keberangkatan**: 100% refund
- **15-29 hari sebelum keberangkatan**: 50% refund
- **7-14 hari sebelum keberangkatan**: 25% refund
- **Kurang dari 7 hari**: Tidak ada refund

### 2. Validasi Otomatis
- Pengecekan status booking (harus 'confirmed')
- Pengecekan status pembayaran (harus 'paid')
- Validasi tanggal keberangkatan
- Pengecekan kelayakan refund berdasarkan waktu

### 3. Integrasi Payment Gateway
- Otomatis memproses refund melalui Midtrans
- Handling error dan retry mechanism
- Logging lengkap untuk audit trail

### 4. Notifikasi Otomatis
- Email notification ke user
- Database notification untuk tracking
- Detail lengkap refund dalam notifikasi

### 5. Command Line Interface
- Artisan command untuk proses refund otomatis
- Dry-run mode untuk testing
- Batch processing dengan limit
- Scheduled task harian

## Struktur File

### Models
- `app/Models/Booking.php` - Model utama dengan logic refund

### Controllers
- `app/Http/Controllers/RefundController.php` - API controller untuk refund
- `app/Http/Controllers/PageController.php` - Web controller (method refund)

### Requests
- `app/Http/Requests/RefundRequest.php` - Validasi request refund

### Commands
- `app/Console/Commands/ProcessAutomaticRefunds.php` - Command untuk proses otomatis

### Notifications
- `app/Notifications/RefundProcessedNotification.php` - Notifikasi refund

### Views
- `resources/views/pages/refund.blade.php` - Halaman refund frontend

### JavaScript
- `resources/js/refund-handler.js` - Handler frontend untuk refund

### Routes
- `routes/api.php` - API routes untuk refund
- `routes/web.php` - Web routes untuk halaman refund
- `routes/console.php` - Console commands dan schedule

## API Endpoints

### GET /api/refund/policy/{booking}
Mendapatkan detail kebijakan refund untuk booking tertentu.

**Response:**
```json
{
    "success": true,
    "data": {
        "booking_reference": "TRV-2024-001",
        "can_be_refunded": true,
        "refund_percentage": 100,
        "refund_amount": 5000000,
        "days_until_departure": 45,
        "policy_tier": "full_refund",
        "policy_details": {
            "description": "Full refund available",
            "percentage": 100,
            "min_days": 30
        }
    }
}
```

### POST /api/refund/process
Memproses request refund.

**Request:**
```json
{
    "booking_id": 1,
    "reason": "Alasan pembatalan",
    "confirm": true
}
```

**Response:**
```json
{
    "success": true,
    "message": "Refund berhasil diproses",
    "data": {
        "refund_amount": 5000000,
        "refund_percentage": 100,
        "transaction_id": "refund_123456"
    }
}
```

### GET /api/refund/eligible
Mendapatkan daftar booking yang eligible untuk refund.

### GET /api/refund/history
Mendapatkan history refund user.

## Artisan Commands

### refund:process-automatic
Memproses refund otomatis untuk booking yang eligible.

**Options:**
- `--dry-run`: Mode testing tanpa eksekusi aktual
- `--days-threshold=N`: Threshold hari sebelum keberangkatan (default: 0)
- `--limit=N`: Limit jumlah booking yang diproses (default: 100)

**Contoh:**
```bash
# Dry run untuk testing
php artisan refund:process-automatic --dry-run

# Proses aktual dengan limit 50 booking
php artisan refund:process-automatic --limit=50

# Proses urgent (1 hari sebelum keberangkatan)
php artisan refund:process-automatic --days-threshold=1 --limit=10
```

## Scheduled Tasks

Sistem akan otomatis menjalankan:
- **Daily 3 AM**: Dry-run check untuk monitoring
- **Manual commands**: Tersedia untuk berbagai skenario

## Methods di Booking Model

### canBeRefunded(): bool
Mengecek apakah booking dapat di-refund.

### getRefundPercentage(): int
Mendapatkan persentase refund berdasarkan hari tersisa.

### calculateRefundAmount(): int
Menghitung jumlah refund dalam rupiah.

### getDaysUntilDeparture(): int
Menghitung hari tersisa hingga keberangkatan.

### getRefundPolicyDetails(): array
Mendapatkan detail kebijakan refund.

### getRefundPolicyTier(): string
Mendapatkan tier kebijakan (full_refund, partial_refund_high, dll).

### processRefund(string $reason = null): array
Memproses refund dengan integrasi payment gateway.

### scopeEligibleForRefund($query)
Scope untuk query booking yang eligible untuk refund.

## Keamanan

### Authorization
- User hanya bisa refund booking milik sendiri
- Admin dapat refund semua booking
- Validasi ownership di setiap endpoint

### Validation
- Validasi status booking dan payment
- Pengecekan kelayakan refund
- Konfirmasi user sebelum proses

### Logging
- Semua aktivitas refund dicatat
- Error handling dengan detail log
- Audit trail untuk compliance

## Error Handling

### Common Errors
- `BOOKING_NOT_FOUND`: Booking tidak ditemukan
- `BOOKING_NOT_ELIGIBLE`: Booking tidak eligible untuk refund
- `PAYMENT_GATEWAY_ERROR`: Error dari Midtrans
- `INSUFFICIENT_PERMISSIONS`: User tidak memiliki akses

### Response Format
```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field": ["Validation error"]
    }
}
```

## Testing

### Manual Testing
1. Akses halaman `/refund`
2. Pilih booking yang eligible
3. Submit refund request
4. Cek email notification
5. Verify di database

### Command Testing
```bash
# Test dry-run
php artisan refund:process-automatic --dry-run

# Test dengan limit kecil
php artisan refund:process-automatic --limit=1
```

## Deployment Notes

### Requirements
- Laravel 10+
- Midtrans PHP SDK
- Queue system untuk notifications
- Email configuration

### Migration
```bash
# Run migrations jika belum
php artisan migrate

# Publish notifications table jika belum ada
php artisan notifications:table
php artisan migrate
```

### Configuration
- Set Midtrans credentials di `.env`
- Configure email settings
- Set queue driver untuk notifications
- Set `REFUND_ENABLED=false` untuk menonaktifkan sistem refund

### Disabling Refund System
Untuk menonaktifkan sementara sistem refund:

1. **Environment Variable**: Set `REFUND_ENABLED=false` di file `.env`
2. **Frontend**: Halaman refund akan menampilkan pesan bahwa layanan dinonaktifkan
3. **API**: Semua endpoint refund akan mengembalikan status 503 (Service Unavailable)
4. **Model**: Method `canBeRefunded()` akan selalu return `false`

**Untuk mengaktifkan kembali**: Ubah `REFUND_ENABLED=true` di `.env`

## Monitoring

### Logs
- Check `storage/logs/laravel.log` untuk refund activities
- Monitor Midtrans dashboard untuk payment status
- Database notifications table untuk user notifications

### Metrics
- Total refunds processed
- Success/failure rates
- Average processing time
- User satisfaction (optional)

## Future Enhancements

### Possible Improvements
1. **Dashboard Admin**: Interface untuk monitoring refunds
2. **Bulk Refund**: Proses multiple bookings sekaligus
3. **Custom Policies**: Kebijakan refund per travel package
4. **Integration**: Webhook dari payment gateway
5. **Analytics**: Reporting dan analytics refund
6. **Mobile App**: API untuk mobile application

### Technical Debt
1. Add more comprehensive tests
2. Implement caching for policy calculations
3. Add rate limiting for API endpoints
4. Implement webhook verification
5. Add database indexes for performance

## Support

Untuk pertanyaan atau issue terkait sistem refund:
1. Check logs di `storage/logs/`
2. Verify Midtrans configuration
3. Test dengan dry-run mode
4. Contact development team jika diperlukan

---

**Version**: 2.0  
**Last Updated**: January 2025  
**Author**: AI Assistant  
**Status**: Production Ready