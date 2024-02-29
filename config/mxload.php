<?php
return [
    /*
|--------------------------------------------------------------------
| Mxload configurations
|--------------------------------------------------------------------
*/

    'enabled' => env('MXLOAD_ENABLED', false),
    'collector_url' => env('MXLOAD_COLLECTOR_URL', 'https://lambda-public.mxload.mx/put-request-log'),
    'project_id' => env('MXLOAD_PROJECT_ID', null),
    'service_key' => env('MXLOAD_SERVICE_KEY', null),
    'custom_headers' => env('MXLOAD_CUSTOM_HEADERS', []),
    'ignore_paths' => env('MXLOAD_IGNORE_PATHS', []),
];
