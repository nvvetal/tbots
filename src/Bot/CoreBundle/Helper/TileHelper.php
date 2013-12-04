<?php
namespace Bot\CoreBundle\Helper;

use Bot\CoreBundle\Entity\Tile;
use Bot\CoreBundle\Entity\TileSlot;
use Bot\CoreBundle\OptimizerEvents;
use Bot\CoreBundle\TileSlotEvents;
use Bot\CoreBundle\Event\FilterTileSlotEvent;
use Doctrine\ORM\NoResultException;


class TileHelper
{
    protected $container;
    protected $em;
    protected $repository;

    public function __construct($container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->repository = $this->em->getRepository('BotCoreBundle:Tile');
    }

    public function getActiveTile()
    {
        $tile = NULL;
        try {
            $tile = $this->repository->findActiveTile();
        }catch(\Exception $e){

        }
        return $tile;
    }

    public function setActiveTile(Tile $tile)
    {
        $result = false;
        try {
            $this->repository->unsetActiveTile();
            $tile->setIsActive(1);
            $this->em->flush();
            $result = true;
        }catch(\Exception $e){

        }
        return $result;
    }

    public function getMapTileData($tileId, $conquestMap)
    {
        $data = null;
        foreach ($conquestMap as $tileData)
        {
            if($tileId != $tileData['system_id']) continue;
            $data = $tileData;
            break;
        }
        return $data;
    }

    public function createTileFromConquestMap($tileId, $conquestMap)
    {
        $tileData = $this->getMapTileData($tileId, $conquestMap);
        $tile = new Tile();
        $tile->setFaction($tileData['faction_id']);
        $tile->setCredits($tileData['terrain']); //todo
        $tile->setSystemId($tileData['system_id']);
        $tile->setX($tileData['x']);
        $tile->setY($tileData['y']);
        if(!empty($tileData['effect'])) $tile->setEffect($tileData['effect']);
        $tile->setAttackStartTime($tileData['attack_start_time']);
        $this->em->persist($tile);
        $this->em->flush();
        return $tile;
    }

    public function createActiveTileFromConquestMap($tileId, $conquestMap)
    {
        $tile = $this->createTileFromConquestMap($tileId, $conquestMap);
        $this->setActiveTile($tile);
        return $tile;
    }

    public function fillTileSlot(Tile $tile, $slotId, $slotData)
    {
        if(is_null($tile->getFaction())) return $this->fillTileSlotWithoutOwner($tile, $slotId, $slotData);
        $tileSlot = NULL;
        $dispatcher = $this->container->get('event_dispatcher');
        $craftedDeckCards = $this->getDeckCards($slotData);
        try {
            $tileSlot = $this->em->getRepository('BotCoreBundle:TileSlot')
                ->findActiveTileSlotByTileAndSlotId($tile, $slotId);
            $currentDeckCards = json_decode($tileSlot->getDeckCards(), true);
            if(!$this->isCurrentDeckCardsValid($currentDeckCards, $craftedDeckCards)){
                $this->em->getRepository('BotCoreBundle:TileSlot')->unsetActiveTileSlotByTileAndSlotId($tile, $slotId);
                $tileSlot = $this->createTileSlot($tile, $slotId, $slotData);
                $enemyHash = $this->container->get('helper.deck')->getDeckHashFromCards($craftedDeckCards['cards'], false);
                $eventData = array(
                    'deckCards'         => $craftedDeckCards,
                    'deckHash'          => $enemyHash,
                    'health'            => $slotData['health'],
                    'isCardsDifferent'  => true,
                );
                $event = new FilterTileSlotEvent($tileSlot, $eventData);
                $dispatcher->dispatch(OptimizerEvents::OPTIMIZER_TILE_SLOT_START_CALCULATE, $event);
            }else{
                $mergedDeckCards = $this->mergeDeckCards($currentDeckCards, $craftedDeckCards);
                $enemyFullDeck = $mergedDeckCards['cards'];
                array_unshift($enemyFullDeck, $mergedDeckCards['commander']);
                $enemyHash = $this->container->get('helper.deck')->getDeckHashFromCards($enemyFullDeck, false);
                $this->em->flush();
                $eventData = array(
                    'deckCards'         => $mergedDeckCards,
                    'deckHash'          => $enemyHash,
                    'health'            => $slotData['health'],
                    'isCardsDifferent'  => $this->isCardsDifferent($currentDeckCards['cards'], $mergedDeckCards['cards']),
                );
                $event = new FilterTileSlotEvent($tileSlot, $eventData);
                $dispatcher->dispatch(TileSlotEvents::TILE_SLOT_UPDATE, $event);
            }
        }catch(NoResultException $e) {
            $tileSlot = $this->createTileSlot($tile, $slotId, $slotData);
            $enemyHash = $this->container->get('helper.deck')->getDeckHashFromCards($craftedDeckCards['cards'], false);
            $eventData = array(
                'deckCards'         => $craftedDeckCards,
                'deckHash'          => $enemyHash,
                'health'            => $slotData['health'],
                'isCardsDifferent'  => false,
            );
            $event = new FilterTileSlotEvent($tileSlot, $eventData);
            $dispatcher->dispatch(OptimizerEvents::OPTIMIZER_TILE_SLOT_START_CALCULATE, $event);
        }catch(\Exception $e){
            echo $e->getMessage();
            exit;
        }
        return $tileSlot;
    }

