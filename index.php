<?php 

require_once("vendor/autoload.php");
use \Slim\Slim;
use \Lexter\Page;
use \Lexter\PageAdmin;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page(); 
	$page->setTpl("index"); //aqui ele joga o conteúdo do index

//Logo na criação do template, ele chama o construtor e joga o header na tela
//O footer ele joga quando terminar de executar a classe, pois ele está dentro do destruct
});


$app->get('/adm', function() {
    
	$page = new PageAdmin(); 
	$page->setTpl("index"); 

});
$app->run();

 ?>