<?php
require_once 'lib/Slim/Middleware.php';

/*
 * Authentication Middleware
 */
class Security extends \Slim\Middleware
{
   public function call()
{
    $app = $this->app;

    // CORS (allineato al frontend in dev)
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
    if ($origin === 'http://localhost:3000') {
        $app->response->headers->set('Access-Control-Allow-Origin', $origin);
        $app->response->headers->set('Vary', 'Origin');
        // Abilita solo se usi cookie/sessions dal browser:
        // $app->response->headers->set('Access-Control-Allow-Credentials', 'true');
    } else {
        $app->response->headers->set('Access-Control-Allow-Origin', $origin);
        $app->response->headers->set('Vary', 'Origin');
    }

    $app->response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $app->response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

    // Preflight: lascia passare subito
    if ($app->request()->getMethod() === 'OPTIONS') {
        $app->response->setStatus(204);
        return;
    }

    // Content type default
    $app->response->headers->set('Content-Type', 'application/json');

    // IMPORTANTISSIMO: se non chiami next, blocchi tutte le route
    $this->next->call();
}

}
?>