    private function fillTileSlotWithoutOwner(Tile $tile, $slotId, $slotData)
    {
        $tileSlot = NULL;
        try {
            $tileSlot = $this->em->getRepository('BotCoreBundle:TileSlot')
                ->findActiveTileSlotByTileAndSlotId($tile, $slotId);
            $craftedDeckCards = $this->getDeckCards($slotData);
            $currentDeckCards = json_decode($tileSlot->getDeckCards(), true);
            $mergedDeckCards = $this->mergeDeckCards($currentDeckCards, $craftedDeckCards);
            $enemyFullDeck = $mergedDeckCards['cards'];
            array_unshift($enemyFullDeck, $mergedDeckCards['commander']);
            $enemyHash = $this->container->get('helper.deck')->getDeckHashFromCards($enemyFullDeck, false);
            $dispatcher = $this->container->get('event_dispatcher');
            $eventData = array(
                'deckCards'         => $mergedDeckCards,
                'deckHash'          => $enemyHash,
                'health'            => $slotData['health'],
                'isCardsDifferent'  => $this->isCardsDifferent($currentDeckCards['cards'], $mergedDeckCards['cards']),
            );
            $event = new FilterTileSlotEvent($tileSlot, $eventData);
            $dispatcher->dispatch(TileSlotEvents::TILE_SLOT_UPDATE, $event);
        }catch(NoResultException $e) {
            $tileSlot = $this->createTileSlot($tile, $slotId, $slotData);
            $enemyHash = $this->container->get('helper.deck')->getDeckHashFromCards($craftedDeckCards['cards'], false);
            $eventData = array(
                'deckCards'         => $craftedDeckCards,
                'deckHash'          => $enemyHash,
                'health'            => $slotData['health'],
                'isCardsDifferent'  => false,
            );
            $event = new FilterTileSlotEvent($tileSlot, $eventData);
            $dispatcher->dispatch(OptimizerEvents::OPTIMIZER_TILE_SLOT_START_CALCULATE, $event);
        }catch(\Exception $e){
            echo $e->getMessage();
            exit;
        }
        return $tileSlot;
    }

    private function isCardsDifferent($cards1, $cards2)
    {
        if(count($cards1) != count($cards2)) return true;
        $cards1 = $this->getDeckCardsCounted($cards1);
        ksort($cards1);
        $cards2 = $this->getDeckCardsCounted($cards2);
        ksort($cards2);
        if(count($cards1) != count($cards2)) return true;
        foreach($cards1 as $cardId => $cardsCount)
        {
            if(!isset($cards2[$cardId]) || $cardsCount != $cards2[$cardId]) return true;
        }
        return false;
    }

