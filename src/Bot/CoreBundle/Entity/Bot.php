<?php

namespace Bot\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bot
 *
 * @ORM\Table(name="bot")
 * @ORM\Entity
 */
class Bot
{
    const CURRENT_ACTION_MISSION = 1;
    const CURRENT_ACTION_WAR = 2;
    const CURRENT_ACTION_TILE = 3;
    const CURRENT_ACTION_SLEEP = 10;


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
     * @ORM\Column(name="login", type="string")
     */
    protected $login;


    /**
     * @var string
     * @ORM\Column(name="password", type="string")
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(name="nickname", type="string")
     */
    protected $nickname;


    /**
     * @var string
     * @ORM\Column(name="user_id", type="string")
     */
    protected $userId;

    /**
     * @var string
     * @ORM\Column(name="flash_code", type="string")
     */
    protected $flashCode;

    /**
     * @var text
     * @ORM\Column(name="tile_decks", type="text")
     */
    protected $tileDecks; //json array of DeckCalculate

    /**
     * @var integer
     * @ORM\Column(name="energy", type="integer")
     */
    protected $energy;

    /**
     * @var integer
     * @ORM\Column(name="stamina", type="integer")
     */
    protected $stamina;

    /**
     * @var integer
     * @ORM\Column(name="max_energy", type="integer")
     */
    protected $maxEnergy;

    /**
     * @var integer
     * @ORM\Column(name="current_mission_id", type="integer")
     */
    protected $currentMissionId;

    /**
     * @var integer
     * @ORM\Column(name="current_mission_hash", type="integer")
     */
    protected $currentMissionHash;

    /**
     * @var integer
     * @ORM\Column(name="current_mission_energy", type="integer")
     */
    protected $currentMissionEnergy;

    /**
     * @var integer
     * @ORM\Column(name="current_action", type="integer")
     */
    protected $currentAction;

    /**
     * @var integer
     * @ORM\Column(name="last_action_time", type="integer")
     */
    protected $lastActionTime;

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
     * Set login
     *
     * @param string $login
     * @return Bot
     */
    public function setLogin($login)
    {
        $this->login = $login;
    
        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Bot
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set nickname
     *
     * @param string $nickname
     * @return Bot
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    
        return $this;
    }

    /**
     * Get nickname
     *
     * @return string 
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set userId
     *
     * @param string $userId
     * @return Bot
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    
        return $this;
    }

    /**
     * Get userId
     *
     * @return string 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set flashCode
     *
     * @param string $flashCode
     * @return Bot
     */
    public function setFlashCode($flashCode)
    {
        $this->flashCode = $flashCode;
    
        return $this;
    }

    /**
     * Get flashCode
     *
     * @return string 
     */
    public function getFlashCode()
    {
        return $this->flashCode;
    }

    /**
     * Set tileDecks
     *
     * @param string $tileDecks
     * @return Bot
     */
    public function setTileDecks($tileDecks)
    {
        $this->tileDecks = $tileDecks;
    
        return $this;
    }

    /**
     * Get tileDecks
     *
     * @return string 
     */
    public function getTileDecks()
    {
        return $this->tileDecks;
    }

    /**
     * Set energy
     *
     * @param integer $energy
     * @return Bot
     */
    public function setEnergy($energy)
    {
        $this->energy = $energy;
    
        return $this;
    }

    /**
     * Get energy
     *
     * @return integer 
     */
    public function getEnergy()
    {
        return $this->energy;
    }

    /**
     * Set stamina
     *
     * @param integer $stamina
     * @return Bot
     */
    public function setStamina($stamina)
    {
        $this->stamina = $stamina;
    
        return $this;
    }

    /**
     * Get stamina
     *
     * @return integer 
     */
    public function getStamina()
    {
        return $this->stamina;
    }

    /**
     * Set maxEnergy
     *
     * @param integer $maxEnergy
     * @return Bot
     */
    public function setMaxEnergy($maxEnergy)
    {
        $this->maxEnergy = $maxEnergy;
    
        return $this;
    }

    /**
     * Get maxEnergy
     *
     * @return integer 
     */
    public function getMaxEnergy()
    {
        return $this->maxEnergy;
    }

    /**
     * Set currentMissionId
     *
     * @param integer $currentMissionId
     * @return Bot
     */
    public function setCurrentMissionId($currentMissionId)
    {
        $this->currentMissionId = $currentMissionId;
    
        return $this;
    }

    /**
     * Get currentMissionId
     *
     * @return integer 
     */
    public function getCurrentMissionId()
    {
        return $this->currentMissionId;
    }

    /**
     * Set currentMissionHash
     *
     * @param integer $currentMissionHash
     * @return Bot
     */
    public function setCurrentMissionHash($currentMissionHash)
    {
        $this->currentMissionHash = $currentMissionHash;
    
        return $this;
    }

    /**
     * Get currentMissionHash
     *
     * @return integer 
     */
    public function getCurrentMissionHash()
    {
        return $this->currentMissionHash;
    }

    /**
     * Set currentMissionEnergy
     *
     * @param integer $currentMissionEnergy
     * @return Bot
     */
    public function setCurrentMissionEnergy($currentMissionEnergy)
    {
        $this->currentMissionEnergy = $currentMissionEnergy;
    
        return $this;
    }

    /**
     * Get currentMissionEnergy
     *
     * @return integer 
     */
    public function getCurrentMissionEnergy()
    {
        return $this->currentMissionEnergy;
    }

    /**
     * Set currentAction
     *
     * @param integer $currentAction
     * @return Bot
     */
    public function setCurrentAction($currentAction)
    {
        $this->currentAction = $currentAction;
    
        return $this;
    }

    /**
     * Get currentAction
     *
     * @return integer 
     */
    public function getCurrentAction()
    {
        return $this->currentAction;
    }

    /**
     * Set lastActionTime
     *
     * @param integer $lastActionTime
     * @return Bot
     */
    public function setLastActionTime($lastActionTime)
    {
        $this->lastActionTime = $lastActionTime;
    
        return $this;
    }

    /**
     * Get lastActionTime
     *
     * @return integer 
     */
    public function getLastActionTime()
    {
        return $this->lastActionTime;
    }
}