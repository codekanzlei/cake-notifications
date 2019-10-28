<?php
declare(strict_types = 1);

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Mailer\Email;
use Cake\Mailer\TransportFactory;

require_once 'vendor/cakephp/cakephp/src/basics.php';

Configure::write('debug', true);

/**
 * Test suite bootstrap for Notifications.
 */
// Customize this to be a relative path for embedded plugins.
// For standalone plugins, this should point at a CakePHP installation.
$vendorPos = strpos(__DIR__, 'vendor/codekanzlei/cake-notifications');
if ($vendorPos !== false) {
    // Package has been cloned within another composer package, resolve path to autoloader
    $vendorDir = substr(__DIR__, 0, $vendorPos) . 'vendor/';
    $loader = require $vendorDir . 'autoload.php';
} else {
    // Package itself (cloned standalone)
    $loader = require __DIR__ . '/../vendor/autoload.php';
}

ConnectionManager::setConfig('test', [
    'className' => 'Cake\Database\Connection',
    'driver' => 'Cake\Database\Driver\Mysql',
    'persistent' => false,
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'cake_notifications_test',
    'encoding' => 'utf8',
    'timezone' => 'UTC'
]);

Configure::write('App.encoding', 'UTF-8');

Configure::write('Notifications.queueOptions.queue', 'default');
Configure::write('Notifications.defaultLocale', 'en_US');

TransportFactory::setConfig('debug', [
    'className' => 'Debug',
    'charset' => 'utf-8',
]);
Email::setConfig('default', [
    'transport' => 'debug'
]);

loadPHPUnitAliases();