    private function isCurrentDeckCardsValid($currentDeckCards, $craftedDeckCards)
    {
        $isCommanderSame = $this->isCommanderSame($currentDeckCards, $craftedDeckCards['commander']);
        if(!$isCommanderSame) return array('ok' => false, 'error' => 'different commander');

        $currentDeckCardsCounted = $this->getDeckCardsCounted($currentDeckCards);
        $craftedDeckCardsCounted = $this->getDeckCardsCounted($craftedDeckCards);

        foreach ($craftedDeckCardsCounted as $cardId => $cardCount)
        {
            if(!isset($currentDeckCardsCounted[$cardId]) && (count($craftedDeckCards['cards']) == 10 || count($currentDeckCards['cards']) == 10)){
                return array('ok' => false, 'error' => 'new card matched - '.$cardId);
            }

            if(isset($currentDeckCardsCounted[$cardId]) && $cardCount > $currentDeckCardsCounted[$cardId] && count($currentDeckCards['cards']) == 10)
            {
                return array('ok' => false, 'error' => 'same cards increased - '.$cardId.', count - '.$cardCount);
            }
        }
        return array('ok' => true);
    }

    private function mergeDeckCards($currentDeckCards, $craftedDeckCards)
    {
        $currentDeckCardsCounted = $this->getDeckCardsCounted($currentDeckCards);
        $craftedDeckCardsCounted = $this->getDeckCardsCounted($craftedDeckCards);
        $mergedDeckCards = array();
        foreach ($currentDeckCardsCounted as $cardId => $cardCount){
            for ($i = 0; $i < $cardCount; $i++){
                $mergedDeckCards[] = $cardId;
            }
        }
        foreach ($craftedDeckCardsCounted as $cardId => $cardCount)
        {
            if(!isset($currentDeckCardsCounted[$cardId])) {
                for ($i = 0; $i < $cardCount; $i++){
                    $mergedDeckCards[] = $cardId;
                }
            }else{
                $maxCards = $cardCount > $currentDeckCardsCounted[$cardId] ? $cardCount - $currentDeckCardsCounted[$cardId] : 0;
                for ($i = 0; $i < $maxCards; $i++){
                    $mergedDeckCards[] = $cardId;
                }
            }
        }

        return array('commander' => $currentDeckCards['commander'], 'cards' => $mergedDeckCards);
    }

    private function getDeckCardsCounted($deckCards)
    {
        $cards = array();
        foreach($deckCards['cards'] as $card)
        {
            $cards[$card] = isset($cards[$card]) ? $cards[$card]+1 : 1;
        }
        return $cards;
    }

    public function isCommanderSame($currentDeckCards, $craftedCommanderId)
    {
        $isSame = $currentDeckCards['commander'] == $craftedCommanderId;
        return $isSame;
    }

    public function createTileSlot($tile, $slotId, $slotData){
        $deckCards = $this->getDeckCards($slotData);
        $tileSlot = new TileSlot();
        $tileSlot->setScoutStatus(TileSlot::SCOUT_STATUS_FINISHED);
        $tileSlot->setTile($tile);
        $tileSlot->setSlotId($slotId);
        $this->em->persist($tileSlot);
        $this->em->flush();
        $dispatcher = $this->container->get('event_dispatcher');
        $eventData = array(
            'deckCards' => $deckCards,
            'deckHash'  => $slotData['enemyDeckHash'],
            'health'    => $slotData['health'],
        );
        $event = new FilterTileSlotEvent($tileSlot, $eventData);
        $dispatcher->dispatch(TileSlotEvents::TILE_SLOT_UPDATE, $event);
        return $tileSlot;
    }

    public function getDeckCards($slotData)
    {
        $deckCards = array('commander' => $slotData['enemyCommanderId'], 'cards' => $slotData['enemyDeck']);
        return $deckCards;
    }
}
