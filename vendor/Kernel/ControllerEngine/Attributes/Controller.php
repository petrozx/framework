<?php
namespace Kernel\ControllerEngine\Attributes;

use \Attribute;
use Kernel\ControllerEngine\ControllerEngine;
use Kernel\ControllerEngine\Router;

#[Attribute]
class Controller extends Router
{}