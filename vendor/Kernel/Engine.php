<?php
define("ASYNC", str_contains($_SERVER['REQUEST_URI'], '/async'));
require_once('./autoload.php');
use Main\Application;
use Kernel\HttpEngine\HttpRequest;
use Kernel\HttpEngine\HttpResponse;
use Kernel\Collection\StringCollection;

Application::main(
    new StringCollection(request: HttpRequest::class, response: HttpResponse::class)
);