<?php

namespace App\Services\EventSubscribers\ProjectionGenerators;

use App\Services\EventSubscribers\Projector;
use EricksonReyes\DomainDrivenDesign\Domain\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AccountProjectionGenerator implements Projector
{
    /**
     * @return string
     */
    public function name(): string
    {
        return 'ContactFolderProjectionGenerator';
    }

    /**
     * @param Event $domainEvent
     * @return bool
     */
    public function project(Event $domainEvent): bool
    {
        /**
         * @var $container ContainerInterface
         */
        $container = app()->get(ContainerInterface::class);
        $wasProjected = false;

//        if ($domainEvent instanceof AccountWasCreated) {
//            $contactFolderModel = $container->get('account_model')::create();
//            $contactFolderModel->belongs_to = $domainEvent->belongsTo();
//            $contactFolderModel->name = $domainEvent->name();
//            $contactFolderModel->folder_id = $domainEvent->entityId();
//            $wasProjected = $contactFolderModel->save();
//        }
        return $wasProjected;
    }
}
