<?php

namespace Emartech\TestHelper;

use PHPUnit_Framework_Constraint;
use PHPUnit_Framework_Constraint_IsEqual;

class ArrayKeyIs extends PHPUnit_Framework_Constraint
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var PHPUnit_Framework_Constraint
     */
    private $valueConstraint;

    public function __construct($key, $valueConstraint)
    {
        parent::__construct();
        $this->key = $key;
        $this->valueConstraint = $valueConstraint instanceof PHPUnit_Framework_Constraint
            ? $valueConstraint
            : new PHPUnit_Framework_Constraint_IsEqual($valueConstraint);
    }

    /**
    * @param mixed $other Value or object to evaluate.
     * @return bool
     */
    protected function matches($other)
    {
        return isset($other[$this->key]) && $this->valueConstraint->evaluate($other[$this->key], '', true);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "\n\tis an array that has the key '$this->key' and the corresponding value {$this->valueConstraint->toString()}";
    }
}
