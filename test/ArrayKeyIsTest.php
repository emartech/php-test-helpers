<?php

namespace Test\Emartech\TestHelper;

use Emartech\TestHelper\ArrayKeyIs;
use Emartech\TestHelper\BaseTestCase;

class ArrayKeyIsTest extends BaseTestCase
{
    /**
     * @test
     */
    public function matches_KeyMissing_EvaluationFails()
    {
        $this->assertAssertionFailsIn(
            $this->exceptionHasMessage($this->stringContains("is an array that has the key 'key'"), true),
            function () {
                (new ArrayKeyIs('key', $this->anything()))->evaluate([]);
            }
        );
    }

    /**
     * @test
     */
    public function matches_KeyIsPresentButValueDoesNotMatchValueConstraint_EvaluationFails()
    {
        $this->assertAssertionFailsIn(
            $this->exceptionHasMessage($this->logicalAnd(
                $this->stringContains("and the corresponding value is equal to"),
                $this->stringContains('the_value')
            ), true),
            function () {
                (new ArrayKeyIs('key', $this->equalTo('the_value')))->evaluate(['key' => 'different value']);
            }
        );
    }

    /**
     * @test
     */
    public function matches_KeyIsPresentAndValueMatchesValueConstraint_EvaluationSucceeds()
    {
        (new ArrayKeyIs('key', $this->equalTo('the_value')))->evaluate(['key' => 'the_value']);
    }

    /**
     * @test
     */
    public function matches_ValueConstraintNotAConstraintObject_ValueConstraintAutoBoxed()
    {
        (new ArrayKeyIs('key', 'the_value'))->evaluate(['key' => 'the_value']);
    }
}
