<?php
	require_once './db/dbmovarisch_dbManager.php';

function getSostanzaFrasiHIds($id) {
	$params = array(
		'id' => $id,
	);
	$list = makeQuery("SELECT id_FrasiH FROM Sostanza_FrasiH WHERE id_Sostanza = :id", $params, false);
	$ids = [];
	foreach ($list as $item) {
		array_push($ids, $item->id_FrasiH);
	}
	return $ids;
}

function getMiscelanonpericolosaSostanzeIds($id) {
	$params = array(
		'id' => $id,
	);
	$list = makeQuery("SELECT id_Sostanza FROM Miscelanonpericolosa_Sostanza WHERE id_Miscelanonpericolosa = :id", $params, false);
	$ids = [];
	foreach ($list as $item) {
		array_push($ids, $item->id_Sostanza);
	}
	return $ids;
}

function getProcessoSostanzeIds($id) {
	$params = array(
		'id' => $id,
	);
	$list = makeQuery("SELECT id_Sostanza FROM Processo_Sostanza WHERE id_Processo = :id", $params, false);
	$ids = [];
	foreach ($list as $item) {
		array_push($ids, $item->id_Sostanza);
	}
	return $ids;
}

function syncSostanzaFrasiHIds($id, $frasiHIds) {
	$in = " and id_FrasiH NOT IN (:FrasiH)";
	$sql = "DELETE FROM Sostanza_FrasiH WHERE id_Sostanza=:id_Sostanza ";

	$params = array(
		'id_Sostanza' => $id,
	);

	if (isset($frasiHIds) && $frasiHIds != null && sizeOf($frasiHIds) > 0) {
		$sql = $sql.$in;
		$params['FrasiH'] = join("', '", $frasiHIds);
	}

	makeQuery($sql, $params, false);

	$actual = makeQuery("SELECT id_FrasiH FROM Sostanza_FrasiH WHERE id_Sostanza=:id", array('id' => $id), false);
	$actualArray = [];
	foreach ($actual as $val) {
		array_push($actualArray, $val->id_FrasiH);
	}

	if (isset($frasiHIds)) {
		foreach ($frasiHIds as $id_FrasiH) {
			if (!in_array($id_FrasiH, $actualArray)) {
				$params = array(
					'id_Sostanza' => $id,
					'id_FrasiH' => $id_FrasiH
				);
				makeQuery("INSERT INTO Sostanza_FrasiH (_id, id_Sostanza, id_FrasiH ) VALUES (null, :id_Sostanza, :id_FrasiH)", $params, false);
			}
		}
	}
}

function syncMiscelanonpericolosaSostanzeIds($id, $sostanzeIds) {
	$in = " and id_Sostanza NOT IN (:Sostanza)";
	$sql = "DELETE FROM Miscelanonpericolosa_Sostanza WHERE id_Miscelanonpericolosa=:id_Miscelanonpericolosa ";

	$params = array(
		'id_Miscelanonpericolosa' => $id,
	);

	if (isset($sostanzeIds) && $sostanzeIds != null && sizeOf($sostanzeIds) > 0) {
		$sql = $sql.$in;
		$params['Sostanza'] = join("', '", $sostanzeIds);
	}

	makeQuery($sql, $params, false);

	$actual = makeQuery("SELECT id_Sostanza FROM Miscelanonpericolosa_Sostanza WHERE id_Miscelanonpericolosa=:id", array('id' => $id), false);
	$actualArray = [];
	foreach ($actual as $val) {
		array_push($actualArray, $val->id_Sostanza);
	}

	if (isset($sostanzeIds)) {
		foreach ($sostanzeIds as $id_Sostanza) {
			if (!in_array($id_Sostanza, $actualArray)) {
				$params = array(
					'id_Miscelanonpericolosa' => $id,
					'id_Sostanza' => $id_Sostanza
				);
				makeQuery("INSERT INTO Miscelanonpericolosa_Sostanza (_id, id_Miscelanonpericolosa, id_Sostanza ) VALUES (null, :id_Miscelanonpericolosa, :id_Sostanza)", $params, false);
			}
		}
	}
}

