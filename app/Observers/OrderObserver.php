<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Notification;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if status was changed
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;
            
            // Send notification to customer
            $customer = $order->cart->user;
            
            $this->sendStatusNotification($customer->id, $order, $oldStatus, $newStatus);
            
            // Also send notification to seller if needed
            $orderDetails = $order->cart->cartDetails;
            $sellers = [];
            
            foreach ($orderDetails as $detail) {
                $sellerId = $detail->product->iduserseller;
                if (!in_array($sellerId, $sellers)) {
                    $sellers[] = $sellerId;
                    $this->sendStatusNotificationToSeller($sellerId, $order, $oldStatus, $newStatus);
                }
            }
        }
    }
    
    private function sendStatusNotification($userId, $order, $oldStatus, $newStatus)
    {
        $statusLabels = [
            'pending' => 'Menunggu Konfirmasi',
            'processing' => 'Sedang Diproses',
            'shipped' => 'Sedang Dikirim',
            'delivered' => 'Telah Diterima',
            'cancelled' => 'Dibatalkan',
            'completed' => 'Selesai'
        ];
        
        $title = 'Status Pesanan Diperbarui';
        $message = "Pesanan #{$order->order_number} telah diperbarui dari '{$statusLabels[$oldStatus]}' menjadi '{$statusLabels[$newStatus]}'.";
        
        if ($newStatus === 'shipped') {
            $message .= " Pesanan Anda sedang dalam perjalanan.";
        } elseif ($newStatus === 'delivered') {
            $message .= " Pesanan Anda telah sampai. Terima kasih!";
        } elseif ($newStatus === 'cancelled') {
            $message .= " Mohon maaf atas ketidaknyamanannya.";
        }
        
        Notification::create([
            'iduser' => $userId,
            'title' => $title,
            'notification' => $message,
            'type' => 'order',
            'readstatus' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    private function sendStatusNotificationToSeller($sellerId, $order, $oldStatus, $newStatus)
    {
        $statusLabels = [
            'pending' => 'Menunggu Konfirmasi',
            'processing' => 'Sedang Diproses', 
            'shipped' => 'Sedang Dikirim',
            'delivered' => 'Telah Diterima',
            'cancelled' => 'Dibatalkan',
            'completed' => 'Selesai'
        ];
        
        $title = 'Update Status Pesanan';
        $message = "Pesanan #{$order->order_number} telah diperbarui menjadi '{$statusLabels[$newStatus]}'. Silakan periksa dashboard untuk detail lebih lanjut.";
        
        Notification::create([
            'iduser' => $sellerId,
            'title' => $title,
            'notification' => $message,
            'type' => 'order',
            'readstatus' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}