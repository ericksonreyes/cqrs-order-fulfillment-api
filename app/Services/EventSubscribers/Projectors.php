<?php

namespace App\Services\EventSubscribers;

/**
 * Class Projectors
 * @package App\Projectors
 */
class Projectors
{
    /**
     * @var Projector[]
     */
    private $projectors = [];

    /**
     * @param Projector $projector
     */
    public function addProjector(Projector $projector): void
    {
        $this->projectors[] = $projector;
    }

    /**
     * @return Projector[]
     */
    public function projectors(): array
    {
        return $this->projectors;
    }
}
