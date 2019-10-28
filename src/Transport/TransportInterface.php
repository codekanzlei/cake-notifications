<?php
declare(strict_types = 1);
namespace Notifications\Transport;

use Notifications\Notification\Notification;

interface TransportInterface
{

    /**
     * sendNotification method
     *
     * @param \Notifications\Notification\Notification $notification Notification object
     * @param string|array|null $content String with message or array with messages
     * @return \Notifications\Notification\Notification
     */
    public static function sendNotification(Notification $notification, $content = null): Notification;
}
