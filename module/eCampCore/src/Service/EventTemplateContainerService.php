<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\EventTemplateContainerHydrator;
use eCamp\Core\Entity\EventTemplateContainer;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class EventTemplateContainerService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , EventTemplateContainerHydrator $eventTemplateContainerHydrator
    ) {
        parent::__construct($acl, $entityManager, $eventTemplateContainerHydrator, EventTemplateContainer::class);
    }
}
