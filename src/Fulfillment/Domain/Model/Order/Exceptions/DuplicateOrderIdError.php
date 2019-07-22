<?php


namespace Fulfillment\Domain\Model\Order\Exceptions;

use EricksonReyes\DomainDrivenDesign\Common\Exception\RecordConflictException;

final class DuplicateOrderIdError extends RecordConflictException
{

}
