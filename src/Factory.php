<?php

namespace PHPallas\AbstractTypes;

use ReflectionClass;
use Exception;

/**
 * Abstract class Factory.
 *
 * This class enforces that all methods starting with 'create' are defined
 * in any subclass and that they are either protected or private.
 */
abstract class Factory
{

    const FACTORY_SIMPLE = 1;
    const FACTORY_ABSTRACT = 2;
    const FACTORY_METHOD = 3;
    const FACTORY_STATIC = 4;
    const FACTORY_BUILDER = 5;
    protected static $mode = 1;
    protected $product = null;

    /**
     * Factory constructor.
     *
     * Checks all methods in the subclass to ensure they start with 'create'
     * and are not public. Throws an exception if any method does not meet
     * these criteria.
     *
     * @throws Exception If a method does not start with 'create' or is public.
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
                    throw new Exception("FACTORY_ABSTRACT must have atleast one `create` method");
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
                    throw new Exception("FACTORY_BUILDER must have atleast one non `create` method");
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
                    throw new Exception("FACTORY_METHOD must haven't non `create` methods");
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
                    throw new Exception("FACTORY_SIMPLE must haven't non `create` methods");
                }
                break;
            case static::FACTORY_STATIC:
                if (1 !== $staticMethodsCount)
                {
                    throw new Exception("FACTORY_STATIC must have exactly one `static` method names `factory`");
                }
                if (0 < $nonStaticMethodsCount) {
                    throw new Exception("FACTORY_STATIC must not have any non`static` method");
                }
                if (false === $hasStaticFactoryMethod) {
                    throw new Exception("FACTORY_STATIC must have exactly one `static` method names `factory`");
                }
                break;
            default:
                throw new Exception("Undefined FACTORY PATTERN");
        }

    }

    /**
     * Magic method to handle calls to inaccessible methods.
     *
     * This method is triggered when invoking inaccessible methods in an
     * object context. It checks if the method name starts with 'create'
     * and invokes the corresponding protected method if it exists.
     *
     * @param string $name      The name of the method being called.
     * @param array  $arguments An array of arguments to pass to the method.
     *
     * @return mixed The result of the called method.
     * 
     * @throws Exception If the method does not exist or does not start with 'create'.
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
    public function get()
    {
        return $this->product;
    }
}
