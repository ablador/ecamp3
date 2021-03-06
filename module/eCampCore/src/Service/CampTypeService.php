<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\CampTypeHydrator;
use eCamp\Core\Entity\CampType;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class CampTypeService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , CampTypeHydrator $campTypeHydrator
    ) {
        parent::__construct($acl, $entityManager, $campTypeHydrator, CampType::class);
    }
}
