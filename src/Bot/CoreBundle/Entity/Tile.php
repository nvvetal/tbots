<?php

namespace Bot\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tile
 *
 * @ORM\Table(name="tile")
 * @ORM\Entity(repositoryClass="Bot\CoreBundle\Entity\TileRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Tile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="system_id", type="string")
     */
    protected $systemId;

    /**
     * @var integer
     * @ORM\Column(name="x", type="integer")
     */
    protected $x;


    /**
     * @var integer
     * @ORM\Column(name="y", type="integer")
     */
    protected $y;

    /**
     * @var integer
     * @ORM\Column(name="effect", type="integer", nullable=true)
     */
    protected $effect;

    /**
     * @var integer
     * @ORM\Column(name="credits", type="integer", nullable=true)
     */
    protected $credits;

    /**
     * @var string
     * @ORM\Column(name="faction", type="string", nullable=true)
     */
    protected $faction;

    /**
     * @var integer
     * @ORM\Column(name="decks_count", type="integer", nullable=true)
     */
    protected $decksCount;

    /**
     * @var integer
     * @ORM\Column(name="min_energy_need", type="integer", nullable=true)
     */
    protected $minEnergyNeed;

    /**
     * @var integer
     * @ORM\Column(name="max_percent", type="integer", nullable=true)
     */
    protected $maxPercent; //use for def decks in future

    /**
     * @var integer
     * @ORM\Column(name="is_active", type="integer")
     */
    protected $isActive;

    /**
     * @var integer
     * @ORM\Column(name="last_attack_time", type="integer", nullable=true)
     */
    protected $lastAttackTime;

    /**
     * @var integer
     * @ORM\Column(name="created_time", type="integer")
     */
    protected $createdTime;

    /**
     * @var integer
     * @ORM\Column(name="attack_start_time", type="integer")
     */
    protected $attackStartTime;


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
     * Set credits
     *
     * @param integer $credits
     * @return Tile
     */
    public function setCredits($credits)
    {
        $this->credits = $credits;
    
        return $this;
    }

    /**
     * Get credits
     *
     * @return integer 
     */
    public function getCredits()
    {
        return $this->credits;
    }

    /**
     * Set faction
     *
     * @param string $faction
     * @return Tile
     */
    public function setFaction($faction)
    {
        $this->faction = $faction;
    
        return $this;
    }

    /**
     * Get faction
     *
     * @return string 
     */
    public function getFaction()
    {
        return $this->faction;
    }

    /**
     * Set decksCount
     *
     * @param integer $decksCount
     * @return Tile
     */
    public function setDecksCount($decksCount)
    {
        $this->decksCount = $decksCount;
    
        return $this;
    }

    /**
     * Get decksCount
     *
     * @return integer 
     */
    public function getDecksCount()
    {
        return $this->decksCount;
    }

    /**
     * Set minEnergyNeed
     *
     * @param integer $minEnergyNeed
     * @return Tile
     */
    public function setMinEnergyNeed($minEnergyNeed)
    {
        $this->minEnergyNeed = $minEnergyNeed;
    
        return $this;
    }

    /**
     * Get minEnergyNeed
     *
     * @return integer 
     */
    public function getMinEnergyNeed()
    {
        return $this->minEnergyNeed;
    }

    /**
     * Set isActive
     *
     * @param integer $isActive
     * @return Tile
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return integer 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set lastAttackTime
     *
     * @param integer $lastAttackTime
     * @return Tile
     */
    public function setLastAttackTime($lastAttackTime)
    {
        $this->lastAttackTime = $lastAttackTime;
    
        return $this;
    }

    /**
     * Get lastAttackTime
     *
     * @return integer 
     */
    public function getLastAttackTime()
    {
        return $this->lastAttackTime;
    }

    /**
     * Set createdTime
     *
     * @param integer $createdTime
     * @return Tile
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
     * @param string $systemId
     */
    public function setSystemId($systemId)
    {
        $this->systemId = $systemId;
    }

    /**
     * @return string
     */
    public function getSystemId()
    {
        return $this->systemId;
    }

    /**
     * @param int $x
     */
    public function setX($x)
    {
        $this->x = $x;
    }

    /**
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @param int $y
     */
    public function setY($y)
    {
        $this->y = $y;
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param int $effect
     */
    public function setEffect($effect)
    {
        $this->effect = $effect;
    }

    /**
     * @return int
     */
    public function getEffect()
    {
        return $this->effect;
    }

    /**
     * @param int $attackStartTime
     */
    public function setAttackStartTime($attackStartTime)
    {
        $this->attackStartTime = $attackStartTime;
    }

    /**
     * @return int
     */
    public function getAttackStartTime()
    {
        return $this->attackStartTime;
    }

    /**
     * Set maxPercent
     *
     * @param integer $maxPercent
     * @return Tile
     */
    public function setMaxPercent($maxPercent)
    {
        $this->maxPercent = $maxPercent;

        return $this;
    }

    /**
     * Get maxPercent
     *
     * @return integer
     */
    public function getMaxPercent()
    {
        return $this->maxPercent;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if (is_null($this->getIsActive())) {
            $this->setIsActive(0);
        }
        $this->setCreatedTime(time());
    }

}