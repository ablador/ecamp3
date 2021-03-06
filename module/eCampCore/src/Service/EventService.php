<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\EventHydrator;
use eCamp\Core\Entity\Event;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class EventService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , EventHydrator $eventHydrator
    ) {
        parent::__construct($acl, $entityManager, $eventHydrator, Event::class);
    }

}
