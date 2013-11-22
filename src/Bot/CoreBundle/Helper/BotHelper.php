<?php
namespace Bot\CoreBundle\Helper;
use Bot\CoreBundle\Entity\Bot;
use Bot\CoreBundle\Classes\User;

class BotHelper
{
    private $container;
    private $scouts = NULL;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getOwnedCardsFileName(Bot $bot)
    {
        $path = $this->container->getParameter('bot_data');
        return $path.'/'.$bot->getUserId().'.txt';
    }

    public function getBotClient(Bot $bot)
    {
        $user = new User($bot->getUserId(), $bot->getFlashCode());
        $user->setContainer($this->container);
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
                $this->container->get('logger')->write('[error cant load card: '.$cardId.'][botId '.$bot->getUserId().']', 'error_bot_helper_get_bot_client');
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
        return $user;
    }

    public function getScouts()
    {
        if(!is_null($this->scouts) && count($this->scouts) > 0) return $this->scouts;
        $scoutsActive = $this->container->get('Doctrine')->getManager()->getRepository('BotCoreBundle:Bot')->findScouts();
        $this->scouts = array();
        if(count($scoutsActive) == 0) return $this->scouts;
        $i = 0;
        foreach($scoutsActive as $scout)
        {

            $this->scouts[] = array(
                'loaded'    => false,
                'scout'     => $scout,
                'id'        => $i,
            );
            $i++;
        }
        return $this->scouts;
    }


    public function getNextScout($currentId = -1)
    {
        if(is_null($this->scouts)) $this->getScouts();
        if(count($this->scouts) == 0) return NULL;
        $nextId = $currentId+1;
        if(!isset($this->scouts[$nextId])) return NULL;
        if(!$this->scouts[$nextId]['loaded']){
            $this->scouts[$nextId]['loaded'] = true;
            $this->scouts[$nextId]['client'] = $this->getBotClient($this->scouts[$nextId]['scout']);
        }
        return $this->scouts[$nextId];
    }
}
