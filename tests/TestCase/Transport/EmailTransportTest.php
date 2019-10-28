<?php
declare(strict_types = 1);
namespace Notifications\Test\TestCase\Notification;

use Cake\Datasource\ConnectionManager;
use Cake\I18n\I18n;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Josegonzalez\CakeQueuesadilla\Queue\Queue;
use josegonzalez\Queuesadilla\Job\Base as BaseJob;
use Notifications\Notification\EmailNotification;
use Notifications\Transport\EmailTransport;

/**
 * Helper class to test callbacks
 *
 */
class SomeClass
{

    public static $someProperty = null;
    public static $anotherProperty = null;
    public static $wasTheCallableCalled = false;

    public function someMethod()
    {
        self::$someProperty = 'was_called';

        return function (EmailNotification $email) {
            self::$wasTheCallableCalled = true;
        };
    }

    public static function someStaticMethod()
    {
        self::$anotherProperty = 'was_called';
    }
}

class EmailTransportTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Jobs' => 'plugin.Notifications.Jobs'
    ];

    public function setup()
    {
        parent::setUp();
        Log::reset();
        Log::setConfig('stdout', ['engine' => 'File']);

        $dbConfig = ConnectionManager::getConfig('test');

        Queue::reset();
        Queue::setConfig([
            'default' => [
                'engine' => 'josegonzalez\Queuesadilla\Engine\MysqlEngine',
                'database' => $dbConfig['database'],
                'host' => $dbConfig['host'],
                'user' => $dbConfig['username'],
                'pass' => $dbConfig['password']
            ]
        ]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        Log::reset();
        Queue::reset();
    }

    public function testNotExistingCallbackCall()
    {
        $this->expectException('\InvalidArgumentException');

        $email = new EmailNotification([
            'transport' => 'debug',
            'from' => 'foo@bar.com'
        ]);
        $email
            ->setTo('foo@bar.com')
            ->setBeforeSendCallback(['SomeClassThatDoesNotExists', 'someMethod']);
        $return = EmailTransport::sendNotification($email);

        $this->assertEquals(SomeClass::$someProperty, null);
        $this->assertEquals(SomeClass::$anotherProperty, null);
        $this->assertFalse(SomeClass::$wasTheCallableCalled);
    }

    public function testNotExistingStaticCallbackCall()
    {
        $this->expectException('\InvalidArgumentException');

        $email = new EmailNotification([
            'transport' => 'debug',
            'from' => 'foo@bar.com'
        ]);
        $email
            ->setTo('foo@bar.com')
            ->setBeforeSendCallback('SomeClassThatDoesNotExists::someMethod');
        $return = EmailTransport::sendNotification($email);

        $this->assertEquals(SomeClass::$someProperty, null);
        $this->assertEquals(SomeClass::$anotherProperty, null);
        $this->assertFalse(SomeClass::$wasTheCallableCalled);
    }

    public function testSendNotification()
    {
        $email = new EmailNotification([
            'transport' => 'debug',
            'from' => 'foo@bar.com'
        ]);
        $email
            ->setTo('foo@bar.com')
            ->setLocale('de_DE')
            ->setBeforeSendCallback(['Notifications\Test\TestCase\Notification\SomeClass', 'someMethod'])
            ->setAfterSendCallback('Notifications\Test\TestCase\Notification\SomeClass::someStaticMethod');
        EmailTransport::sendNotification($email);

        $this->assertEquals(I18n::getLocale(), 'de_DE');

        $this->assertEquals(SomeClass::$someProperty, 'was_called');
        $this->assertEquals(SomeClass::$anotherProperty, 'was_called');
        $this->assertTrue(SomeClass::$wasTheCallableCalled);
    }

    public function testProcessQueueObject()
    {
        SomeClass::$someProperty = null;
        SomeClass::$anotherProperty = null;
        SomeClass::$wasTheCallableCalled = false;

        $email = new EmailNotification([
            'transport' => 'debug',
            'from' => 'foo@bar.com'
        ]);
        $email
            ->setTo('foo@bar.com')
            ->setLocale('de_AT')
            ->setBeforeSendCallback(['Notifications\Test\TestCase\Notification\SomeClass', 'someMethod'])
            ->setAfterSendCallback('Notifications\Test\TestCase\Notification\SomeClass::someStaticMethod')
            ->push();

        $result = TableRegistry::getTableLocator()->get('Jobs')->get(1)->toArray();

        $data = json_decode($result['data'], true);
        $jobItem = [
            'id' => $result['id'],
            'class' => $data['class'],
            'args' => $data['args'],
            'queue' => 'default',
            'options' => $data['options'],
            'attempts' => (int)$result['attempts']
        ];
        $job = new BaseJob($jobItem, null);

        $resultNotification = EmailTransport::processQueueObject($job);

        $this->assertEquals($resultNotification->email()->getTo(), ['foo@bar.com' => 'foo@bar.com']);
        $this->assertEquals($resultNotification->getLocale(), 'de_AT');
        $this->assertEquals(SomeClass::$someProperty, 'was_called');
        $this->assertEquals(SomeClass::$anotherProperty, 'was_called');
        $this->assertTrue(SomeClass::$wasTheCallableCalled);
    }
}
