<?php

namespace Lexter\Model;

use \Lexter\DB\Sql;
use \Lexter\Model;
use Lexter\Mailer;

class Photo extends Model {



    public function checkPhoto()
    {
    
        if(file_exists(
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
    
        // $src = $url;
    
        // $this->savePhoto($this->getidproduct(), $src);
        return $this->setdesphoto($url);
    }


////////////////////////////////////////////////////////////////////////
///       SETA  A FOTO

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


//////////////////////////////////////////////////////
///            SALVA FOTO


public function savePhoto()
{
    $sql = new Sql();
    
 $src = $this->getdesphoto();

 var_dump($this-getdesphoto());
 exit;

$sql->query("INSERT INTO tb_photos (desphoto, idproduct) VALUES(:desphoto, :idproduct)", array(
    ':desphoto'=>$src,
    ':idproduct'=>$this->getidproduct()
));

}



/////////////////////////////////////////////////////////////////


public function getPhoto($id = null)
{
    $sql = new Sql();
    $photo = NULL;

    $valor =  $sql->select("SELECT desphoto FROM tb_photos WHERE idproduct = :idproduct", array(
    ':idproduct'=> $id
));

    if(isset($valor))
    {
        $photo = $valor;
    }
}

//////////////////////////////////////////////////////////////////
public function getValues(){

    $this->checkPhoto();

    $values = parent::getValues();

    return $values;
}

}

?>