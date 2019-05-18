<?php 

session_start();  //inicia a sessão

require_once("vendor/autoload.php");
use \Slim\Slim;
use \Lexter\Page;
use \Lexter\PageAdmin;
use \Lexter\Model\User;
use \Lexter\Model\Category;


$app = new Slim();

$app->config('debug', true);

require_once("functions.php");
require_once("site-rotas.php");
require_once("admin-rotas.php");
require_once("admin-user-rotas.php");
require_once("admin-categories-rotas.php");
require_once("admin-products-rotas.php");



$app->run();

 ?>