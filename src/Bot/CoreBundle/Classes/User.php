<?php
namespace Bot\CoreBundle\Classes;

class User extends Client
{
    protected $_initialized = false;
    protected $_optimizedDecks = array();
    protected $_optimizer;

    public function __construct($userId, $flashCode)
    {
        $this->userId = $userId;
        $this->flashCode = $flashCode;
    }
}
