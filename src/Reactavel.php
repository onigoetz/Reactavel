<?php
namespace Onigoetz\Reactavel;

use Onigoetz\Reactavel\Request as LumenRequest;
use React\EventLoop\StreamSelectLoop;
use React\Http\Request;
use React\Http\Response;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;

class Reactavel
{
    /**
     * @var Application
     */
    protected $app;

    protected static $port = 0;

    public function __construct($dir)
    {
        // Initialize the base application,all requests will
        // create their own Instance, but they will redirect
        // most of their calls to this "master" application.
        $this->app = require_once $dir . '/bootstrap/app.php';

        echo "Lumen initialized\n";
    }

    public static function getPort() {
        return self::$port;
    }

    public static function getHost() {
        return '0.0.0.0';
    }

    public function run($port)
    {
        self::$port = $port;

        $loop = new StreamSelectLoop();
        $socket = new SocketServer($loop);
        $http = new HttpServer($socket, $loop);

        $http->on('request', [$this, 'serve']);

        $socket->listen($port);

        echo "Reactavel server started on localhost:$port\n";

        $loop->run();
    }

    public function serve(Request $request, Response $response)
    {
        $startTime = microtime(TRUE);

        $app = new ProxyApplication($this->app);
        $app->serve($this->buildRequest($request, $app), $response);

        $time = number_format((microtime(TRUE) - $startTime) * 1000, 2);
        echo $request->getMethod() . " " . $request->getUrl() . " - " . $time . "ms\n";
    }

    public function buildRequest(Request $request, ProxyApplication $app) {
        $innerRequest = LumenRequest::createFromReact($request);

        // Taken from `laravel/lumen-framework/src/Application.php` in `registerRequestBindings()`
        $innerRequest->setUserResolver(
            function () use ($app) {
                return $app->make('auth')->user();
            }
        );

        $innerRequest->setRouteResolver(
            function () use ($app) {
                return $app->currentRoute;
            }
        );

        $app->instance('Illuminate\Http\Request', $innerRequest);

        return $innerRequest;
    }
}
