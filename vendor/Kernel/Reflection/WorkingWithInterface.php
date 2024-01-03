<?php
namespace Kernel\Reflection;

interface WorkingWithInterface
{
    public function find(string $name, ?array $args): array;

    public function execute($args): mixed;
}