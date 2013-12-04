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

        $botHelper  = $this->container->get('helper.bot');
        $scoutBot   = $botHelper->getNextScout();
        if(is_null($scoutBot)){
            $this->container->get('logger')->write('[type scout bot][error cannot get scout bot]', 'error_scout');
            echo "cannot get scout bot";
            exit;
        }
        $mapRes = $scoutBot['client']->getConquestMap();
        if($mapRes['result'] == false){
            $this->container->get('logger')->write('[type map][error cannot get map]', 'error_scout');
            echo "cannot get map";
            exit;
        }

        $tileId = NULL;
        $attackStartTime = NULL;
        foreach ($mapRes['conquest_map']['map'] as $tileData)
        {
            if($tileData['attacking_faction_id'] != $scoutBot['client']->getFactionId()) continue;
            $tileId = $tileData['system_id'];
            $attackStartTime = $tileData['attack_start_time'];
            break;
        }

        $scout = new Scout($tileId, $scoutBot['client']);
        if(!is_null($tileId) && (is_null($tileActive) || $tileActive->getAttackStartTime() != $attackStartTime)){
            $tileActive = $tileHelper->createActiveTileFromConquestMap($tileId, $mapRes['conquest_map']['map']);
        }
        $tileInfo = $scout->getTileInfo();
        if(is_null($tileInfo)){
            $this->container->get('logger')->write('[type map][error error or tile is not in attack]', 'error_scout');
            echo "error or tile is not in attack";
            //TODO: check if we are owning any tile, if no - attack x 2  y -14; if yes - attack by plan near
            exit;
        }
        foreach ($tileInfo as $tileSlotData){
            if(
                (isset($tileSlotData['defeated']) && $tileSlotData['defeated'] == 1) ||
                (isset($tileSlotData['health']) && $tileSlotData['health'] == 0)
            ) continue;
            $slotId = $tileSlotData['systemSlotId'];
            //get bot with stamina
            while(true){
                if($scoutBot['client']->getStamina() >= 20) {
                    $scout = new Scout($tileId, $scoutBot['client']);
                    break;
                }
                $scoutBot   = $botHelper->getNextScout($scoutBot['id']);
                if(is_null($scoutBot)){
                    $this->container->get('logger')->write('[type scout bot][error cannot get scout bot]', 'error_scout');
                    echo "cannot get scout bot";
                    exit;
                }
            }

            $res = $scout->scoutTileSlot($slotId);
            if($res['ok'] === false) {
                $this->container->get('logger')->write('[type slot][slot '.$slotId.'][error '.$res['error'].']', 'error_scout');
                continue;
            }
            $tileHelper->fillTileSlot($tileActive, $slotId, $res['slotData']);
            sleep(10);
        }
        echo "DONE";
        exit;
        return array('name' => 'test');
    }
}
