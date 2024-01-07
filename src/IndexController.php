<?php

use Kernel\ControllerEngine\Attributes\Controller;
use Kernel\ControllerEngine\Attributes\POST;
use Kernel\ControllerEngine\Attributes\GET;
use Kernel\HttpEngine\HttpResponse;
use Kernel\HttpEngine\HttpRequest;
use \Kernel\Async\AsyncEngine;

#[Controller(uri: "/test")]
class IndexController
{

    public function __construct(
        private HttpResponse $response,
        private HttpRequest $request,
        private AsyncEngine $asyncEngine,
    ){}

    #[GET(uri: "/data/{check2}", params: ['check2' => 'string'])]
    public function index($check2)
    {
        return $this->response->success(['test2' => $check2]);
    }

    #[POST(uri: "/get/one")]
    public function check()
    {
        $this->asyncEngine->async(function($int) {
            sleep(10);
            return $int;
        }, range(1,2));
        $this->asyncEngine->async('Test::do', range(1,2));
        $responses = $this->asyncEngine->getResponses();
        return $this->response->success($responses);
    }
}