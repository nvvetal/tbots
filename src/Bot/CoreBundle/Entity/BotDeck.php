<?php

namespace Bot\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BotDeck
 *
 * @ORM\Table(name="bot_deck")
 * @ORM\Entity
 */
class BotDeck
{
    const DECK_TYPE_ATTACK = 1;
    const DECK_TYPE_DEFEND = 2;
    const DECK_TYPE_USUAL = 3;


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Bot")
     * @ORM\JoinColumn(name="bot_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $bot;

    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="hash", type="string")
     */
    protected $hash;

    /**
     * @var string
     * @ORM\Column(name="stolen_cards", type="string")
     */
    protected $stolenCards;

    /**
     * @var integer
     * @ORM\Column(name="type", type="integer")
     */
    protected $type;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return BotDeck
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return BotDeck
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    
        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set stolenCards
     *
     * @param string $stolenCards
     * @return BotDeck
     */
    public function setStolenCards($stolenCards)
    {
        $this->stolenCards = $stolenCards;
    
        return $this;
    }

    /**
     * Get stolenCards
     *
     * @return string 
     */
    public function getStolenCards()
    {
        return $this->stolenCards;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return BotDeck
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set bot
     *
     * @param \Bot\CoreBundle\Entity\Bot $bot
     * @return BotDeck
     */
    public function setBot(\Bot\CoreBundle\Entity\Bot $bot = null)
    {
        $this->bot = $bot;
    
        return $this;
    }

    /**
     * Get bot
     *
     * @return \Bot\CoreBundle\Entity\Bot 
     */
    public function getBot()
    {
        return $this->bot;
    }
}