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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
