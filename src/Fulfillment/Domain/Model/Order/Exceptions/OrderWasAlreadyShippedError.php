<?php


namespace Fulfillment\Domain\Model\Order\Exceptions;

use InvalidArgumentException;

final class OrderWasAlreadyShippedError extends InvalidArgumentException
{

}
