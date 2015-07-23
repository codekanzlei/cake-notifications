<?php
namespace Notifications\Model\Table;

use Cake\ORM\Behavior\Translate\TranslateTrait;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * NotificationContents Model
 */
class NotificationContentsTable extends Table {

/**
 * Initialize method
 *
 * @param array $config The configuration for the Table.
 * @return void
 */
	public function initialize(array $config) {
		$this->table('notification_contents');
		$this->displayField('id');
		$this->primaryKey('id');
		$this->addBehavior('Timestamp');

		$this->addBehavior('Translate', ['fields' => [
			'email_subject',
			'email_text',
			'email_html',
			'push_message',
			'sms_message',
			'hipchat_message',
            'onpage'
		],
            'allowEmptyTranslations' => true
        ]);
	}

/**
 * Default validation rules.
 *
 * @param \Cake\Validation\Validator $validator The validator to use when validating the entity.
 * @return \Cake\Validation\Validator
 */
	public function validationDefault(Validator $validator) {
		$table = $this;
		$validator
			->add('id', 'valid', ['rule' => 'uuid'])
			->allowEmpty('id', 'create')
			->requirePresence('notification_identifier', 'create')
			->notEmpty('notification_identifier')
			->allowEmpty('notes')
			->allowEmpty('email_subject')
			->requirePresence('email_subject')
			->add('email_subject', 'custom', [
				'message' => 'At least one of the transport messages must be filled.',
				'rule' => function ($value, $context) {
					$table = $context['providers']['table'];
					$translateFields = $table->behaviors()->Translate->config('fields');

					$allEmpty = true;
					foreach ($translateFields as $field) {
						if (!empty($context['data'][$field])) {
							$allEmpty = false;
							break;
						}
					}
					if ($allEmpty) {
						return false;
					}
					return true;
				}
			]);

		/*$validator->add('notification_identifier', [
			'unique' => ['rule' => 'validateUnique', 'provider' => 'table']
		]);*/
		return $validator;
	}

/**
 * Find a notification by its string identifier
 *
 * @param string $identifier identifier to look for
 * @param string $locale will override the locale of TranslateBehavior temporarily
 * @return Notification
 */
	public function getByIdentifier($identifier, $locale = null) {
		if ($locale) {
			$oldLocale = $this->locale();
			$this->locale($locale);
		}
		$res = $this->find()->where([
			'notification_identifier' => $identifier
		])->first();
		if ($locale) {
			$this->locale($oldLocale);
		}
		return $res;
	}
}
