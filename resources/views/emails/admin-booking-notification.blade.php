{{-- resources/views/emails/admin-booking-notification.blade.php --}}
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Notification - Admin</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 700px;
            margin: 20px auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #e94560 0%, #c73e56 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
        }
        .alert-badge {
            background: #ffd700;
            color: #333;
            display: inline-block;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        .content {
            padding: 30px;
        }
        .section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .section h2 {
            margin-top: 0;
            color: #e94560;
            font-size: 18px;
            border-left: 4px solid #e94560;
            padding-left: 12px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 12px;
            margin-top: 15px;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .info-value {
            color: #333;
        }
        .total-amount {
            font-size: 28px;
            font-weight: bold;
            color: #e94560;
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 8px;
            margin-top: 15px;
        }
        .action-button {
            display: inline-block;
            background: #e94560;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 50px;
            margin-top: 15px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #e0e0e0;
        }
        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🔔 New Booking Alert!</h1>
        <div class="alert-badge">ACTION REQUIRED</div>
    </div>

    <div class="content">
        <p>A new booking has been created on the system. Please review the details below:</p>

        <div class="section">
            <h2>📋 Booking Information</h2>
            <div class="info-grid">
                <div class="info-label">Booking Reference:</div>
                <div class="info-value"><strong>{{ $booking->booking_reference ?? 'N/A' }}</strong></div>

                <div class="info-label">Booking Type:</div>
                <div class="info-value">{{ ucfirst($booking->booking_type ?? 'N/A') }}</div>

                <div class="info-label">Item Name:</div>
                <div class="info-value">{{ $booking->item_name ?? 'N/A' }}</div>

                <div class="info-label">Item ID:</div>
                <div class="info-value">#{{ $booking->item_id ?? 'N/A' }}</div>

                <div class="info-label">Booking Date:</div>
                <div class="info-value">{{ $bookingDate }}</div>

                <div class="info-label">Payment Method:</div>
                <div class="info-value">{{ ucfirst($booking->payment_method ?? 'N/A') }}</div>

                <div class="info-label">Payment Status:</div>
                <div class="info-value">
                        <span style="background: #fff3cd; color: #856404; padding: 3px 10px; border-radius: 50px; font-size: 12px;">
                            {{ ucfirst($booking->payment_status ?? 'Pending') }}
                        </span>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>👤 Customer Details</h2>
            <div class="info-grid">
                <div class="info-label">Full Name:</div>
                <div class="info-value">{{ $booking->first_name ?? '' }} {{ $booking->last_name ?? '' }}</div>

                <div class="info-label">Email:</div>
                <div class="info-value">{{ $booking->email ?? 'N/A' }}</div>

                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $booking->phone ?? 'N/A' }}</div>

                <div class="info-label">Nationality:</div>
                <div class="info-value">{{ $booking->nationality ?? 'N/A' }}</div>

                @if($booking->passport_number)
                    <div class="info-label">Passport Number:</div>
                    <div class="info-value">{{ $booking->passport_number }}</div>

                    <div class="info-label">Passport Expiry:</div>
                    <div class="info-value">{{ $booking->passport_expiry ?? 'N/A' }}</div>
                @endif
            </div>
        </div>

        <div class="section">
            <h2>💰 Payment Details</h2>
            <div class="info-grid">
                <div class="info-label">Base Price:</div>
                <div class="info-value">${{ number_format($booking->base_price ?? 0, 2) }}</div>

                <div class="info-label">Surcharge:</div>
                <div class="info-value">${{ number_format($booking->surcharge ?? 0, 2) }}</div>
            </div>
            <div class="total-amount">
                Total Amount: ${{ number_format($booking->total_amount ?? 0, 2) }}
            </div>
        </div>

        @if($booking->additional_data)
            <div class="section">
                <h2>📊 Additional Information</h2>
                <pre style="background: white; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 12px;">
                    {{ json_encode(json_decode($booking->additional_data), JSON_PRETTY_PRINT) }}
                </pre>
            </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ url('/admin/bookings/' . ($booking->id ?? '')) }}" class="action-button">
                View Full Booking Details →
            </a>
        </div>

        <hr>

        <p style="font-size: 14px; color: #666;">
            <strong>Next Steps:</strong><br>
            1. Verify customer information<br>
            2. Confirm payment status<br>
            3. Process booking in the system<br>
            4. Send further instructions to customer
        </p>
    </div>

    <div class="footer">
        <p>This is an automated notification from your Travel Agency System.</p>
        <p>You can manage all bookings from the admin dashboard.</p>
    </div>
</div>
</body>
</html>
