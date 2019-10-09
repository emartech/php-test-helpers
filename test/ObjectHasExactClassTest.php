<?php

namespace Test\Emartech\TestHelper;

use Emartech\TestHelper\BaseTestCase;
use Emartech\TestHelper\ObjectHasExactClass;

class TheClass
{
}

class AnotherClass
{
}

class ASubClass extends TheClass
{
}

class ObjectHasExactClassTest extends BaseTestCase
{
    /**
     * @test
     */
    public function matches_ObjectIsInstanceOfUnrelatedClass_EvaluationFails()
    {
        $this->assertAssertionFailsIn(
            $this->exceptionHasMessage($this->logicalAnd(
                $this->stringContains("is strictly an instance (and not of a descendant class) of"),
                $this->stringContains(TheClass::class, false)
            ), true),
            function () {
                (new ObjectHasExactClass(TheClass::class))->evaluate(new AnotherClass());
            }
        );
    }

    /**
     * @test
     */
    public function matches_ObjectIsInstanceOfSubClass_EvaluationFails()
    {
        $this->assertAssertionFailsIn(
            $this->exceptionHasMessage($this->logicalAnd(
                $this->stringContains("is strictly an instance (and not of a descendant class) of"),
                $this->stringContains(TheClass::class, false)
            ), true),
            function () {
                (new ObjectHasExactClass(TheClass::class))->evaluate(new ASubClass());
            }
        );
    }

    /**
     * @test
     */
    public function matches_KeyIsPresentAndValueMatchesValueConstraint_EvaluationSucceeds()
    {
        (new ObjectHasExactClass(TheClass::class))->evaluate(new TheClass());
    }
}
