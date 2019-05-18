<?php

/**
 * ROTA HOME ADMIM
 */

use \Lexter\PageAdmin;
use \Lexter\Page;
use \Lexter\Model\User;

$app->get('/adm', function() {
	
	User::verifyLogin();

	$page = new PageAdmin(); 
	$page->setTpl("index"); 

});


$app->get('/adm/login', function() {
	
	// A página de login não precisa utilizar o header e o footer do template padrão, então temos que desa-
	//bilitá-los no construtor
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]); 
	$page->setTpl("login"); 

});
///////////////////////////////////
//        ROTA LOGIN ADM POST

$app->post('/adm/login', function() {
	
	User::login($_POST["login"], $_POST["password"]);

	header("Location: /adm");
	exit;

});

////////////////////////////////////
//      ROTA LOGOUT

$app->get('/adm/logout', function() {
	
	User::logout();

	header("Location: /adm/login");
	exit;

});


////////////////////////////////////////////////
//         ROTA GET FORGOT

$app->get("/adm/forgot", function(){
	
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]); 
	$page->setTpl("forgot"); 
});


////////////////////////////////////////////////////
//        ROTA POST FORGOT

$app->post("/adm/forgot", function(){

	$user = User::getForgot($_POST["email"]);

	header("Location: /adm/forgot/sent");
	exit;
});


$app->get("/adm/forgot/sent", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-sent");
	
});


$app->get("/adm/forgot/reset", function(){
	$user = User::validForgotDecrypt($_GET["code"]);
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
});


$app->post("/adm/forgot/reset", function(){
	$forgot = User::validForgotDecrypt($_POST["code"]);
	
	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=> 12
	]);

	$user->setPassword($password); //senha criptografada sendo passada

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");
});
?>