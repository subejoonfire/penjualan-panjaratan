# WhatsApp Password Reset Troubleshooting Guide

## Problem: "Nomor WhatsApp tidak ditemukan dalam sistem"

### Root Causes and Solutions

#### 1. **Database Issues**
**Problem**: Database belum dibuat atau belum ada data user
**Solution**:
```bash
# Run the setup script
php setup_database.php

# Or manually:
php artisan migrate:fresh
php artisan db:seed
```

#### 2. **Phone Number Format Mismatch**
**Problem**: Format nomor di database berbeda dengan input user
**Solution**: 
- Sistem sekarang mendukung multiple format:
  - `628123456789` (62xxx)
  - `08123456789` (08xxx)
  - `8123456789` (8xxx)

#### 3. **Missing Database Index**
**Problem**: Field phone tidak memiliki index untuk performa query
**Solution**: 
- Migration `2025_01_15_130000_add_index_to_users_phone_field.php` sudah dibuat
- Jalankan: `php artisan migrate`

#### 4. **Cache Issues**
**Problem**: Cache menyimpan hasil pencarian yang salah
**Solution**: 
- Cache sudah dihapus dari PasswordResetController
- Gunakan query langsung ke database

### Testing Steps

#### 1. **Verify Database Setup**
```bash
# Check if database exists
ls -la database/database.sqlite

# Check if tables exist
php artisan migrate:status

# Check if users exist
php artisan tinker
>>> App\Models\User::all(['id', 'username', 'email', 'phone'])
```

#### 2. **Test Phone Number Formats**
Sistem sekarang mendukung format berikut:
- Input: `08123456789` → Cari: `08123456789`, `8123456789`, `628123456789`
- Input: `8123456789` → Cari: `8123456789`, `08123456789`, `628123456789`
- Input: `628123456789` → Cari: `628123456789`, `8123456789`, `08123456789`

#### 3. **Check Logs**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Look for these log entries:
# - "Phone number normalized"
# - "Searching for phone number"
# - "User found with phone"
# - "All users with phone numbers"
```

### Test Data Available

Setelah menjalankan seeder, user berikut tersedia:

| Username | Email | Phone | Password |
|----------|-------|-------|----------|
| admin | admin@example.com | 628123456789 | admin123 |
| seller1 | seller1@example.com | 081234567890 | seller123 |
| customer1 | customer1@example.com | 81234567891 | customer123 |
| testuser | test@example.com | 628765432109 | password123 |

### Debugging Commands

#### 1. **Check User Data**
```php
// In tinker
$users = App\Models\User::all(['id', 'username', 'email', 'phone']);
foreach($users as $user) {
    echo "ID: {$user->id}, Username: {$user->username}, Email: {$user->email}, Phone: {$user->phone}\n";
}
```

#### 2. **Test Phone Normalization**
```php
// In tinker
echo App\Models\User::normalizePhone('08123456789'); // Should return: 628123456789
echo App\Models\User::normalizePhone('8123456789');  // Should return: 628123456789
echo App\Models\User::normalizePhone('628123456789'); // Should return: 628123456789
```

#### 3. **Test User Search**
```php
// In tinker
$user = App\Models\User::findByPhone('08123456789');
if($user) {
    echo "Found user: {$user->username} with phone: {$user->phone}\n";
} else {
    echo "User not found\n";
}
```

### Common Issues and Fixes

#### 1. **"Database file not found"**
```bash
touch database/database.sqlite
php artisan migrate:fresh
php artisan db:seed
```

#### 2. **"Table users doesn't exist"**
```bash
php artisan migrate
```

#### 3. **"No users in database"**
```bash
php artisan db:seed
```

#### 4. **"Phone number not found"**
- Check if user exists in database
- Verify phone number format
- Check logs for debugging info

### Verification Steps

#### 1. **Database Verification**
```bash
# Check database file
ls -la database/database.sqlite

# Check tables
sqlite3 database/database.sqlite ".tables"

# Check users table
sqlite3 database/database.sqlite "SELECT id, username, email, phone FROM users;"
```

#### 2. **Application Verification**
```bash
# Check if Laravel can connect to database
php artisan tinker
>>> DB::table('users')->count()
```

#### 3. **Phone Number Verification**
```bash
# Test phone number search
php artisan tinker
>>> App\Models\User::findByPhone('08123456789')
```

### Expected Behavior

#### 1. **Successful Password Reset**
1. User input nomor WhatsApp
2. Sistem normalisasi nomor ke format 62xxx
3. Sistem cari user dengan multiple format
4. User ditemukan
5. Kode reset dikirim via WhatsApp
6. User verifikasi kode
7. Password berhasil direset

#### 2. **Error Handling**
1. User input nomor WhatsApp
2. Sistem normalisasi nomor
3. Sistem cari user dengan multiple format
4. User tidak ditemukan
5. Sistem tampilkan error dengan format yang didukung
6. Sistem log semua user untuk debugging

### Log Messages to Look For

#### Success Logs:
```
[INFO] Phone number normalized: original=08123456789, normalized=628123456789
[INFO] Searching for phone number: original=08123456789, normalized=628123456789
[INFO] User found with phone: user_id=1, phone=081234567890
[INFO] Password reset WhatsApp sent successfully
```

#### Error Logs:
```
[INFO] Phone number normalized: original=999999999, normalized=62999999999
[INFO] Searching for phone number: original=999999999, normalized=62999999999
[INFO] All users with phone numbers: users=[...]
[ERROR] Nomor WhatsApp tidak ditemukan dalam sistem
```

### Performance Considerations

#### 1. **Database Indexes**
- Field `phone` memiliki unique index
- Field `phone` memiliki regular index untuk query performance
- Field `created_at` di `phone_password_reset_tokens` memiliki index

#### 2. **Query Optimization**
- Multiple format search menggunakan loop
- Logging untuk debugging
- Cache dihapus untuk menghindari stale data

#### 3. **Memory Usage**
- Query menggunakan `first()` bukan `get()`
- Logging dibatasi untuk debugging
- Session cleanup yang proper

### Security Considerations

#### 1. **Input Validation**
- Phone number format validation
- Length validation (10-13 digits)
- Prefix validation (62, 08, 8, 0)

#### 2. **Rate Limiting**
- Send reset code: 5 attempts per minute
- Verify code: 10 attempts per minute
- Reset password: 3 attempts per minute

#### 3. **Logging**
- IP address logging
- User agent logging
- Phone number normalization logging
- Search attempts logging

### Next Steps

Jika masalah masih berlanjut:

1. **Check logs** untuk debugging info
2. **Verify database** setup dan data
3. **Test phone formats** dengan tinker
4. **Check migration status** dan run jika perlu
5. **Verify seeder** data creation

### Contact Support

Jika semua troubleshooting steps sudah dilakukan tapi masalah masih ada:
1. Collect logs dari `storage/logs/laravel.log`
2. Collect database dump
3. Provide steps to reproduce
4. Include phone number format yang digunakan