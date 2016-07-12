#CakePHP 3 Notifications Plugin

[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)

A CakePHP notification plugin which can send out emails asynchron due to the cakephp-queuesadilla job queue.

##Requirements

- [CakePHP Queuesadilla Plugin 3.0](https://github.com/josegonzalez/cakephp-queuesadilla)
- PHP 5.4+

##Installation

###1. Install the plugin via composer

Add the following lines to your application's composer.json:

```
"require": {
    "codekanzlei/cake-notifications": "dev-master"
}
```

followed by the command:

`composer update`

Or run the following command directly without changing your `composer.json:

```composer require codekanzlei/cake-notifications:dev-master```

###2. Configure ```config/bootstrap.php``

```Plugin::load('Notifications', ['bootstrap' => false, 'routes' => true]);```

###3. Configure ```config/app.php```

Be sure to set up the the cakephp-queuesadilla plugin config. You can find an example config here: [https://cakephp-queuesadilla.readthedocs.io/en/latest/](https://github.com/josegonzalez/cakephp-queuesadilla).

Or you can find avalable config options inside your used Engine file (`vendor/josegonzalez/queuesadilla/src/josegonzalez/Queuesadilla/Engine/*Engine.php`) inside the `$baseConfig` property.

##Usage

###Email

The EmailNotification is completely compatible wiht the CakePHP Email.

Add the following to your class where you want to send an email:

`use Notifications\Notification\EmailNotification;`

Then simply creta a new EmailNotification object.

```
$email = new EmailNotification();
$email->to('john.doe@example.com')
	->subject('Send with cake-notifications v2')
  	->send('Hello :)');

```

You can chain all methods provided by the CakePHP Email Class [http://book.cakephp.org/3.0/en/core-libraries/email.html](http://book.cakephp.org/3.0/en/core-libraries/email.html)

###Additional, follwing functions are available:

###` send( array|string|null $content null ) `

Send out the email immediately. before- and afterSend callbacks are still available


####` push() `

Push the email into the queue to send it asynchron

###` queueOptions( array $options null ) `

You can change some of the default options from the cakephp-queuesadilla plugin.

Supported options:

- `attempts` how often the notification will be executed again after a failure
- `attempts_delay` how long it takes in seconds until the notification will be executed again
- `delay` how long it takes until the notification will be executed for the first time  in seconds
- `expires_in` how long the notification will stay in the queue in seconds
- `queue` name of the queue

### `beforeSendCallback( array|string|null $class null, array $args [] )`

Pass a calable as the `$class` parameter. Static and none-static functions are supported.

```
	$email->beforeSendCallback(['Foo', 'bar'], ['first_param', 'second_param'])

```     
This will call the `bar` method inside the Foo class with two parameters before the email is send.

### `afterSendCallback( array|string|null $class null, array $args [] )`

Pass a calable as the `$class` parameter. Static and none-static functions are supported.

```
	$email-> afterSendCallback(['Foo::bar'], ['first_param', 'second_param'])

```     
This will call the static `bar` method inside the Foo class with two parameters after the email was send.


##ToDo

- Finish tests
- Implement more transports like WebSMS or PushNotifications
