<?php

namespace Emartech\TestHelper;


use PHPUnit\Framework\Constraint\Constraint;
use Throwable;

abstract class ExceptionConstraint extends Constraint
{
    /**
     * @var Constraint
     */
    private $delegateConstraint;

    /**
     * @var bool
     */
    private $omitTrace;


    public function __construct(Constraint $messageConstraint, bool $omitTrace = false)
    {
        $this->delegateConstraint = $messageConstraint;
        $this->omitTrace = $omitTrace;
    }

    protected abstract function getRelevantPart(Throwable $other);

    protected abstract function getRelevantPartName(): string;

    protected function matches($other): bool
    {
        return $other instanceof Throwable
            && $this->delegateConstraint->evaluate($this->getRelevantPart($other), '', true);
    }

    public function toString(): string
    {
        return "\n\tis a Throwable that has the {$this->getRelevantPartName()} that {$this->delegateConstraint->toString()}";
    }

    /**
     * @param mixed $other Evaluated value or object.
     * @return string
     */
    protected function failureDescription($other): string
    {
        if ($other instanceof Throwable) {
            $class = get_class($other);
            $message = $this->exporter()->export($other->getMessage());
            $export = "a Throwable({$class}) with message {$message} and code {$other->getCode()}";
            if (!$this->omitTrace) {
                $export .= " and trace:\n" . $other->getTraceAsString();
            }
        } else {
            $export = $this->exporter()->shortenedExport($other);
        }
        return $export . $this->toString();
    }

}