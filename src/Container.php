<?php

declare(strict_types=1);

namespace ReceiverControl;

use Psr\Container\ContainerInterface;
use function array_key_exists;
use function class_exists;

class Container implements ContainerInterface
{
    /** @var array<string, object> */
    private array $resources;

    /**
     * @param array<string, object>
     */
    public function __construct(array $resources = [])
    {
        $this->resources = $resources;
    }

    public function __get(string $id) : object
    {
        return $this->get($id);
    }

    public function __set(string $id, object $resource) : void
    {
        $this->set($id, $resource);
    }

    public function get($id) : object
    {
        return $this->resources[$id] ?? new $id($this);
    }

    public function set(string $id, object $resource) : void
    {
        $this->resources[$id] = $resource;
    }

    public function __isset(string $id) : bool
    {
        return $this->has($id);
    }

    public function has($id) : bool
    {
        return array_key_exists($id, $this->resources) || class_exists($id);
    }
}
