<?php

namespace Ronanchilvers\Container\Test;

use Ronanchilvers\Container\Container;
use Ronanchilvers\Container\ServiceProviderInterface;

/**
 * Dummy class to test autowiring
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class DummyServiceProvider implements ServiceProviderInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function register(Container $container)
    {
        foreach ($this->data as $key => $value) {
            $container->set($key, $value);
        }
    }
}
