<?php
namespace Main;

use Kernel\Collection\StringCollection;
use Kernel\ControllerEngine\PointMatches;

class Application
{

    public static function main(StringCollection $args): void
    {
        new PointMatches(request: $args->request::getInstance(), response: $args->response::getInstance());
    }
}
