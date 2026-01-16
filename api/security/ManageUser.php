<?php
require_once './db/dbmovarisch_dbManager.php';

function validateNoAdditionalProperties($payload, $allowedKeys) {
	if (!is_object($payload)) {
		return array('payload' => 'not_object');
	}
	$extra = array();
	foreach (array_keys(get_object_vars($payload)) as $key) {
		if (!in_array($key, $allowedKeys, true)) {
			$extra[] = $key;
		}
	}
	if (count($extra) > 0) {
		return $extra;
	}
	return null;
}

function normalizeEmailValue($value) {
	if ($value === null) {
		return null;
	}
	$mail = trim((string)$value);
	if ($mail === '') {
		return null;
	}
	return strtolower($mail);
}

function isPasswordHashValue($value) {
	if (!is_string($value)) {
		return false;
	}
	return preg_match('/^(\\$2[ayb]\\$|\\$argon2)/', $value) === 1;
}

function verifyPasswordValue($plain, $stored) {
	if (isPasswordHashValue($stored)) {
		return password_verify($plain, $stored);
	}
	return hash_equals((string)$stored, (string)$plain);
}

function hashPasswordValue($plain, $stored) {
	if (isPasswordHashValue($stored)) {
		return password_hash($plain, PASSWORD_DEFAULT);
	}
	return $plain;
}
	
/*
 * SCHEMA DB User
 * 
	{
		mail: {
			type: 'String'
		},
		name: {
			type: 'String'
		},
		password: {
			type: 'String', 
			required : true
		},
		surname: {
			type: 'String'
		},
		username: {
			type: 'String', 
			required : true
		},
		//RELAZIONI
		
		
		//RELAZIONI ESTERNE
		
		
	}
 * 
 */


//CRUD METHODS


//CRUD - CREATE


$app->post('/Users/',	function () use ($app){

	$body = json_decode($app->request()->getBody());
	
	$params = array (
		'mail'	=> isset($body->mail)?$body->mail:'',
		'name'	=> isset($body->name)?$body->name:'',
		'password'	=> $body->password,
		'surname'	=> isset($body->surname)?$body->surname:'',
		'username'	=> $body->username
	);

	$user = makeQuery("INSERT INTO user (_id, mail, name, password, surname, username )  VALUES ( null, :mail, :name, :password, :surname, :username   )", $params, false);
    
    // Create Roles
    updateRoles($user['id'], $body->roles);
    
	echo json_encode($body);
});
	
//CRUD - REMOVE

$app->delete('/Users/:id',	function ($id) use ($app){
	
	$params = array (
		'id'	=> $id,
	);

	makeQuery("DELETE FROM user WHERE _id = :id LIMIT 1", $params, false);

    // Create Roles
    updateRoles($id, []);

});
	
//CRUD - GET ONE
	
$app->get('/Users/:id',	function ($id) use ($app){
	$params = array (
		'id'	=> $id,
	);
	
	$user = makeQuery("SELECT * FROM user WHERE _id = :id LIMIT 1", $params, false);
    
    // Get roles
	$roles = makeQuery( "SELECT * FROM roles WHERE _user=:id" , $params, false);
	$user->roles = [];
	foreach ($roles as $role) {
		array_push($user->roles, $role->role);
    }
    
    echo json_encode($user);
	
});
	
	
//CRUD - GET LIST

$app->get('/Users/',	function () use ($app){
    $list = makeQuery("SELECT * FROM user", [], false);
    
    foreach ($list as $user) {
        // Get roles
        $params = array (
            'id'	=> $user->_id,
        );

        $roles = makeQuery( "SELECT * FROM roles WHERE _user=:id" , $params, false);
        $user->roles = [];
        foreach ($roles as $role) {
            array_push($user->roles, $role->role);
        }
    }
    
    echo json_encode($list);

});


//CRUD - EDIT

$app->post('/Users/:id',	function ($id) use ($app){

	$body = json_decode($app->request()->getBody());
	
	$params = array (
		'id'	=> $id,
		'mail'	    => isset($body->mail)?$body->mail:'',
		'name'	    => isset($body->name)?$body->name:'',
		'surname'	    => isset($body->surname)?$body->surname:'',
	);

	$user = makeQuery("UPDATE user SET  mail = :mail,  name = :name,  surname = :surname WHERE _id = :id LIMIT 1", $params, false);
    
    // Create Roles
    updateRoles($id, $body->roles);
    
    echo json_encode($body);

});

