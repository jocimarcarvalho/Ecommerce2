<?php
    /**
 * ROTA HOME
 */
use \Lexter\Page;
use \Lexter\Model\Category;
use \Lexter\Model\Product;

$app->get('/', function() {
	
	$products = Product::listAll();
var_dump($products);
exit;
	$page = new Page(); 
	$page->setTpl("index", [
		'products'=>$products
	]);

//Logo na criação do template, ele chama o construtor e joga o header na tela
//O footer ele joga quando terminar de executar a classe, pois ele está dentro do destruct
});


$app->get("/categories/:idcategory", function($idcategory){

	$product = new Product();

	$category = new Category();
	$category->get((int)$idcategory);
	
	 
	for($i = 0; $i < sizeof($category->getProducts()); $i++)
	{
		//$category->get((int)$category->getProducts()[$i]["idproduct"]);
		$product->get((int)$category->getProducts()[$i]["idproduct"]);
		$array[] = $product->listById((int)$product->getidproduct());
		
	}

	
	for($a = 0; $a < sizeof($array); $a++)
	{
		
		if($array[$a] != null)
		{
			$products[] = $array[$a];
		}
	
		
	}


	//$product->setData($products);
	

	
	var_dump($products);
	exit;
	

	$page = new Page();

	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>$products[1]
	]);
});


?>