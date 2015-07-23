<?php
namespace Notifications\Model\Entity;

use Cake\ORM\Entity;

/**
 * NotificationQueue Entity.
 */
class Notification extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
	protected $_accessible = [
		'locale' => true,
		'recipient_user_id' => true,
		'notification_identifier' => true,
		'config' => true,
        'transport_config' => true,
		'transport' => true,
		'locked' => true,
		'sent' => true,
		'send_tries' => true,
		'send_after' => true,
        'seen' => true,
		'debug' => true,
		'recipient_user' => true,
		'created' => true
	];

    /**
     * Increases the send tries
     *
     * @return void
     */
	public function fail()
    {
		$this->send_tries++;
	}

    /**
     * Marks the notification as sent successfully
     *
     * @return void
     */
	public function success()
    {
		$this->sent = true;
	}

    /**
     * Marks the notification as seen
     *
     * @return void
     */
    public function read()
    {
        $this->seen = true;
    }
}
