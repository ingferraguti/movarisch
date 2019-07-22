<?php
	require_once './db/dbmovarisch_dbManager.php';
	
/*
 * SCHEMA DB Miscelanonpericolosa
 * 
	{
		Nome: {
			type: 'String', 
			required : true
		},
		Score: {
			type: 'Decimal'
		},
		//RELAZIONI
		
		
		//RELAZIONI ESTERNE
		
		Sostanza: [{
			type: Schema.ObjectId,
			ref : "Miscelanonpericolosa"
		}],
		
	}
 * 
 */


//CRUD METHODS


//CRUD - CREATE


$app->post('/miscelanonpericolosa',	function () use ($app){

	$body = json_decode($app->request()->getBody());
	
	$params = array (
		'Nome'	=> $body->Nome,
		'Score'	=> isset($body->Score)?$body->Score:'',
		
	);

	$obj = makeQuery("INSERT INTO miscelanonpericolosa (_id, Nome, Score )  VALUES ( null, :Nome, :Score   )", $params, false);
    
    
	// Delete not in array
	$in = " and id_Sostanza NOT IN (:Sostanza)";
	$sql = "DELETE FROM Miscelanonpericolosa_Sostanza WHERE id_Miscelanonpericolosa=:id_Miscelanonpericolosa ";
		
	$params = array (
		'id_Miscelanonpericolosa'	=> $obj['id']
	);
	
	if (isset($body->Sostanza) && $body->Sostanza != null && sizeOf($body->Sostanza) > 0) {
		$sql = $sql.$in;
		$params['Sostanza'] = join("', '", $body->Sostanza);
	}
	
	makeQuery($sql, $params, false);
	
	
	// Get actual
	$sql="SELECT id_Sostanza FROM Miscelanonpericolosa_Sostanza WHERE id_Miscelanonpericolosa=:id";
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
    			$sql = "INSERT INTO Miscelanonpericolosa_Sostanza (_id, id_Miscelanonpericolosa, id_Sostanza ) VALUES (null, :id_Miscelanonpericolosa, :id_Sostanza)";
    
    			$params = array (
    				'id_Miscelanonpericolosa'	=> $obj['id'],
    				'id_Sostanza'	=> $id_Sostanza
    			);
        		makeQuery($sql, $params, false);
    		}
    	}
	}
	
	
	
	echo json_encode($body);
	
});
	
//CRUD - REMOVE

$app->delete('/miscelanonpericolosa/:id',	function ($id) use ($app){
	
	$params = array (
		'id'	=> $id,
	);

	makeQuery("DELETE FROM miscelanonpericolosa WHERE _id = :id LIMIT 1", $params);

});

//CRUD - FIND BY Nome

$app->get('/miscelanonpericolosa/findByNome/:key',	function ($key) use ($app){	

	$params = array (
		'key'	=> $key,
	);
	makeQuery("SELECT * FROM miscelanonpericolosa WHERE Nome = :key", $params);
	
});

//CRUD - FIND BY Sostanza

$app->get('/miscelanonpericolosa/findBySostanza/:key',	function ($key) use ($app){	

	$params = array (
		'key'	=> $key,
	);
	makeQuery("SELECT * FROM miscelanonpericolosa WHERE Sostanza = :key", $params);
	
});
	
//CRUD - GET ONE
	
$app->get('/miscelanonpericolosa/:id',	function ($id) use ($app){
	$params = array (
		'id'	=> $id,
	);
	
	$obj = makeQuery("SELECT * FROM miscelanonpericolosa WHERE _id = :id LIMIT 1", $params, false);
	
	
	$list_Sostanza = makeQuery("SELECT id_Sostanza FROM Miscelanonpericolosa_Sostanza WHERE id_Miscelanonpericolosa = :id", $params, false);
	$list_Sostanza_Array=[];
	foreach ($list_Sostanza as $val) {
		array_push($list_Sostanza_Array, $val->id_Sostanza);
	}
	$obj->Sostanza = $list_Sostanza_Array;
	
	
	
	echo json_encode($obj);
	
});
	
	
//CRUD - GET LIST

$app->get('/miscelanonpericolosa',	function () use ($app){
	makeQuery("SELECT * FROM miscelanonpericolosa");
});


//CRUD - EDIT

$app->post('/miscelanonpericolosa/:id',	function ($id) use ($app){

	$body = json_decode($app->request()->getBody());
	
	$params = array (
		'id'	=> $id,
		'Nome'	    => $body->Nome,
		'Score'	    => isset($body->Score)?$body->Score:''
	);

	$obj = makeQuery("UPDATE miscelanonpericolosa SET  Nome = :Nome,  Score = :Score   WHERE _id = :id LIMIT 1", $params, false);
    
	// Delete not in array
	$in = " and id_Sostanza NOT IN (:Sostanza)";
	$sql = "DELETE FROM Miscelanonpericolosa_Sostanza WHERE id_Miscelanonpericolosa=:id_Miscelanonpericolosa ";
	
	$params = array (
		'id_Miscelanonpericolosa'	=> $body->_id
	);
	
	if (isset($body->Sostanza) && $body->Sostanza != null && sizeOf($body->Sostanza) > 0) {
		$sql = $sql.$in;
		$params['Sostanza'] = join("', '", $body->Sostanza);
	}
	
	makeQuery($sql, $params, false);
	
	
	// Get actual
	$sql="SELECT id_Sostanza FROM Miscelanonpericolosa_Sostanza WHERE id_Miscelanonpericolosa=:id";
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
    			$sql = "INSERT INTO Miscelanonpericolosa_Sostanza (_id, id_Miscelanonpericolosa, id_Sostanza ) VALUES (null, :id_Miscelanonpericolosa, :id_Sostanza)";
    
    			$params = array (
    				'id_Miscelanonpericolosa'	=> $body->_id,
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