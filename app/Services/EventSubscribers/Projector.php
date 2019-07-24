<?php

namespace App\Services\EventSubscribers;

use EricksonReyes\DomainDrivenDesign\Domain\Event;

/**
 * Interface Projector
 * @package Projectors
 */
interface Projector
{

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @param Event $event
     * @return bool
     */
    public function project(Event $event): bool;
}
