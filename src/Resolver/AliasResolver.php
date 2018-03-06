<?php

namespace Ronanchilvers\Container\Resolver;

use Psr\Container\ContainerInterface;

/**
 * Resolver for definitions that are aliases for other services
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class AliasResolver implements ResolverInterface
{
    /**
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function supports($definition)
    {
        return is_string($definition) && '@' == substr($definition, 0, 1);
    }

    /**
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function resolve(ContainerInterface $container, $id, $definition)
    {
        $definition = substr($definition, 1);
        if ($container->has($definition)) {
            return $container->get($definition);
        }

        return false;
    }
}
