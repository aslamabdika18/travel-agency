<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment - Sumatra Tour Travel</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .loader {
            width: 50px;
            height: 50px;
            border: 4px solid #e5e7eb;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .message {
            text-align: center;
            color: #6b7280;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div>
        <div class="loader"></div>
        <div class="message">
            <p>Redirecting...</p>
        </div>
    </div>

    <!-- Payment Callback JavaScript -->
    <script src="{{ asset('js/payment-callback-simple.js') }}"></script>
</body>
</html>