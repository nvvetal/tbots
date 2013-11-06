<?php
class Tyrant_Card
{
    protected $_data;

    public function __construct($data)
    {
        $this->_data = $data;
    }

    public function isEqual($cardName)
    {
        if (strpos($cardName, '*') === false) {
            return $cardName === $this->getName();
        }

        return trim($cardName, '*') === $this->getName() && $this->isUpdatedCard();
    }

    public function isUpdatedCard()
    {
        return $this->getSet() == Tyrant_Cards::UPGRADED_SET;
    }

    public function getName()
    {
        return $this->_data['name'];
    }

    public function getSet()
    {
        return $this->_data['set'];
    }

    public function getId()
    {
        return $this->_data['id'];
    }

    public function getRarity()
    {
        return $this->_data['rarity'];
    }

    public function getUnique()
    {
        return !empty($this->_data['unique']);
    }

    public function isCommander()
    {
        return $this->getId() >= 1000 && $this->getId() < 2000;
    }
}