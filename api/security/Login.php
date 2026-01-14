<?php

require_once './db/dbmovarisch_dbManager.php';

require './lib/JWT/JWT.php';

use \Firebase\JWT\JWT;

$app = new \Slim\Slim();

function getAuthorizationHeader() {
	if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
		return trim($_SERVER['HTTP_AUTHORIZATION']);
	}
	if (isset($_SERVER['Authorization'])) {
		return trim($_SERVER['Authorization']);
	}
	if (function_exists('apache_request_headers')) {
		$headers = apache_request_headers();
		foreach ($headers as $key => $value) {
			if (strtolower($key) === 'authorization') {
				return trim($value);
			}
		}
	}
	return null;
}

function getBearerTokenFromRequest() {
	$header = getAuthorizationHeader();
	if (!$header) {
		return null;
	}
	if (preg_match('/Bearer\s+(\S+)/i', $header, $matches)) {
		return $matches[1];
	}
	return null;
}

function decodeJwtToken($token) {
	if (!$token) {
		return null;
	}
	global $jwt_secret_key;
	try {
		return JWT::decode($token, $jwt_secret_key, array('HS256'));
	} catch (Exception $err) {
		return null;
	}
}

function getAuthenticatedUserFromRequest() {
	$token = getBearerTokenFromRequest();
	return decodeJwtToken($token);
}

function respondApiError($app, $status, $code, $message) {
	$app->response()->status($status);
	echo json_encode(array('code' => $code, 'message' => $message));
}

function ensureAuthenticatedUser($app) {
	$user = getAuthenticatedUserFromRequest();
	if ($user == null) {
		respondApiError($app, 401, 'UNAUTHORIZED', 'Not Authorized');
		return null;
	}
	return $user;
}

function isAdminUser($user) {
	return isset($user->roles) && is_array($user->roles) && in_array('ADMIN', $user->roles);
}

function ensureAdminUser($app) {
	$user = ensureAuthenticatedUser($app);
	if ($user == null) {
		return null;
	}
	if (!isAdminUser($user)) {
		respondApiError($app, 403, 'FORBIDDEN', 'Forbidden');
		return null;
	}
	return $user;
}

function getTokenUserId($user) {
	if (isset($user->_id)) {
		return $user->_id;
	}
	if (isset($user->id)) {
		return $user->id;
	}
	return null;
}

function fetchUserWithRolesById($id) {
	$params = array(
		'id' => $id,
	);
	$user = makeQuery("SELECT * FROM user WHERE _id = :id LIMIT 1", $params, false);
	if ($user == null) {
		return null;
	}
	$roles = makeQuery("SELECT * FROM roles WHERE _user=:id", $params, false);
	$user->roles = array();
	foreach ($roles as $role) {
		array_push($user->roles, $role->role);
	}
	return $user;
}

function normalizeUserResponse($user) {
	$id = null;
	if (isset($user->id)) {
		$id = $user->id;
	} elseif (isset($user->_id)) {
		$id = $user->_id;
	}
	$now = date('c');
	$mail = isset($user->mail) && $user->mail !== '' ? $user->mail : null;
	$name = isset($user->name) && $user->name !== '' ? $user->name : null;
	$surname = isset($user->surname) && $user->surname !== '' ? $user->surname : null;
	$roles = isset($user->roles) && is_array($user->roles) ? $user->roles : array();

	return (object) array(
		'id' => $id,
		'username' => isset($user->username) ? $user->username : null,
		'mail' => $mail,
		'name' => $name,
		'surname' => $surname,
		'roles' => $roles,
		'isActive' => true,
		'createdAt' => $now,
		'updatedAt' => $now,
	);
}

function buildTokenResponse($user, $token) {
	return array(
		'accessToken' => $token,
		'tokenType' => 'Bearer',
		'expiresIn' => 3600,
		'user' => $user,
	);
}

function authenticateUserByCredentials($username, $password) {
	$params = array(
		'username' => $username,
		'password' => $password,
	);
	$user = makeQuery("SELECT * FROM user WHERE username=:username AND password=:password LIMIT 1", $params, false);
	if ($user == null) {
		return null;
	}

	$user->password = null;

	$params = array(
		'user_id' => $user->_id
	);
	$roles = makeQuery("SELECT * FROM roles WHERE _user=:user_id", $params, false);
	$user->roles = array();
	foreach ($roles as $role) {
		array_push($user->roles, $role->role);
	}

	global $jwt_secret_key;
	$token = JWT::encode($user, $jwt_secret_key);

	return array($user, $token);
}


// Login Action
$app->post('/login',	function () use ($app){

	$body = json_decode($app->request()->getBody());
	
	$result = authenticateUserByCredentials(
		isset($body->username) ? $body->username : '',
		isset($body->password) ? $body->password : ''
	);
	if ($result == null) {
		$app->response()->status(401);
		echo '{ "message": "Not Authorized" }';
		return;
	}

	list($user, $token) = $result;
	$user->token = $token;

	// Print result
	echo json_encode($user);

});

// Login (TO-BE)
$app->post('/auth/login',	function () use ($app){

	$body = json_decode($app->request()->getBody());
	$result = authenticateUserByCredentials(
		isset($body->username) ? $body->username : '',
		isset($body->password) ? $body->password : ''
	);
	if ($result == null) {
		respondApiError($app, 401, 'UNAUTHORIZED', 'Not Authorized');
		return;
	}

	list($user, $token) = $result;
	$normalized = normalizeUserResponse($user);
	echo json_encode(buildTokenResponse($normalized, $token));

});

// Return authenticated user (TO-BE)
$app->get('/auth/me',	function () use ($app){
	$tokenUser = ensureAuthenticatedUser($app);
	if ($tokenUser == null) {
		return;
	}
	$userId = getTokenUserId($tokenUser);
	if ($userId == null) {
		respondApiError($app, 401, 'UNAUTHORIZED', 'Not Authorized');
		return;
	}
	$user = fetchUserWithRolesById($userId);
	if ($user == null) {
		respondApiError($app, 404, 'NOT_FOUND', 'Not found');
		return;
	}
	echo json_encode(normalizeUserResponse($user));
});

// Verify Token action
$app->post('/verifyToken',	function () use ($app) {
	$body = json_decode($app->request()->getBody());
	
	// Token non provided
	if (!isset($body->token)) {
		$app->response()->status(403);
		echo '{ "success": false, "message": "No token provided"}';
		return;
	}
	
	// Decode token
	$token = $body->token;
	global $jwt_secret_key;

	try {
		$decoded = JWT::decode($token, $jwt_secret_key, array('HS256'));
	} catch (Exception $err) {
		// Token not valid
		$app->response()->status(401);
		echo '{ "success": false, "message": "Failed to authenticate token"}';
		return;
	}

	// Token valid
	echo json_encode($decoded);

});

?>
