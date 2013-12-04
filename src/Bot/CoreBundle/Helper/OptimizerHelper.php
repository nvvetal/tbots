<?php
namespace Bot\CoreBundle\Helper;

use Bot\CoreBundle\Helper\CardsHelper;
use Bot\CoreBundle\Helper\DeckHelper;
use Bot\CoreBundle\Entity\Bot;
use Bot\CoreBundle\Entity\DeckCalculate;

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

    public function optimize($enemyDeckHash, Bot $bot, $params)
    {
        $ret = $this->call($enemyDeckHash, $bot, $params);
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

    protected function call($enemyDeckHash, Bot $bot, $params)
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
            .($params['effect'] ? '-e '.$params['effect'].' ' : '').''
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

    public function addDeckCalculate(Bot $bot, $deckType, $deckHash, $enemyOptions, $params)
    {
        try{
            $em = $this->container->get('Doctrine')->getManager();
            $deckCalculate = new DeckCalculate();
            $deckCalculate->setEnemyDeckType(DeckCalculate::ENEMY_DECK_TYPE_TILE_SLOT);
            $deckCalculate->setBot($bot);
            $deckCalculate->setEnemyDeckHash($deckHash);
            $deckCalculate->setEnemyOptions(json_encode($enemyOptions));
            switch($deckType)
            {
                case DeckCalculate::ENEMY_DECK_TYPE_TILE_SLOT:
                    $tileSlot = $params['tileSlot'];
                    $deckCalculate->setTileSlot($tileSlot);
                    break;
            }
            $em->persist($deckCalculate);
            $em->flush();
        }catch(\Exception $e)
        {
            echo $e->getMessage();
        }
    }

    protected function getDefaultDeckHash()
    {
        return 'PoAB';
    }
}
