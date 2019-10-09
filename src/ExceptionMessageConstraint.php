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


    public function __construct(Constraint $messageConstraint)
    {
        parent::__construct();
        $this->messageConstraint = $messageConstraint;
    }

    public function matches($other)
    {
        return $other instanceof Throwable
            && $this->messageConstraint->matches($other->getMessage());
    }

    public function toString()
    {
        return "\n\tis an exception that has the message that {$this->messageConstraint->toString()}";
    }

    /**
     * @param mixed $other Evaluated value or object.
     * @return string
     */
    protected function failureDescription($other)
    {
        if ($other instanceof Throwable) {
            $class = get_class($other);
            $message = $this->exporter->export($other->getMessage());
            $export = "a Throwable({$class}) with message {$message} and trace:\n" . $other->getTraceAsString();
        } else {
            $export = $this->exporter->shortenedExport($other);
        }
        return $export . $this->toString();
    }
}
