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
            if($tileSlot->getDeckHash() != $slotData['enemyDeckHash']){
                $this->em->getRepository('BotCoreBundle:TileSlot')->unsetActiveTileSlotByTileAndSlotId($tile, $slotId);
                $tileSlot = $this->createTileSlot($tile, $slotId, $slotData);
            }
        }catch(NoResultException $e) {
            $tileSlot = $this->createTileSlot($tile, $slotId, $slotData);
        }catch(\Exception $e){

        }
        return $tileSlot;
    }

    public function createTileSlot($tile, $slotId, $slotData){
        $tileSlot = new TileSlot();
        $tileSlot->setDeckHash($slotData['enemyDeckHash']);
        $tileSlot->setScoutStatus(TileSlot::SCOUT_STATUS_FINISHED);
        $tileSlot->setCardsCount(count($slotData['enemyDeck']));
        $tileSlot->setTile($tile);
        $tileSlot->setSlotId($slotId);
        $this->em->persist($tileSlot);
        $this->em->flush();
        return $tileSlot;
    }
}
