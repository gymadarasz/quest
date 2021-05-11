<?php

namespace Madlib;

use ReflectionClass;

class Factory
{
    protected static array $instances = [];

    public function __construct()
    {
        $instances[self::class] = $this;
    }

    public function getInstance(string $class): object
    {
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = '__in_progress__';
            $args = [];
            if ($constructor = (new ReflectionClass($class))->getConstructor()) {
                foreach ($constructor->getParameters() as $param) {
                    $args[] = $this->getInstance($param->getType()->getName());
                }
            }
            self::$instances[$class] = new $class(...$args);
        }
        if (self::$instances[$class] === '__in_progress__') {
            $path = [];
            foreach (self::$instances as $clazz => $instance) {
                if ($instance === '__in_progress__') {
                    $path[] = $clazz;
                }
            }
            throw new Exception('Circular dependency: ' . implode(' -> ', $path) . ' -> *recursion*');
        }
        return self::$instances[$class];
    }
}
