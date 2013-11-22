<?php
namespace Bot\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Bot\CoreBundle\Entity\TileSlot;

class FilterTileSlotEvent extends Event
{
    /*
     * @param Bot\CoreBundle\Entity\TileSlot $tileSlot
     */
    protected $tileSlot;

    /*
     * @param mixed $slotData
     */
    protected $slotData;

    public function __construct(TileSlot $tileSlot, $slotData)
    {
        $this->setTileSlot($tileSlot);
        $this->setSlotData($slotData);
    }

    /**
     * @param mixed $slotData
     */
    public function setSlotData($slotData)
    {
        $this->slotData = $slotData;
    }

    /**
     * @return mixed
     */
    public function getSlotData()
    {
        return $this->slotData;
    }

    /**
     * @param mixed $tileSlot
     */
    public function setTileSlot($tileSlot)
    {
        $this->tileSlot = $tileSlot;
    }

    /**
     * @return mixed
     */
    public function getTileSlot()
    {
        return $this->tileSlot;
    }

}