<?php
namespace Bot\CoreBundle\Classes;
use Bot\CoreBundle\Classes\User;

class Scout
{
    private $tailId;
    private $bot;
    private $info;
    private $tileInfo;


    public function __construct($tailId, User $bot)
    {
        $this->tailId = $tailId;
        $this->bot = $bot;
    }

    public function getSlotDeck($slotId)
    {
        if (!empty($this->info[$slotId])) {
            return $this->info[$slotId];
        }
        return false;
    }


    public function getTileInfo()
    {
        if (!$this->tileInfo) {
            if (empty($ret['result'])) {
                return false;
            }
            $ret = $this->bot->getConquestTileInfo($this->tailId);
            $this->tileInfo = $ret['system'];
        }

        $slots = array();

        foreach ($this->tileInfo['slots'] as $id => $slot) {
            $slots[] = array(
                'slotId'            => $id,
                'systemSlotId'      => $slot['system_slot_id'],
                'health'            => $slot['health'],
                'commanderId'       => $slot['commander_id'],
                'protectionEndTime' => $slot['protection_end_time'],
            );
        }
        return $slots;
    }

    public function scoutTileSlot($slotId)
    {
        if (!empty($this->info[$slotId])) {
            return $this->info[$slotId];
        }
        $ret = $this->bot->attackConquestTile($this->tailId, $slotId);
        $this->updateScoutTileSlot($slotId, $ret);
        return $this->getSlotDeck($slotId);
    }

    public function updateScoutTileSlot($slotId, $data)
    {
        $this->_scoutInfo[$slotId] = $data;
    }

    public function attackTile()
    {
        $slots = $this->getTileInfo();
        foreach ($slots as $slot) {
            while ($slot['health'] > 0) {
                // TODO update $slot
                $deck = $this->getSlotDeck($slot['systemSlotId']);
                if ($deck === false) {
                    $deck = $this->scoutTileSlot($slot['systemSlotId']);
                }
                var_dump($deck);
                //TODO:
            }
        }

    }
}