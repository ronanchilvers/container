<?php

namespace Ronanchilvers\Container\Resolver;

use Psr\Container\ContainerInterface;
use ReflectionClass;

/**
 * A resolver that uses reflection to try to autowire services
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class ReflectionResolver implements ResolverInterface
{
    /**
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function supports($id, $definition)
    {
        if (is_null($definition)) {
            $definition = $id;
        }

        return is_string($definition) && class_exists($definition);
    }

    /**
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function resolve(ContainerInterface $container, $id, $definition)
    {
        if (is_null($definition)) {
            $definition = $id;
        }

        $reflection = new ReflectionClass($definition);
        if (!$reflection->isInstantiable()) {
            return false;
        }

        $constructor = $reflection->getConstructor();
        if (is_null($constructor)) {
            return $reflection->newInstance();
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $class = $parameter->getClass();
            if (is_null($class)) {
                return false;
            }
            $dependencies[] = $container->get($class->getName());
        };

        return $reflection->newInstanceArgs($dependencies);
    }
}
