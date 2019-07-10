<?php
	require_once './db/dbmovarisch_dbManager.php';
	
/*
 * SCHEMA DB FrasiH
 * 
	{
		Codice: {
			type: 'String', 
			required : true
		},
		Descrizione: {
			type: 'String', 
			required : true
		},
		Punteggio: {
			type: 'Decimal', 
			required : true
		},
		//RELAZIONI
		
		
		//RELAZIONI ESTERNE
		
		FrasiH: [{
			type: Schema.ObjectId,
			ref : "Sostanza"
		}],
		
	}
 * 
 */


//CRUD METHODS


//CRUD - CREATE


$app->post('/frasih',	function () use ($app){

	$body = json_decode($app->request()->getBody());
	
	$params = array (
		'Codice'	=> $body->Codice,
		'Descrizione'	=> $body->Descrizione,
		'Punteggio'	=> $body->Punteggio,
		
	);

	$obj = makeQuery("INSERT INTO frasih (_id, Codice, Descrizione, Punteggio )  VALUES ( null, :Codice, :Descrizione, :Punteggio   )", $params, false);
    
	
	echo json_encode($body);
	
});
	
//CRUD - REMOVE

$app->delete('/frasih/:id',	function ($id) use ($app){
	
	$params = array (
		'id'	=> $id,
	);

	makeQuery("DELETE FROM frasih WHERE _id = :id LIMIT 1", $params);

});
	
//CRUD - GET ONE
	
$app->get('/frasih/:id',	function ($id) use ($app){
	$params = array (
		'id'	=> $id,
	);
	
	$obj = makeQuery("SELECT * FROM frasih WHERE _id = :id LIMIT 1", $params, false);
	
	
	
	echo json_encode($obj);
	
});
	
	
//CRUD - GET LIST

$app->get('/frasih',	function () use ($app){
	makeQuery("SELECT * FROM frasih");
});


//CRUD - EDIT

$app->post('/frasih/:id',	function ($id) use ($app){

	$body = json_decode($app->request()->getBody());
	
	$params = array (
		'id'	=> $id,
		'Codice'	    => $body->Codice,
		'Descrizione'	    => $body->Descrizione,
		'Punteggio'	    => $body->Punteggio
	);

	$obj = makeQuery("UPDATE frasih SET  Codice = :Codice,  Descrizione = :Descrizione,  Punteggio = :Punteggio   WHERE _id = :id LIMIT 1", $params, false);
    
	
	echo json_encode($body);
    	
});


/*
 * CUSTOM SERVICES
 *
 *	These services will be overwritten and implemented in  Custom.js
 */

			
?>