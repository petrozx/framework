<?php
namespace Main;

use Kernel\Collection\StringCollection;
use Kernel\ControllerEngine\PointMatches;

class Application
{

    public static function main(StringCollection $args): void
    {
        if (str_contains($args->request::getInstance()->getRoute(), '/async')) {
            $args->response::getInstance()->__invoke(call_user_func($_GET['oper'], $_GET['num']));
        } else {
            new PointMatches(request: $args->request::getInstance(), response: $args->response::getInstance());
        }
    }
}
