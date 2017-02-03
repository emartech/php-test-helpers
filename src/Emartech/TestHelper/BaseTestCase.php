<?php

namespace Emartech\TestHelper;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit_Framework_MockObject_MockObject;

abstract class BaseTestCase extends TestCase
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
        return $this->getMockBuilder($originalClassName)
            ->setMethods($methods)
            ->setConstructorArgs([])
            ->setMockClassName('')
            ->disableOriginalConstructor()
            ->getMock();
    }


    /**
     * @param array $array
     * @return Constraint
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
