<?php
namespace Onigoetz\Reactavel;

use React\Http\Request as ReactRequest;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\ServerBag;

class Request extends \Illuminate\Http\Request
{

    public function __construct(
        array $query = array(),
        array $request = array(),
        array $attributes = array(),
        array $cookies = array(),
        array $files = array(),
        array $server = array(),
        $content = null
    ) {
        //Do not init, we don't want to ...
    }

    public function transformFromReact(ReactRequest $request)
    {

        $this->baseUrl = '';
        $this->requestUri = $request->getPath();

        //TODO :: handle request
        $this->request = new ParameterBag([]);

        $this->query = new ParameterBag($request->getQuery());

        //TODO :: handle attributes
        $this->attributes = new ParameterBag([]);

        //TODO :: handle cookies
        $this->cookies = new ParameterBag([]);

        //TODO :: handle files
        $this->files = new FileBag([]);

        //TODO :: emulate server
        $this->server = new ServerBag([]);
        $this->server->set('REQUEST_METHOD', $request->getMethod());
        $this->server->set('HTTPS', 'off'); //TODO :: get real information


        $this->headers = new HeaderBag($request->getHeaders());

        //TODO :: handle content
        //$this->content = $content;
    }


    /**
     * Creates a new request with values from a React Request
     *
     * @param ReactRequest $origin
     * @return static
     */
    public static function createFromReact(ReactRequest $origin)
    {
        $request = new static();

        $request->transformFromReact($origin);

        return $request;
    }
}
