<?php
require_once(__DIR__.'/config.php');

$tyrant = new Tyrant(<syestemId>);

$users = array(
);
foreach ($users as $userId => $flashCode) {
    $tyrant->addUser($userId, $flashCode);
}
$tyrant->setScout('<scoutUserId>');
$tyrant->attackTile();
