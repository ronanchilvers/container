<?php

namespace Ronanchilvers\Container;

use Psr\Container\ContainerInterface;
use Ronanchilvers\Container\NotFoundException;
use Ronanchilvers\Container\Resolver\AliasResolver;
use Ronanchilvers\Container\Resolver\CallableResolver;
use Ronanchilvers\Container\Resolver\PrimitiveResolver;
use Ronanchilvers\Container\Resolver\ReflectionResolver;
use Ronanchilvers\Container\Resolver\ResolverInterface;
use ArrayAccess;

/**
 * A basic PSR-11 compliant container
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class Container implements
    ContainerInterface,
    ArrayAccess
{
    /**
     * @var Ronanchilvers\Container\Resolver\ResolverInterface[]
     */
    protected $resolvers = [
        AliasResolver::class,
        CallableResolver::class,
        ReflectionResolver::class,
        PrimitiveResolver::class,
    ];

    /**
     * @var array
     */
    protected $definitions = [];

    /**
     * @var array
     */
    protected $factories = [];

    /**
     * @var array
     */
    protected $resolved = [];

    /**
     * Class constructor
     *
     * @param array $values A set of container values to initialise the container with
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(array $items = [])
    {
        $this->set(ContainerInterface::class, $this);

        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

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
        // Resolved services are returned immediately
        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }
        // Factories are checked next
        if (isset($this->factories[$id])) {
            $callable = $this->factories[$id];
            return $callable($this);
        }
        // Resolve the service
        $definition = $id;
        if (isset($this->definitions[$id])) {
            $definition = $this->definitions[$id];
        }
        foreach ($this->resolvers as &$resolver) {
            if (is_string($resolver)) {
                $resolver = new $resolver;
            }
            if (!$resolver->supports($definition)) {
                continue;
            }
            $service = $resolver->resolve(
                $this,
                $definition
            );
            if (false !== $service) {
                $this->resolved[$id] = $service;
                return $service;
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
        if (isset($this->resolved[$id])) {
            return true;
        }

        if (isset($this->definitions[$id])) {
            return true;
        }

        if (isset($this->factories[$id])) {
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

    /**
     * Create a factory service
     *
     * @param string $id
     * @param mixed $callable
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function factory($id, $callable)
    {
        $this->factories[$id] = $callable;
    }

    /** START ArrayAccess compliance **/

    public function offsetExists($offset)
    {
        return isset($this->definitions[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        if (isset($this->definitions[$offset])) {
            unset($this->definitions[$offset]);
        }
    }

    /** END ArrayAccess compliance **/

}
