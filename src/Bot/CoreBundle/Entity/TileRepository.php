<?php
namespace Bot\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TileRepository extends EntityRepository
{
    public function findActiveTile()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT t FROM BotCoreBundle:Tile t WHERE t.isActive = 1'
            )
            ->getSingleResult();
    }

    public function unsetActiveTile()
    {
        $this->getEntityManager()
            ->createQuery(
                'UPDATE BotCoreBundle:Tile t SET t.isActive = 0 WHERE t.isActive = 1'
            )
            ->execute()
        ;
        $this->getEntityManager()->flush();
    }

}