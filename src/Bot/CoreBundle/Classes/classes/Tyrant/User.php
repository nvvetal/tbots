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

        $this->_initClient();
    }

    public function getId()
    {
        return $this->userId;
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
                continue;
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

    public function getOptimizationInfo($hash, $effect)
    {
        $key = $hash.':'.$effect;
        if (!empty($this->_optimizedDecks[$key])) {
            return $this->_optimizedDecks[$key];
        }

        $to = $this->getTyrantOptimizer();
        $this->_optimizedDecks[$key] = $to->optimize($hash, $effect);

        return $this->_optimizedDecks[$key];
    }

    public function getTyrantOptimizer()
    {
        if (!$this->_optimizer) {
            $this->_optimizer = new Tyrant_Optimizer($this);
        }

        return $this->_optimizer;
    }

    public function prepareAndSetDeckCards($deckId, $commanderId, $cards)
    {
        if ($deckId === false) {
            $deckId = $this->activeDeckId;
        }

        $fullDeck = $cards;
        array_unshift($fullDeck, $commanderId);
        $prepareHash = Tyrant_Deck::getDeckHashFromCards($fullDeck);
        Tyrant::l('[%d] Try to setup deck: %s', array($this->userId, $prepareHash));

        $cDeck = $this->getCurrentDeck();

        // TODO: update my deck |
        if ($cDeck['hash'] === $prepareHash) {
            return true;
        }

        if (true) {
            return $this->setCardsForDesk($deckId, $prepareHash);
        }

        $ret = $this->clearDeck($deckId);
        if (!$ret) {
            Tyrant::l('cant clear deck '.$this->userId);
            return false;
        }

        foreach ($fullDeck as $cardId) {
            $card = Tyrant_Cards::getCardById($cardId);
            $commander = $card->isCommander();
            $ca = $this->cardAvailable($cardId);

            if ($ca == -2) { // card wasn't found
                continue;
            }

            if ($ca == -1) { // card found, but used
                $decks = $this->getDecksForCard($cardId);
                $found = false;
                foreach ($decks as $caDeckId) {
                    if ($caDeckId != $deckId) {
                        if ($commander) {
                            foreach ($this->myCards as $myCardId => $u) {
                                if ($myCardId == $cardId) {
                                    continue;
                                }
                                $myCard = Tyrant_Cards::getCardById($myCardId);

                                if (!$myCard->isCommander()) {
                                    continue;
                                }

                                if ($u['num_used'] >= $u['num_owned']) {
                                    continue;
                                }

                                $ret = $this->setCardToDeck($caDeckId, $myCardId);
                                break;
                            }
                        } else {
                            $ret = $this->removeCardDeck($caDeckId, $cardId);
                        }
                        if (!$ret) {
                            continue;
                        }
                        $found = true;
                        break;
                    }
                }
                if (!$found) { // card used in deck for filling
                    continue;
                }
            }
        }

        return $this->setDeckCards($deckId, $commanderId, $cards);
    }

    public function getCurrentDeck()
    {
        $deck = parent::getCurrentDeck();
        if ($deck === false) {
            return false;
        }

        $fullDeck = $cards = array();
        $fullDeck[] = $deck['commander_id'];
        foreach ($deck['cards'] as $cardId => $cnt) {
            for ($i = 0; $i < $cnt; $i++) {
                $fullDeck[] = $cardId;
                $cards[] = $cardId;
            }
        }

        return array(
            'cardCommanderId'   => $deck['commander_id'],
            'cards'             => $deck['cards'],
            'fullDeck'          => $fullDeck,
            'hash'              => Tyrant_Deck::getDeckHashFromCards($fullDeck, false)
        );
    }
}
