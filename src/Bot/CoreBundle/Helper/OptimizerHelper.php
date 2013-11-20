<?php
namespace Bot\CoreBundle\Helper;

use Bot\CoreBundle\Helper\CardsHelper;
use Bot\CoreBundle\Helper\DeckHelper;
use Bot\CoreBundle\Entity\Bot;

class OptimizerHelper
{
    const OPTIMIZE_RUN_CNT = 1000;
    const MIN_WIN_RATE = 90;

    private $cardsHelper;
    private $deckHelper;
    private $container;

    public function __construct($container, CardsHelper $CardsHelper, DeckHelper $DeckHelper)
    {
        $this->cardsHelper = $CardsHelper;
        $this->deckHelper = $DeckHelper;
        $this->container = $container;
    }

    public function optimize($enemyDeckHash, $effect, Bot $bot)
    {
        $ret = $this->call($enemyDeckHash, $effect, $bot);
        if ($ret === false) {
            return false;
        }

        if (!preg_match('~^Optimized Deck: ([\d\.]+):(.*)$~im', $ret, $m)) {
            return false;
        }

        $winRate = $m[1];
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

            $card = $this->cardsHelper->getCardByName($cardName);


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
            'hash'              => $this->deckHelper->getDeckHashFromCards($fullDeck, false),
            'winRate'           => $winRate
        );
    }

    protected function call($enemyDeckHash, $effect, Bot $bot)
    {
        $botHelper = $this->container->get('helper.bot');
        $optimizerPath = $this->container->getParameter('optimizer_path');
        $result = "";
        $optimizerCommand = $optimizerPath.'/tyrant_optimize';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $optimizerCommand .= '.exe';
        }
        $command = $optimizerCommand.' '
            .escapeshellarg($this->getDefaultDeckHash()).' '
            .escapeshellarg($enemyDeckHash).' '
            .($effect ? '-e '.$effect : '').' '
            .'-o='.escapeshellarg($botHelper->getOwnedCardsFileName($bot)).' '
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

    protected function getDefaultDeckHash()
    {
        return 'PoAB';
    }
}
