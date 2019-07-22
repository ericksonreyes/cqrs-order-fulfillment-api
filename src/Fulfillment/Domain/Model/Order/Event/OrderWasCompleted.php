<?php

namespace Fulfillment\Domain\Model\Order\Event;

/**
 * Class OrderWasCompleted
 * @package Fulfillment\Domain\Model\Order\Event
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OrderWasCompleted extends EmptyEvent
{

    /**
     * @return string
     */
    public static function staticEventName(): string
    {
        return 'OrderWasCompleted';
    }
}
