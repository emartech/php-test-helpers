<?php

namespace Emartech\TestHelper;

use Exception;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestListener;
use PHPUnit_Framework_TestSuite;

class TestResultPrinter implements PHPUnit_Framework_TestListener
{
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->getLogFileContents($test);
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->getLogFileContents($test);
    }

    private function getLogFileContents(PHPUnit_Framework_Test $test)
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

    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    public function startTest(PHPUnit_Framework_Test $test)
    {
        exec('rm -rf ./log/error/');
        exec('mkdir ./log/error/');
    }

    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
    }
}
