![CakePHP 3 Notifications Plugin](https://raw.githubusercontent.com/scherersoftware/cake-notifications/v2/cake-notifications.png)

[![Build Status](https://travis-ci.org/scherersoftware/cake-notifications.svg?branch=v2-dev)](https://travis-ci.org/scherersoftware/cake-notifications)
[![Code Coverage v2-dev](https://codecov.io/gh/scherersoftware/cake-notifications/branch/v2/graph/badge.svg)](https://codecov.io/gh/scherersoftware/cake-notifications)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)

A CakePHP 3.8 notification plugin which can send out emails asynchronously through to the cakephp-queuesadilla job queue.

## Requirements

- [CakePHP Queuesadilla Plugin 3.0](https://github.com/josegonzalez/cakephp-queuesadilla)
- PHP 7.1+

## Installation

### 1. Install the plugin via composer

    composer require codekanzlei/cake-notifications

### 2. Load the plugin in your `src/Application.php`

    $this->addPlugin('Notifications');

### 3. Configure `config/app.php`

Set your default locale in a config file, for example in `app.php`.
This config is mandatory and will cause an exception if not set.

```
'Notifications' => [
    'defaultLocale' => 'en_US'
]
```

You can also override the queue options like `attempts`, `attempts_delay`, `delay`, `expires_in` and `queue`.

```
'Notifications' => [
    'queueOptions' => [
        'queue' => 'notification'
    ]
]
```

This doesn't affect the use of `queueOptions()` later. You can still override the options there.

Also, be sure to set up the the cakephp-queuesadilla plugin config. You can find an example config here: [https://cakephp-queuesadilla.readthedocs.io/en/latest/](https://github.com/josegonzalez/cakephp-queuesadilla).

Or you can find available config options inside your used Engine file (`vendor/josegonzalez/queuesadilla/src/josegonzalez/Queuesadilla/Engine/*Engine.php`) inside the `$baseConfig` property.


**IMPORTANT**: Set "date.timezone" in your cli/php.ini to an appropriate value, else notifications with a delay_until could be sent out at the wrong time.

## Usage

### Email

The EmailNotification is completely compatible with the CakePHP Email.

Add the following to your class where you want to send an email:

`use Notifications\Notification\EmailNotification;`

Then simply create a new EmailNotification object.

```
$email = new EmailNotification();
$email->to('john.doe@example.com')
    ->setSubject('Send with cake-notifications v2')
    ->send('Hello :)');

```

You can chain all methods provided by the CakePHP Email Class [http://book.cakephp.org/3.0/en/core-libraries/email.html](http://book.cakephp.org/3.0/en/core-libraries/email.html)

### Additional, following functions are available:

### ` send( array|string|null $content null ) `

Send out the email immediately. before- and afterSend callbacks are still available

### ` setLocale( string|null $locale null ) `

Set the locale for the notification. If null, ```Configure::read('Notifications.defaultLocale')``` is used.

#### ` push() `

Push the email into the queue to send it asynchronous

### ` setQueueOptions( array $options null ) `

You can change some of the default options from the cakephp-queuesadilla plugin.

Supported options:

- `attempts` how often the notification will be executed again after a failure
- `attempts_delay` how long it takes in seconds until the notification will be executed again
- `delay` how long it takes until the notification will be executed for the first time  in seconds
- `expires_in` how long the notification will stay in the queue in seconds
- `queue` name of the queue

### `setBeforeSendCallback( array|string|null $class null, array $args [] )`

Pass a callable as the `$class` parameter. Static and none-static functions are supported.

```
$email->beforeSendCallback(['Foo', 'bar'], ['first_param', 'second_param'])

```     
This will call the `bar` method inside the Foo class with two parameters before the email is send.

To manipulate the EmailNotification instance before sending, the beforeSendCallback may return a function taking the notification instance reference and for example changing the profile.
The `bar` method may then look something like this:

```
public function bar($first_param, $second_param)
{
    // do something
    return function (&$instance) {
        $instance->profile([
            'from' => 'email@example.com'
        ]);
    };
}
```

### `setAfterSendCallback( array|string|null $class null, array $args [] )`

Pass a callable as the `$class` parameter. Static and none-static functions are supported.

```
$email-> afterSendCallback(['Foo::bar'], ['first_param', 'second_param'])
```     
This will call the static `bar` method inside the Foo class with two parameters after the email was send.

### `addBeforeSendCallback( array|string|null $class null, array $args [] )`

Add an additional callback to beforeSend.

### `addAfterSendCallback( array|string|null $class null, array $args [] )`

Add an additional callback to afterSend.
