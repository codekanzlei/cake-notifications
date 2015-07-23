<?php
namespace Notifications\Model\Entity;

use Cake\ORM\Entity;
use Cake\Utility\Text;

/**
 * NotificationContent Entity.
 */
class NotificationContent extends Entity {

/**
 * Fields that can be mass assigned using newEntity() or patchEntity().
 *
 * @var array
 */
	protected $_accessible = [
		'notification_identifier' => true,
		'notes' => true,
		'email_subject' => true,
		'email_html' => true,
		'email_text' => true,
		'push_message' => true,
		'sms_message' => true,
		'hipchat_message' => true,
        'onpage' => true
	];

/**
 * Render a field by replacing the placeholders
 *
 * @param string $field field name
 * @param Notification $notification notification containing the view vars
 * @return string
 */
	public function render($field, Notification $notification) {
		return Text::insert($this->get($field), $notification->config, [
			'before' => '{{',
			'after' => '}}',
		]);
	}
}
