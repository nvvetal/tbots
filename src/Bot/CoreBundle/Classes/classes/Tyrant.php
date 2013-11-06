<?php
class Tyrant
{
    protected $users = array();
    protected $_scoutId;
    protected $_systemId;
    protected $_tileInfo;
    protected $_scoutInfo = array();

    public function __construct($tailSystemId)
    {
        $this->_systemId = $tailSystemId;
    }

    public function addUser($userId, $flashCode)
    {
        $this->users[$userId] = new Tyrant_User($userId, $flashCode);
    }

    public function getUserIds()
    {
        return array_keys($this->users);
    }

    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param $userId
     * @return boolean|Tyrant_User
     */
    public function getUserById($userId)
    {
        if (!empty($this->users[$userId])) {
            return $this->users[$userId];
        }

        return false;
    }

    public function getSlotDeck($systemSlotId)
    {
        if (!empty($this->_scoutInfo[$systemSlotId])) {
            return $this->_scoutInfo[$systemSlotId];
        }

        return false;
    }

    public function setScout($userId)
    {
        $this->_scoutId = $userId;
    }

    public function getTileInfo()
    {
        $scout = $this->getScout();

        if (!$this->_tileInfo) {
            if (empty($ret['result'])) {
                return false;
            }
            $ret = $scout->getConquestTileInfo($this->_systemId);
            $this->_tileInfo = $ret['system'];
        }

        $slots = array();

        foreach ($this->_tileInfo['slots'] as $id => $slot) {
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

    public function scoutTileSlot($systemSlotId)
    {
        if (!empty($this->_scoutInfo[$systemSlotId])) {
            return $this->_scoutInfo[$systemSlotId];
        }

        $scout = $this->getScout();
        if ($scout->getStamina() < 20) {
            $scout = $this->setRandomScout();
            if ($scout === false) {
                return false;
            }
        }

        $ret = $scout->attackConquestTile($this->_systemId, $systemSlotId);
        $this->updateScoutTileSlot($systemSlotId, $ret);

        return true;
    }

    public function updateScoutTileSlot($systemSlotId, $data)
    {
        $dir = $this->getBattleLogPath();
        file_put_contents($dir.'/'.$systemSlotId.'.deck', serialize($data));
        $this->_scoutInfo[$systemSlotId] = $data;
    }

    public function setRandomScout()
    {
        /** @var Tyrant_User $user */
        foreach ($this->users as $userId => $user) {
            if ($user->getStamina() > 20) {
                $this->setScout($userId);
                return $this->getScout();
            }
        }

        return false;
    }

    /**
     * @return Tyrant_User
     */
    public function getScout()
    {
        return $this->users[$this->_scoutId];
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

                /** @var Tyrant_User $user */
                foreach ($this->users as $user) {
                    if ($slot['health'] <= 0) {
                        break;
                    }
                    $winRate = $user->getWinRateForDeckHash($deck['enemyDeckHash']);
                    if ($winRate < Tyrant_Optimizer::MIN_WIN_RATE) {
                        continue;
                    }

                    // TODO: check time;

                    $ret = $user->attackConquestTile($this->_systemId, $slot['systemSlotId']);
                    // TODO: analize $ret;
                }


                /*

                        'enemyCommanderId'  => $enemyCommanderId,
                        'enemyDeck'         => $enemyDeck,
                        'enemyDeckHash'     => Tyrant_Deck::getDeckHashFromCards($enemyFullDeck, false),
                        'winner'            => !empty($t['winner']),
                */

                //TODO: check deck and update scoutTileInfo
            }
        }

    }

    protected function getBattleLogPath()
    {
        $dir = DIR_CACHE.'/'.$this->_systemId;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }
}