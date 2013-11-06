<?php
namespace Bot\CoreBundle\Classes;

class User extends Client
{
    protected $_initialized = false;
    protected $_optimizedDecks = array();
    protected $_optimizer;

    public function __construct($userId, $flashCode)
    {
        $this->userId = $userId;
        $this->flashCode = $flashCode;
    }

    public function getWinRateForDeckHash($hash)
    {
        if (!empty($this->_optimizedDecks[$hash])) {
            return $this->_optimizedDecks;
        }

        $to = $this->getTyrantOptimizer();
        $this->_optimizedDecks[$hash] = $to->optimize($hash);

        return $this->_optimizedDecks[$hash];
    }

    public function getTyrantOptimizer()
    {
        if (!$this->_optimizer) {
            $this->_optimizer = new Tyrant_Optimizer($this);
        }

        return $this->_optimizer;
    }
}
