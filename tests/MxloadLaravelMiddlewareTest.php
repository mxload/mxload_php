<?php

namespace MxloadTests;

use Mxload\Laravel\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\TestCase;
use Mockery;

class MxloadLaravelMiddlewareTest extends TestCase
{
    protected function setUp(): void {
        parent::setUp();

        // default sample configurations
        Config::shouldReceive('get')->with('mxload.collector_url')->andReturn('https://lambda-public.mxload.mx/put-request-log');
        Config::shouldReceive('get')->with('mxload.project_id')->andReturn(0);
        Config::shouldReceive('get')->with('mxload.service_key')->andReturn('cafebabe');
    }

    public function testSuccessfulGetRequest() : void
    {
        Config::shouldReceive('get')->with('mxload.enabled')->andReturnTrue()->once();
        Config::shouldReceive('get')->with('mxload.custom_headers')->andReturn([])->once();
        Config::shouldReceive('get')->with('mxload.ignore_paths')->andReturn([])->once();
        $closure = function($url, $body) {
            if ($url != Config::get("mxload.collector_url")) {
                return false;
            }
            if ($body['project_id'] != Config::get("mxload.project_id")) {
                return false;
            }
            if ($body['service_key'] != Config::get("mxload.service_key")) {
                return false;
            }
            if (!is_int($body['requested_at'])) {
                return false;
            }
            if ($body['method'] != "GET") {
                return false;
            }
            if ($body['path'] != "/path/to/api") {
                return false;
            }
            if ($body['query']['q'] != "1") {
                return false;
            }
            if ($body['cookie']['session_id'] != "abcdef") {
                return false;
            }
            if (!isset($body['request_id'])) {
                return false;
            }
            if (isset($body['header']['Authorization'])) {
                return false;
            }
            if (!is_null($body['body'])) {
                return false;
            }

            return true;
        };
        Http::shouldReceive('post')->withArgs($closure)->once();

        $response = \Mockery::mock('Illuminate\Http\Response');
        $response->shouldReceive('status')->andReturn(200);

        $request = Request::create(
            'http://example.test/path/to/api',
            'GET',
            ['q' => "1"],
            ['session_id' => 'abcdef'],
            [],
            ['HTTP_AUTHORIZATION' => '01234abcdef'],
        );

        $middleware = new Middleware();
        $middleware_response = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertNotNull($middleware_response);
    }

    public function testSuccessfulPostRequest() : void
    {
        Config::shouldReceive('get')->with('mxload.enabled')->andReturnTrue()->once();
        Config::shouldReceive('get')->with('mxload.custom_headers')->andReturn([])->once();
        Config::shouldReceive('get')->with('mxload.ignore_paths')->andReturn([])->once();
        $closure = function($url, $body) {
            if ($url != Config::get("mxload.collector_url")) {
                return false;
            }
            if ($body['project_id'] != Config::get("mxload.project_id")) {
                return false;
            }
            if ($body['service_key'] != Config::get("mxload.service_key")) {
                return false;
            }
            if (!is_int($body['requested_at'])) {
                return false;
            }
            if ($body['method'] != "POST") {
                return false;
            }
            if ($body['path'] != "/path/to/api") {
                return false;
            }
            if ($body['cookie']['SESSION_ID'] != "abcdef") {
                return false;
            }
            if (!isset($body['request_id'])) {
                return false;
            }
            if (isset($body['header']['Authorization'])) {
                return false;
            }
            if ($body['body']['key'] != "value") {
                return false;
            }

            return true;
        };
        Http::shouldReceive('post')->withArgs($closure)->once();

        $response = \Mockery::mock('Illuminate\Http\Response');
        $response->shouldReceive('status')->andReturn(200);

        $request = Request::create(
            'http://example.test/path/to/api',
            'POST',
            [],
            ['SESSION_ID' => 'abcdef'],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => '01234abcdef',
            ],
            '{"key": "value"}',
        );

        $middleware = new Middleware();
        $middleware_response = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertNotNull($middleware_response);
    }

    public function testCustomHeaders() : void {
        Config::shouldReceive('get')->with('mxload.enabled')->andReturnTrue()->once();
        Config::shouldReceive('get')->with('mxload.custom_headers')->andReturn([
            'Authorization',
        ])->once();
        Config::shouldReceive('get')->with('mxload.ignore_paths')->andReturn([])->once();
        $closure = function($url, $body) {
            if ($url != Config::get("mxload.collector_url")) {
                return false;
            }
            if ($body['project_id'] != Config::get("mxload.project_id")) {
                return false;
            }
            if ($body['service_key'] != Config::get("mxload.service_key")) {
                return false;
            }
            if (!is_int($body['requested_at'])) {
                return false;
            }
            if ($body['method'] != "GET") {
                return false;
            }
            if ($body['path'] != "/path/to/api") {
                return false;
            }
            if ($body['query']['q'] != "1") {
                return false;
            }
            if ($body['cookie']['session_id'] != "abcdef") {
                return false;
            }
            if (!isset($body['request_id'])) {
                return false;
            }
            if ($body['header']['Authorization'] != "01234abcdef") {
                return false;
            }
            if (!is_null($body['body'])) {
                return false;
            }
            return true;
        };
        Http::shouldReceive('post')->withArgs($closure)->once();

        $response = \Mockery::mock('Illuminate\Http\Response');
        $response->shouldReceive('status')->andReturn(200);

        $request = Request::create(
            'http://example.test/path/to/api',
            'GET',
            ['q' => 1],
            ['session_id' => 'abcdef'],
            [],
            ['HTTP_AUTHORIZATION' => '01234abcdef'],
        );
        $middleware = new Middleware();
        $middleware_response = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertNotNull(true);
    }

    public function testIgnorePaths() : void
    {
        Config::shouldReceive('get')->with('mxload.enabled')->andReturnTrue()->once();
        Config::shouldReceive('get')->with('mxload.ignore_paths')->andReturn(["/ignored/path"])->once();
        Http::shouldReceive('post')->withAnyArgs()->never();

        $response = \Mockery::mock('Illuminate\Http\Response');

        $request = Request::create(
            'http://example.test/path/to/ignored/path',
            'GET'
        );

        $middleware = new Middleware();
        $middleware_response = $middleware->handle($request, function () use ($response) {
            return $response;
        });
        $this->assertNotNull($middleware_response);
    }

    public function testDisabled() : void
    {
        Config::shouldReceive('get')->with('mxload.enabled')->andReturnFalse()->once();
        Http::shouldReceive('post')->withAnyArgs()->never();

        $response = \Mockery::mock('Illuminate\Http\Response');

        $request = Request::create(
            'http://example.test/path/to/api',
            'GET'
        );

        $middleware = new Middleware();
        $middleware_response = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertNotNull($middleware_response);
    }

    protected function tearDown() : void
    {
        Mockery::close();
    }
}
