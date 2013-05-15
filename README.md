# NAMSHI | EmailVision

This small library provides support for the
"REST" interface exposed by EmailVision.

**THE LIBRARY IS CURRENTLY NOT FULLY TESTED YET**:
even though we have unit tests, we still need to
write integration tests, this is a WIP.

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
$emailvisionClient->sendEmail("someone@gmail.com");
```

## Tests

You can run the test suite by first installing the
dependencies and running PHPUnit:

```
php composer.phar update

phpunit
```