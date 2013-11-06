<?php
define('DOC_ROOT', __DIR__);
define('DIR_CACHE', __DIR__.'/cache');
define('CMD_OPTYMAIZER', '/path/to/tyrant_optimize');

require_once(DOC_ROOT.'/classes/AutoLoader.php');
AutoLoader::init();
AutoLoader::register();

