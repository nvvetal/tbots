<?php
namespace Bot\CoreBundle\Helper;
use Bot\CoreBundle\Entity\Card;
use Bot\CoreBundle\Helper\XMLHelper;

class CardsHelper
{
    protected $cards;

    public function __construct(XMLHelper $XMLHelper)
    {
        $cardsXML = $XMLHelper->getCards();

        $this->cards = array();

        foreach ($cardsXML['unit'] as $unit) {
            $card = new Card();
            $card->setCardByData((array)$unit);
            $this->cards[$card->getId()] = $card;
        }
        return true;
    }

    public function getCardByName($cardName)
    {
        foreach ($this->cards as $card) {
            if ($card->isEqual($cardName)) {
                return $card;
            }
        }

        return false;
    }

    public function getCardById($id)
    {

        if (!empty($this->cards[$id])) {
            return $this->cards[$id];
        }

        return false;
    }
}
?>