<?php
declare(strict_types = 1);
namespace Notifications\Notification;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use InvalidArgumentException;
use Notifications\Notification\NotificationInterface;

abstract class Notification implements NotificationInterface
{
    /**
     * Before send callback.
     *
     * @var array
     */
    protected $_beforeSendCallback = [];

    /**
     * After send callback.
     *
     * @var array
     */
    protected $_afterSendCallback = [];

    /**
     * Queue options
     *
     * @var array
     */
    protected $_queueOptions = [];

    /**
     * Locale string
     *
     * @var string
     */
    protected $_locale = null;

    /**
     * Push the Notification into the queue
     *
     * @return bool
     */
    abstract public function push(): bool;

    /**
     * Send the Notification immediately
     *
     * @param string|array|null $content String with message or array with messages
     * @return \Notifications\Notification\NotificationInterface
     */
    abstract public function send($content = null): NotificationInterface;

    /**
     * Constructor
     *
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        if (Configure::read('Notifications.defaultLocale') === null) {
            throw new InvalidArgumentException("Notifications.defaultLocale is not configured");
        }
        if (Configure::check('Notifications.queueOptions') && is_array(Configure::read('Notifications.queueOptions'))) {
            $this->_queueOptions = Configure::read('Notifications.queueOptions');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSendCallback($class = null, array $args = [])
    {
        trigger_error(
            'Notification::beforeSendCallback() is deprecated.' .
            'Use Notification::setBeforeSendCallback()/getBeforeSendCallback() instead.',
            E_USER_DEPRECATED
        );

        if ($class === null) {
            return $this->_beforeSendCallback;
        }

        return $this->__setCallback('_beforeSendCallback', $class, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getBeforeSendCallback(): array
    {
        return $this->_beforeSendCallback;
    }

    /**
     * {@inheritdoc}
     */
    public function setBeforeSendCallback($class = null, array $args = []): NotificationInterface
    {
        return $this->__setCallback('_beforeSendCallback', $class, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function addBeforeSendCallback($class, array $args = [])
    {
        return $this->__addCallback('_beforeSendCallback', $class, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSendCallback($class = null, array $args = [])
    {
        trigger_error(
            'Notification::afterSendCallback() is deprecated.' .
            'Use Notification::setAfterSendCallback()/getAfterSendCallback() instead.',
            E_USER_DEPRECATED
        );

        if ($class === null) {
            return $this->_afterSendCallback;
        }

        return $this->__setCallback('_afterSendCallback', $class, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getAfterSendCallback(): array
    {
        return $this->_afterSendCallback;
    }

    /**
     * {@inheritdoc}
     */
    public function setAfterSendCallback($class = null, array $args = []): NotificationInterface
    {
        return $this->__setCallback('_afterSendCallback', $class, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function addAfterSendCallback($class, array $args = [])
    {
        return $this->__addCallback('_afterSendCallback', $class, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function queueOptions(array $options = null)
    {
        trigger_error(
            'Notification::queueOptions() is deprecated.' .
            'Use Notification::setQueueOptions()/getQueueOptions() instead.',
            E_USER_DEPRECATED
        );

        if ($options === null) {
            return $this->_queueOptions;
        }

        return $this->__setQueueOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getQueueOptions(): ?array
    {
        return $this->_queueOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function setQueueOptions(array $options = null): NotificationInterface
    {
        return $this->__setQueueOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function locale(string $locale = null)
    {
        trigger_error(
            'Notification::locale() is deprecated. Use Notification::setLocale()/getLocale() instead.',
            E_USER_DEPRECATED
        );

        if ($locale === null) {
            return $this->_locale;
        }

        return $this->__setLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): ?string
    {
        return $this->_locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale(string $locale = null): NotificationInterface
    {
        return $this->__setLocale($locale);
    }

    /**
     * Set locale
     *
     * @param string $locale locale - must be i18n conform
     * @return $this
     */
    private function __setLocale(string $locale): NotificationInterface
    {
        $this->_locale = $locale;

        return $this;
    }

    /**
     * Set settings
     *
     * @param array $options Queue options
     * @return $this
     */
    private function __setQueueOptions(array $options): NotificationInterface
    {
        $this->_queueOptions = Hash::merge($this->_queueOptions, $options);

        return $this;
    }

    /**
     * Set callback
     *
     * @param string $type _beforeSendCallback or _afterSendCallback
     * @param string|array $class name of the class
     * @param array $args array of arguments
     * @return $this
     */
    private function __setCallback(string $type, $class, array $args): NotificationInterface
    {
        if (!is_array($class)) {
            $this->{$type} = [
                [
                    'class' => $class,
                    'args' => $args,
                ],
            ];

            return $this;
        } elseif (is_array($class) && count($class) == 2) {
            $className = $class[0];
            $methodName = $class[1];
        } else {
            if (is_array($class)) {
                $class = implode($class);
            }
            throw new InvalidArgumentException("{$class} is misformated");
        }

        $this->{$type} = [
            [
                'class' => [$className, $methodName],
                'args' => $args,
            ],
        ];

        return $this;
    }

    /**
     * Add callback
     *
     * @param string $type _beforeSendCallback or _afterSendCallback
     * @param string|array $class name of the class
     * @param array $args array of arguments
     * @return $this
     */
    private function __addCallback(string $type, $class, array $args): NotificationInterface
    {
        if (!is_array($class)) {
            $this->{$type}[] = [
                'class' => $class,
                'args' => $args,
            ];

            return $this;
        } elseif (is_array($class) && count($class) == 2) {
            $className = $class[0];
            $methodName = $class[1];
        } else {
            if (is_array($class)) {
                $class = implode($class);
            }
            throw new InvalidArgumentException("{$class} is misformated");
        }

        $this->{$type}[] = [
            'class' => [$className, $methodName],
            'args' => $args,
        ];

        return $this;
    }
}
