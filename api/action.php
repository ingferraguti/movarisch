<?php

// --- CORS / Preflight (DEV) ---
// Risponde ai preflight OPTIONS prima di Slim (evita 404 routing/middleware)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

    // In dev mettiamo esplicito il frontend
    if ($origin === 'http://localhost:3000') {
        header("Access-Control-Allow-Origin: $origin");
        header("Vary: Origin");
        // abilita credenziali SOLO se ti servono cookie/sessions
        // header("Access-Control-Allow-Credentials: true");
    } else {
        header("Access-Control-Allow-Origin: $origin");
        header("Vary: Origin");
    }

    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Max-Age: 86400");
    http_response_code(204); // No Content (va benissimo per preflight)
    exit;
}


// PHP 8+: polyfill per Slim 2 (get_magic_quotes_gpc rimossa)
if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc() { return false; }
}


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
/*
$app->hook('slim.before', function () use ($app, $origin) {
    $res = $app->response();
    $res->headers->set('Access-Control-Allow-Origin', $origin);
    $res->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $res->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    $res->headers->set('Access-Control-Max-Age', '86400');
});*/

// --- CORS headers sulle risposte normali ---
$app->hook('slim.before', function () use ($app) {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    if ($origin === 'http://localhost:3000') {
        $app->response->headers->set('Access-Control-Allow-Origin', $origin);
        $app->response->headers->set('Vary', 'Origin');
        // $app->response->headers->set('Access-Control-Allow-Credentials', 'true'); // solo se ti serve
    } else {
        $app->response->headers->set('Access-Control-Allow-Origin', $origin);
        $app->response->headers->set('Vary', 'Origin');
    }
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
