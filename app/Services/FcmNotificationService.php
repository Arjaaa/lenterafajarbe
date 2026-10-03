<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use Illuminate\Support\Facades\Log;

class FcmNotificationService
{
    private $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/credentials.json'));

        $this->messaging = $factory->createMessaging();
    }

   public function sendToUser(?string $fcmToken, string $title, string $body, array $data = [], ?string $imageUrl = null): bool
{
    if (!$fcmToken) {
        Log::info("FCM skip: user tidak punya fcm_token.");
        return false;
    }

    try {
        $notification = FcmNotification::create($title, $body);
        if ($imageUrl) {
            $notification = $notification->withImageUrl($imageUrl);
        }

        $message = CloudMessage::withTarget('token', $fcmToken)
            ->withNotification($notification)
            ->withData($data);

        $this->messaging->send($message);
        Log::info("FCM notif berhasil dikirim ke token: " . substr($fcmToken, 0, 15) . "...");
        return true;
    } catch (\Kreait\Firebase\Exception\Messaging\NotFound | \Kreait\Firebase\Exception\Messaging\InvalidArgument $e) {
        // ✅ Token udah invalid/expired — bersihin dari DB biar gak dicoba lagi
        \App\Models\User::where('fcm_token', $fcmToken)->update(['fcm_token' => null]);
        Log::warning("FCM token invalid, dihapus dari DB: " . substr($fcmToken, 0, 15));
        return false;
    } catch (\Exception $e) {
        Log::error("FCM gagal kirim notif: " . $e->getMessage());
        return false;
    }
}
}