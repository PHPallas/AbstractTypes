<?php

/*
 * This file is part of the PHPallas package.
 *
 * (c) Sina Kuhestani <sinakuhestani@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPallas\AbstractTypes;

use ReflectionClass;
use Exception;

/**
 * Class Factory
 * 
 * This abstract class serves as a base for different factory types.
 * It enforces rules regarding the creation methods based on the selected factory mode.
 */
abstract class Factory
{
    const FACTORY_SIMPLE = 1;
    const FACTORY_ABSTRACT = 2;
    const FACTORY_METHOD = 3;
    const FACTORY_STATIC = 4;
    const FACTORY_BUILDER = 5;

    /**
     * @var int The current factory mode.
     */
    protected static $mode = 1;

    /**
     * @var mixed The product created by the factory.
     */
    protected $product = null;

    /**
     * Factory constructor.
     * 
     * Validates the factory configuration based on the selected mode.
     * 
     * @throws Exception If any of the validation rules are violated.
     */
    public function __construct()
    {
        $reflection = new ReflectionClass($this);
        $methods = $reflection->getMethods();
        $createMethodsCount = 0;
        $nonCreateMethodsCount = 0;
        $publicMethodsCount = 0;
        $staticMethodsCount = 0;
        $nonStaticMethodsCount = 0;
        $hasStaticFactoryMethod = false;

        foreach ($methods as $method)
        {
            if (strpos($method->getName(), '__') === 0 || "get" === $method->getName())
            {
                continue;
            }
            if ($method->isStatic())
            {
                $staticMethodsCount++;
                if ("factory" === $method->getName())
                {
                    $hasStaticFactoryMethod = true;
                }
            }
            else
            {
                $nonStaticMethodsCount++;
                if (0 === strpos($method->getName(), 'create'))
                {
                    $createMethodsCount++;
                    if ($method->isPublic())
                    {
                        $publicMethodsCount++;
                    }
                }
                else
                {
                    $nonCreateMethodsCount++;
                }
            }
        }

        // Validate factory configuration
        if (0 < $publicMethodsCount)
        {
            throw new Exception("`public` methods are not allowed");
        }
        switch (static::$mode)
        {
            case static::FACTORY_ABSTRACT:
                if (0 < $nonCreateMethodsCount)
                {
                    throw new Exception("non `create` methods are not allowed in FACTORY_ABSTRACT");
                }
                if (0 < $staticMethodsCount)
                {
                    throw new Exception("`static` methods are not allowed in FACTORY_ABSTRACT");
                }
                if (1 > $createMethodsCount)
                {
                    throw new Exception("FACTORY_ABSTRACT must have at least one `create` method");
                }
                break;
            case static::FACTORY_BUILDER:
                if (0 < $staticMethodsCount)
                {
                    throw new Exception("`static` methods are not allowed in FACTORY_BUILDER");
                }
                if (1 !== $createMethodsCount)
                {
                    throw new Exception("FACTORY_BUILDER must have exactly one `create` method");
                }
                if (1 > $nonCreateMethodsCount)
                {
                    throw new Exception("FACTORY_BUILDER must have at least one non `create` method");
                }
                break;
            case static::FACTORY_METHOD:
                if (0 < $staticMethodsCount)
                {
                    throw new Exception("`static` methods are not allowed in FACTORY_METHOD");
                }
                if (1 !== $createMethodsCount)
                {
                    throw new Exception("FACTORY_METHOD must have exactly one `create` method");
                }
                if (0 < $nonCreateMethodsCount)
                {
                    throw new Exception("FACTORY_METHOD must not have non `create` methods");
                }
                break;
            case static::FACTORY_SIMPLE:
                if (0 < $staticMethodsCount)
                {
                    throw new Exception("`static` methods are not allowed in FACTORY_SIMPLE");
                }
                if (1 !== $createMethodsCount)
                {
                    throw new Exception("FACTORY_SIMPLE must have exactly one `create` method");
                }
                if (0 < $nonCreateMethodsCount)
                {
                    throw new Exception("FACTORY_SIMPLE must not have non `create` methods");
                }
                break;
            case static::FACTORY_STATIC:
                if (1 !== $staticMethodsCount)
                {
                    throw new Exception("FACTORY_STATIC must have exactly one `static` method named `factory`");
                }
                if (0 < $nonStaticMethodsCount) {
                    throw new Exception("FACTORY_STATIC must not have any non-`static` methods");
                }
                if (false === $hasStaticFactoryMethod) {
                    throw new Exception("FACTORY_STATIC must have exactly one `static` method named `factory`");
                }
                break;
            default:
                throw new Exception("Undefined FACTORY PATTERN");
        }
    }

    /**
     * Magic method to handle calls to undefined methods.
     * 
     * @param string $name The name of the method being called.
     * @param array $arguments The arguments passed to the method.
     * @return mixed The result of the method call.
     * @throws Exception If the method does not exist.
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name))
        {
            return $this->$name(...$arguments);
        }
        else
        {
            throw new Exception("Method '$name' does not exist.");
        }
    }

    /**
     * Gets the created product.
     * 
     * @return mixed The product created by the factory.
     */
    public function get()
    {
        return $this->product;
    }
}
