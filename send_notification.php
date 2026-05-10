<?php
require 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

// 1. Point to your downloaded Service Account JSON
$factory = (new Factory)->withServiceAccount('path/to/your-service-account.json');
$messaging = $factory->createMessaging();

// 2. The 'Device Token' (You get this from your JavaScript/Frontend)
$deviceToken = 'USER_DEVICE_TOKEN_FROM_DATABASE';

// 3. Create the notification
$message = CloudMessage::withTarget('token', $deviceToken)
    ->withNotification(Notification::create('Barangay San Pedro Alert', 'New community meeting scheduled for tomorrow!'))
    ->withData(['click_action' => 'FLUTTER_NOTIFICATION_CLICK']); // Optional extra data

// 4. Send it!
try {
    $messaging->send($message);
    echo "Notification sent successfully!";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}