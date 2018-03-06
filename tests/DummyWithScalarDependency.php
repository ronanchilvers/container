<?php

namespace Ronanchilvers\Container\Test;

/**
 * Dummy class with dependency to test autowiring
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class DummyWithScalarDependency
{
    public $dependency = null;

    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(string $dependency)
    {
        $this->dependency = $dependency;
    }
}
