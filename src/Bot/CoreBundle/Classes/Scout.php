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
            $ret = $this->bot->getConquestTileInfo($this->tailId);
            $this->tileInfo = $ret['system'];
        }
        if(is_null($ret)) return NULL;

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

        $canAttack = $this->bot->canAttackTile($this->tailId, $slotId);
        if(!$canAttack['ok']) return $canAttack;
        $ret = $this->bot->attackConquestTile($this->tailId, $slotId);
        if($ret['ok'] == false) return $ret;
        $this->updateScoutTileSlot($slotId, $ret);
        return array('ok' => true, 'slotData' => $this->getSlotDeck($slotId));
    }

    public function updateScoutTileSlot($slotId, $data)
    {
        $this->info[$slotId] = $data;
    }

}