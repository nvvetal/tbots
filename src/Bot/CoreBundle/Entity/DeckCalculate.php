<?php

namespace Bot\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeckCalculate
 *
 * @ORM\Table(name="deck_calculate")
 * @ORM\Entity(repositoryClass="Bot\CoreBundle\Entity\DeckCalculateRepository")
 */
class DeckCalculate
{
    const STATE_NEW = 1;
    const STATS_PROCESS = 2;
    const STATE_CALCULATED = 3;
    const STATE_ERROR = 4;
    const STATE_DEFEAT = 5;

    const ENEMY_DECK_TYPE_MISSION = 1;
    const ENEMY_DECK_TYPE_TILE_SLOT = 2;

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
     * @ORM\ManyToOne(targetEntity="TileSlot")
     * @ORM\JoinColumn(name="tile_slot_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $tileSlot;

    /**
     * @var integer
     * @ORM\Column(name="enemy_deck_type", type="integer")
     */
    protected $enemyDeckType;

    /**
     * @var string
     * @ORM\Column(name="enemy_deck_hash", type="string")
     */
    protected $enemyDeckHash;

    /**
     * @var text
     * @ORM\Column(name="enemy_options", type="text")
     */
    protected $enemyOptions;

    /**
     * @var string
     * @ORM\Column(name="calculated_hash", type="string")
     */
    protected $calculatedHash;

    /**
     * @var integer
     * @ORM\Column(name="calculated_percent", type="integer")
     */
    protected $calculatedPercent;

    /**
     * @var integer
     * @ORM\Column(name="state", type="integer")
     */
    protected $state;


    /**
     * @var integer
     * @ORM\Column(name="created_time", type="integer")
     */
    protected $createdTime;

    /**
     * @var integer
     * @ORM\Column(name="finished_time", type="integer")
     */
    protected $finishedTime;

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
     * Set enemyDeckType
     *
     * @param integer $enemyDeckType
     * @return DeckCalculate
     */
    public function setEnemyDeckType($enemyDeckType)
    {
        $this->enemyDeckType = $enemyDeckType;
    
        return $this;
    }

    /**
     * Get enemyDeckType
     *
     * @return integer 
     */
    public function getEnemyDeckType()
    {
        return $this->enemyDeckType;
    }

    /**
     * Set enemyDeckHash
     *
     * @param string $enemyDeckHash
     * @return DeckCalculate
     */
    public function setEnemyDeckHash($enemyDeckHash)
    {
        $this->enemyDeckHash = $enemyDeckHash;
    
        return $this;
    }

    /**
     * Get enemyDeckHash
     *
     * @return string 
     */
    public function getEnemyDeckHash()
    {
        return $this->enemyDeckHash;
    }

    /**
     * Set enemyOptions
     *
     * @param string $enemyOptions
     * @return DeckCalculate
     */
    public function setEnemyOptions($enemyOptions)
    {
        $this->enemyOptions = $enemyOptions;
    
        return $this;
    }

    /**
     * Get enemyOptions
     *
     * @return string 
     */
    public function getEnemyOptions()
    {
        return $this->enemyOptions;
    }

    /**
     * Set calculatedHash
     *
     * @param string $calculatedHash
     * @return DeckCalculate
     */
    public function setCalculatedHash($calculatedHash)
    {
        $this->calculatedHash = $calculatedHash;
    
        return $this;
    }

    /**
     * Get calculatedHash
     *
     * @return string 
     */
    public function getCalculatedHash()
    {
        return $this->calculatedHash;
    }

    /**
     * Set calculatedPercent
     *
     * @param integer $calculatedPercent
     * @return DeckCalculate
     */
    public function setCalculatedPercent($calculatedPercent)
    {
        $this->calculatedPercent = $calculatedPercent;
    
        return $this;
    }

    /**
     * Get calculatedPercent
     *
     * @return integer 
     */
    public function getCalculatedPercent()
    {
        return $this->calculatedPercent;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return DeckCalculate
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set createdTime
     *
     * @param integer $createdTime
     * @return DeckCalculate
     */
    public function setCreatedTime($createdTime)
    {
        $this->createdTime = $createdTime;
    
        return $this;
    }

    /**
     * Get createdTime
     *
     * @return integer 
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    /**
     * Set finishedTime
     *
     * @param integer $finishedTime
     * @return DeckCalculate
     */
    public function setFinishedTime($finishedTime)
    {
        $this->finishedTime = $finishedTime;
    
        return $this;
    }

    /**
     * Get finishedTime
     *
     * @return integer 
     */
    public function getFinishedTime()
    {
        return $this->finishedTime;
    }

    /**
     * Set bot
     *
     * @param \Bot\CoreBundle\Entity\Bot $bot
     * @return DeckCalculate
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