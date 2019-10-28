<?php
declare(strict_types = 1);
namespace Notifications\Notification;

interface NotificationInterface
{

    /**
     * Get/Set before send callback.
     *
     * @deprecated 2.2.0 Use setBeforeSendCallback()/getBeforeSendCallback() instead.
     * @param string|array|null $class Name of the class and method
     * - Pass a string in the class::method format to call a static method
     * - Pass an array in the [class => method] format to call a non static method
     * @param array $args the method parameters you want to pass to the called method
     * @return array|self
     */
    public function beforeSendCallback($class = null, array $args = []);

    /**
     * Get/Set after send callback.
     *
     * @deprecated 2.2.0 Use setAfterSendCallback()/getAfterSendCallback() instead.
     * @param string|array|null $class Name of the class and method
     * - Pass a string in the class::method format to call a static method
     * - Pass an array in the [class => method] format to call a non static method
     * @param array $args the method parameters you want to pass to the called method
     * @return array|self
     */
    public function afterSendCallback($class = null, array $args = []);

    /**
     * Get/Set Queue Options.
     *
     * @deprecated 2.2.0 Use setQueueOptions()/getQueueOptions() instead.
     * @param array|null $options Queue options
     *
     * ### Supported options
     *
     * - attempts: how often the notification will be executed again after failure
     * - attempts_delay: how long it takes in seconds until the notification will be executed again
     * - delay: how long it takes until the notification will be executed for the first time  in seconds
     * - expires_in: how long the notification will stay in the queue in seconds
     * - queue: name of the queue
     * @return array|self
     */
    public function queueOptions(array $options = null);

    /**
     * Push the Notification into the queue
     *
     * @return bool
     */
    public function push(): bool;

    /**
     * Get/Set locale used for the notification
     *
     * @deprecated 2.2.0 Use setLocale()/getLocale() instead.
     * @param string $locale The name of the locale to set
     * @return string|self
     */
    public function locale(string $locale = null);
}