function syncProcessoSostanzeIds($id, $sostanzeIds) {
	$in = " and id_Sostanza NOT IN (:Sostanza)";
	$sql = "DELETE FROM Processo_Sostanza WHERE id_Processo=:id_Processo ";

	$params = array(
		'id_Processo' => $id,
	);

	if (isset($sostanzeIds) && $sostanzeIds != null && sizeOf($sostanzeIds) > 0) {
		$sql = $sql.$in;
		$params['Sostanza'] = join("', '", $sostanzeIds);
	}

	makeQuery($sql, $params, false);

	$actual = makeQuery("SELECT id_Sostanza FROM Processo_Sostanza WHERE id_Processo=:id", array('id' => $id), false);
	$actualArray = [];
	foreach ($actual as $val) {
		array_push($actualArray, $val->id_Sostanza);
	}

	if (isset($sostanzeIds)) {
		foreach ($sostanzeIds as $id_Sostanza) {
			if (!in_array($id_Sostanza, $actualArray)) {
				$params = array(
					'id_Processo' => $id,
					'id_Sostanza' => $id_Sostanza
				);
				makeQuery("INSERT INTO Processo_Sostanza (_id, id_Processo, id_Sostanza ) VALUES (null, :id_Processo, :id_Sostanza)", $params, false);
			}
		}
	}
}

function buildAgenteChimicoBase($id, $nome, $identificativo, $vlep, $alta_emissione, $tipo, $frasiHIds, $sostanzeComponentiIds) {
	$result = array(
		'id' => $id,
		'nome' => $nome,
		'identificativo' => $identificativo,
		'vlep' => (bool)$vlep,
		'alta_emissione' => (bool)$alta_emissione,
		'tipo' => $tipo,
		'frasiHIds' => $frasiHIds
	);

	if ($sostanzeComponentiIds != null) {
		$result['sostanzeComponentiIds'] = $sostanzeComponentiIds;
	}

	return $result;
}

function buildAgenteChimicoFromSostanza($sostanza) {
	$frasiHIds = getSostanzaFrasiHIds($sostanza->_id);
	return buildAgenteChimicoBase(
		$sostanza->_id,
		$sostanza->Nome,
		isset($sostanza->Identificativo) ? $sostanza->Identificativo : '',
		isset($sostanza->VLEP) ? $sostanza->VLEP : false,
		false,
		'sostanza',
		$frasiHIds,
		null
	);
}

function buildAgenteChimicoFromMiscelanonpericolosa($miscela) {
	$sostanzeIds = getMiscelanonpericolosaSostanzeIds($miscela->_id);
	return buildAgenteChimicoBase(
		$miscela->_id,
		$miscela->Nome,
		'',
		false,
		false,
		'miscelaNP',
		[],
		$sostanzeIds
	);
}

function buildAgenteChimicoFromProcesso($processo) {
	$sostanzeIds = getProcessoSostanzeIds($processo->_id);
	return buildAgenteChimicoBase(
		$processo->_id,
		$processo->Nome,
		'',
		false,
		isset($processo->AltaEmissione) ? $processo->AltaEmissione : false,
		'processo',
		[],
		$sostanzeIds
	);
}

function validateAgenteChimicoCreate($body) {
	if ($body == null || !isset($body->tipo)) {
		return 'Campo obbligatorio mancante';
	}

	if (!isset($body->nome) || !isset($body->identificativo) || !isset($body->vlep) || !isset($body->alta_emissione)) {
		return 'Campo obbligatorio mancante';
	}

	if ($body->tipo === 'miscelaP' || $body->tipo === 'miscelaNP' || $body->tipo === 'processo') {
		if (!isset($body->sostanzeComponentiIds) || !is_array($body->sostanzeComponentiIds)) {
			return 'Campo obbligatorio mancante';
		}
	}

	return null;
}

$app->get('/agenti-chimici',	function () use ($app){
	$user = ensureAuthenticatedUser($app);
	if ($user == null) {
		return;
	}

	$result = [];

	$sostanze = makeQuery("SELECT * FROM sostanza", [], false);
	foreach ($sostanze as $sostanza) {
		array_push($result, buildAgenteChimicoFromSostanza($sostanza));
	}

	$miscele = makeQuery("SELECT * FROM miscelanonpericolosa", [], false);
	foreach ($miscele as $miscela) {
		array_push($result, buildAgenteChimicoFromMiscelanonpericolosa($miscela));
	}

	$processi = makeQuery("SELECT * FROM processo", [], false);
	foreach ($processi as $processo) {
		array_push($result, buildAgenteChimicoFromProcesso($processo));
	}

	echo json_encode($result);
});

