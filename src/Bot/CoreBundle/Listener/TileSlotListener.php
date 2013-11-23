<?php
namespace Bot\CoreBundle\Listener;
use Bot\CoreBundle\TileSlotEvents;
use Bot\CoreBundle\OptimizerEvents;
use Bot\CoreBundle\Event\FilterTileSlotEvent;


class TileSlotListener
{
    protected $container;
    protected $em;

    public function __construct($container, $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function onUpdate(FilterTileSlotEvent $event)
    {
        try {
            $tileSlot = $event->getTileSlot();
            $data = $event->getSlotData();

            if(isset($data['deckCards'])){
                $tileSlot->setDeckCards(json_encode($data['deckCards']));
                $tileSlot->setCardsCount(count($data['deckCards']['cards']));

            }

            if(isset($data['deckHash'])){
                $tileSlot->setDeckHash($data['deckHash']);
            }

            if(isset($data['health'])){
                $tileSlot->setHealth($data['health']);

                if($data['health'] == 0){
                    $event->getDispatcher()->dispatch(TileSlotEvents::TILE_SLOT_DEFEAT, $event);
                }
            }
            $this->em->flush();
        }catch(\Exception $e){

        }
    }

    public function onDefeat(FilterTileSlotEvent $event)
    {
        try {
            $tileSlot = $event->getTileSlot();
            $data = $event->getSlotData();
            $tileSlot->setIsActive(0);
            $this->em->flush();
            $event->getDispatcher()->dispatch(OptimizerEvents::OPTIMIZER_TILE_SLOT_STOP_CALCULATE, $event);
        }catch(\Exception $e){

        }
    }
}
