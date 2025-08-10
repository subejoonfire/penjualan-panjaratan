<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentCategory;
use App\Models\SellerPaymentMethod;
use App\Models\User;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bank Transfer', 'description' => 'Virtual Account / Rekening Bank'],
            ['name' => 'E-Wallet', 'description' => 'OVO, DANA, GoPay, ShopeePay'],
            ['name' => 'QRIS', 'description' => 'QRIS static/dynamic'],
            ['name' => 'COD', 'description' => 'Cash on Delivery'],
        ];

        foreach ($categories as $cat) {
            PaymentCategory::firstOrCreate(['name' => $cat['name']], [
                'description' => $cat['description'] ?? null,
                'is_active' => true,
            ]);
        }

        $bank = PaymentCategory::where('name', 'Bank Transfer')->first();
        $ewallet = PaymentCategory::where('name', 'E-Wallet')->first();
        $qris = PaymentCategory::where('name', 'QRIS')->first();

        $sellers = User::where('role', 'seller')->get();
        foreach ($sellers as $index => $seller) {
            // Bank BRI
            SellerPaymentMethod::create([
                'iduserseller' => $seller->id,
                'payment_category_id' => $bank->id,
                'method_name' => 'BRI',
                'account_name' => $seller->nickname ?? $seller->username,
                'account_number' => '0023' . str_pad((string)($seller->id), 6, '0', STR_PAD_LEFT),
                'instructions' => 'Transfer ke rekening BRI di atas lalu unggah bukti pembayaran.',
                'is_active' => true,
                'priority' => 1,
            ]);

            // DANA
            SellerPaymentMethod::create([
                'iduserseller' => $seller->id,
                'payment_category_id' => $ewallet->id,
                'method_name' => 'DANA',
                'account_name' => $seller->nickname ?? $seller->username,
                'account_number' => '08' . str_pad((string)($seller->id), 9, '0', STR_PAD_LEFT),
                'instructions' => 'Kirim via DANA ke nomor di atas lalu unggah bukti pembayaran.',
                'is_active' => true,
                'priority' => 2,
            ]);

            // QRIS (opsional)
            SellerPaymentMethod::create([
                'iduserseller' => $seller->id,
                'payment_category_id' => $qris->id,
                'method_name' => 'QRIS',
                'account_name' => null,
                'account_number' => null,
                'instructions' => 'Scan QRIS yang diberikan seller setelah checkout.',
                'is_active' => true,
                'priority' => 3,
            ]);
        }
    }
}