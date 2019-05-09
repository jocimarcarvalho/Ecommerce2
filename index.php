<?php 

session_start();  //inicia a sessão

require_once("vendor/autoload.php");
use \Slim\Slim;
use \Lexter\Page;
use \Lexter\PageAdmin;
use \Lexter\Model\User;

$app = new Slim();

$app->config('debug', true);

/**
 * ROTA HOME
 */
$app->get('/', function() {
    
	$page = new Page(); 
	$page->setTpl("index"); //aqui ele joga o conteúdo do index

//Logo na criação do template, ele chama o construtor e joga o header na tela
//O footer ele joga quando terminar de executar a classe, pois ele está dentro do destruct
});

/**
 * ROTA HOME ADMIM
 */
$app->get('/adm', function() {
	
	User::verifyLogin();

	$page = new PageAdmin(); 
	$page->setTpl("index"); 

});

///////////////////////////////////
//        ROTA LOGIN ADM GET 

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

//////////////////////////////////////
//        ROTA LISTA USUARIO

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



$app->run();

 ?>