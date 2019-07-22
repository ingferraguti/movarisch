<?php
	require_once './db/dbmovarisch_dbManager.php';
	
/*
 * SCHEMA DB Sostanza
 * 
	{
		Identificativo: {
			type: 'String'
		},
		Nome: {
			type: 'String', 
			required : true
		},
		Score: {
			type: 'Decimal'
		},
		VLEP: {
			type: 'Boolean', 
			required : true
		},
		//RELAZIONI
		
		
		//RELAZIONI ESTERNE
		
		FrasiH: [{
			type: Schema.ObjectId,
			ref : "Sostanza"
		}],
		Sostanza: [{
			type: Schema.ObjectId,
			ref : "Miscelanonpericolosa"
		}],
		Sostanza: [{
			type: Schema.ObjectId,
			ref : "Processo"
		}],
		User: {
			type: Schema.ObjectId,
			ref : "Sostanza"
		},
		
	}
 * 
 */


//CRUD METHODS


//CRUD - CREATE


$app->post('/sostanza',	function () use ($app){

	$body = json_decode($app->request()->getBody());
	
	$params = array (
		'Identificativo'	=> isset($body->Identificativo)?$body->Identificativo:'',
		'Nome'	=> $body->Nome,
		'Score'	=> isset($body->Score)?$body->Score:'',
		'VLEP'	=> $body->VLEP,
		



		'User' => isset($body->User)?$body->User:'',
	);

	$obj = makeQuery("INSERT INTO sostanza (_id, Identificativo, Nome, Score, VLEP , User )  VALUES ( null, :Identificativo, :Nome, :Score, :VLEP , :User   )", $params, false);
    
    
	// Delete not in array
	$in = " and id_FrasiH NOT IN (:FrasiH)";
	$sql = "DELETE FROM Sostanza_FrasiH WHERE id_Sostanza=:id_Sostanza ";
		
	$params = array (
		'id_Sostanza'	=> $obj['id']
	);
	
	if (isset($body->FrasiH) && $body->FrasiH != null && sizeOf($body->FrasiH) > 0) {
		$sql = $sql.$in;
		$params['FrasiH'] = join("', '", $body->FrasiH);
	}
	
	makeQuery($sql, $params, false);
	
	
	// Get actual
	$sql="SELECT id_FrasiH FROM Sostanza_FrasiH WHERE id_Sostanza=:id";
	$params = array (
		'id'	=> $obj['id'],
	);
    $actual = makeQuery($sql, $params, false);
	$actualArray=[];
	foreach ($actual as $val) {
		array_push($actualArray, $val->id_FrasiH);
	}

	// Insert new
	if (isset($body->FrasiH)) {
    	foreach ($body->FrasiH as $id_FrasiH) {
    		if (!in_array($id_FrasiH, $actualArray)){
    			$sql = "INSERT INTO Sostanza_FrasiH (_id, id_Sostanza, id_FrasiH ) VALUES (null, :id_Sostanza, :id_FrasiH)";
    
    			$params = array (
    				'id_Sostanza'	=> $obj['id'],
    				'id_FrasiH'	=> $id_FrasiH
    			);
        		makeQuery($sql, $params, false);
    		}
    	}
	}
	
	            
	
	echo json_encode($body);
	
});
	
//CRUD - REMOVE

$app->delete('/sostanza/:id',	function ($id) use ($app){
	
	$params = array (
		'id'	=> $id,
	);

	makeQuery("DELETE FROM sostanza WHERE _id = :id LIMIT 1", $params);

});

//CRUD - FIND BY FrasiH

$app->get('/sostanza/findByFrasiH/:key',	function ($key) use ($app){	

	$params = array (
		'key'	=> $key,
	);
	makeQuery("SELECT * FROM sostanza WHERE FrasiH = :key", $params);
	
});

//CRUD - FIND BY User

$app->get('/sostanza/findByUser/:key',	function ($key) use ($app){	

	$params = array (
		'key'	=> $key,
	);
	makeQuery("SELECT * FROM sostanza WHERE User = :key", $params);
	
});

//CRUD - FIND BY VLEP

$app->get('/sostanza/findByVLEP/:key',	function ($key) use ($app){	

	$params = array (
		'key'	=> $key,
	);
	makeQuery("SELECT * FROM sostanza WHERE VLEP = :key", $params);
	
});
	
//CRUD - GET ONE
	
$app->get('/sostanza/:id',	function ($id) use ($app){
	$params = array (
		'id'	=> $id,
	);
	
	$obj = makeQuery("SELECT * FROM sostanza WHERE _id = :id LIMIT 1", $params, false);
	
	
	$list_FrasiH = makeQuery("SELECT id_FrasiH FROM Sostanza_FrasiH WHERE id_Sostanza = :id", $params, false);
	$list_FrasiH_Array=[];
	foreach ($list_FrasiH as $val) {
		array_push($list_FrasiH_Array, $val->id_FrasiH);
	}
	$obj->FrasiH = $list_FrasiH_Array;
	
	
	
	echo json_encode($obj);
	
});
	
	
//CRUD - GET LIST

$app->get('/sostanza',	function () use ($app){
	makeQuery("SELECT * FROM sostanza");
});


//CRUD - EDIT

$app->post('/sostanza/:id',	function ($id) use ($app){

	$body = json_decode($app->request()->getBody());
	
	$params = array (
		'id'	=> $id,
		'Identificativo'	    => isset($body->Identificativo)?$body->Identificativo:'',
		'Nome'	    => $body->Nome,
		'Score'	    => isset($body->Score)?$body->Score:'',
		'VLEP'	    => $body->VLEP


,
		'User'      => isset($body->User)?$body->User:'' 
	);

	$obj = makeQuery("UPDATE sostanza SET  Identificativo = :Identificativo,  Nome = :Nome,  Score = :Score,  VLEP = :VLEP  , User=:User  WHERE _id = :id LIMIT 1", $params, false);
    
	// Delete not in array
	$in = " and id_FrasiH NOT IN (:FrasiH)";
	$sql = "DELETE FROM Sostanza_FrasiH WHERE id_Sostanza=:id_Sostanza ";
	
	$params = array (
		'id_Sostanza'	=> $body->_id
	);
	
	if (isset($body->FrasiH) && $body->FrasiH != null && sizeOf($body->FrasiH) > 0) {
		$sql = $sql.$in;
		$params['FrasiH'] = join("', '", $body->FrasiH);
	}
	
	makeQuery($sql, $params, false);
	
	
	// Get actual
	$sql="SELECT id_FrasiH FROM Sostanza_FrasiH WHERE id_Sostanza=:id";
	$params = array (
		'id'	=> $body->_id,
	);
    $actual = makeQuery($sql, $params, false);
	$actualArray=[];
	foreach ($actual as $val) {
		array_push($actualArray, $val->id_FrasiH);
	}

	// Insert new
	if (isset($body->FrasiH)) {
    	foreach ($body->FrasiH as $id_FrasiH) {
    		if (!in_array($id_FrasiH, $actualArray)){
    			$sql = "INSERT INTO Sostanza_FrasiH (_id, id_Sostanza, id_FrasiH ) VALUES (null, :id_Sostanza, :id_FrasiH)";
    
    			$params = array (
    				'id_Sostanza'	=> $body->_id,
    				'id_FrasiH'	=> $id_FrasiH
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