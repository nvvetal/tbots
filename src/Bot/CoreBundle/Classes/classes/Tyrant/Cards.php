<?php
class Tyrant_Cards
{
    protected static $_cards;
    const UPGRADED_SET = 5002;

    public static function getCardByName($cardName)
    {
        self::_loadCards();
        /** @var Tyrant_Card $card */
        foreach (self::$_cards as $card) {
            if ($card->isEqual($cardName)) {
                return $card;
            }
        }

        return false;
    }

    protected static function _loadCards()
    {
        if (self::$_cards) {
            return true;
        }

        $fileName = dirname(CMD_OPTYMAIZER).'/cards.xml';
        $xml = simplexml_load_file($fileName);
        if (!$xml) {
            return false;
        }

        $xml = (array)$xml;
        self::$_cards = array();

        foreach ($xml['unit'] as $unit) {
            $card = new Tyrant_Card((array)$unit);
            self::$_cards[$card->getId()] = $card;
        }

        return true;
    }

    /**
     * @param $id
     * @return bool|Tyrant_Card
     */
    public static function getCardById($id)
    {
        self::_loadCards();

        if (!empty(self::$_cards[$id])) {
            return self::$_cards[$id];
        }

        return false;
    }
}