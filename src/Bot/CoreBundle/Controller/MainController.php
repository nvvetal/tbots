<?php

namespace Bot\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Bot\CoreBundle\Entity\Bot;

class MainController extends Controller
{
    public function indexAction()
    {

        $botHelper = $this->container->get('helper.bot');
        $bot = new Bot();
        $bot->setUserId($this->container->getParameter('test_bot_id'));
        $bot->setFlashCode($this->container->getParameter('test_bot_hash'));
        echo "<pre>";
        var_dump($botHelper->getBotClient($bot));
        exit;

        return array('name' => 'test');
    }
}
