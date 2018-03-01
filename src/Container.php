<?php

namespace Ronanchilvers\Container;

use Psr\Container\ContainerInterface;
use Ronanchilvers\Container\NotFoundException;
use Ronanchilvers\Container\Resolver\ResolverInterface;

/**
 * A basic PSR-11 compliant container
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class Container implements ContainerInterface
{
    /**
     * @var Ronanchilvers\Container\Resolver\ResolverInterface[]
     */
    protected $resolvers = [];

    /**
     * @var array
     */
    protected $definitions = [];

    /**
     * Register a resolver
     *
     * @param Ronanchilvers\Container\Resolver\ResolverInterface $resolver
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function registerResolver(ResolverInterface $resolver)
    {
        $this->resolvers[get_class($resolver)] = $resolver;
    }

    /**
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function get($id)
    {
        if ($this->has($id)) {
            $definition = $this->definitions[$id];
            foreach ($this->resolvers as $resolver) {
                if (!$resolver->supports($definition)) {
                    continue;
                }
                $service = $resolver->resolve(
                    $this,
                    $definition
                );
                if (false !== $service) {
                    return $service;
                }
            }
        }

        throw new NotFoundException(
            sprintf('Service \'%s\' not found', $id)
        );
    }

    /**
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function has($id)
    {
        if (isset($this->definitions[$id])) {
            return true;
        }

        return false;
    }

    /**
     * Set a definition in the container
     *
     * @param string $key
     * @param mixed $definition
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function set($id, $definition)
    {
        $this->definitions[$id] = $definition;
    }
}
