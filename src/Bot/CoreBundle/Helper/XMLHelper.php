<?php
namespace Bot\CoreBundle\Helper;

class XMLHelper
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getCards()
    {
        $fileName = $this->container->getParameter('optimizer_path').'cards.xml';
        $xml = (array) simplexml_load_file($fileName);
        if (!$xml) {
            return NULL;
        }
        return $xml;
    }

}


?>