# Reactavel

Laravel framework on top of ReactPHP

## Why ?
Mainly as an experiment, I wanted to try to discover if the performance boost you get by using a "pre-bootstrapped framework" is worth the pain to put in place.

This highly alpha and nothing near production, but you can try it if you feel like it

## Goals

I have some ideas for this project, we'll se if we can get something out of it.

- Laravel compatibility, even with Reactavel enabled, and app still should be able to run in an apache server or with `./artisan serve`
- Make sessions work correctly
- Decode Http request correctly (Cookies, POST, GET, File uploads)


## Wanna try at home ?

With these 4 easy steps you can try the laravel default page

```
composer create-project --prefer-dist -sdev laravel/laravel reactavel dev-develop
cd reactavel/
composer require onigoetz/reactavel dev-master
sed -i.bak s/Illuminate\\\\Foundation\\\\Application/Onigoetz\\\\Reactavel\\\\Application/g bootstrap/start.php
```

You are now ready to try :

```
./vendor/bin/reactavel 
```

or

```
hhvm -v"Eval.Jit=true" ./vendor/bin/reactavel
```

to compare, you can also run 

```
./artisan serve
```

### static files

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