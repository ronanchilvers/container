<?php

namespace Ronanchilvers\Container\Test;

/**
 * Dummy class with dependency to test autowiring
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class DummyWithDependency
{
    public $dependency = null;

    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(Dummy $dependency)
    {
        $this->dependency = $dependency;
    }
}
