<?php

namespace Emartech\TestHelper;

use Exception;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\Warning;
use PHPUnit\Framework\TestSuite;

class TestResultPrinter implements TestListener
{
    public function addError(Test $test, Exception $e, $time)
    {
        $this->getLogFileContents($test);
    }

    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->getLogFileContents($test);
    }

    private function getLogFileContents(TestCase $test)
    {
        echo "\nTest '{$test->getName()}' failed.\n";
        echo "\nLogs:\n";

        $logFiles = glob("log/error/*.log");

        foreach ($logFiles as $fileName) {
            echo "\nFile: $fileName\n";
            echo file_get_contents($fileName)."\n";
        }

        echo "\n";
    }

    public function addIncompleteTest(Test $test, Exception $e, $time)
    {
    }

    public function addRiskyTest(Test $test, Exception $e, $time)
    {
    }

    public function addSkippedTest(Test $test, Exception $e, $time)
    {
    }

    public function startTest(Test $test)
    {
        $logFiles = glob("log/error/*.log");

        foreach ($logFiles as $fileName) {
            file_put_contents($fileName, '');
        }
    }

    public function endTest(Test $test, $time)
    {
    }

    public function startTestSuite(TestSuite $suite)
    {
    }

    public function endTestSuite(TestSuite $suite)
    {
    }

    public function addWarning(Test $test, Warning $e, $time)
    {
    }
}
