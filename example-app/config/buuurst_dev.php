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
