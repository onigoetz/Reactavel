<?php
namespace Onigoetz\Reactavel;

use React\Http\Request as ReactRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\ServerBag;

class Request extends \Illuminate\Http\Request
{

    private $cookiePairSplitRegexp = "/; */";

    public function __construct(
        array $query = array(),
        array $request = array(),
        array $attributes = array(),
        array $cookies = array(),
        array $files = array(),
        array $server = array(),
        $content = null
    )
    {
        //Do not init, we don't want to ...
    }

    private function buildServerBag(ReactRequest $request) {

        // TODO :: add a few more informations if needed

        $server = [
            //'DOCUMENT_ROOT' => '.../reactavel/public',
            'REMOTE_ADDR' => $request->getRemoteAddress(),
            //'REMOTE_PORT' => '62945',
            'SERVER_SOFTWARE' => 'ReactPHP Reactavel',
            'SERVER_PROTOCOL' => 'HTTP/1.0',
            'SERVER_NAME' => Reactavel::getHost(),
            'SERVER_PORT' => Reactavel::getPort(),
            //'REQUEST_URI' => '/?yep=true',
            'REQUEST_METHOD' => $request->getMethod(),
            //'SCRIPT_NAME' => '/index.php',
            //'SCRIPT_FILENAME' => '.../public/index.php',
            //'PHP_SELF' => '/index.php',
            //'QUERY_STRING' => 'yep=true',
            'REQUEST_TIME_FLOAT' => microtime(true),
            'REQUEST_TIME' => floor(microtime(true))
        ];

        foreach ($this->headers as $key => $header) {
            $server['HTTP_' . str_replace('-', '_', strtoupper($key))] = $header[0];
        }

        return $server;
    }

    private function buildFileBag(ReactRequest $request)
    {
        $files = [];
        foreach ($request->getFiles() as $file) {
            $path = tempnam(sys_get_temp_dir(), "php");
            $result = file_put_contents($path, $file['stream']);
            $error = ($result === false) ? UPLOAD_ERR_NO_FILE : UPLOAD_ERR_OK;
            $files[] = new UploadedFile($path, $file['name'], $file['type'], filesize($path), $error);
        }

        return $files;
    }

    private function buildCookieBag()
    {
        if (!$this->headers->has('Cookie')) {
            return [];
        }

        $cookies = [];
        $pairs = preg_split($this->cookiePairSplitRegexp, $this->headers->get('Cookie'));

        foreach ($pairs as $pair) {
            $eq_idx = strpos($pair, '=');

            // skip things that don't look like key=value
            if ($eq_idx < 0) {
                return;
            }

            $key = trim(substr($pair, 0, $eq_idx));
            $val = trim(substr($pair, ++$eq_idx, strlen($pair)));

            // quoted values
            if ('"' == $val[0]) {
                $val = substr($val, 1, -1);
            }

            // only assign once
            if (!array_key_exists($key, $cookies)) {
                $cookies[$key] = urldecode($val);
            }
        }

        return $cookies;
    }

    public function transformFromReact(ReactRequest $request)
    {
        $this->baseUrl = '';
        $this->requestUri = $request->getPath();
        $this->content = $request->getBody();

        // Request Headers
        $this->headers = new HeaderBag($request->getHeaders());

        // Server informations ($_SERVER)
        $this->server = new ServerBag($this->buildServerBag($request));

        // Request body parameters ($_POST).
        if (0 === strpos($this->headers->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($this->server->get('REQUEST_METHOD', 'GET')), ['POST', 'PUT', 'DELETE', 'PATCH'])
        ) {
            $this->request = new ParameterBag($request->getPost());
        }

        // Query string parameters ($_GET).
        $this->query = new ParameterBag($request->getQuery());

        // Request attributes
        $this->attributes = new ParameterBag([]);

        // Cookies
        $this->cookies = new ParameterBag($this->buildCookieBag());

        // Files uploads
        $this->files = new FileBag($this->buildFileBag($request));
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
