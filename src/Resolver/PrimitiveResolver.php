<?php

namespace Ronanchilvers\Container\Resolver;

use Psr\Container\ContainerInterface;

/**
 * Resolver for definitions that are arrays
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class PrimitiveResolver implements ResolverInterface
{
    /**
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function supports($id, $definition)
    {
        if (is_string($definition) && '@' == substr($definition, 0, 1)) {
            return false;
        }
        return is_scalar($definition) ||
               is_array($definition) ||
               (is_object($definition) && !is_callable($definition));
    }

    /**
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function resolve(ContainerInterface $container, $id, $definition)
    {
        return $definition;
    }
}
