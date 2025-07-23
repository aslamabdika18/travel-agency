<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $booking->booking_reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 10px;
            color: #666;
            line-height: 1.3;
        }
        
        .invoice-info {
            text-align: right;
            flex: 1;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        
        .invoice-details {
            font-size: 11px;
        }
        
        .invoice-details div {
            margin-bottom: 3px;
        }
        
        .customer-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        .customer-info {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
        }
        
        .booking-details {
            margin-bottom: 30px;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .detail-label {
            font-weight: bold;
            color: #475569;
        }
        
        .detail-value {
            color: #1e293b;
        }
        
        .package-info {
            background-color: #f0f9ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .package-name {
            font-size: 16px;
            font-weight: bold;
            color: #0369a1;
            margin-bottom: 10px;
        }
        
        .price-breakdown {
            margin-bottom: 30px;
        }
        
        .price-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .price-table th,
        .price-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .price-table th {
            background-color: #f8fafc;
            font-weight: bold;
            color: #374151;
        }
        
        .price-table .text-right {
            text-align: right;
        }
        
        .total-section {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .total-final {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            border-top: 2px solid #2563eb;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .payment-info {
            margin-bottom: 30px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-paid {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
        
        .thank-you {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ $company['name'] }}</div>
                <div class="company-details">
                    {{ $company['address'] }}<br>
                    Telp: {{ $company['phone'] }}<br>
                    Email: {{ $company['email'] }}<br>
                    Website: {{ $company['website'] }}
                </div>
            </div>
            <div class="invoice-info">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-details">
                    <div><strong>No. Invoice:</strong> {{ $invoice_number }}</div>
                    <div><strong>Tanggal:</strong> {{ $invoice_date }}</div>
                    <div><strong>Booking Ref:</strong> {{ $booking->booking_reference }}</div>
                </div>
            </div>
        </div>
        
        <!-- Customer Information -->
        <div class="customer-section">
            <div class="section-title">Informasi Pelanggan</div>
            <div class="customer-info">
                <div><strong>Nama:</strong> {{ $user->name }}</div>
                <div><strong>Email:</strong> {{ $user->email }}</div>
                @if($user->phone)
                <div><strong>Telepon:</strong> {{ $user->phone }}</div>
                @endif
            </div>
        </div>
        
        <!-- Travel Package Information -->
        <div class="booking-details">
            <div class="section-title">Detail Paket Wisata</div>
            <div class="package-info">
                <div class="package-name">{{ $travel_package->name }}</div>
                @if($travel_package->description)
                <div style="margin-bottom: 10px; font-size: 11px; color: #64748b;">
                    {{ Str::limit($travel_package->description, 200) }}
                </div>
                @endif
                <div class="details-grid">
                    <div>
                        <div class="detail-item">
                            <span class="detail-label">Tanggal Booking:</span>
                            <span class="detail-value">{{ $booking->booking_date->format('d M Y') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Jumlah Peserta:</span>
                            <span class="detail-value">{{ $booking->person_count }} orang</span>
                        </div>
                        @if($booking->special_requests)
                        <div class="detail-item">
                            <span class="detail-label">Permintaan Khusus:</span>
                            <span class="detail-value">{{ $booking->special_requests }}</span>
                        </div>
                        @endif
                    </div>
                    <div>
                        <div class="detail-item">
                            <span class="detail-label">Durasi:</span>
                            <span class="detail-value">{{ $travel_package->duration }} hari</span>
                        </div>
                        @if($travel_package->location)
                        <div class="detail-item">
                            <span class="detail-label">Lokasi:</span>
                            <span class="detail-value">{{ $travel_package->location }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Price Breakdown -->
        <div class="price-breakdown">
            <div class="section-title">Rincian Harga</div>
            <table class="price-table">
                <thead>
                    <tr>
                        <th>Deskripsi</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-right">Harga Satuan</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $travel_package->name }}</td>
                        <td class="text-right">{{ $booking->person_count }} orang</td>
                        <td class="text-right">{{ formatRupiah($booking->base_price) }}</td>
                        <td class="text-right">{{ formatRupiah($booking->base_price * $booking->person_count) }}</td>
                    </tr>
                    @if($booking->additional_price > 0)
                    <tr>
                        <td>Biaya Tambahan</td>
                        <td class="text-right">1</td>
                        <td class="text-right">{{ formatRupiah($booking->additional_price) }}</td>
                        <td class="text-right">{{ formatRupiah($booking->additional_price) }}</td>
                    </tr>
                    @endif
                    @if($booking->tax_amount > 0)
                    <tr>
                        <td>Pajak & Biaya Admin</td>
                        <td class="text-right">1</td>
                        <td class="text-right">{{ formatRupiah($booking->tax_amount) }}</td>
                        <td class="text-right">{{ formatRupiah($booking->tax_amount) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            
            <div class="total-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>{{ formatRupiah(($booking->base_price * $booking->person_count) + $booking->additional_price) }}</span>
                </div>
                @if($booking->tax_amount > 0)
                <div class="total-row">
                    <span>Pajak & Biaya Admin:</span>
                    <span>{{ formatRupiah($booking->tax_amount) }}</span>
                </div>
                @endif
                <div class="total-row total-final">
                    <span>TOTAL PEMBAYARAN:</span>
                    <span>{{ formatRupiah($payment->total_price) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Payment Information -->
        <div class="payment-info">
            <div class="section-title">Informasi Pembayaran</div>
            <div class="details-grid">
                <div>
                    <div class="detail-item">
                        <span class="detail-label">Metode Pembayaran:</span>
                        <span class="detail-value">{{ $payment->gateway_status ?? 'Online Payment' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Transaction ID:</span>
                        <span class="detail-value">{{ $payment->transaction_id }}</span>
                    </div>
                </div>
                <div>
                    <div class="detail-item">
                        <span class="detail-label">Tanggal Pembayaran:</span>
                        <span class="detail-value">{{ $payment->payment_date->format('d M Y H:i') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">
                            <span class="status-badge status-paid">LUNAS</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">Terima kasih telah memilih layanan kami!</div>
            <div>
                Invoice ini dibuat secara otomatis pada {{ now()->format('d M Y H:i') }}<br>
                Untuk pertanyaan lebih lanjut, silakan hubungi customer service kami.
            </div>
        </div>
    </div>
</body>
</html>