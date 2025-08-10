<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email - {{ config('app.name') }}</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f7fafc; margin:0; padding:0;">
    <div style="max-width: 480px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #e2e8f0; padding: 32px;">
        <h2 style="color: #2563eb; margin-bottom: 16px;">Verifikasi Email Anda</h2>
        <p>Halo <b>{{ $name }}</b>,</p>
        <p>Terima kasih telah mendaftar di <b>{{ config('app.name') }}</b>.<br>
        Berikut adalah kode verifikasi email Anda:</p>
        <div style="text-align: center; margin: 32px 0;">
            <span style="display: inline-block; font-size: 2.2em; letter-spacing: 8px; background: #f1f5f9; color: #2563eb; padding: 16px 32px; border-radius: 8px; font-weight: bold;">
                {{ $token }}
            </span>
        </div>
        <p>Silakan masukkan kode di atas pada halaman verifikasi email untuk mengaktifkan akun Anda.</p>
        <p style="color: #64748b; font-size: 0.95em; margin-top: 32px;">Jika Anda tidak merasa melakukan pendaftaran, abaikan email ini.</p>
        <p style="margin-top: 32px; color: #94a3b8; font-size: 0.9em;">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </div>
</body>
</html>