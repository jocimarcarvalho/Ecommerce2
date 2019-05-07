<?php 

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
    
	$page = new PageAdmin(); 
	$page->setTpl("index"); 

});

/**
 * ROTA DE LOGIN ADMIM
 */
$app->get('/adm/login', function() {
	
	// A página de login não precisa utilizar o header e o footer do template padrão, então temos que desa-
	//bilitá-los no construtor
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]); 
	$page->setTpl("login"); 

});


$app->post('/adm/login', function() {
	
	User::login($_POST["login"], $_POST["password"]);

	header("Location: /adm");
	exit();

});


$app->run();

 ?>