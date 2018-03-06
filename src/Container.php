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
class Container implements ContainerInterface
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
    protected $resolved = [];

    /**
     * @var array
     */
    protected $shared = [];

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
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function get($id)
    {
        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }
        $definition = null;
        if (isset($this->definitions[$id])) {
            $definition = $this->definitions[$id];
        }
        foreach ($this->resolvers as &$resolver) {
            if (is_string($resolver)) {
                $resolver = new $resolver;
            }
            if (!$resolver->supports($id, $definition)) {
                continue;
            }
            $service = $resolver->resolve(
                $this,
                $id,
                $definition
            );
            if (false !== $service) {
                if (isset($this->shared[$id])) {
                    $this->resolved[$id] = $service;
                }
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
        $this->setDefinition($id, $definition, false);
    }

    /**
     * Set a shared service definition in the container
     *
     * @param string $key
     * @param mixed $definition
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function share($id, $definition)
    {
        $this->setDefinition($id, $definition, true);
    }

    /**
     * Set a definition in the container
     *
     * @param string $key
     * @param mixed $definition
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function setDefinition($id, $definition, $shared = false)
    {
        $this->definitions[$id] = $definition;
        if (true === $shared) {
            $this->shared[$id] = $id;
        }
    }
}
