# AbstractTypes

Abstract Layers to implement design patterns in PHP.

## Overview

This package provides an abstract implementation of various creational design patterns in PHP, including Simple Factory, Abstract Factory, Factory Method, Static Factory, and Builder. Each pattern is encapsulated in its own class that extends the base `Factory` class.

## Simple Factory

The **Simple Factory** pattern creates objects based on a given type. It is a straightforward approach to object creation.

```php
<?php

use PHPallas\AbstractTypes\Factory;

class SimpleFactory extends Factory
{
    protected static $mode = Factory::FACTORY_SIMPLE;

    protected function create(string $type)
    {
        switch ($type) {
            case 'car':
                return new Car();
            case 'bike':
                return new Bike();
            default:
                throw new Exception("Unknown type");
        }
    }
}
```

### Usage

```php
$factory = new SimpleFactory();
$vehicle = $factory->create('car'); // Returns a Car object
```

## Abstract Factory

The **Abstract Factory** pattern provides an interface for creating families of related or dependent objects without specifying their concrete classes.

```php
<?php

use PHPallas\AbstractTypes\Factory;

class AbstractFactory extends Factory
{
    protected static $mode = Factory::FACTORY_ABSTRACT;

    protected function createSomething()
    {
        return new Something();
    }

    protected function createAnotherThing()
    {
        return new AnotherThing();
    }
}
```

### Usage

```php
$factory = new AbstractFactory();
$something = $factory->createSomething(); // Returns a Something object
$anotherThing = $factory->createAnotherThing(); // Returns an AnotherThing object
```

## Factory Method

The **Factory Method** pattern defines an interface for creating an object but allows subclasses to alter the type of objects that will be created.

```php
<?php

use PHPallas\AbstractTypes\Factory;

class FactoryMethod extends Factory
{
    protected static $mode = Factory::FACTORY_METHOD;

    protected function createSomething()
    {
        return new Something();
    }
}
```

### Usage

```php
$factory = new FactoryMethod();
$something = $factory->createSomething(); // Returns a Something object
```

## Static Factory

The **Static Factory** pattern uses static methods to create objects, allowing for a more straightforward creation process.

```php
<?php

use PHPallas\AbstractTypes\Factory;

class StaticFactory extends Factory
{
    protected static $mode = Factory::FACTORY_STATIC;

    public static function factory()
    {
        return new Something();
    }
}
```

### Usage

```php
$something = StaticFactory::factory(); // Returns a Something object
```

## Builder

The **Builder** pattern constructs a complex object step by step. It separates the construction of a complex object from its representation.

```php
<?php

use PHPallas\AbstractTypes\Factory;

class Builder extends Factory
{
    protected static $mode = Factory::FACTORY_BUILDER;

    protected function createSomething()
    {
        $this->product = new Something();
    }

    protected function setColor($color)
    {
        $this->product->color = $color;
    }
}
```

### Usage

```php
$builder = new Builder();
$builder->createSomething();
$builder->setColor('red');
$product = $builder->get(); // Returns a Something object with the color set to red
```
