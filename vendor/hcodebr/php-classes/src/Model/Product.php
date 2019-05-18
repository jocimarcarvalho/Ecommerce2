<?php

namespace Lexter\Model;

use \Lexter\DB\Sql;
use \Lexter\Model;
use Lexter\Mailer;

class Product extends Model{

   
    


    public static function listAll()
    {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_products  ORDER BY desproduct");

        //Cono a talela users extende a tabela persons, para buscar os dados completos temos que fazer
        // um inner join, podemos usar o USING para inserir um campo em comum.
        //Agora é só inserir a variável no template( setTpl())
    }

    public static function listAllWithPhoto()
    {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_products a INNER JOIN tb_photos b USING(idproduct) WHERE  a.idproduct = b.idproduct ORDER BY desproduct");

        //Cono a talela users extende a tabela persons, para buscar os dados completos temos que fazer
        // um inner join, podemos usar o USING para inserir um campo em comum.
        //Agora é só inserir a variável no template( setTpl())
    }


    public static function listById($id)
    {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_products a INNER JOIN tb_photos b USING(idproduct) WHERE  a.idproduct = b.idproduct AND a.idproduct = :id ORDER BY desproduct", [':id'=>$id]);

        //Cono a talela users extende a tabela persons, para buscar os dados completos temos que fazer
        // um inner join, podemos usar o USING para inserir um campo em comum.
        //Agora é só inserir a variável no template( setTpl())
    }

public function save(){
    $sql = new Sql();
    
    //os campos abaixo serão geranos automaticamente pelo setdata() na classe Model

//    $result =  $sql->select("CALL sp_products_save(:idproduct :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(  
//     ":idproduct"=>$this->getidproduct(),
//     ":desproduct"=>$this->getdesproduct(),
//     ":vlprice"=>$this->getvlprice(),
//     ":vlwidth"=>$this->getvlwidth(),
//      ":vlheight"=>$this->getvlheight(),
//      ":vllength"=>$this->getvllength(),
//      ":vlweight"=>$this->getvlweight(),
//      ":desurl"=>$this->getdesurl()
      
//     ));

 //Só precisaremos da primeira linha do resultado e setaremos no proprio setData para uso futuro
//$this->setData($result[0]);

    $sql->query("INSERT INTO tb_products (desproduct, vlprice, vlwidth, vlheight, vllength, vlweight, desurl) VALUES (:desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(
        ':desproduct'=>$this->getdesproduct(),
        ':vlprice'=>$this->getvlprice(),
        ':vlwidth'=>$this->getvlwidth(),
        ':vlheight'=>$this->getvlheight(),
        ':vllength'=>$this->getvllength(),
        ':vlweight'=>$this->getvlweight(),
        ':desurl'=>$this->getdesurl() 
    ));

    //Só precisaremos da primeira linha do resultado e setaremos no proprio setData para uso futuro
   
    
   
}


public function update()
{
    $sql = new Sql();

    
    $sql->query("UPDATE tb_products SET desproduct = :desproduct, vlprice = :vlprice, vlwidth = :vlwidth, vlheight = :vlheight, vllength = :vllength, vlweight = :vlweight, desurl = :desurl, desphoto = :desphoto)", array(
        ':desproduct'=>$this->getdesproduct(),
        ':vlprice'=>$this->getvlprice(),
        ':vlwidth'=>$this->getvlwidth(),
        ':vlheight'=>$this->getvlheight(),
        ':vllength'=>$this->getvllength(),
        ':vlweight'=>$this->getvlweight(),
        ':desurl'=>$this->getdesurl(),
        ':desphoto'=>$this->getdesphoto()
    )); 

   
}


public function get($idproduct){
    $sql = new Sql();

    $results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", array(
        ':idproduct'=>$idproduct
    ));
   
    $this->setData($results[0]);
}

public function delete(){
    $sql = new Sql();

    $sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", array(
        ':idproduct'=>$this->getidproduct()    //nesse caso não precisa passar o id pq o objeto quando foi clicado já carregou os atributos em memória, podendo se recuperados pelo $this->getcaterpy()
    ));

}

public function checkPhoto()
{

    if(!file_exists(
        $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
        "res" . DIRECTORY_SEPARATOR . 
        "site" . DIRECTORY_SEPARATOR . 
        "img" . DIRECTORY_SEPARATOR . 
        "products" . DIRECTORY_SEPARATOR . 
        $this->getidProduct() . "jpg"
    )) {
        $url =  "/res/site/img/products/" . $this->getidproduct() . "jpg";
    } else {
        $url =  "/res/site/img/product.jpg";
    }

    
    $this->setdesphoto($url);
   
}


// public function savePhoto()
// {
//     $sql = new Sql();
    
//  $src = $this->getdesphoto();

//  var_dump($this-getdesphoto());
//  exit;

// $sql->query("INSERT INTO tb_photos (desphoto, idproduct) VALUES(:desphoto, :idproduct)", array(
//     ':desphoto'=>$src,
//     ':idproduct'=>$this->getidproduct()
// ));

// }

// public function getPhoto($id = null)
// {
//     $sql = new Sql();
//     $photo = NULL;

//     $valor =  $sql->select("SELECT desphoto FROM tb_photos WHERE idproduct = :idproduct", array(
//     ':idproduct'=> $id
// ));

// if(isset($valor))
// {
//     $photo = $valor;
// }
// }

public function getValues(){

    $this->checkPhoto();

    $values = parent::getValues();

    return $values;
}


public function setPhoto($file)

{


    $extension = explode('.', $file['name']);
    $extension = end($extension);

   
   

    switch($extension)
    {
        case "jpg":
        case "jpeg":
        $image = imagecreatefromjpeg($file['tmp_name']);
        break;

        case "gif":
        $image = imagecreatefromgif($file['tmp_name']);
        break;

        case "png":
        $image = imagecreatefrompng($file['tmp_name']);
        break;

    }

    $dist =  $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
             "res" . DIRECTORY_SEPARATOR . 
            "site" . DIRECTORY_SEPARATOR . 
            "img" . DIRECTORY_SEPARATOR . 
            "products" . DIRECTORY_SEPARATOR . 
            $this->getidproduct() . "jpg";

         
    imagejpeg($image, $dist);      //Salva a imagem escolhida acima no local '$dist' em formato 'jpg'

    imagedestroy($image);          //fecha o arquivo temporário de criação de imagem

    $this->checkPhoto();           //carrega a foto na descrição
}
// public function update($idproduct){
//     $sql = new Sql();
  
//     $sql->query("UPDATE tb_products SET(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, vlweight :desurl", array(
//         ':idcategory'=>$idcategory,
//         ':descategory'=>$_POST['descategory']
//     ));
//}

}
?>