<?php
namespace Bot\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TileSlotRepository extends EntityRepository
{
    public function findActiveTileSlotByTileAndSlotId($tile, $slotId)
    {
        return $this->getEntityManager()
            ->createQuery("
                SELECT ts
                FROM BotCoreBundle:TileSlot ts
                WHERE ts.tile = :tile AND ts.slotId = :slotId AND ts.isActive = 1
            ")
            ->setParameter(':tile', $tile)
            ->setParameter(':slotId', $slotId)
            ->getSingleResult();
    }

    public function unsetActiveTileSlotByTileAndSlotId($tile, $slotId)
    {
        $this->getEntityManager()
            ->createQuery("
                UPDATE BotCoreBundle:TileSlot ts
                SET ts.isActive = 0
                WHERE ts.tile = :tile AND ts.slotId = :slotId AND ts.isActive = 1
            ")
            ->setParameter(':tile', $tile)
            ->setParameter(':slotId', $slotId)
            ->execute()
        ;
        $this->getEntityManager()->flush();
    }

}