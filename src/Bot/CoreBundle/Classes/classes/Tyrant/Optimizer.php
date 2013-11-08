<?php
class Tyrant_Optimizer
{
    const OPTIMIZE_RUN_CNT = 1000;
    const MIN_WIN_RATE = 90;

    protected $_user;

    public function __construct(Tyrant_User $user)
    {
        $this->_user = $user;
    }

    public function optimize($enemyDeckHash, $effect)
    {
        $ret = $this->_call($enemyDeckHash, $effect);
        if ($ret === false) {
            return false;
        }

        if (!preg_match('~^Optimized Deck: ([\d\.]+):(.*)$~im', $ret, $m)) {
            return false;
        }

        $winRate = $m[1];

        Tyrant::l(
            '[%d] Try to optimize deck. WinRate: %s, cards: %s',
            array($this->_user->getId(), $winRate, $m[2])
        );

        if ($winRate < self::MIN_WIN_RATE) {
            return false;
        }

        $cards = array();
        $commanderId = 0;
        $c = explode(',', $m[2]);
        foreach ($c as $cardName) {
            $cardName = trim($cardName);

            $cnt = 1;
            if (preg_match('~^(.*)#(\d+)$~', $cardName, $m)) {
                $cnt = $m[2];
                $cardName = trim($m[1]);
            }

            $card = Tyrant_Cards::getCardByName($cardName);


            if ($card->isCommander()) {
                $commanderId = $card->getId();
            } else {
                for ($i = 0; $i < $cnt; $i++) {
                    $cards[] = $card->getId();
                }
            }
        }

        $fullDeck = $cards;
        array_unshift($fullDeck, $commanderId);

        return array(
            'cardCommanderId'   => $commanderId,
            'cards'             => $cards,
            'fullDeck'          => $fullDeck,
            'hash'              => Tyrant_Deck::getDeckHashFromCards($fullDeck, false),
            'winRate'           => $winRate
        );
    }

    protected function _call($enemyDeckHash, $effect)
    {
        $result = "";
        chdir(dirname(CMD_OPTYMAIZER));
        $command = CMD_OPTYMAIZER.' '
            .escapeshellarg($this->_getDefaultDeckHash()).' '
            .escapeshellarg($enemyDeckHash).' '
            .($effect ? '-e '.$effect : '').' '
            .'-o='.escapeshellarg($this->_user->getOwnedCardsFileName()).' '
            .'climb '.self::OPTIMIZE_RUN_CNT;
        if ($p = popen("($command)2>&1","r")) {
            while (!feof($p)) {
                $result .= fgets($p, 1000);
            }
            pclose($p);
            return $result;
        }

        return false;
    }

    protected function _getDefaultDeckHash()
    {
        return 'PoAB';
    }
}
