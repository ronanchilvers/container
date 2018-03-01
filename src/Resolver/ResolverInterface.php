<?php

namespace Ronanchilvers\Container\Resolver;

use Psr\Container\ContainerInterface;

/**
 * Interface for service resolvers
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
interface ResolverInterface
{
    /**
     * Does this resolver support a given service definition?
     *
     * @param mixed $definition
     * @return boolean
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function supports($definition) : boolean;

    /**
     * Resolve a given definition
     *
     * @param Psr\Container\ContainerInterface $container
     * @param mixed $definition
     * @return mixed
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function resolve(ContainerInterface $container, $definition);
}