// ADMIN USERS (TO-BE)
$app->get('/admin/users',	function () use ($app){
	$admin = ensureAdminUser($app);
	if ($admin == null) {
		return;
	}

	$list = makeQuery("SELECT * FROM user", [], false);
	foreach ($list as $user) {
		$params = array(
			'id'	=> $user->_id,
		);
		$roles = makeQuery("SELECT * FROM roles WHERE _user=:id", $params, false);
		$user->roles = [];
		foreach ($roles as $role) {
			array_push($user->roles, $role->role);
		}
	}

	$normalized = [];
	foreach ($list as $user) {
		array_push($normalized, normalizeUserResponse($user));
	}
	echo json_encode($normalized);
});

$app->post('/admin/users',	function () use ($app){
	$admin = ensureAdminUser($app);
	if ($admin == null) {
		return;
	}

	$body = json_decode($app->request()->getBody());
	if ($body == null || !isset($body->username) || !isset($body->password) || !isset($body->roles)) {
		respondApiError($app, 400, 'VALIDATION_ERROR', 'Campo obbligatorio mancante');
		return;
	}

	$params = array(
		'mail'	=> isset($body->mail)?$body->mail:'',
		'name'	=> isset($body->name)?$body->name:'',
		'password'	=> $body->password,
		'surname'	=> isset($body->surname)?$body->surname:'',
		'username'	=> $body->username
	);

	$user = makeQuery("INSERT INTO user (_id, mail, name, password, surname, username )  VALUES ( null, :mail, :name, :password, :surname, :username   )", $params, false);
	updateRoles($user['id'], $body->roles);

	$created = fetchUserWithRolesById($user['id']);
	if ($created == null) {
		respondApiError($app, 500, 'SERVER_ERROR', 'Unexpected error');
		return;
	}

	$app->response()->status(201);
	echo json_encode(normalizeUserResponse($created));
});

$app->get('/admin/users/:id',	function ($id) use ($app){
	$admin = ensureAdminUser($app);
	if ($admin == null) {
		return;
	}

	$user = fetchUserWithRolesById($id);
	if ($user == null) {
		respondApiError($app, 404, 'NOT_FOUND', 'Not found');
		return;
	}
	echo json_encode(normalizeUserResponse($user));
});

$app->patch('/admin/users/:id',	function ($id) use ($app){
	$admin = ensureAdminUser($app);
	if ($admin == null) {
		return;
	}

	$body = json_decode($app->request()->getBody());
	$params = array(
		'id'	=> $id,
		'mail'	    => isset($body->mail)?$body->mail:'',
		'name'	    => isset($body->name)?$body->name:'',
		'surname'	    => isset($body->surname)?$body->surname:'',
	);

	makeQuery("UPDATE user SET  mail = :mail,  name = :name,  surname = :surname WHERE _id = :id LIMIT 1", $params, false);

	if (isset($body->roles)) {
		updateRoles($id, $body->roles);
	} else {
		$existing = fetchUserWithRolesById($id);
		if ($existing == null) {
			respondApiError($app, 404, 'NOT_FOUND', 'Not found');
			return;
		}
		updateRoles($id, $existing->roles);
	}

	$updated = fetchUserWithRolesById($id);
	if ($updated == null) {
		respondApiError($app, 404, 'NOT_FOUND', 'Not found');
		return;
	}
	echo json_encode(normalizeUserResponse($updated));
});

$app->delete('/admin/users/:id',	function ($id) use ($app){
	$admin = ensureAdminUser($app);
	if ($admin == null) {
		return;
	}

	$params = array(
		'id'	=> $id,
	);

	makeQuery("DELETE FROM user WHERE _id = :id LIMIT 1", $params, false);
	updateRoles($id, []);

	$app->response()->status(204);
});

