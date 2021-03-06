<?php

use \Lexter\Page;
use \Lexter\PageAdmin;
use \Lexter\Model\User;

$app->get('/adm/users', function(){
	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();

	$page->setTpl("users", array(
		"users"=>$users
	));
});


//////////////////////////////////////
//      ROTA CREATE USUARIO

$app->get('/adm/users/create', function(){
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");
});

/////////////////////////////////////////////////////////
//           ROTA DELETE USUÁRIO

$app->get('/adm/users/:iduser/delete', function($iduser){  
	User::verifyLogin();

	$user = new User();

	$user->get($iduser);

	$user->delete();

	header("Location: /adm/users");
	exit;

});

///////////////////////////////////////
//           ROTA GET  UPDATE USUARIO

$app->get('/adm/users/:iduser', function($iduser){  //o parâmetro iduser será capturado por $iduser na func
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);


	$page = new PageAdmin();

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));
});



$app->post('/adm/users/create', function(){  
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;
	
	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

		"cost"=>12

	]);

	$user->setData($_POST);

	$user->save();  //insere os dados no banco

	header("Location: /adm/users");  //redireciona para a página de usuarios
	exit;
	

});

/////////////////////////////////////////////////////
//           ROTA UPDATE POST
$app->post('/adm/users/:iduser', function($iduser){  
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /adm/users");
	exit;

});

?>