$app->post('/agenti-chimici',	function () use ($app){
	$user = ensureAuthenticatedUser($app);
	if ($user == null) {
		return;
	}

	$body = json_decode($app->request()->getBody());
	$validationError = validateAgenteChimicoCreate($body);
	if ($validationError != null) {
		respondApiError($app, 400, 'VALIDATION_ERROR', $validationError);
		return;
	}

	if ($body->tipo === 'sostanza') {
		$params = array(
			'Identificativo' => $body->identificativo,
			'Nome' => $body->nome,
			'Score' => '',
			'VLEP' => $body->vlep,
			'User' => ''
		);
		$obj = makeQuery("INSERT INTO sostanza (_id, Identificativo, Nome, Score, VLEP , User )  VALUES ( null, :Identificativo, :Nome, :Score, :VLEP , :User   )", $params, false);

		if (isset($body->frasiHIds) && is_array($body->frasiHIds)) {
			syncSostanzaFrasiHIds($obj['id'], $body->frasiHIds);
		}

		$sostanza = makeQuery("SELECT * FROM sostanza WHERE _id = :id LIMIT 1", array('id' => $obj['id']), false);
		$app->response()->status(201);
		echo json_encode(buildAgenteChimicoFromSostanza($sostanza));
		return;
	}

	if ($body->tipo === 'miscelaP' || $body->tipo === 'miscelaNP') {
		$params = array(
			'Nome' => $body->nome,
			'Score' => ''
		);
		$obj = makeQuery("INSERT INTO miscelanonpericolosa (_id, Nome, Score )  VALUES ( null, :Nome, :Score   )", $params, false);

		syncMiscelanonpericolosaSostanzeIds($obj['id'], $body->sostanzeComponentiIds);

		$miscela = makeQuery("SELECT * FROM miscelanonpericolosa WHERE _id = :id LIMIT 1", array('id' => $obj['id']), false);
		$response = buildAgenteChimicoFromMiscelanonpericolosa($miscela);
		$response['tipo'] = $body->tipo;
		$app->response()->status(201);
		echo json_encode($response);
		return;
	}

	if ($body->tipo === 'processo') {
		$params = array(
			'AltaEmissione' => $body->alta_emissione,
			'Nome' => $body->nome
		);
		$obj = makeQuery("INSERT INTO processo (_id, AltaEmissione, Nome )  VALUES ( null, :AltaEmissione, :Nome   )", $params, false);

		syncProcessoSostanzeIds($obj['id'], $body->sostanzeComponentiIds);

		$processo = makeQuery("SELECT * FROM processo WHERE _id = :id LIMIT 1", array('id' => $obj['id']), false);
		$app->response()->status(201);
		echo json_encode(buildAgenteChimicoFromProcesso($processo));
		return;
	}

	respondApiError($app, 400, 'VALIDATION_ERROR', 'Tipo non valido');
});

$app->get('/agenti-chimici/:id',	function ($id) use ($app){
	$user = ensureAuthenticatedUser($app);
	if ($user == null) {
		return;
	}

	$params = array('id' => $id);

	$sostanza = makeQuery("SELECT * FROM sostanza WHERE _id = :id LIMIT 1", $params, false);
	if ($sostanza != null) {
		echo json_encode(buildAgenteChimicoFromSostanza($sostanza));
		return;
	}

	$miscela = makeQuery("SELECT * FROM miscelanonpericolosa WHERE _id = :id LIMIT 1", $params, false);
	if ($miscela != null) {
		echo json_encode(buildAgenteChimicoFromMiscelanonpericolosa($miscela));
		return;
	}

	$processo = makeQuery("SELECT * FROM processo WHERE _id = :id LIMIT 1", $params, false);
	if ($processo != null) {
		echo json_encode(buildAgenteChimicoFromProcesso($processo));
		return;
	}

	respondApiError($app, 404, 'NOT_FOUND', 'Not found');
});

