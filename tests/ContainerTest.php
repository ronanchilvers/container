<?php

namespace RonanChilvers\Container\Test;

use PHPUnit\Framework\TestCase;
use Ronanchilvers\Container\Container;
use Ronanchilvers\Container\NotFoundException;
use StdClass;

/**
 * Base test case for container
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class ContainerTest extends TestCase
{
    /**
     * Test has() for a defined service
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testHasForDefinedService()
    {
        $container = new Container;
        $container->set('test', 'foobar');

        $this->assertTrue($container->has('test'));
    }

    /**
     * Test has() for a shared service
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testHasForSharedService()
    {
        $container = new Container;
        $container->share('test', 'foobar');

        $this->assertTrue($container->has('test'));
    }

    /**
     * Test has() for a resolved service
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testHasForPreviouslySeenSharedService()
    {
        $container = new Container;
        $container->share('test', 'foobar');
        $container->get('test');

        $this->assertTrue($container->has('test'));
    }

    /**
     * Test has() for unknown service
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testHasForUnknownService()
    {
        $container = new Container;

        $this->assertFalse($container->has('test'));
    }

    /**
     * Provider for primitives
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function primitiveProvider()
    {
        return [
            ['test', 'foobar'],
            ['test', true],
            ['test', ['foo']],
        ];
    }

    /**
     * Test get and set for a simple string
     *
     * @dataProvider primitiveProvider
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testSetGetAString($id, $definition)
    {
        $container = new Container;
        $container->set($id, $definition);

        $this->assertEquals(
            $definition,
            $container->get($id)
        );
    }

    /**
     * Test that by default services are factories
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testGetSetServiceAsFactory()
    {
        $container = new Container;
        $container->set(
            'test',
            function ($c) {
                return uniqid();
            }
        );

        $h1 = $container->get('test');
        $h2 = $container->get('test');

        $this->assertNotEquals($h1, $h2);
    }

    /**
     * Test getting / setting a shared service
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testGetSetServiceAsShared()
    {
        $container = new Container;
        $container->share(
            'test',
            function ($c) {
                return uniqid();
            }
        );

        $h1 = $container->get('test');
        $h2 = $container->get('test');

        $this->assertEquals($h1, $h2);
    }

    /**
     * Test settings services via the constructor
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testGetSetViaConstructor()
    {
        $array = ['test' => 'foobar'];
        $container = new Container($array);

        $this->assertTrue($container->has('test'));
        $this->assertEquals('foobar', $container->get('test'));
    }

    /**
     * Test that unresolved services throw an exception
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testGetUnresolvedServiceThrowsException()
    {
        $container = new Container;

        $this->expectException(NotFoundException::class);
        $container->get('test');
    }

    /**
     * Test a service alias works
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testServiceAliasWorks()
    {
        $container = new Container;
        $container->set('test', 'foobar');
        $container->set('test2', '@test');

        $this->assertEquals('foobar', $container->get('test2'));
    }

    /**
     * Test unknown aliases aren't found
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testUnknownAliasIsNotFound()
    {
        $container = new Container;
        $container->set('test2', '@test');

        $this->expectException(NotFoundException::class);
        $container->get('test2');
    }
}
