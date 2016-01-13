<?php namespace Onigoetz\Reactavel;

use Closure;
use Exception;
use FastRoute\Dispatcher;
use HttpResponseException;
use Laravel\Lumen\Routing\Closure as RoutingClosure;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Throwable;

/**
 * This class is a proxy to call the router of the main application. Hovever 4 methods are not proxied :
 * - dispatch
 * - handleDispatcherResponse
 * - handleFoundRoute
 * - callActionOnArrayBasedRoute
 *
 * This is needed so that when `call` is, well, called, it is done in the child context, not the parent.
 *
 * @property \Laravel\Lumen\Application $parentApp
 */
trait ProxyRoutesRequests
{
    // public function dispatch($request = null)
    // protected function handleDispatcherResponse($routeInfo)
    // protected function handleFoundRoute($routeInfo)
    // protected function callActionOnArrayBasedRoute($routeInfo)

    /* Proxies to the original `RoutesRequests` */

    public function group(array $attributes, Closure $callback)
    {
        $this->parentApp->group($attributes, $callback);
    }

    public function get($uri, $action)
    {
        return $this->parentApp->get($uri, $action);
    }

    public function post($uri, $action)
    {
        return $this->parentApp->post($uri, $action);
    }

    public function put($uri, $action)
    {
        return $this->parentApp->put($uri, $action);
    }

    public function patch($uri, $action)
    {
        return $this->parentApp->patch($uri, $action);
    }

    public function delete($uri, $action)
    {
        return $this->parentApp->delete($uri, $action);
    }

    public function options($uri, $action)
    {
        return $this->parentApp->options($uri, $action);
    }

    public function addRoute($method, $uri, $action)
    {
        $this->parentApp->addRoute($method, $uri, $action);
    }

    protected function parseAction($action)
    {
        return $this->parentApp->parseAction($action);
    }

    protected function mergeGroupAttributes(array $action)
    {
        return $this->parentApp->mergeGroupAttributes($action);
    }

    protected function mergeNamespaceGroup(array $action)
    {
        return $this->parentApp->mergeNamespaceGroup($action);
    }

    protected function mergeMiddlewareGroup($action)
    {
        return $this->parentApp->mergeMiddlewareGroup($action);
    }

    public function middleware($middleware)
    {
        return $this->parentApp->middleware($middleware);
    }

    public function routeMiddleware(array $middleware)
    {
        return $this->parentApp->routeMiddleware($middleware);
    }

    public function handle(SymfonyRequest $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        return $this->parentApp->handle($request, $type, $catch);
    }

    public function run($request = null)
    {
        return $this->parentApp->run($request);
    }

    protected function callTerminableMiddleware($response)
    {
        return $this->parentApp->callTerminableMiddleware($response);
    }

    protected function parseIncomingRequest($request)
    {
        return $this->parentApp->parseIncomingRequest($request);
    }

    protected function createDispatcher()
    {
        return $this->parentApp->createDispatcher();
    }

    public function setDispatcher(Dispatcher $dispatcher)
    {
        return $this->parentApp->setDispatcher($dispatcher);
    }

    protected function callControllerAction($routeInfo)
    {
        return $this->parentApp->callControllerAction($routeInfo);
    }

    protected function callLumenController($instance, $method, $routeInfo)
    {
        return $this->parentApp->callLumenController($instance, $method, $routeInfo);
    }

    protected function callLumenControllerWithMiddleware($instance, $method, $routeInfo, $middleware)
    {
        return $this->parentApp->callLumenControllerWithMiddleware($instance, $method, $routeInfo, $middleware);
    }

    protected function callControllerCallable(callable $callable, array $parameters = [])
    {
        return $this->parentApp->callControllerCallable($callable, $parameters);
    }

    protected function gatherMiddlewareClassNames($middleware)
    {
        return $this->parentApp->gatherMiddlewareClassNames($middleware);
    }

    protected function sendThroughPipeline(array $middleware, Closure $then)
    {
        return $this->parentApp->sendThroughPipeline($middleware, $then);
    }

    public function prepareResponse($response)
    {
        return $this->parentApp->prepareResponse($response);
    }

    protected function getMethod()
    {
        return $this->parentApp->getMethod();
    }

    protected function getPathInfo()
    {
        return $this->parentApp->getPathInfo();
    }
}
