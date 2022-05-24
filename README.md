# TYPO3 Distributed Cache Clearing

Utility extension to clear the local file caches in a multi-container setup. It is designed to be easily extendable
with all kinds of message queue backends. By default a DB/TYPO3 Registry and Redis backend are included.

## Requirements

- TYPO3 v9
- [TYPO3 - Better API](https://github.com/labor-digital/typo3-better-api)
- Installation using Composer

## Installation

Install this package using Composer:

```
composer require labor-digital/typo3-distributed-cache-clearing
```

## Usage

To utilize the distributed cache clearing you need to register a backend class using the ext config class.
This will activate the message handling when the TYPO3 cache is cleared in the backend or though the CLI.

```php
<?php
use LaborDigital\T3dcc\Core\Message\Backend\Registry\RegistryMessageBackend;
use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigContext;
use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigInterface;
use LaborDigital\Typo3BetterApi\ExtConfig\OptionList\ExtConfigOptionList;

class MyExtConfig implements ExtConfigInterface{
    public function configure(ExtConfigOptionList $configurator, ExtConfigContext $context){
        // You can use any of the built-in backends or a class that implements the MessageBackendInterface here.
        // Additional options for the backend can be provided as second parameter
        $configurator->distCache()->setMessageBackend(RegistryMessageBackend::class);
    }
}
```

In order to listen for the cache cleared event emitted in another container you can use multiple approaches.
The first one, which is the simplest to implement is by setting the "check in every request" option to true.
It is also done in the ext config class. The major downside of this option is a potential dip in your performance,
especially if external event queues (SNS, ServiceBus, ...) are used.

```php
<?php
use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigContext;
use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigInterface;
use LaborDigital\Typo3BetterApi\ExtConfig\OptionList\ExtConfigOptionList;

class MyExtConfig implements ExtConfigInterface{
    public function configure(ExtConfigOptionList $configurator, ExtConfigContext $context){
        $configurator->distCache()->setCheckInEveryRequest(true);
    }
}
```

If your infrastructure allows you to perform an HTTP request to EACH container independently,
and you use the T3FA extension, you can use the provided route in your ext config.
You can now use the `/dcc/clearIfRequired` route to handle clear cache messages if required.

```php
<?php
use LaborDigital\T3dcc\Api\Route\DccRoute;
use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigContext;
use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigInterface;
use LaborDigital\Typo3BetterApi\ExtConfig\OptionList\ExtConfigOptionList;

class MyExtConfig implements ExtConfigInterface{
    public function configure(ExtConfigOptionList $configurator, ExtConfigContext $context){
        $configurator->frontendApi()->routing()->registerRouteController(DccRoute::class);
    }
}
```

Alternatively you can use the built-in cli command `t3dcc:handleMessages` which does the same.

As as last resort for highly specialized setups you can use the `/ext/t3dcc/handleMessages.php`
file provided by the extension. It can be executed in ANY other PHP script, even without TYPO3 context.
It contains an encapsulated bootstrap and small application to boot up the TYPO3 core and flush the caches
if needed. Before you can include the file you need to set `T3DCC_AUTOLOAD_PATH` either as environment variable,
or constant to the absolute path to your composer autoload.php. The rest should work automatically.

This script would be located in the `public/typo3conf` directory of your installation.

```php
<?php
define('T3DCC_AUTOLOAD_PATH', dirname(__DIR__, 2) . '/vendor/autoload.php');
require __DIR__ . '/ext/t3dcc/handleMessages.php';
```

## Backends

The extension provides two simple backends:

### RegistryMessageBackend

This backend utilizes the TYPO3 `sys_registry` table to send messages between the containers.
It is the most simple implementation and does not require additional options or configuration.

### RedisMessageBackend

This backend utilizes a Redis database as messaging queue between containers.
The configuration follows the same structure as the redis cache backend.

```php
<?php
use LaborDigital\T3dcc\Core\Message\Backend\Redis\RedisMessageBackend;use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigContext;
use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigInterface;
use LaborDigital\Typo3BetterApi\ExtConfig\OptionList\ExtConfigOptionList;

class MyExtConfig implements ExtConfigInterface{
    public function configure(ExtConfigOptionList $configurator, ExtConfigContext $context){
        $configurator->distCache()->setMessageBackend(
        RedisMessageBackend::class,
        [
            // Required
            'hostname' => 'host',
            // The following options are optional
            'password' => '', // Only needed if the host requires a password
            'database' => 1, // It is not required, but recommended to set a database id here (Default: 1),
            'port' => 6379,
            'ttl' => 60 * 15
        ]
        );
    }
}
```

## Postcardware

You're free to use this package, but if it makes it to your production environment, we highly appreciate you sending us a postcard from your hometown,
mentioning which of our package(s) you are using.

Our address is: LABOR.digital - Fischtorplatz 21 - 55116 Mainz, Germany.

We publish all received postcards on our [company website](https://labor.digital).

