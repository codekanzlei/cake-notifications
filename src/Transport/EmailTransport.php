<?php
declare(strict_types = 1);
namespace Notifications\Transport;

use Cake\Core\Configure;
use Cake\I18n\I18n;
use josegonzalez\Queuesadilla\Job\Base;
use Notifications\Notification\EmailNotification;
use Notifications\Notification\NotificationInterface;
use Notifications\Transport\TransportInterface;

class EmailTransport extends Transport implements TransportInterface
{

    /**
     * Send function
     *
     * @param \Notifications\Notification\NotificationInterface $notification Notification object
     * @param string|array|null $content String with message or array with messages
     * @return \Notifications\Notification\NotificationInterface
     */
    public static function sendNotification(NotificationInterface $notification, $content = null): NotificationInterface
    {
        $beforeSendCallback = $notification->getBeforeSendCallback();
        self::_performCallback($beforeSendCallback, $notification);

        if ($notification->getLocale() !== null) {
            I18n::setLocale($notification->getLocale());
        } else {
            I18n::setLocale(Configure::read('Notifications.defaultLocale'));
        }

        $notification->email()->send($content);

        $afterSendCallback = $notification->getAfterSendCallback();
        self::_performCallback($afterSendCallback);

        return $notification;
    }

    /**
     * Process the job coming from the queue
     *
     * @param \josegonzalez\Queuesadilla\Job\Base $job Queuesadilla base job
     * @return \Notifications\Notification\NotificationInterface
     */
    public static function processQueueObject(Base $job): NotificationInterface
    {
        $notification = new EmailNotification();

        if ($job->data('beforeSendCallback') !== []) {
            foreach ($job->data('beforeSendCallback') as $callback) {
                $notification->addBeforeSendCallback($callback['class'], $callback['args']);
            }
        }
        if ($job->data('afterSendCallback') !== []) {
            foreach ($job->data('afterSendCallback') as $callback) {
                $notification->addAfterSendCallback($callback['class'], $callback['args']);
            }
        }
        if ($job->data('locale') !== '') {
            $notification->setLocale($job->data('locale'));
        }
        $notification->unserialize($job->data('email'));

        return self::sendNotification($notification);
    }
}
