<?php
use Kernel\ControllerEngine\Attributes\Controller;
use Kernel\ControllerEngine\Attributes\POST;
use Kernel\ControllerEngine\Attributes\GET;
use Kernel\HttpEngine\HttpResponse;
use Kernel\HttpEngine\HttpRequest;

#[Controller(uri: "/test")]
class IndexController
{

    public function __construct(
        private HttpResponse $response,
        private HttpRequest $request,
    ){}

    #[GET(uri: "/data/{check2}", params: ['check2' => 'string'])]
    public function index($check2)
    {
        return $this->response->success(['test2' => $check2]);
    }

    #[POST(uri: "/get/one")]
    public function check()
    {
        return $this->response->success($this->request->toArray());
    }
}