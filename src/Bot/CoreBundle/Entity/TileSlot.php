<?php

namespace Bot\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TileSlot
 *
 * @ORM\Table(name="tile_slot")
 * @ORM\Entity(repositoryClass="Bot\CoreBundle\Entity\TileSlotRepository")
 * @ORM\HasLifecycleCallbacks
 */
class TileSlot
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
     * @var integer
     * @ORM\Column(name="slot_id", type="integer")
     */
    protected $slotId;

    /**
     * @var integer
     * @ORM\Column(name="cards_count", type="integer", nullable=true)
     */
    protected $cardsCount;


    /**
     * @var string
     * @ORM\Column(name="deck_hash", type="string", nullable=true)
     */
    protected $deckHash;

    /**
     * @var string
     * @ORM\Column(name="deck_cards", type="text", nullable=true)
     */
    protected $deckCards;

    /**
     * @var integer
     * @ORM\Column(name="health", type="integer", nullable=true)
     */
    protected $health;



    /**
     * @var integer
     * @ORM\Column(name="scout_status", type="integer", nullable=true)
     */
    protected $scoutStatus;

    /**
     * @var integer
     * @ORM\Column(name="is_active", type="integer", nullable=true)
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
    public function setDeckHash($hash)
    {
        $this->deckHash = $hash;
    
        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getDeckHash()
    {
        return $this->deckHash;
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

    /**
     * @param int $cardsCount
     */
    public function setCardsCount($cardsCount)
    {
        $this->cardsCount = $cardsCount;
    }

    /**
     * @return int
     */
    public function getCardsCount()
    {
        return $this->cardsCount;
    }

    /**
     * @param int $scoutStatus
     */
    public function setScoutStatus($scoutStatus)
    {
        $this->scoutStatus = $scoutStatus;
    }

    /**
     * @return int
     */
    public function getScoutStatus()
    {
        return $this->scoutStatus;
    }

    /**
     * @param int $slotId
     */
    public function setSlotId($slotId)
    {
        $this->slotId = $slotId;
    }

    /**
     * @return int
     */
    public function getSlotId()
    {
        return $this->slotId;
    }

    /**
     * @param mixed $deckCards
     */
    public function setDeckCards($deckCards)
    {
        $this->deckCards = $deckCards;
    }

    /**
     * @return mixed
     */
    public function getDeckCards()
    {
        return $this->deckCards;
    }

    /**
     * @param mixed $health
     */
    public function setHealth($health)
    {
        $this->health = $health;
    }

    /**
     * @return mixed
     */
    public function getHealth()
    {
        return $this->health;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if (is_null($this->getScoutStatus())) {
            $this->setScoutStatus(self::SCOUT_STATUS_NEW);
        }
        $this->setIsActive(1);
    }



}