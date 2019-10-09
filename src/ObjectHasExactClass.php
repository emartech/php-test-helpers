<?php

namespace Emartech\TestHelper;


use PHPUnit\Framework\Constraint\Constraint;

class ObjectHasExactClass extends Constraint
{
    /**
     * @var string
     */
    private $expectedClass;


    public function __construct($expectedClass)
    {
        $this->expectedClass = $expectedClass;
    }

    public function matches($other): bool
    {
        if (!is_object($other)) {
            return false;
        }
        return get_class($other) === $this->expectedClass;
    }

    public function toString(): string
    {
        return PHP_EOL . "\tis strictly an instance (and not of a descendant class) of '{$this->expectedClass}'";
    }
}
