<?php
/**
 * Test script untuk simulasi callback Duitku
 * Jalankan dengan: php test_payment.php
 */

// Konfigurasi
$apiKey = '8ac867d0e05e06d2e26797b29aec2c7a';
$merchantCode = 'DS24203';
$merchantOrderId = 'TRX-20241201-000001'; // Ganti dengan transaction number yang valid
$resultCode = '00'; // 00 = success, 01 = failed

// Generate signature
$signature = md5($merchantCode . $merchantOrderId . $apiKey);

// Data callback
$callbackData = [
    'merchantOrderId' => $merchantOrderId,
    'resultCode' => $resultCode,
    'signature' => $signature,
    'reference' => 'TEST-' . time(),
    'amount' => 50000,
    'paymentMethod' => 'VA',
    'paymentCode' => 'BCA',
    'paymentName' => 'BCA Virtual Account',
    'paymentFee' => 0,
    'paymentFeeType' => 'FREE',
    'merchantCode' => $merchantCode
];

// URL callback (ganti dengan URL ngrok Anda)
$callbackUrl = 'https://d2176aae3759.ngrok-free.app/callback/duitku';

echo "Testing Duitku Callback\n";
echo "=======================\n";
echo "Callback URL: $callbackUrl\n";
echo "Merchant Order ID: $merchantOrderId\n";
echo "Result Code: $resultCode\n";
echo "Signature: $signature\n";
echo "Signature Input: $merchantCode$merchantOrderId$apiKey\n";
echo "\n";

// Kirim callback
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $callbackUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($callbackData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'User-Agent: Duitku-Callback-Test/1.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "Response:\n";
echo "HTTP Code: $httpCode\n";
echo "Response Body: $response\n";
if ($error) {
    echo "cURL Error: $error\n";
}

echo "\nTest completed.\n";
?>