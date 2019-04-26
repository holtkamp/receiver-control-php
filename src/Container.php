<?php

declare(strict_types=1);

namespace ReceiverControl;

use Psr\Container\ContainerInterface;
use function class_exists;

class Container implements ContainerInterface
{
    public function get($id) : object
    {
        return new $id($this);
    }

    public function has($id) : bool
    {
        return class_exists($id);
    }
}
