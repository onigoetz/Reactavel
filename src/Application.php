<?php
namespace Onigoetz\Reactavel;

use React\Http\Response;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Application extends \Illuminate\Foundation\Application
{
    public function serve(SymfonyRequest $request, Response $response)
    {
        $stack = $this->app->getStackedClient();

        $laravelResponse = $stack->handle($request);
        $this->sendResponse($laravelResponse, $response);
        $stack->terminate($request, $laravelResponse);
    }

    private function sendResponse(SymfonyResponse $symfonyResponse, Response $response)
    {
        $response->writeHead($symfonyResponse->getStatusCode(), $symfonyResponse->headers->all());
        $response->end($symfonyResponse->getContent());
    }
}