// USER SELF (TO-BE)
$app->patch('/users/me',	function () use ($app){
	$tokenUser = ensureAuthenticatedUser($app);
	if ($tokenUser == null) {
		return;
	}
	$userId = getTokenUserId($tokenUser);
	if ($userId == null) {
		respondApiError($app, 401, 'UNAUTHORIZED', 'Not Authorized');
		return;
	}

	$body = json_decode($app->request()->getBody());
	if ($body == null) {
		respondApiError($app, 400, 'VALIDATION_ERROR', 'Payload non valido');
		return;
	}
	$allowed = array('mail', 'name', 'surname');
	$extra = validateNoAdditionalProperties($body, $allowed);
	if ($extra !== null) {
		respondApiError($app, 400, 'VALIDATION_ERROR', 'Proprieta non consentita', array('extra' => $extra));
		return;
	}

	$existing = makeQuery("SELECT * FROM user WHERE _id = :id LIMIT 1", array('id' => $userId), false);
	if ($existing == null) {
		respondApiError($app, 404, 'NOT_FOUND', 'Not found');
		return;
	}

	$mail = isset($body->mail) ? normalizeEmailValue($body->mail) : (isset($existing->mail) ? $existing->mail : null);
	if (isset($body->mail) && $mail !== null && !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
		respondApiError($app, 400, 'VALIDATION_ERROR', 'Email non valida');
		return;
	}

	if ($mail !== null) {
		$check = makeQuery(
			"SELECT _id FROM user WHERE mail = :mail AND _id <> :id LIMIT 1",
			array('mail' => $mail, 'id' => $userId),
			false
		);
		if ($check != null) {
			respondApiError($app, 409, 'CONFLICT', 'Email gia in uso');
			return;
		}
	}

	$params = array(
		'id'	=> $userId,
		'mail'	    => $mail,
		'name'	    => isset($body->name)?$body->name:$existing->name,
		'surname'	    => isset($body->surname)?$body->surname:$existing->surname,
	);

	makeQuery("UPDATE user SET  mail = :mail,  name = :name,  surname = :surname WHERE _id = :id LIMIT 1", $params, false);

	$user = fetchUserWithRolesById($userId);
	if ($user == null) {
		respondApiError($app, 404, 'NOT_FOUND', 'Not found');
		return;
	}
	echo json_encode(normalizeUserResponse($user));
});

$app->post('/users/me/change-password',	function () use ($app){
	$tokenUser = ensureAuthenticatedUser($app);
	if ($tokenUser == null) {
		return;
	}
	$userId = getTokenUserId($tokenUser);
	if ($userId == null) {
		respondApiError($app, 401, 'UNAUTHORIZED', 'Not Authorized');
		return;
	}

	$body = json_decode($app->request()->getBody());
	if ($body == null) {
		respondApiError($app, 400, 'VALIDATION_ERROR', 'Payload non valido');
		return;
	}
	$allowed = array('oldPassword', 'newPassword');
	$extra = validateNoAdditionalProperties($body, $allowed);
	if ($extra !== null) {
		respondApiError($app, 400, 'VALIDATION_ERROR', 'Proprieta non consentita', array('extra' => $extra));
		return;
	}
	if ($body == null || !isset($body->oldPassword) || !isset($body->newPassword)) {
		respondApiError($app, 400, 'VALIDATION_ERROR', 'Campo obbligatorio mancante');
		return;
	}
	if (strlen($body->newPassword) < 8) {
		respondApiError($app, 400, 'VALIDATION_ERROR', 'Password non valida');
		return;
	}

	$params = array(
		'id'	=> $userId,
	);
	$user = makeQuery("SELECT * FROM user WHERE _id = :id LIMIT 1", $params, false);
	if ($user == null || !isset($user->password) || !verifyPasswordValue($body->oldPassword, $user->password)) {
		respondApiError($app, 400, 'VALIDATION_ERROR', 'Password non valida');
		return;
	}

	$params = array(
		'id'	=> $userId,
		'password'	=> hashPasswordValue($body->newPassword, $user->password),
	);
	makeQuery("UPDATE user SET password = :password WHERE _id = :id LIMIT 1", $params, false);

	$app->response()->status(204);
});

// Utils functions
function updateRoles($id_user, $roles) {
    
    // Remove roles not present
    $rolesStr = "";
    $first = true;
    foreach ($roles as $role) {
        if ($first) {
            $first = false;
        } else {
            $rolesStr = $rolesStr.',';
        }
        $rolesStr = $rolesStr."'".$role."'";
    }

	$params = array (
        'id_user'	=> $id_user,
    );

	$in = " and \"role\" NOT IN ( ".$rolesStr." )";
	$sql = "DELETE FROM Roles WHERE _user=:id_user ";
    
    if ($roles != null && count($roles) > 0)
        $sql = $sql.$in;

    $del = makeQuery($sql, $params, false);    

    // Get actual roles
    $actual_roles_obj = makeQuery("SELECT * from Roles WHERE \"_user\"=:id_user", $params, false);
    $actual_roles = [];
    foreach ($actual_roles_obj as $role) {
        array_push($actual_roles, $role->role);
    }

    // Insert new
    foreach ($roles as $role) {
        if (!in_array($role, $actual_roles)) {
            $params['role'] = $role;
            makeQuery("INSERT INTO Roles (_id, role, _user) VALUES (null, :role, :id_user)", $params, false);
        }
    }

}

/*
 * CUSTOM SERVICES
 *
 */

			
?>
