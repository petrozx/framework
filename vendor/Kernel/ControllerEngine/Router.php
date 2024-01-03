<?php
namespace Kernel\ControllerEngine;

abstract class Router
{

    public function __construct(
        private string $uri = '',
        private array $params = [],
    ) {}

}