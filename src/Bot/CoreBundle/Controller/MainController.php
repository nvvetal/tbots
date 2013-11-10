<?php

namespace Bot\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Bot\CoreBundle\Entity\Bot;
use Bot\CoreBundle\Classes\Scout;
use Bot\CoreBundle\Entity\Tile;

class MainController extends Controller
{
    public function indexAction()
    {
        $tileHelper = $this->container->get('helper.tile');
        $tileActive = $tileHelper->getActiveTile();

        $botHelper = $this->container->get('helper.bot');
        $bot = new Bot();
        $bot->setUserId($this->container->getParameter('test_bot_id'));
        $bot->setFlashCode($this->container->getParameter('test_bot_hash'));
        $bClient = $botHelper->getBotClient($bot);
        $mapRes = $bClient->getConquestMap();

        if($mapRes['result'] == false){
            echo "cannot get map";
            exit;
        }

        $tileId = NULL;
        $attackStartTime = NULL;
        foreach ($mapRes['conquest_map']['map'] as $tileData)
        {
            if($tileData['attacking_faction_id'] != $bClient->getFactionId()) continue;
            $tileId = $tileData['system_id'];
            $attackStartTime = $tileData['attack_start_time'];
            break;
        }

        $scout = new Scout($tileId, $bClient);
        if(!is_null($tileId) && (is_null($tileActive) || $tileActive->getAttackStartTime() != $attackStartTime)){
            $tileActive = $tileHelper->createActiveTileFromConquestMap($tileId, $mapRes['conquest_map']['map']);
        }
        $tileInfo = $scout->getTileInfo();
        foreach ($tileInfo as $tileSlotData){
            $slotId = $tileSlotData['systemSlotId'];
            $scoutSlotData = $scout->scoutTileSlot($slotId);
            $tileHelper->fillTileSlot($tileActive, $slotId, $scoutSlotData);
            sleep(10);
        }
        return array('name' => 'test');
    }
}
