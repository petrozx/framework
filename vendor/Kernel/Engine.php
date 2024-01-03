<?php
require_once('./autoload.php');
use Main\Application;
use Kernel\HttpEngine\HttpRequest;
use Kernel\HttpEngine\HttpResponse;
use Kernel\Collection\StringCollection;

Application::main(
    new StringCollection(request: HttpRequest::class, response: HttpResponse::class)
);