<?php
namespace Notifications\Controller;

use Cake\Core\Configure;
use Notifications\Controller\AppController;

/**
 * NotificationContents Controller
 *
 * @property Notifications\Model\Table\NotificationContentsTable $NotificationContents
 */
class NotificationContentsController extends AppController {

/**
 * Event
 *
 * @param Event $event event
 * @return void
 */
	public function beforeFilter(\Cake\Event\Event $event) {
		$this->loadModel('Notifications.NotificationContents');
        if (Configure::check('Notifications.default_language')) {
    		$this->NotificationContents->locale(Configure::read('Notifications.default_language'));
        } else {
    		$this->NotificationContents->locale('eng');
        }
		parent::beforeFilter($event);
	}

/**
 * Index method
 *
 * @return void
 */
	public function index() {
		$this->set('notificationContents', $this->paginate($this->NotificationContents));
	}

/**
 * View method
 *
 * @param string $id content id
 * @return void
 * @throws \Cake\Network\Exception\NotFoundException
 */
	public function view($id = null) {
		$notificationContent = $this->NotificationContents->get($id, [
			'contain' => []
		]);
		$this->set('notificationContent', $notificationContent);
	}

/**
 * Add method
 *
 * @return void
 */
	public function add() {
		$notificationContent = $this->NotificationContents->newEntity();
		$transports = Configure::read('Notifications.transports');
		if ($this->request->is('post')) {
            $this->NotificationContents->patchEntity($notificationContent, $this->request->data);
			if ($this->NotificationContents->save($notificationContent)) {
				$this->Flash->success(__('crud.save_successful'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('crud.validation_failed'));
			}
		}
		$this->set(compact('notificationContent', 'transports'));
		return $this->render('edit');
	}

/**
 * Edit method
 *
 * @param string $id content id
 * @return void
 * @throws \Cake\Network\Exception\NotFoundException
 */
	public function edit($id = null) {
		$notificationContent = $this->NotificationContents->get($id, [
			'contain' => []
		]);
		$transports = Configure::read('Notifications.transports');
		if ($this->request->is(['patch', 'post', 'put'])) {
			$notificationContent = $this->NotificationContents->patchEntity($notificationContent, $this->request->data);
			if ($this->NotificationContents->save($notificationContent)) {
				$this->Flash->success(__('crud.save_successful'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('crud.validation_failed'));
			}
		}
		$this->set(compact('notificationContent', 'transports'));
	}

}
