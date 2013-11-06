<?php

namespace Bot\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeckCalculate
 *
 * @ORM\Table(name="deck_calculate")
 * @ORM\Entity
 */
class DeckCalculate
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
     * @var integer
     * @ORM\Column(name="bot_id", type="integer")
     */
    protected $botId;

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
}
