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

function isNumberValue($value) {
	return is_int($value) || is_float($value);
}

function getValutazioneOwnerId($valutazione) {
	$fields = array(
		'User', 'user', '_user', 'userId', 'id_user',
		'Utente', 'utente', 'utenteId', 'id_utente', 'idUtente',
		'owner', 'ownerId'
	);
	foreach ($fields as $field) {
		if (isset($valutazione->$field)) {
			return $valutazione->$field;
		}
	}
	return null;
}

function validateCalcoloValutazioneRequest($body) {
	if (!is_object($body)) {
		return array('message' => 'Payload non valido');
	}

	$allowedKeys = array(
		'lavoratoreId', 'agenteChimicoId', 'metodoVersione',
		'einal', 'statoFisicoInal', 'quantitaKg', 'tipoUsoInal', 'tipoControlloInal',
		'tempoInalMin', 'distanzaM', 'ecute', 'esposizioneCutanea',
		'livelliContattoCutaneo', 'tipoControlloProc', 'quantitaProcKg', 'tempoProcMin'
	);
	$extra = validateNoAdditionalProperties($body, $allowedKeys);
	if ($extra !== null) {
		return array('message' => 'Proprieta non consentita', 'details' => array('extra' => $extra));
	}

	$required = array('lavoratoreId', 'agenteChimicoId', 'metodoVersione');
	foreach ($required as $field) {
		if (!isset($body->$field)) {
			return array('message' => 'Campo obbligatorio mancante', 'details' => array('field' => $field));
		}
		if ($body->$field === null) {
			return array('message' => 'Campo obbligatorio mancante', 'details' => array('field' => $field));
		}
	}

	if (!is_int($body->lavoratoreId)) {
		return array('message' => 'Tipo non valido', 'details' => array('field' => 'lavoratoreId', 'expected' => 'integer'));
	}
	if (!is_int($body->agenteChimicoId)) {
		return array('message' => 'Tipo non valido', 'details' => array('field' => 'agenteChimicoId', 'expected' => 'integer'));
	}
	if (!is_string($body->metodoVersione) || trim($body->metodoVersione) === '') {
		return array('message' => 'Tipo non valido', 'details' => array('field' => 'metodoVersione', 'expected' => 'string'));
	}

	$intFields = array(
		'statoFisicoInal', 'tipoUsoInal', 'tipoControlloInal',
		'tempoInalMin', 'livelliContattoCutaneo', 'tipoControlloProc', 'tempoProcMin'
	);
	foreach ($intFields as $field) {
		if (isset($body->$field) && $body->$field !== null && !is_int($body->$field)) {
			return array('message' => 'Tipo non valido', 'details' => array('field' => $field, 'expected' => 'integer'));
		}
	}

	$numberFields = array('einal', 'quantitaKg', 'distanzaM', 'ecute', 'quantitaProcKg');
	foreach ($numberFields as $field) {
		if (isset($body->$field) && $body->$field !== null && !isNumberValue($body->$field)) {
			return array('message' => 'Tipo non valido', 'details' => array('field' => $field, 'expected' => 'number'));
		}
	}

	if (isset($body->esposizioneCutanea) && $body->esposizioneCutanea !== null && !is_bool($body->esposizioneCutanea)) {
		return array('message' => 'Tipo non valido', 'details' => array('field' => 'esposizioneCutanea', 'expected' => 'boolean'));
	}

	return null;
}

function validateCalcoloValutazioneByIdRequest($body) {
	if (!is_object($body)) {
		return array('message' => 'Payload non valido');
	}

	$allowedKeys = array('metodoVersione');
	$extra = validateNoAdditionalProperties($body, $allowedKeys);
	if ($extra !== null) {
		return array('message' => 'Proprieta non consentita', 'details' => array('extra' => $extra));
	}

	if (!isset($body->metodoVersione) || $body->metodoVersione === null) {
		return array('message' => 'Campo obbligatorio mancante', 'details' => array('field' => 'metodoVersione'));
	}
	if (!is_string($body->metodoVersione) || trim($body->metodoVersione) === '') {
		return array('message' => 'Tipo non valido', 'details' => array('field' => 'metodoVersione', 'expected' => 'string'));
	}

	return null;
}

function buildCalcoloValutazioneResponse($metodoVersione) {
	return array(
		'metodoVersione' => $metodoVersione,
		'rInal' => 1.0,
		'rCute' => 1.0,
		'rischioCumulativo' => 1.0,
		'warnings' => array()
	);
}

$app->post('/calcoli/valutazioni', function () use ($app) {
	$user = ensureAuthenticatedUser($app);
	if ($user == null) {
		return;
	}

	$body = json_decode($app->request()->getBody());
	$error = validateCalcoloValutazioneRequest($body);
	if ($error != null) {
		respondApiError(
			$app,
			400,
			'VALIDATION_ERROR',
			$error['message'],
			isset($error['details']) ? $error['details'] : null
		);
		return;
	}

	// TODO: validate lavoratoreId/agenteChimicoId ownership/existence when repository is available.
	echo json_encode(buildCalcoloValutazioneResponse($body->metodoVersione));
});

$app->post('/calcoli/valutazioni/:id', function ($id) use ($app) {
	$user = ensureAuthenticatedUser($app);
	if ($user == null) {
		return;
	}

	if (!is_numeric($id) || (string)(int)$id !== (string)$id) {
		respondApiError($app, 400, 'VALIDATION_ERROR', 'Tipo non valido', array('field' => 'id', 'expected' => 'integer'));
		return;
	}

	$body = json_decode($app->request()->getBody());
	$error = validateCalcoloValutazioneByIdRequest($body);
	if ($error != null) {
		respondApiError(
			$app,
			400,
			'VALIDATION_ERROR',
			$error['message'],
			isset($error['details']) ? $error['details'] : null
		);
		return;
	}

	$tokenUserId = getTokenUserId($user);
	if ($tokenUserId == null) {
		respondApiError($app, 401, 'UNAUTHORIZED', 'Not Authorized');
		return;
	}

	$valutazione = makeQuery("SELECT * FROM valutazioni WHERE _id = :id LIMIT 1", array('id' => (int)$id), false);
	if ($valutazione == null) {
		respondApiError($app, 404, 'NOT_FOUND', 'Not found');
		return;
	}

	$ownerId = getValutazioneOwnerId($valutazione);
	if ($ownerId !== null && (string)$ownerId !== (string)$tokenUserId) {
		respondApiError($app, 404, 'NOT_FOUND', 'Not found');
		return;
	}

	// TODO: enforce ownership via valutazione -> user mapping if ownership field differs.
	echo json_encode(buildCalcoloValutazioneResponse($body->metodoVersione));
});
