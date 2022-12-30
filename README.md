# drecom/buuurst_dev_php

BuuurstDev service PHP client
This client currently supports Laravel framework only.

## Installation

### Install from composer

```bash
$ composer require drecom/buuurst_dev_php
```

### Install from git

Add repository to your project's composer.json

```json
   ...
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/drecom/buuurst_dev_php"
        }
    ],
    ...
```

```bash
$ composer install
```

##  Configuration(Laravel middleware)

Add below to `app/Http/Kernel.php`

```php
    protected $middleware = [
        ...
        \BuuurstDev\Laravel\Middleware::class,
    ];
```

Then, Create configuration file `config/buuurst_dev.php`

```php
<?php
return [
    /*
|--------------------------------------------------------------------
| Buuurst dev configurations
|--------------------------------------------------------------------
*/

    'enabled' => env('BUUURST_DEV_ENABLED', true),
    'collector_url' => env('BUUURST_DEV_COLLECTOR_URL', 'https://lambda-public.buuurst.dev/put-request-log'),
    'project_id' => env('BUUURST_DEV_PROJECT_ID', 'YOUR_PROJECT_ID'),
    'service_key' => env('BUUURST_DEV_SERVICE_KEY', 'YOUR_SERVICE_KEY'),
    'custom_headers' => env('BUUURST_DEV_CUSTOM_HEADERS', []),
    'ignore_paths' => env('BUUURST_DEV_IGNORE_PATHS', []),
];
```

* `buuurst_dev.enabled`
  * switch enabled/disabled this middleware
* `buuurst_dev.project_id`
  * loadtest target project id
* `buuurst_dev.service_key`
  * get at account info page in BUUURST.DEV BETA
* `buuurst_dev.custom_headers` (optional)
  * specify custom HTTP header names
* `buuurst_dev.ignore_paths` (optional)
  * ignore sending request log when request path is in ignore_paths
