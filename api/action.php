<?php
//dependency import
require 'properties.php';
require 'lib/Slim/Slim.php';
require 'security/Security.php';

//init Slim Framework
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->add(new \Security($app));
require 'security/Login.php';
require 'security/ManageUser.php';

//resources
	//db movarisch_db
		require('./resource/movarisch_db/custom/FrasiHCustom.php');
		require('./resource/movarisch_db/FrasiH.php');
		require('./resource/movarisch_db/custom/SostanzaCustom.php');
		require('./resource/movarisch_db/Sostanza.php');
		require('./resource/movarisch_db/custom/UserCustom.php');
		require('./resource/movarisch_db/User.php');
	

$app->run();


?>
