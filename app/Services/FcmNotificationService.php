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

    public function sendToUser(
        ?string $fcmToken,
        string $title,
        string $body,
        array $data = []
    ): bool {
        if (!$fcmToken) {
            Log::info("FCM skip: user tidak punya fcm_token.");
            return false;
        }

        try {
            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification(
                    FcmNotification::create($title, $body)
                )
                ->withData($data);

            $this->messaging->send($message);

            Log::info(
                "FCM notif berhasil dikirim ke token: "
                . substr($fcmToken, 0, 15)
                . "..."
            );

            return true;
        } catch (\Exception $e) {
            Log::error(
                "FCM gagal kirim notif: " . $e->getMessage()
            );

            return false;
        }
    }
}
