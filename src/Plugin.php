<?php
namespace Notifications;

use Cake\Core\Configure;
use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Routing\Router;
use Josegonzalez\CakeQueuesadilla\Queue\Queue;

class Plugin extends BasePlugin
{
    /**
     * Plugin name.
     *
     * @var string
     */
    protected $name = 'Notifications';

    /*
     * {@inheritDoc}
     */
    public function bootstrap(PluginApplicationInterface $app)
    {
        parent::bootstrap($app);

        $app->addPlugin('Josegonzalez/CakeQueuesadilla');

        $config = Configure::read('Queuesadilla');

        // Reset Queue config
        foreach ($config as $configName => $settings) {
            if (!is_null(Queue::getConfig($configName))) {
                Queue::drop($configName);
            }
        }

        // Load Queue config
        Queue::setConfig($config);

        if (!defined('SIGQUIT')) {
            define('SIGQUIT', 'SIGQUIT');
        }
        if (!defined('SIGTERM')) {
            define('SIGTERM', 'SIGTERM');
        }
        if (!defined('SIGINT')) {
            define('SIGINT', 'SIGINT');
        }
        if (!defined('SIGUSR1')) {
            define('SIGUSR1', 'SIGUSR1');
        }
    }

    /*
     * {@inheritDoc}
     */
    public function routes($routes)
    {
        parent::routes($routes);

        Router::plugin('Notifications', function ($routes) {
            $routes->fallbacks('DashedRoute');
        });
    }
}
