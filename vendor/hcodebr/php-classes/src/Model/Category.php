<?php

namespace Lexter\Model;

use \Lexter\DB\Sql;
use \Lexter\Model;
use Lexter\Mailer;

class Category extends Model{

   
    


    public static function listAll()
    {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");

        //Cono a talela users extende a tabela persons, para buscar os dados completos temos que fazer
        // um inner join, podemos usar o USING para inserir um campo em comum.
        //Agora é só inserir a variável no template( setTpl())
    }

public function save(){
    $sql = new Sql();
       
    //os campos abaixo serão geranos automaticamente pelo setdata() na classe Model

   $result =  $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
    ":idcategory" =>$this->getcategory(),   
    "descategory"=>$this->getdescategory()
      
    ));

    //Só precisaremos da primeira linha do resultado e setaremos no proprio setData para uso futuro

    $this->setData($result[0]);

    Category::updateFile();
}


public function get($idcategory){
    $sql = new Sql();

    $results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", array(
        ":idcategory"=>$idcategory
    ));

    $this->setData($results[0]);
}

public function delete(){
    $sql = new Sql();

    $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", array(
        ':idcategory'=>$this->getidcategory()    //nesse caso não precisa passar o id pq o objeto quando foi clicado já carregou os atributos em memória, podendo se recuperados pelo $this->getcaterpy()
    ));

    Category::updateFile();
}

public function update($idcategory){
    $sql = new Sql();
  
    $sql->query("UPDATE tb_categories SET descategory = :descategory WHERE idcategory = :idcategory", array(
        ':idcategory'=>$idcategory,
        ':descategory'=>$_POST['descategory']
    ));

    Category::updateFile();
}


public static function updateFile(){
    $categories = Category::listAll();

    $html = [];

    foreach($categories as $row){
        array_push($html, '<li><a href="/categories/'. $row['idcategory'].'">'. $row['descategory']. '</a></li>');
    }

    //Arquivo que vai armazenar as categorias
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" 
    . DIRECTORY_SEPARATOR . "categories-menu.html", implode("",$html));
}



////////////////////////////////////////////////////////////////////////
//       RELACIONAMENTO PRODUTO/CATEGORIA

public function getProducts($related = true)
{
    $sql = new Sql();

  
    if($related === true){
        return $sql->select("SELECT * FROM tb_products  WHERE idproduct IN(
            SELECT  a.idproduct
            FROM tb_products a
            INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
            WHERE b.idcategory = :idcategory
          
        );
        ", [
            ':idcategory'=>$this->getidcategory(),

        ]);
    }else {
       return $sql->select("SELECT * FROM tb_products WHERE idproduct NOT IN(
        SELECT  a.idproduct
        FROM tb_products a
        INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
        WHERE b.idcategory = :idcategory
    );
        ", [
            ':idcategory'=>$this->getidcategory()
        ]);

    }
}

// "SELECT * FROM tb_products WHERE idproduct NOT IN(
//     SELECT  a.idproduct
//     FROM tb_products a
//     INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
//     WHERE b.idcategory = :idcategory

public function addProduct(Product $product)
{

    // var_dump($product->getidproduct());
    // exit;
    $sql = new Sql();

    $sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) VALUES(:idcategory, :idproduct)", [
        ':idcategory'=>$this->getidcategory(),
        ':idproduct'=>$product->getidproduct()
    ]);
}


public function removeProduct(Product $product)
{
    // var_dump($product->getidproduct());
    // exit;

    $sql = new Sql();

    $sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct", [
        ':idcategory'=>$this->getidcategory(),
        ':idproduct'=>$product->getidproduct()
    ]);
}
}
?>