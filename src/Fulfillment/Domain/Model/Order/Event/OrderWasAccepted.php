<?php

namespace Fulfillment\Domain\Model\Order\Event;

/**
 * Class OrderWasAccepted
 * @package Fulfillment\Domain\Model\Order\Event
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OrderWasAccepted extends EmptyEvent
{
    /**
     * @return string
     */
    public static function staticEventName(): string
    {
        return 'OrderWasAccepted';
    }
}
