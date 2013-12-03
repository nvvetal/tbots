<?php
namespace Bot\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BotRepository extends EntityRepository
{
    public function findScouts()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT b FROM BotCoreBundle:Bot b WHERE b.isScout = 1'
            )
            ->getResult();
    }

    public function findAttackers()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT b FROM BotCoreBundle:Bot b WHERE b.isAttacker = 1'
            )
            ->getResult();
    }
}