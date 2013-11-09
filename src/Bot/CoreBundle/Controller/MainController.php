<?php

namespace Bot\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Bot\CoreBundle\Entity\Bot;
use Bot\CoreBundle\Classes\Scout;

class MainController extends Controller
{
    public function indexAction()
    {

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
        foreach ($mapRes['conquest_map']['map'] as $tileData)
        {
            //TODO get faction id from scout
            if($tileData['attacking_faction_id'] == '9902001')
            {
                $tileId = $tileData['system_id'];
                break;
            }
        }

        $scout = new Scout($tileId, $bClient);
        $tileInfo = $scout->getTileInfo();
        $slotId = $tileInfo[1]['systemSlotId'];
        echo "<pre>";

        var_dump($scout->scoutTileSlot($slotId));
        exit;
        return array('name' => 'test');
    }
}
