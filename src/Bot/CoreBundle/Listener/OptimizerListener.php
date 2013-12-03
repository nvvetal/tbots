<?php
namespace Bot\CoreBundle\Listener;
use Bot\CoreBundle\OptimizerEvents;

class OptimizerListener
{
    protected $container;
    protected $em;
    protected $optimizer;

    public function __construct($container, $em, $optimizer)
    {
        $this->container = $container;
        $this->em = $em;
        $this->optimizer = $optimizer;
    }

    public function onStartTileSlotCalculate(FilterTileSlotEvent $event)
    {
        //todo: get all attackers and send to calculate
        $attackers = $this->em->getRepository('BotCoreBundle:Bot')->findAttackers();
        if(count($attackers) == 0) return false;
        foreach($attackers as $attacker)
        {

        }
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
