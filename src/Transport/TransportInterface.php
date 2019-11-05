<?php
declare(strict_types = 1);
namespace Notifications\Transport;

use Notifications\Notification\NotificationInterface;

interface TransportInterface
{

    /**
     * sendNotification method
     *
     * @param \Notifications\Notification\NotificationInterface $notification Notification object
     * @param string|array|null $content String with message or array with messages
     * @return \Notifications\Notification\NotificationInterface
     */
    public static function sendNotification(
        NotificationInterface $notification,
        $content = null
    ): NotificationInterface;
}
