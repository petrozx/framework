<?php
namespace Kernel\ErrorRequest;
use Kernel\ControllerEngine\Attributes\Controller;
use Kernel\ControllerEngine\Attributes\ALL;
use Kernel\HttpEngine\HttpResponse;

#[Controller]
class Response_404
{
    #[ALL]
    public function NOT_FOUND()
    {
        return HttpResponse::getInstance()->fail(errors: [404], message:'This request is faling.', statusCode: 404);
    }
}