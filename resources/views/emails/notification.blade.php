<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Email</title>
    <style>
    /* Global Styles */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .email-container {
        max-width: 600px;
        margin: 20px auto;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .email-header {
        background-color: #4CAF50;
        color: #ffffff;
        text-align: center;
        padding: 20px;
    }

    .email-header h1 {
        margin: 0;
        font-size: 20px;
    }

    .email-body {
        padding: 20px;
        color: #333333;
    }

    .email-body p {
        font-size: 16px;
        line-height: 1.5;
        margin: 10px 0;
    }

    .email-body .highlight {
        background-color: #f9f9f9;
        border-left: 4px solid #4CAF50;
        padding: 10px;
        margin: 20px 0;
        font-style: italic;
    }

    .email-body .action-button {
        display: inline-block;
        margin: 20px 0;
        padding: 12px 20px;
        background-color: #4CAF50;
        color: #ffffff;
        text-decoration: none;
        font-weight: bold;
        border-radius: 4px;
        text-align: center;
    }

    .email-footer {
        background-color: #f4f4f4;
        color: #777777;
        text-align: center;
        padding: 20px;
        font-size: 14px;
    }

    .email-footer a {
        color: #4CAF50;
        text-decoration: none;
    }

    /* Responsive Styles */
    @media (max-width: 600px) {
        .email-container {
            width: 100%;
        }
    }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>Notifikasi</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            {!! nl2br($bodyMessage) !!}

            <div class="highlight">
                <p>Detail Status: {{ $status }}</p>
            </div>

            <p>Untuk melihat detail lebih lanjut, klik tombol di bawah ini:</p>
            <a href="{{ $actionUrl }}" class="action-button">Lihat Detail</a>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>Terima kasih,</p>
            <p><strong>Keluarga Ma Haya</strong></p>
            <p>Butuh bantuan? <a href="mailto:support@example.com">Hubungi Kami</a></p>
        </div>
    </div>
</body>

</html>