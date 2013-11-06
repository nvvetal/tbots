<?php
class Tyrant_User extends Tyrant_Client
{
    protected $_initialized = false;
    protected $_optimizedDecks = array();
    protected $_optimizer;

    public function __construct($userId, $flashCode)
    {
        $this->userId = $userId;
        $this->flashCode = $flashCode;
        //$this->_initClient();
    }

    public function getOwnedCardsFileName()
    {
        return dirname(CMD_OPTYMAIZER).'/'.$this->userId.'.txt';
    }

    protected function _initClient()
    {
        if ($this->_initialized) {
            return true;
        }

        $this->init();
        $this->_initialized = true;

        // write user cards file.
        umask(0);
        $f = fopen($this->getOwnedCardsFileName(), 'w');
        foreach ($this->myCards as $cardId => $cardInfo) {
            if ($cardId > 10000) {
                $cardId -= 10000;
            }

            $card = Tyrant_Cards::getCardById($cardId);
            if ($card === false) {
                echo "cant load card: ".$cardId."\n";
            }
            $lvl = '';
            if ($card->isUpdatedCard()) {
                $lvl = ', Lvl2';
            }
            $line = sprintf('[%d] %s%s (%d)', $cardId, $card->getName(), $lvl, $cardInfo['num_owned']);
            fwrite($f, $line."\n");
        }
        fclose($f);
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
