<?php

namespace Ronanchilvers\Container\Resolver;

use Psr\Container\ContainerInterface;

/**
 * Resolver for definitions that are callable
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class CallableResolver implements ResolverInterface
{
    /**
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function supports($definition)
    {
        return is_callable($definition);
    }

    /**
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function resolve(ContainerInterface $container, $id, $definition)
    {
        return $definition($container);
    }
}
