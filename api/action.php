<?php
//dependency import
require 'properties.php';
require 'lib/Slim/Slim.php';
require 'security/Security.php';

//init Slim Framework
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

// ---- CORS (DEV) ----
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';

// Se vuoi essere più restrittivo, metti esplicitamente:
// $origin = 'http://localhost:3000';

$app->hook('slim.before', function () use ($app, $origin) {
    $res = $app->response();
    $res->headers->set('Access-Control-Allow-Origin', $origin);
    $res->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $res->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    $res->headers->set('Access-Control-Max-Age', '86400');
});

// Catch-all preflight
$app->options('/(:name+)', function () use ($app) {
    $app->response()->setStatus(200);
});


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
