<?php
namespace Bot\CoreBundle\Listener;
use Bot\CoreBundle\OptimizerEvents;
use Bot\CoreBundle\Entity\DeckCalculate;
use Bot\CoreBundle\Event\FilterTileSlotEvent;

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
        $attackers = $this->em->getRepository('BotCoreBundle:Bot')->findAttackers();
        if(count($attackers) == 0) return false;

        $tileSlot = $event->getTileSlot();
        $data = $event->getSlotData();
        $enemyOptions = array();
        $tile = $tileSlot->getTile();
        if(!is_null($tile->getEffect())) $enemyOptions['effect'] = $tile->getEffect();
        $params = array(
            'tileSlot' => $tileSlot,
        );
        foreach($attackers as $attacker)
        {
            $this->optimizer->addDeckCalculate($attacker, DeckCalculate::ENEMY_DECK_TYPE_TILE_SLOT, $data['deckHash'], $enemyOptions, $params);
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
