<?php

namespace BuuurstDev\Laravel;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class Middleware
{
    /**
     * Buuurst.dev capture middleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Config::get('buuurst_dev.enabled') || !$this->isCollectablePath($request->getPathInfo())) {
            return $next($request);
        }

        $request_id = $request->headers->get('X-Request-Id');

        if (is_null($request_id)) {
            $request_id = Str::uuid();
            $request->headers->set('X-Request-Id', $request_id);
        }

        $params = $this->extractRequestParams($request);
        $response = $next($request);
        $params['status'] = $this->extractResponseStatusCode($response);

        $this->collect($params);

        return $response;
    }

    private function extractRequestParams(Request $request)
    {
        $body = null;
        if ($request->method() != "GET" && $request->method() != "HEAD") {
            $body = $request->input();
        }

        return [
            'requested_at' => $request->server('REQUEST_TIME'),
            'method' => $request->method(),
            'path' => $request->getPathInfo(),
            'query' => $request->query(),
            'cookie' => $request->cookie(),
            'request_id' => $request->headers->get('X-Request-Id'),
            'header' => $this->extractHeaders($request),
            'body' => $body
        ];
    }

    private function extractResponseStatusCode(Response $response)
    {
        return $response->status();
    }

    private function collect($params)
    {
        $data = array_merge($params, [
            'project_id' => Config::get('buuurst_dev.project_id'),
            'service_key' => Config::get('buuurst_dev.service_key'),
        ]);

        $response = Http::post(Config::get('buuurst_dev.collector_url'), $data);
    }

    private function extractHeaders(Request $request)
    {
        $server_params = $request->server();
        if (!is_array($server_params)) {
            return [];
        }

        $extract_headers = [];
        foreach($server_params as $k => $v) {
            if (strpos($k, "HTTP_") !== 0) {
                continue;
            } else {
              if (!isset($extract_headers[$k])) {
                $extract_headers[$k] = $v;
              }
            }
        }

        $custom_headers = Config::get('buuurst_dev.custom_headers');
        if (empty($custom_headers)) {
            return $extract_headers;
        }

        foreach($custom_headers as $name) {
            $v = $request->header($name);
            if(!is_null($v)) {
                $extract_headers[$name] = $v;
            }
        }

        return $extract_headers;
    }

    private function isCollectablePath(string $path)
    {
        $ignore_paths = Config::get('buuurst_dev.ignore_paths');
        if (empty($ignore_paths)) {
            return true;
        }

        foreach ($ignore_paths as $pattern) {
            if ( strpos($path, $pattern) !== false){
                return false;
            }
        }
        return true;
    }
}
