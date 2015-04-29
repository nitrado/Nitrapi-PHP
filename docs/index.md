**Nitrapi PHP SDK**
===================

![alt tag](https://static.nitrado.net/img/logo-orange.png)

This documentation shows the usage of the PHP based SDK for the Nitrapi.
Nitrapi is the official public API of nitrado.net.

API Endpoint: https://api.nitrado.net/

Requirements
------------
* PHP >=5.3
* cURL extension for PHP

Installation
------------
This library needs to be installed through Composer:

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php

# Require nitrado/nitrapi-php as a dependency
php composer.phar require nitrado/nitrapi-php:dev-master
```

Usage Example
-------------

```php
<?php

require_once '../vendor/autoload.php';

# Use API
$api = new \Nitrapi\Nitrapi("<ACCESS TOKEN>"));

/**
 * Example: Get all services as objects
 * @var $service \Nitrapi\Services\Service
 */
try {
    foreach ($api->getServices() as $service) {
        echo "Service " . $service->getId() . " started at " . $service->getStartDate()->format('Y-m-d H:i:s') . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "Failed! Error Message: " . $e->getMessage() . PHP_EOL;
}

```