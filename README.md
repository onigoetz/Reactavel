# Reactavel

Running Lumen on top of ReactPHP, making fast even faster.

> This is just an experiment and you should not let this near your production environment.

## Why ?

1. for fun !
2. I wanted to see if we can get a big performance boost by just running the request through the framework, without all the initialization.

## Goals

I have some ideas for this project, we'll se if we can get something out of it.

- A Reactavel enabled installation should still run on apache normally (√ Already working)
- Decode Http request correctly (Cookies, POST, GET, File uploads) (√ Mostly working, Cookies not implemented)

## Installation

```
composer require onigoetz/reactavel dev-master
```

You are now ready to roll by running:  `./vendor/bin/reactavel`

If performance is your main goal, you can also try this with HHVM : `hhvm -v"Eval.Jit=true" ./vendor/bin/reactavel`

## Routes

In the context of an application server, you can't rely on global variables or instances, as they might have informations from other users.
In that case, it is recommended to request the Request and/or the Application in your Controller or route.

```php
use Laravel\Lumen\Application;
use Illuminate\Http\Request;

$app->get(
    '/',
    function (Application $app) {
        return $app->version();
    }
);

$app->get(
    '/user/{id}',
    function ($id, Request $request) {
        return [$id, $request->cookies->all()];
    }
);
```

### Static files

Here is a sample configuration for nginx to serve static files

```
upstream backend_reactavel  {
    server 127.0.0.1:8080;
}

server {
    root /work/external/reactavel/public;
    server_name localhost;
	listen 8090;	
	
    access_log  /logs/nginx/reactavel_access.log;
    error_log   /logs/nginx/reactavel_error.log;
	
	location @reactavel {
		proxy_pass http://backend_reactavel;
	}
	
	location ~* ^.+\.php$ {
		return 404;
	}
	
    try_files $uri @reactavel;
}
```

## A few words about state

Because now that all requests run in the same thread, without any cleanup, any state left behind by another request might have side effects.

Be careful with that.
