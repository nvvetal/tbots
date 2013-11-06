<?php
namespace Bot\CoreBundle\Helper;
use Bot\CoreBundle\Entity\Bot;
use Bot\CoreBundle\Classes\User;

class BotHelper
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getOwnedCardsFileName(Bot $bot)
    {
        $path = $this->container->getParameter('optimizer_path');
        return $path.'/'.$bot->getUserId().'.txt';
    }

    public function getBotClient(Bot $bot)
    {
        $user = new User($bot->getUserId(), $bot->getFlashCode());
        $user->init();

        // write user cards file.
        umask(0);
        $f = fopen($this->getOwnedCardsFileName($bot), 'w');
        foreach ($user->getMyCards() as $cardId => $cardInfo) {
            if ($cardId > 10000) {
                $cardId -= 10000;
            }
            $cardsHelper = $this->container->get('helper.cards');
            $card = $cardsHelper->getCardById($cardId);
            if ($card === false) {
                //echo "cant load card: ".$cardId."\n";
            }
            $lvl = '';
            if ($card->isUpdatedCard()) {
                $lvl = ', Lvl2';
            }
            $line = sprintf('[%d] %s%s (%d)', $cardId, $card->getName(), $lvl, $cardInfo['num_owned']);
            fwrite($f, $line."\n");
        }
        fclose($f);
        return $user;
    }


}
