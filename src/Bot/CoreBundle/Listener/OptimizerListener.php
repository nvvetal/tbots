<?php
namespace Bot\CoreBundle\Listener;
use Bot\CoreBundle\OptimizerEvents;

class OptimizerListener
{
    protected $container;
    protected $em;

    public function __construct($container, $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function onStopTileSlotCalculate(FilterTileSlotEvent $event)
    {
        try {
            $tileSlot = $event->getTileSlot();
            $data = $event->getSlotData();
            $this->em->getRepository('BotCoreBundle:DeckCalculate')->stopCalculateTileSlotDeck($tileSlot);
            $this->em->flush();
        }catch(\Exception $e){

        }
    }

}
