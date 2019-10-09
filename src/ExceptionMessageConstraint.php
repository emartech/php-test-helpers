<?php

namespace Emartech\TestHelper;

use PHPUnit\Framework\Constraint\Constraint;
use Throwable;

class ExceptionMessageConstraint extends Constraint
{
    /**
     * @var Constraint
     */
    private $messageConstraint;

    /**
     * @var bool
     */
    private $omitTrace;


    public function __construct(Constraint $messageConstraint, bool $omitTrace = false)
    {
        $this->messageConstraint = $messageConstraint;
        $this->omitTrace = $omitTrace;
    }

    protected function matches($other): bool
    {
        return $other instanceof Throwable
            && $this->messageConstraint->evaluate($other->getMessage(), '', true);
    }

    public function toString(): string
    {
        return "\n\tis a Throwable that has the message that {$this->messageConstraint->toString()}";
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
            $export = "a Throwable({$class}) with message {$message}";
            if (!$this->omitTrace) {
                $export .= " and trace:\n" . $other->getTraceAsString();
            }
        } else {
            $export = $this->exporter()->shortenedExport($other);
        }
        return $export . $this->toString();
    }
}