$app->patch('/agenti-chimici/:id',	function ($id) use ($app){
	$user = ensureAuthenticatedUser($app);
	if ($user == null) {
		return;
	}

	$body = json_decode($app->request()->getBody());
	$params = array('id' => $id);

	$sostanza = makeQuery("SELECT * FROM sostanza WHERE _id = :id LIMIT 1", $params, false);
	if ($sostanza != null) {
		$params = array(
			'id' => $id,
			'Identificativo' => isset($body->identificativo) ? $body->identificativo : $sostanza->Identificativo,
			'Nome' => isset($body->nome) ? $body->nome : $sostanza->Nome,
			'Score' => '',
			'VLEP' => isset($body->vlep) ? $body->vlep : $sostanza->VLEP,
			'User' => isset($sostanza->User) ? $sostanza->User : ''
		);
		makeQuery("UPDATE sostanza SET  Identificativo = :Identificativo,  Nome = :Nome,  Score = :Score,  VLEP = :VLEP  , User=:User  WHERE _id = :id LIMIT 1", $params, false);

		if (isset($body->frasiHIds) && is_array($body->frasiHIds)) {
			syncSostanzaFrasiHIds($id, $body->frasiHIds);
		}

		$updated = makeQuery("SELECT * FROM sostanza WHERE _id = :id LIMIT 1", array('id' => $id), false);
		echo json_encode(buildAgenteChimicoFromSostanza($updated));
		return;
	}

	$miscela = makeQuery("SELECT * FROM miscelanonpericolosa WHERE _id = :id LIMIT 1", $params, false);
	if ($miscela != null) {
		$params = array(
			'id' => $id,
			'Nome' => isset($body->nome) ? $body->nome : $miscela->Nome,
			'Score' => ''
		);
		makeQuery("UPDATE miscelanonpericolosa SET  Nome = :Nome,  Score = :Score   WHERE _id = :id LIMIT 1", $params, false);

		if (isset($body->sostanzeComponentiIds) && is_array($body->sostanzeComponentiIds)) {
			syncMiscelanonpericolosaSostanzeIds($id, $body->sostanzeComponentiIds);
		}

		$updated = makeQuery("SELECT * FROM miscelanonpericolosa WHERE _id = :id LIMIT 1", array('id' => $id), false);
		echo json_encode(buildAgenteChimicoFromMiscelanonpericolosa($updated));
		return;
	}

	$processo = makeQuery("SELECT * FROM processo WHERE _id = :id LIMIT 1", $params, false);
	if ($processo != null) {
		$params = array(
			'id' => $id,
			'AltaEmissione' => isset($body->alta_emissione) ? $body->alta_emissione : $processo->AltaEmissione,
			'Nome' => isset($body->nome) ? $body->nome : $processo->Nome
		);
		makeQuery("UPDATE processo SET  AltaEmissione = :AltaEmissione,  Nome = :Nome   WHERE _id = :id LIMIT 1", $params, false);

		if (isset($body->sostanzeComponentiIds) && is_array($body->sostanzeComponentiIds)) {
			syncProcessoSostanzeIds($id, $body->sostanzeComponentiIds);
		}

		$updated = makeQuery("SELECT * FROM processo WHERE _id = :id LIMIT 1", array('id' => $id), false);
		echo json_encode(buildAgenteChimicoFromProcesso($updated));
		return;
	}

	respondApiError($app, 404, 'NOT_FOUND', 'Not found');
});

$app->delete('/agenti-chimici/:id',	function ($id) use ($app){
	$user = ensureAuthenticatedUser($app);
	if ($user == null) {
		return;
	}

	$params = array('id' => $id);

	$sostanza = makeQuery("SELECT * FROM sostanza WHERE _id = :id LIMIT 1", $params, false);
	if ($sostanza != null) {
		makeQuery("DELETE FROM sostanza WHERE _id = :id LIMIT 1", $params, false);
		makeQuery("DELETE FROM Sostanza_FrasiH WHERE id_Sostanza = :id", $params, false);
		$app->response()->status(204);
		return;
	}

	$miscela = makeQuery("SELECT * FROM miscelanonpericolosa WHERE _id = :id LIMIT 1", $params, false);
	if ($miscela != null) {
		makeQuery("DELETE FROM miscelanonpericolosa WHERE _id = :id LIMIT 1", $params, false);
		makeQuery("DELETE FROM Miscelanonpericolosa_Sostanza WHERE id_Miscelanonpericolosa = :id", $params, false);
		$app->response()->status(204);
		return;
	}

	$processo = makeQuery("SELECT * FROM processo WHERE _id = :id LIMIT 1", $params, false);
	if ($processo != null) {
		makeQuery("DELETE FROM processo WHERE _id = :id LIMIT 1", $params, false);
		makeQuery("DELETE FROM Processo_Sostanza WHERE id_Processo = :id", $params, false);
		$app->response()->status(204);
		return;
	}

	respondApiError($app, 404, 'NOT_FOUND', 'Not found');
});

?>
