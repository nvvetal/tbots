<?php
namespace Bot\CoreBundle\Listener;
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
                $tileSlot->setCardsCount(count($data['deckCards']));

            }

            if(isset($data['deckHash'])){
                $tileSlot->setDeckHash($data['deckHash']);
            }

            if(isset($data['health'])){
                $tileSlot->setHealth($data['health']);
            }
            $this->em->flush();
        }catch(\Exception $e){

        }
    }
}
