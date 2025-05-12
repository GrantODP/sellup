<?php

// Load required classes (use autoloading in real projects)
require_once './backend/core/OAuth2.php';
require_once './backend/core/Responder.php';
require_once './backend/core/RequestRouter.php';
require_once './backend/config/Config.php';

Config::load('sys_config.php');


$token = OAuth2::issue('user12345');
$data = OAuth2::$tokens;
print_r($data);

echo '<br>';
echo $token;
echo '<br>';
echo '<br>';
?>