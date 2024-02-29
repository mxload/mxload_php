# mxload/mxload_php

Mxload service PHP client
This client currently supports Laravel framework only.

## Installation

### Install from composer

```bash
$ composer require mxload/mxload_php
```

### Install from git

Add repository to your project's composer.json

```json
   ...
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mxload/mxload_php"
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
        \Mxload\Laravel\Middleware::class,
    ];
```

Then, Create configuration file `config/mxload.php`

```php
<?php
return [
    /*
|--------------------------------------------------------------------
| Mxload configurations
|--------------------------------------------------------------------
*/

    'enabled' => env('MXLOAD_ENABLED', true),
    'collector_url' => env('MXLOAD_COLLECTOR_URL', 'https://lambda-public.mxload.mx/put-request-log'),
    'project_id' => env('MXLOAD_PROJECT_ID', 'YOUR_PROJECT_ID'),
    'service_key' => env('MXLOAD_SERVICE_KEY', 'YOUR_SERVICE_KEY'),
    'custom_headers' => env('MXLOAD_CUSTOM_HEADERS', []),
    'ignore_paths' => env('MXLOAD_IGNORE_PATHS', []),
];
```

* `mxload.enabled`
  * switch enabled/disabled this middleware
* `mxload.project_id`
  * loadtest target project id
* `mxload.service_key`
  * get at account info page in MXLOAD
* `mxload.custom_headers` (optional)
  * specify custom HTTP header names
* `mxload.ignore_paths` (optional)
  * ignore sending request log when request path is in ignore_paths
