<?php
namespace Bot\CoreBundle\Helper;

use Bot\CoreBundle\Entity\Tile;
use Bot\CoreBundle\Entity\TileSlot;
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
        $tile->setEffect($tileData['effect']);
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

    public function fillTileSlot($tile, $slotId, $slotData)
    {
        $tileSlot = NULL;
        try {
            $tileSlot = $this->em->getRepository('BotCoreBundle:TileSlot')
                ->findActiveTileSlotByTileAndSlotId($tile, $slotId);
            $craftedDeckCards = $this->getDeckCards($slotData);
            $currentDeckCards = json_decode($tileSlot->getDeckCards(), true);
            if(!$this->isCurrentDeckCardsValid($currentDeckCards, $craftedDeckCards)){
                $this->em->getRepository('BotCoreBundle:TileSlot')->unsetActiveTileSlotByTileAndSlotId($tile, $slotId);
                $tileSlot = $this->createTileSlot($tile, $slotId, $slotData);
            }else{
                $mergedDeckCards = $this->mergeDeckCards($currentDeckCards, $craftedDeckCards);
                $enemyFullDeck = array_unhift($mergedDeckCards['cards'], $mergedDeckCards['commander']);
                $enemyHash = $this->container->get('helper.deck')->getDeckHashFromCards($enemyFullDeck, false);
                $tileSlot->setDeckCards(json_encode($mergedDeckCards));
                $tileSlot->setDeckHash($enemyHash);
                //TODO: throw event that deck changed
            }
        }catch(NoResultException $e) {
            $tileSlot = $this->createTileSlot($tile, $slotId, $slotData);
        }catch(\Exception $e){

        }
        return $tileSlot;
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
        foreach ($craftedDeckCardsCounted as $cardId => $cardCount)
        {
            if(!isset($currentDeckCardsCounted[$cardId])) {
                for ($i = 0; $i < $cardCount; $i++){
                    $mergedDeckCards[] = $cardCount;
                }
            }else{
                $maxCards = $cardCount > $currentDeckCardsCounted[$cardId] ? $cardCount : $currentDeckCardsCounted[$cardId];
                for ($i = 0; $i < $maxCards; $i++){
                    $mergedDeckCards[] = $cardCount;
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
        $tileSlot->setDeckHash($slotData['enemyDeckHash']);
        $tileSlot->setScoutStatus(TileSlot::SCOUT_STATUS_FINISHED);
        $tileSlot->setCardsCount(count($slotData['enemyDeck']));
        $tileSlot->setDeckCards(json_encode($deckCards));
        $tileSlot->setTile($tile);
        $tileSlot->setSlotId($slotId);
        $this->em->persist($tileSlot);
        $this->em->flush();
        return $tileSlot;
    }

    public function getDeckCards($slotData)
    {
        $deckCards = array('commander' => $slotData['enemyCommanderId'], 'cards' => $slotData['enemyDeck']);
        return $deckCards;
    }
}
