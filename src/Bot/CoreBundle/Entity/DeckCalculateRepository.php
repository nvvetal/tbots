<?php
namespace Bot\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Bot\CoreBundle\Entity\DeckCalculate;
class DeckCalculateRepository extends EntityRepository
{

    public function stopCalculateTileSlotDeck($tileSlot)
    {
        $this->getEntityManager()
            ->createQuery('
                UPDATE BotCoreBundle:DeckCalculate dc
                SET dc.state = :state
                WHERE dc.tileSlot = :tileSlot AND dc.state = :curState

            ')
            ->setParameter(':state', DeckCalculate::STATE_DEFEAT)
            ->setParameter(':tileSlot', $tileSlot)
            ->setParameter(':curState', DeckCalculate::STATE_NEW)
        ->execute()
        ;
        $this->getEntityManager()->flush();
    }

}