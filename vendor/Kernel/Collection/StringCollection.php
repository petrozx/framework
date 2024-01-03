<?php
namespace Kernel\Collection;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class StringCollection implements IteratorAggregate
{
    public function __construct(
        public string $request,
        public string $response,
    ) {}

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this);
    }
}