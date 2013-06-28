# NAMSHI | EmailVision

This small library provides support for the
"REST" interface exposed by EmailVision.

## Installation

You can install this library via composer: have a look
at the [package on packagist](https://packagist.org/packages/namshi/emailvision).

## Usage

Using this library is pretty trivial, the only "difficult" thing to do
is to define the configuration needed by Emailvision:

``` php
<?php

use Namshi\Emailvision\Client;

$config = array(
    'random'            => 'iTag',
    'encrypt'           => 'sTag',
    'senddate'          => '2012-01-01',
    'uidkey'            => 'uKey',
    'stype'             => 'stype',
);

$emailvisionClient = new Client($config);
$emailvisionClient->sendEmail("someone@gmail.com", array('name' => 'Alex'));
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