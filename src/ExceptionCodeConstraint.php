<?php

namespace Emartech\TestHelper;

use Throwable;

class ExceptionCodeConstraint extends ExceptionConstraint
{
    protected function getRelevantPart(Throwable $other)
    {
        return $other->getCode();
    }

    protected function getRelevantPartName(): string
    {
        return 'code';
    }
}
