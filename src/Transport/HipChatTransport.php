<?php
namespace Notifications\Transport;

use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Notifications\Model\Entity\Notification;
use Notifications\Model\Entity\NotificationContent;
use HipChat\HipChat;

class HipChatTransport extends Transport
{

	/**
	 * Get the config from app.php and merge it with existing
	 * @param array $config [description]
	 */
	public function __construct(array $config)
	{
		$config = Hash::merge(Configure::read('Notifications.transports.hipchat'), $config);
		parent::__construct($config);
	}

	/**
	 * Create a HipChat instance and send the notification
	 *
	 * @param  User                $user
	 * @param  Notification        $notification
	 * @param  NotificationContent $content
	 * @return bool
	 */
	public function sendNotification(User $user, Notification $notification, NotificationContent $content)
	{
		// Init HipChat API

		$hipChat = new HipChat($this->_config['api_key'], $this->_config['endpoint']);

		// Put together message and config stuff

		$message = $content->render('hipchat_message', $notification);

		// Define the room

		$room = isset($notification->transport_config['room']) ? $notification->transport_config['room'] : $this->_config['defaultRoom'];

		// Define the place where the message was sent from

		$sentFrom = isset($notification->transport_config['sentFrom']) ? $notification->transport_config['sentFrom'] : $this->_config['defaultSentFrom'];

		// Define the message format

		$messageFormat = isset($notification->transport_config['messageFormat']) ? $notification->transport_config['messageFormat'] : HipChat::FORMAT_TEXT;

		// Define the color, normal is yellow, error is red

		$textColor = isset($notification->transport_config['textColor']) ? $notification->transport_config['textColor'] : HipChat::COLOR_YELLOW;

		// Give back the result

		return $hipChat->message_room($room, $sentFrom, $message, false, $textColor, $messageFormat);

	}

}