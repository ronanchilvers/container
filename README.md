# container

[![Build Status](https://travis-ci.org/ronanchilvers/container.svg?branch=master)](https://travis-ci.org/ronanchilvers/container)
[![codecov](https://codecov.io/gh/ronanchilvers/container/branch/master/graph/badge.svg)](https://codecov.io/gh/ronanchilvers/container)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

A simple, small container for PHP 7+. It has the following features:

* Factory and shared definitions
* Support for non object services (ie: storing key values)
* Aliases
* Autowiring

## Installation

The easiest way to install is via composer:

```
composer install ronanchilvers/container
```

## Usage

Basic usage is simple:

```php
$container = new Container;
$container->set('my_service', function () {
    return new \My\Service();
});

$myService = $container->get('my_service');
```

By default services added to the container are factory services - you'll get a new one every time. If you want to define
a shared service you can do:

```php
$container = new Container;
$container->share('my_shared_service', function () {
    return new \My\Service();
});

$sharedService = $container->get('my_shared_service');
```

You can also register primitives with the container:

```php
$container = new Container;
$container->set('settings', [
    'db' => [
        'adaptor'  => 'mysql',
        'username' => 'foobar',
        'password' => 'supersecret',
        'hostname' => '127.0.0.1'
    ]
]);
$container->set('my_string', 'foobar');

$settings = $container->get('settings');
$settings = $container->get('my_string');
```

### Aliases

Sometimes its useful to be able to alias a service. For example if you want to register a service with a simple string name but also refer to it by an interface name. To do this you can use a Symfony style prefix on the definition to indicate that its a reference to another service.

Here's an example:

```php
$container = new Container;
$container->share('logger', function (){
    return new PSR11Logger();
});
$container->set('Psr\Log\LoggerInterface', '@logger');

// This:
$logger = $container->get('logger');
// returns the same instance as this:
$logger = $container->get('Psr\Log\LoggerInterface');
```

### Autowiring

The container supports basic autowiring. This means that you can supply a fully qualified class name as a service definition and the container will attempt to instantiate it for you.

```php
$container = new Container;
$container->set('logger', '\App\MyLogger');

$logger = $container->get('logger');
```

Constructor injection is also supported for type hinted parameters.

```php
use Psr\Log\LoggerInterface;

class MyLogger implements LoggerInterface
{
    ...
}
class MyService
{
    public function __construct(LoggerInterface $logger)
    {
        ...
    }
}
$container = new Container;
$container->share(LoggerInterface::class, 'MyLogger');
$container->share(MyService::class, 'MyService');

// This will return an instantiated service with the logger injected
$service = $container->get(MyService::class);
```

The injected objects do not have to be registered with the container to be injected. If the container encounters a dependency that is not defined as a service it will attempt to create a new instance with no constructor parameters.

### Service Providers

The container supports pimple style service providers. Your provider must implement ```Ronanchilvers\Container\ServiceProviderInterface```.

```php
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function register(Container $container)
    {
        $container->set('my_service', function () {
            return new StdClass;
        });
    }
}

$container = new Container;
$container->register(new ServiceProvider);

$myService = $container->get('my_service');
```

## Testing

The container is quite simple and has 100% test coverage. You can run the tests by doing:

```
./vendor/bin/phpunit
```

The default phpunit.xml.dist file creates coverage information in a build/coverage subdirectory.

## Contributing

If anyone has any patches they want to contribute I'd be more than happy to review them. Please raise a PR. You should:

* Follow PSR2
* Maintain 100% test coverage or give the reasons why you aren't
* Follow a one feature per pull request rule

## License

This software is licensed under the MIT license. Please see the [License File](LICENSE.md) for more information.
