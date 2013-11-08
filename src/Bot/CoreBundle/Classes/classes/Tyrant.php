<?php
class Tyrant
{
    protected $users = array();
    protected $_scoutId;
    protected $_systemId;
    protected $_tileInfo;
    protected $_scoutInfo = array();
    protected $_effect = 0;

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

    public function updateSlotDeck($systemSlotId, $data)
    {
        if (empty($this->_scoutInfo[$systemSlotId])) {
            $this->_scoutInfo[$systemSlotId] = $data;
            return true;
        }

        $current = $this->_scoutInfo[$systemSlotId];

        if ($current['enemyDeckHash'] === $data['enemyDeckHash']) {
            return true;
        }


        if ($data['enemyCommanderId'] != $current['enemyCommanderId']) {
            $this->_scoutInfo[$systemSlotId] = $data;
            return true;
        }

        if (count($data['enemyDeck']) > count($current['enemyDeck'])) {
            $this->_scoutInfo[$systemSlotId] = $data;
            return true;
        }
        // TODO: combine cards ?

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
            $ret = $scout->getConquestTileInfo($this->_systemId);
            if (empty($ret['result'])) {
                return false;
            }
            $this->_tileInfo = $ret['system'];
        }

        if (!empty($ret['system']['effect'])) {
            $this->_effect = $ret['system']['effect'];
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
        if ($this->updateScoutTileSlot($systemSlotId, $ret)) {
            return $this->_scoutInfo[$systemSlotId];
        }

        return false;
    }

    public function updateScoutTileSlot($systemSlotId, $data)
    {
        $dir = $this->getBattleLogPath();
        file_put_contents($dir.'/'.$systemSlotId.'.deck', serialize($data));
        $this->_scoutInfo[$systemSlotId] = $data;

        return true;
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
                Tyrant::l('slot: %d; health: %d', array($slot['systemSlotId'], $slot['health']));

                $deck = $this->getSlotDeck($slot['systemSlotId']);
                if ($deck === false) {
                    Tyrant::l('scout slot: %d', array($slot['systemSlotId']));
                    $deck = $this->scoutTileSlot($slot['systemSlotId']);
                }
                Tyrant::l('Enemy Deck is: %s', array($deck['enemyDeckHash']));


                $usrSkip = 0;
                /** @var Tyrant_User $user */
                foreach ($this->users as $user) {
                    if ($slot['health'] <= 0) {
                        break;
                    }
                    $info = $user->getOptimizationInfo($deck['enemyDeckHash'], $this->_effect);
                    if ($info === false) {
                        $usrSkip++;
                        continue;
                    }

                    if (!$user->prepareAndSetDeckCards(false, $info['cardCommanderId'], $info['cards'])) {
                        continue;
                    }

                    $can = $user->canAttackTile($this->_systemId, $slot['systemSlotId']);
                    if (!$can['ok']) {
                        Tyrant::l('cant attack '.(!empty($can['wait']) ? 'need to wait: '.$can['wait'] : ''));
                        continue;
                    }

                    $ret = $user->attackConquestTile($this->_systemId, $slot['systemSlotId']);
                    $this->updateSlotDeck($slot['systemSlotId'], $ret);

                    Tyrant::l(
                        '[%s] %s health: %s',
                        array($user->getId(), $ret['winner'] ? 'WIN' : 'DEFEAT', $ret['slot']['health'])
                    );

                    $slot['health'] = $ret['slot']['health'];
                    $slot['commanderId'] = $ret['slot']['commander_id'];

                    $this->_tileInfo = $ret['system'];
                }

                if (count($this->users) === $usrSkip) {
                    // no one can beat this deck!
                    Tyrant::l('no one can beat this deck');
                    break;
                }
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

    public static function l($src, $params = array())
    {
        if (!empty($params)) {
            array_unshift($params, $src);
            $src = call_user_func_array('sprintf', $params);
        }

        $mt = microtime(true);
        $mls = $mt - floor($mt);
        printf("%s:%.3f| %s\n", date('Y-m-d H:i:s'), $mls, $src);
    }
}