<?php

namespace Ronanchilvers\Container;

use Ronanchilvers\Container\Container;

/**
 * Interface for service provider objects
 *
 * This concept is taken from Pimple
 *
 * @see https://github.com/silexphp/Pimple/blob/master/src/Pimple/Container.php#L288
 * @author Ronan Chilvers <ronan@d3r.com>
 */
interface ServiceProviderInterface
{
    /**
     * Register services against a given container instance
     *
     * @param Ronanchilvers\Container\Container $container
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function register(Container $container);
}
