<?php
	require_once './db/dbmovarisch_dbManager.php';
	
/*
 * SCHEMA DB Processo
 * 
	{
		AltaEmissione: {
			type: 'Boolean', 
			required : true
		},
		Nome: {
			type: 'String', 
			required : true
		},
		//RELAZIONI
		
		
		//RELAZIONI ESTERNE
		
		Sostanza: [{
			type: Schema.ObjectId,
			ref : "Processo"
		}],
		
	}
 * 
 */


//CRUD METHODS


//CRUD - CREATE


$app->post('/processo',	function () use ($app){

	$body = json_decode($app->request()->getBody());
	
	$params = array (
		'AltaEmissione'	=> $body->AltaEmissione,
		'Nome'	=> $body->Nome,
		
	);

	$obj = makeQuery("INSERT INTO processo (_id, AltaEmissione, Nome )  VALUES ( null, :AltaEmissione, :Nome   )", $params, false);
    
    
	// Delete not in array
	$in = " and id_Sostanza NOT IN (:Sostanza)";
	$sql = "DELETE FROM Processo_Sostanza WHERE id_Processo=:id_Processo ";
		
	$params = array (
		'id_Processo'	=> $obj['id']
	);
	
	if (isset($body->Sostanza) && $body->Sostanza != null && sizeOf($body->Sostanza) > 0) {
		$sql = $sql.$in;
		$params['Sostanza'] = join("', '", $body->Sostanza);
	}
	
	makeQuery($sql, $params, false);
	
	
	// Get actual
	$sql="SELECT id_Sostanza FROM Processo_Sostanza WHERE id_Processo=:id";
	$params = array (
		'id'	=> $obj['id'],
	);
    $actual = makeQuery($sql, $params, false);
	$actualArray=[];
	foreach ($actual as $val) {
		array_push($actualArray, $val->id_Sostanza);
	}

	// Insert new
	if (isset($body->Sostanza)) {
    	foreach ($body->Sostanza as $id_Sostanza) {
    		if (!in_array($id_Sostanza, $actualArray)){
    			$sql = "INSERT INTO Processo_Sostanza (_id, id_Processo, id_Sostanza ) VALUES (null, :id_Processo, :id_Sostanza)";
    
    			$params = array (
    				'id_Processo'	=> $obj['id'],
    				'id_Sostanza'	=> $id_Sostanza
    			);
        		makeQuery($sql, $params, false);
    		}
    	}
	}
	
	
	
	echo json_encode($body);
	
});
	
//CRUD - REMOVE

$app->delete('/processo/:id',	function ($id) use ($app){
	
	$params = array (
		'id'	=> $id,
	);

	makeQuery("DELETE FROM processo WHERE _id = :id LIMIT 1", $params);

});

//CRUD - FIND BY Nome

$app->get('/processo/findByNome/:key',	function ($key) use ($app){	

	$params = array (
		'key'	=> $key,
	);
	makeQuery("SELECT * FROM processo WHERE Nome = :key", $params);
	
});
	
//CRUD - GET ONE
	
$app->get('/processo/:id',	function ($id) use ($app){
	$params = array (
		'id'	=> $id,
	);
	
	$obj = makeQuery("SELECT * FROM processo WHERE _id = :id LIMIT 1", $params, false);
	
	
	$list_Sostanza = makeQuery("SELECT id_Sostanza FROM Processo_Sostanza WHERE id_Processo = :id", $params, false);
	$list_Sostanza_Array=[];
	foreach ($list_Sostanza as $val) {
		array_push($list_Sostanza_Array, $val->id_Sostanza);
	}
	$obj->Sostanza = $list_Sostanza_Array;
	
	
	
	echo json_encode($obj);
	
});
	
	
//CRUD - GET LIST

$app->get('/processo',	function () use ($app){
	makeQuery("SELECT * FROM processo");
});


//CRUD - EDIT

$app->post('/processo/:id',	function ($id) use ($app){

	$body = json_decode($app->request()->getBody());
	
	$params = array (
		'id'	=> $id,
		'AltaEmissione'	    => $body->AltaEmissione,
		'Nome'	    => $body->Nome
	);

	$obj = makeQuery("UPDATE processo SET  AltaEmissione = :AltaEmissione,  Nome = :Nome   WHERE _id = :id LIMIT 1", $params, false);
    
	// Delete not in array
	$in = " and id_Sostanza NOT IN (:Sostanza)";
	$sql = "DELETE FROM Processo_Sostanza WHERE id_Processo=:id_Processo ";
	
	$params = array (
		'id_Processo'	=> $body->_id
	);
	
	if (isset($body->Sostanza) && $body->Sostanza != null && sizeOf($body->Sostanza) > 0) {
		$sql = $sql.$in;
		$params['Sostanza'] = join("', '", $body->Sostanza);
	}
	
	makeQuery($sql, $params, false);
	
	
	// Get actual
	$sql="SELECT id_Sostanza FROM Processo_Sostanza WHERE id_Processo=:id";
	$params = array (
		'id'	=> $body->_id,
	);
    $actual = makeQuery($sql, $params, false);
	$actualArray=[];
	foreach ($actual as $val) {
		array_push($actualArray, $val->id_Sostanza);
	}

	// Insert new
	if (isset($body->Sostanza)) {
    	foreach ($body->Sostanza as $id_Sostanza) {
    		if (!in_array($id_Sostanza, $actualArray)){
    			$sql = "INSERT INTO Processo_Sostanza (_id, id_Processo, id_Sostanza ) VALUES (null, :id_Processo, :id_Sostanza)";
    
    			$params = array (
    				'id_Processo'	=> $body->_id,
    				'id_Sostanza'	=> $id_Sostanza
    			);
        		makeQuery($sql, $params, false);
    		}
    	}
	}
	
	
	
	echo json_encode($body);
    	
});


/*
 * CUSTOM SERVICES
 *
 *	These services will be overwritten and implemented in  Custom.js
 */

			
?>