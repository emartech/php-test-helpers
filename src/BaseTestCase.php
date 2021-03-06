<?php

namespace Emartech\TestHelper;

use Exception;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\Constraint;

abstract class BaseTestCase extends TestCase
{
    protected $dummyLogger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dummyLogger = new Logger('dummy', array(new NullHandler()));
    }

    /**
     * @param $originalClassName
     * @return MockObject
     */
    protected function mock($originalClassName)
    {
        return $this->partialMock($originalClassName, []);
    }

    /**
     * @param $originalClassName
     * @param array $methods
     * @return MockObject
     */
    protected function partialMock($originalClassName, array $methods): MockObject
    {
        return $this->getMockBuilder($originalClassName)
            ->onlyMethods($methods)
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

    protected function assertBaseExceptionThrown($callback)
    {
        $this->assertExceptionThrown(Exception::class, $callback);
    }

    /**
     * This is a method that can be used to check if a given code of block throws an exception and whether the thrown
     * exception is the expected one
     * You cannot use this method to check for instances of PHPUnit_Framework_AssertionFailedError; these exceptions
     * will be rethrown unconditionally, as in most cases they will contain valid information about an error that
     * occurred in the system under test.
     *
     * @param Constraint|string $exceptionConstraint
     * @param callable $callback
     */
    protected function assertExceptionThrown($exceptionConstraint, callable $callback)
    {
        $exceptionConstraint = $this->boxExceptionConstraint($exceptionConstraint);
        try {
            call_user_func($callback);
        } catch (AssertionFailedError $ex) {
            throw $ex;
        } catch (Exception $ex) {
            $this->assertThat($ex, $exceptionConstraint, 'Not the expected exception: ' . $ex);
            return;
        }
        $this->fail('An exception that ' . $exceptionConstraint->toString() . ' was expected');
    }

    protected function assertAssertionFailsIn($exceptionConstraint, callable $callback)
    {
        $exceptionConstraint = $this->boxExceptionConstraint($exceptionConstraint);
        try {
            call_user_func($callback);
        } catch (AssertionFailedError $ex) {
            $this->assertThat($ex, $exceptionConstraint, 'The assertion failed not in the expected way.');
            return;
        }
        $this->fail('The assertion should have failed, but did not.');
    }

    public function exceptionHasMessage(Constraint $messageConstraint, bool $omitTrace = false): Constraint
    {
        return new ExceptionMessageConstraint($messageConstraint, $omitTrace);
    }

    public function exceptionHasCode(Constraint $messageConstraint, bool $omitTrace = false): Constraint
    {
        return new ExceptionCodeConstraint($messageConstraint, $omitTrace);
    }

    private function boxExceptionConstraint($exceptionConstraint)
    {
        return $exceptionConstraint instanceof Constraint
            ? $exceptionConstraint
            : $this->isInstanceOf($exceptionConstraint);
    }

    protected function assertExceptionWithStrictClassThrown($exceptionClass, $callback)
    {
        $this->assertExceptionThrown($this->strictInstanceOf($exceptionClass), $callback);
    }

    public function strictInstanceOf($className)
    {
        return new ObjectHasExactClass($className);
    }
}
