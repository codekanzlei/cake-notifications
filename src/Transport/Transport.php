<?php
namespace Notifications\Transport;

use App\Model\Entity\User;
use Cake\Utility\Hash;
use Notifications\Model\Entity\Notification;
use Notifications\Model\Entity\NotificationContent;

abstract class Transport {

/**
 * @var array
 */
	protected $_config = [];

/**
 * Creates a Transport instance
 *
 * @param array $config transport-specific configuration options
 */
	public function __construct(array $config) {
		$this->_config = Hash::merge($this->_config, $config);
	}

/**
 * Abstract sender method
 *
 * @param User $user The recipient user
 * @param Notification $notification the notification to be sent
 * @param NotificationContent $content the content
 * @return mixed
 */
	abstract public function sendNotification(User $user, Notification $notification, NotificationContent $content);

/**
 * Creates a Transport instance based on the given type and config
 *
 * @param string $type email/push_message/sms/hipchat
 * @param array $config text transport-specific configuration options
 * @return Transport
 * @throws \InvalidArgumentException
 */
	public static function factory($type, array $config = []) {
		$map = [
			'email' => 'Notifications\Transport\EmailTransport',
			'push_message' => 'Notifications\Transport\PushMessageTransport',
			'sms' => 'Notifications\Transport\SmsTransport',
			'hipchat' => 'Notifications\Transport\HipChatTransport'
		];
		if (!isset($map[$type])) {
			throw new \InvalidArgumentException("{$type} is not a valid transport");
		}
		$className = $map[$type];
		$transport = new $className($config);
		return $transport;
	}
}