<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Reset Password - Penjualan Panjaratan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .title {
            font-size: 20px;
            color: #1f2937;
            margin: 0;
        }
        .content {
            margin-bottom: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .reset-code {
            background-color: #f8fafc;
            border: 2px dashed #6b7280;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #2563eb;
            letter-spacing: 4px;
            margin: 10px 0;
        }
        .code-info {
            font-size: 14px;
            color: #6b7280;
            margin-top: 10px;
        }
        .warning {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning-title {
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 5px;
        }
        .warning-text {
            color: #991b1b;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6b7280;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Penjualan Panjaratan</div>
            <h1 class="title">Reset Password</h1>
        </div>

        <div class="content">
            <div class="greeting">
                Halo <strong>{{ $user->nickname ?? $user->username }}</strong>,
            </div>

            <p>Kami menerima permintaan untuk mereset password akun Anda. Gunakan kode verifikasi berikut untuk melanjutkan proses reset password:</p>

            <div class="reset-code">
                <div>Kode Verifikasi Anda:</div>
                <div class="code">{{ $resetCode }}</div>
                <div class="code-info">
                    Kode ini berlaku selama <strong>15 menit</strong>
                </div>
            </div>

            <p>Untuk melanjutkan reset password:</p>
            <ol>
                <li>Kembali ke halaman reset password di website</li>
                <li>Masukkan kode verifikasi di atas</li>
                <li>Buat password baru yang aman</li>
            </ol>

            <div class="warning">
                <div class="warning-title">⚠️ Penting untuk Keamanan</div>
                <div class="warning-text">
                    • Jangan bagikan kode ini kepada siapa pun<br>
                    • Jika Anda tidak meminta reset password, abaikan email ini<br>
                    • Kode akan expired dalam 15 menit
                </div>
            </div>

            <p>Jika Anda mengalami kesulitan, silakan hubungi tim support kami.</p>

            <p>Terima kasih,<br><strong>Tim Penjualan Panjaratan</strong></p>
        </div>

        <div class="footer">
            <p>
                Email ini dikirim secara otomatis. Mohon jangan membalas email ini.<br>
                © {{ date('Y') }} Penjualan Panjaratan. Semua hak dilindungi.
            </p>
        </div>
    </div>
</body>
</html>