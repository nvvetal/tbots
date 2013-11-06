<?php
namespace Bot\CoreBundle\Helper;

class XMLHelper
{
    public function __construct()
    {
        $this->setCards();
    }

    public function getCards()
    {
        $fileName = dirname(CMD_OPTYMAIZER).'/cards.xml';
        $xml = (array) simplexml_load_file($fileName);
        if (!$xml) {
            return NULL;
        }
        return $xml;
    }

    private function setCards()
    {
        //TODO: fill somehow with cards
    }
}


?>