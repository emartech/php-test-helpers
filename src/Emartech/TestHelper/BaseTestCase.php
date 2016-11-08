<?php

namespace Emartech\TestHelper;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PHPUnit_Framework_Constraint;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

abstract class BaseTestCase extends PHPUnit_Framework_TestCase
{
    protected $dummyLogger;

    protected function setUp()
    {
        parent::setUp();
        $this->dummyLogger = new Logger('dummy', array(new NullHandler()));
    }

    /**
     * @param $originalClassName
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function mock($originalClassName)
    {
        return $this->partialMock($originalClassName, []);
    }


    /**
     * @param $originalClassName
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function partialMock($originalClassName, $methods)
    {
        return $this->getMock($originalClassName, $methods, [], '', false, false, true, false);
    }


    /**
     * @param array $array
     * @return PHPUnit_Framework_Constraint
     */
    protected function structure(array $array)
    {
        $result = $this->logicalAnd();
        $result->setConstraints(array_map(function ($key, $constraint) {
            return new ArrayKeyIs($key, $constraint);
        }, array_keys($array), array_values($array)));
        return $result;
    }
}
