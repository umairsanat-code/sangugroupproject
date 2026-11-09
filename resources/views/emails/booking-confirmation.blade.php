{{-- resources/views/emails/booking-confirmation.blade.php --}}
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
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
            max-width: 600px;
            margin: 20px auto;
            padding: 0;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .reference {
            background: #e94560;
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            margin-top: 15px;
            font-size: 14px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .booking-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
        }
        .booking-details h3 {
            margin-top: 0;
            color: #1a1a2e;
            font-size: 18px;
            border-bottom: 2px solid #e94560;
            display: inline-block;
            padding-bottom: 5px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #666;
        }
        .detail-value {
            color: #333;
            font-weight: 500;
        }
        .price-breakdown {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .total {
            font-size: 24px;
            font-weight: 700;
            color: #e94560;
            text-align: right;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #e0e0e0;
        }
        .button {
            display: inline-block;
            background: #e94560;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 50px;
            margin-top: 20px;
            font-weight: 600;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
            }
            .content {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Booking Confirmed! ✈️</h1>
        <p>Thank you for choosing our services</p>
        <div class="reference">Reference: {{ $booking->booking_reference ?? 'N/A' }}</div>
    </div>

    <div class="content">
        <div class="greeting">
            Dear <strong>{{ $booking->first_name ?? '' }} {{ $booking->last_name ?? '' }}</strong>,
        </div>

        <p>Your booking has been successfully confirmed. Please review the details below:</p>

        <div class="booking-details">
            <h3>Booking Details</h3>

            <div class="detail-row">
                <span class="detail-label">Booking Reference:</span>
                <span class="detail-value">{{ $booking->booking_reference ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Booking Type:</span>
                <span class="detail-value">{{ ucfirst($booking->booking_type ?? 'N/A') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Item Name:</span>
                <span class="detail-value">{{ $booking->item_name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Booking Date:</span>
                <span class="detail-value">{{ $bookingDate }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">{{ ucfirst($booking->payment_method ?? 'N/A') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                        <span class="status-badge status-confirmed">
                            {{ ucfirst($booking->booking_status ?? 'Confirmed') }}
                        </span>
                    </span>
            </div>
        </div>

        <div class="booking-details">
            <h3>Customer Information</h3>

            <div class="detail-row">
                <span class="detail-label">Full Name:</span>
                <span class="detail-value">{{ $booking->first_name ?? '' }} {{ $booking->last_name ?? '' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $booking->email ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ $booking->phone ?? 'N/A' }}</span>
            </div>
            @if($booking->passport_number)
                <div class="detail-row">
                    <span class="detail-label">Passport Number:</span>
                    <span class="detail-value">{{ $booking->passport_number }}</span>
                </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Nationality:</span>
                <span class="detail-value">{{ $booking->nationality ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="booking-details">
            <h3>Price Breakdown</h3>

            <div class="price-breakdown">
                <div class="detail-row">
                    <span class="detail-label">Base Price:</span>
                    <span class="detail-value">${{ number_format($booking->base_price ?? 0, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Surcharge:</span>
                    <span class="detail-value">${{ number_format($booking->surcharge ?? 0, 2) }}</span>
                </div>
                <div class="total">
                    Total Amount: ${{ number_format($booking->total_amount ?? 0, 2) }}
                </div>
            </div>
        </div>

        <p style="text-align: center; margin-top: 30px;">
            <a href="{{ url('/my-bookings/' . ($booking->booking_reference ?? '')) }}" class="button">
                View My Booking
            </a>
        </p>

        <p style="margin-top: 30px;">
            For any questions or assistance, please contact our support team.<br>
            We look forward to serving you!
        </p>

        <p>Best regards,<br>
            <strong>Travel Agency Team</strong></p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Travel Agency. All rights reserved.</p>
        <p>This is an automated confirmation, please do not reply to this email.</p>
        <p>If you have any questions, contact us at support@travelagency.com</p>
    </div>
</div>
</body>
</html>
