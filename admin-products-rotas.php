<?php


use \Lexter\PageAdmin;
use \Lexter\Model\User;
use \Lexter\Model\Product;

$app->get("/adm/products", function(){
    User::verifyLogin();

    $products = Product::listAll();

    $page = new PageAdmin();

    $page->setTpl("products", [
        "products"=>$products
    ]);
});


$app->get("/adm/products/create", function(){
    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("products-create");
});


$app->post("/adm/products/create", function(){
    User::verifyLogin();

   $product = new Product();

   $product->setData($_POST);

   $product->save();

   header("Location: /adm/products");
   exit;
});


//////////////////////////////////////////////////
//    UPDATE PRODUTO GET

$app->get("/adm/products/:idproduct", function($idproduct){
    User::verifyLogin();

   $product = new Product();
   

   $product->get((int)$idproduct);
  
  

// var_dump($product->getValues());
// exit;

   $page = new PageAdmin();


   $page->setTpl("products-update", [
       'product'=>$product->getValues(),
   ]);

});


//////////////////////////////////////////////////
//    UPDATE PRODUTO POST


$app->post("/adm/products/:idproduct", function($idproduct){
    User::verifyLogin();

   $product = new Product();

   $product->get((int)$idproduct);
  
   $product->setData($_POST);

  

   if($_FILES["file"]["name"] !== "") $product->setPhoto($_FILES["file"]);

   //$product->setPhoto($_FILES["file"]);
   $product->update();

   header("Location: /adm/products");
   exit;

});


$app->get("/adm/products/:idproduct/delete", function($idproduct){
    User::verifyLogin();

   $product = new Product();

   $product->get((int)$idproduct);

   $product->delete();

   header("Location: /adm/products");
   exit;


});


?>