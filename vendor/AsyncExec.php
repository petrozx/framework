<?php
namespace Main;
use Kernel\HttpEngine\HttpResponse;
use Kernel\HttpEngine\HttpRequest;

class AsyncExec
{
    public static function execute(HttpRequest $request, HttpResponse $response)
    {
        $__get = $request->GET();

        if ((bool) $__get['closure']) {
            $response->__invoke(\Kernel\Async\AsyncClosure::execute($__get['function'], unserialize($__get['arg'])));
        } else {
            $response->__invoke(call_user_func($__get['function'], unserialize($__get['arg'])));
        }
    }
}