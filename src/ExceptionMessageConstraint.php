<?php

namespace Emartech\TestHelper;

use Throwable;

class ExceptionMessageConstraint extends ExceptionConstraint
{
    protected function getRelevantPart(Throwable $other)
    {
        return $other->getMessage();
    }

    protected function getRelevantPartName(): string
    {
        return 'message';
    }
}
