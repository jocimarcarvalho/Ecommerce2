<?php
/**
 * Esta classe conterá os geters e seters que as classes de modelo precisarem
 */
namespace Lexter;

class Model {

private $values = [];

//Esta funcçãoirá dizer se o método é get ou set, ele recebe o nome do método que é passado por $user
public function __call($name, $args){

    $method = substr($name, 0, 3);      

    $fieldName = substr($name, 3, strlen($name));  //pega o nome do campo - da pos 3 até o final
    
    switch($method){
        case "get":
            return $this->values[$fieldName];
        break;

        case "set":
            $this->values[$fieldName] = $args[0];
        break;
    }
    
}

public function setData($data = array())
{
    foreach($data as $key=>$value){
        $this->{"set".$key}($value);
    }
}

public function getValues(){
    return $this->values;
}

}

/**
 * Ex: Se o médodo for setiduser a váriável $name receberá esse nome.
 * A variável $method receberá o conteúdo das posições 0, 1 e 2 de $name q no caso é 'set'.
 * A variável $fildName ficará com o restante q vai da pos 3 até o final. 
 * 
 * A variável $args receberá o valor que for passado no argumento, tipo, uma id = 1
 * 
 * No switch -  se for 'get' retorna o valor que está no campo e se for 'set', seta o valor que está vindo 
 * na variável $args no campo.
 * 
 * No método setData, como os campos sets ainda não existem ele devem ser criados dinamicamente, para criá-los
 * devemos usar as chaves e em seu interior o prefixo 'set' concatenando com a chave q contem o nome do campo 
 * e depois o valor entre parênteses.
 */

?>