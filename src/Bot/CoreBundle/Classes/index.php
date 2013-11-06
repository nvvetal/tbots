<?php

$tyrant = new Tyrant(429);

$users = array(
    'userId'    => 'flashCode'
);
foreach ($users as $userId => $flashCode) {
    $tyrant->addUser($userId, $flashCode);
}
$tyrant->setScout('100006395624275');
$tyrant->attackTile();
