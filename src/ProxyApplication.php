<?php
namespace Onigoetz\Reactavel;

use FastRoute\Dispatcher;
use Laravel\Lumen\Application as LumenApplication;
use React\Http\Response;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ProxyApplication extends LumenApplication
{
    use ProxyContainer,
        ProxyRoutesRequests;

    protected $parentApp;

    /**
     * ProxyApplication constructor.
     *
     * We override the parent in order to initialize only the parts we need.
     * The rest is left up to the parent application
     *
     * @param LumenApplication $app The parent application
     */
    public function __construct(LumenApplication $app)
    {
        $this->parentApp = $app;
        $this->basePath = $app->basePath;

        // We must override all bindings in order to
        // delegate that work to the parent container
        $this->availableBindings = [];

        $this->instance('app', $this);
        $this->instance('Laravel\Lumen\Application', $this);
    }

    /**
     * Serve the request linked to this ProxyApplication
     *
     * The serve method is used only by Reactavel,
     * It's added to the Application because the methods it needs are protected.
     *
     * @param SymfonyRequest $request
     * @param Response $response
     * @throws \Exception
     */
    public function serve(SymfonyRequest $request, Response $response)
    {
        $innerResponse = $this->dispatch($request);

        if (!$innerResponse instanceof SymfonyResponse) {
            $innerResponse = new SymfonyResponse((string)$innerResponse);
        }

        $response->writeHead($innerResponse->getStatusCode(), $innerResponse->headers->all());
        $response->end($innerResponse->getContent());

        if (count($this->middleware) > 0) {
            $this->callTerminableMiddleware($innerResponse);
        }
    }
}
