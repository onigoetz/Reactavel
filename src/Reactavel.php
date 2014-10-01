<?php
namespace Onigoetz\Reactavel;

use React\Http\Request;
use React\Http\Response;
use React\EventLoop\StreamSelectLoop;
use React\Socket\Server as SocketServer;
use React\Http\Server as HttpServer;
use Onigoetz\Reactavel\Request as LaravelRequest;

class Reactavel
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct($dir)
    {
        $this->app = require_once $dir . '/bootstrap/start.php';

        echo 'Laravel initialized';
    }

    public function serve(Request $request, Response $response)
    {
        $laravelRequest = LaravelRequest::createFromReact($request);

        $this->app->serve($laravelRequest, $response);
    }

    public function run($port)
    {
        $loop = new StreamSelectLoop();
        $socket = new SocketServer($loop);
        $http = new HttpServer($socket, $loop);

        $http->on('request', [$this, 'serve']);

        $socket->listen($port);
        $loop->run();

    }
}
