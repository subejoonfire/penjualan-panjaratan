# Fonnte WhatsApp API Setup Guide

## Masalah: "Gagal mengirim WhatsApp. Silakan coba lagi atau gunakan metode email."

### Root Cause
Token Fonnte belum dikonfigurasi dengan benar di file `.env`.

### Solusi

#### 1. **Dapatkan Token Fonnte**
1. Daftar di [Fonnte](https://fonnte.com)
2. Buat device WhatsApp
3. Salin token dari dashboard Fonnte

#### 2. **Konfigurasi Environment**
Edit file `.env` dan ganti nilai `FONNTE_TOKEN`:

```env
# Token Fonnte untuk WhatsApp
FONNTE_TOKEN=your_actual_fonnte_token_here
FONNTE_URL=https://api.fonnte.com/send
```

#### 3. **Verifikasi Konfigurasi**
```bash
# Check if token is set
grep FONNTE_TOKEN .env

# Should show:
# FONNTE_TOKEN=your_actual_fonnte_token_here
```

### Testing Mode (Tanpa Token Fonnte)

Jika Anda belum punya token Fonnte, sistem akan menampilkan pesan error yang jelas. Untuk testing, Anda bisa:

#### 1. **Simulasi Pengiriman WhatsApp**
Tambahkan kode berikut di `PasswordResetController.php` untuk testing:

```php
// Di method sendWhatsAppResetCode, sebelum try-catch
if ($fonnteToken === 'isi_token_fonnte_anda_disini' || empty($fonnteToken)) {
    // Simulasi pengiriman untuk testing
    Log::info('WhatsApp simulation mode - no real token configured', [
        'phone' => $phone,
        'token' => $token,
        'message' => $message
    ]);
    
    // Return success simulation
    return [
        'status' => true,
        'message' => 'WhatsApp sent successfully (simulation mode)',
        'simulation' => true
    ];
}
```

#### 2. **Test dengan Email**
Gunakan metode email untuk testing password reset:
- Input email yang valid
- Pilih metode "Email"
- Cek inbox atau folder spam

### Konfigurasi Fonnte yang Benar

#### 1. **Format Request yang Benar**
Berdasarkan `SendVerificationWaJob.php` yang sudah berfungsi:

```php
$response = Http::withHeaders([
    'Authorization' => $fonnteToken,
])->asForm()->post('https://api.fonnte.com/send', [
    'target' => $phone,
    'message' => $message,
]);
```

#### 2. **Format Nomor Telepon**
- Pastikan format: `628123456789` (62xxx)
- Jangan gunakan: `08123456789` atau `8123456789`

#### 3. **Format Pesan**
```php
$message = "🔐 *KODE RESET PASSWORD*\n\n";
$message .= "Kode reset password Anda: *{$token}*\n\n";
$message .= "Gunakan kode ini untuk mereset password akun Penjualan Panjaratan Anda.\n";
$message .= "⏰ Kode berlaku selama 15 menit.\n\n";
$message .= "⚠️ Jangan bagikan kode ini kepada siapa pun.\n";
$message .= "🔒 Jika Anda tidak meminta reset password, abaikan pesan ini.";
```

### Troubleshooting

#### 1. **"Fonnte token not configured"**
- Pastikan `FONNTE_TOKEN` sudah diset di `.env`
- Restart server setelah mengubah `.env`

#### 2. **"Fonnte API returned status: 401"**
- Token tidak valid atau expired
- Periksa token di dashboard Fonnte

#### 3. **"Fonnte API returned status: 400"**
- Format nomor telepon salah
- Pastikan format: `628123456789`

#### 4. **"WhatsApp API error"**
- Device WhatsApp tidak terhubung
- Periksa status device di dashboard Fonnte

### Log Messages

#### Success:
```
[INFO] Sending WhatsApp reset code: phone=628123456789, token=ABC123, fonnte_token_exists=true
[INFO] Fonnte API response: status_code=200, response_body=...
```

#### Error:
```
[ERROR] Failed to send WhatsApp reset code: phone=628123456789, error=Fonnte token not configured
```

### Testing Steps

#### 1. **Setup Database dan Test Data**
```bash
php setup_database.php
```

#### 2. **Test dengan Email (Recommended untuk Testing)**
- Buka `/password/reset`
- Input: `test@example.com`
- Pilih: "Email"
- Cek inbox

#### 3. **Test dengan WhatsApp (Perlu Token Fonnte)**
- Buka `/password/reset`
- Input: `081234567890` (akan dinormalisasi ke `6281234567890`)
- Pilih: "WhatsApp"
- Cek WhatsApp

### Test Accounts

Setelah menjalankan seeder:

| Email | Phone | Password |
|-------|-------|----------|
| test@example.com | 628765432109 | password123 |
| customer1@example.com | 81234567891 | customer123 |

### Next Steps

1. **Untuk Production**: Dapatkan token Fonnte yang valid
2. **Untuk Testing**: Gunakan metode email atau simulasi
3. **Untuk Development**: Setup token Fonnte untuk testing real

### Contact Support

Jika masih ada masalah:
1. Periksa logs di `storage/logs/laravel.log`
2. Pastikan token Fonnte valid
3. Periksa format nomor telepon
4. Verifikasi device WhatsApp terhubung