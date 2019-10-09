<?php

namespace Emartech\TestHelper;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;

class ArrayKeyIs extends Constraint
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var Constraint
     */
    private $valueConstraint;

    public function __construct($key, $valueConstraint)
    {
        $this->key = $key;
        $this->valueConstraint = $valueConstraint instanceof Constraint
            ? $valueConstraint
            : new IsEqual($valueConstraint);
    }

    /**
    * @param mixed $other Value or object to evaluate.
     * @return bool
     */
    protected function matches($other): bool
    {
        return isset($other[$this->key]) && $this->valueConstraint->evaluate($other[$this->key], '', true);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString(): string
    {
        return "\n\tis an array that has the key '$this->key' and the corresponding value {$this->valueConstraint->toString()}";
    }
}
