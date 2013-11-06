<?php

namespace Bot\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TileDeck
 *
 * @ORM\Table(name="tile_deck")
 * @ORM\Entity
 */
class TileDeck
{
    const SCOUT_STATUS_NEW = 1;
    const SCOUT_STATUS_PROCESS = 2;
    const SCOUT_STATUS_FINISHED = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Tile")
     * @ORM\JoinColumn(name="tile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $tile;

    /**
     * @var string
     * @ORM\Column(name="hash", type="string")
     */
    protected $hash;

    /**
     * @var integer
     * @ORM\Column(name="max_percent", type="integer")
     */
    protected $maxPercent; //use for def decks in future

    /**
     * @var integer
     * @ORM\Column(name="scout_status", type="integer")
     */
    protected $scoutStatus;

    /**
     * @var integer
     * @ORM\Column(name="is_active", type="integer")
     */
    protected $isActive;

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
     * Set hash
     *
     * @param string $hash
     * @return TileDeck
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
     * Set maxPercent
     *
     * @param integer $maxPercent
     * @return TileDeck
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
     * Set isActive
     *
     * @param integer $isActive
     * @return TileDeck
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
     * Set tile
     *
     * @param \Bot\CoreBundle\Entity\Tile $tile
     * @return TileDeck
     */
    public function setTile(\Bot\CoreBundle\Entity\Tile $tile = null)
    {
        $this->tile = $tile;
    
        return $this;
    }

    /**
     * Get tile
     *
     * @return \Bot\CoreBundle\Entity\Tile 
     */
    public function getTile()
    {
        return $this->tile;
    }
}