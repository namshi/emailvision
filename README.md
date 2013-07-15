# NAMSHI | EmailVision

[![Build Status](https://travis-ci.org/namshi/emailvision.png)](https://travis-ci.org/namshi/emailvision)

This small library provides support for the
"REST" interface exposed by EmailVision.

## Installation

You can install this library via composer: have a look
at the [package on packagist](https://packagist.org/packages/namshi/emailvision).

The include it into your `composer.json`:

```
"namshi/emailvision": "1.0.*",
```

## Usage

Using this library is pretty trivial, the only "difficult" thing to do
is to define the configuration needed by Emailvision:

``` php
<?php

use Namshi\Emailvision\Client;

$config = array('template_for_password_reset_email' => array(
    'random'            => 'iTag',
    'encrypt'           => 'sTag',
    'senddate'          => new \DateTime(),
    'uidkey'            => 'uKey',
    'stype'             => 'stype',
));

$emailvisionClient = new Client($config);
$emailvisionClient->sendEmail("template_for_password_reset_email", "someone@gmail.com", array('name' => 'Alex'));
```

## Tests

You can run the test suite by first installing the
dependencies and running PHPUnit:

```
php composer.phar update

phpunit
```

There are a couple integration tests that actually verify that the library
works flawlessy, by actually hitting the emailvision API. Given that you need
valid credentials for that, just create a file called `emailvision.config`
in your system's temporary folder (`sys_get_temp_dir()`) with 3 parameters:

``` php
<?php

$encrypt    = 'xxx';
$random     = 'yyy';
$email      = 'your.name@gmail.com';
```

Have a [look at the tests these variables are used](https://github.com/namshi/emailvision/blob/1.0.0/Test/ClientTest.php#L77).
