<?php

namespace Lexter\Model;

use \Lexter\DB\Sql;
use \Lexter\Model;

class User extends Model{

    const SESSION = "User";

    public static function login($login, $password){

        $sql = new Sql();

        $results = $sql->select("SELECT * FROM  tb_users WHERE deslogin = :LOGIN ", array(
            ":LOGIN"=>$login
        ));

        if(count($results) === 0){
            throw new \Exception("Usuário inexistente ou senha inválidos! ");

            //A '\' é serve para a classe achar a Exception principal

        }

            $data = $results[0]; //usuario encontrado no banco
            
            // Verificar senha

            if(password_verify($password, $data["despassword"]) === true){

                $user = new User(); //Cria uma instância da própria classe

               $user->setData($data);

          ///////////////////////////////////////////////
          //                  CRIANDO A SESSÃO
          
          $_SESSION[User::SESSION] = $user->getValues();
               return $user;

            }else {
                throw new Exception("Senha inválida ou usuário inexistente");
            }
        
    }


    public static function verifyLogin($inadmin =true){

        if(!isset($_SESSION[User::SESSION])           //Se a sessão não foi definida
        || 
        !$_SESSION[User::SESSION]                     // ou se a sessão está vazia
        || 
        !(int)$_SESSION[User::SESSION]["iduser"] > 0  //ou se o id do usuario da sessão não for > 0 
        ||
        (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin    //Não é adm
        )                           
        {
            header("Location: /adm/login");   //Redireciona para a página de login

            exit;
        }
    }

    public static function logout(){
        $_SESSION[User::SESSION] = null;
    }
}
/**
 * A função password_verigfy verifica a senha digitada passada como parâmetro com o hash vindo do banco
 * 
 * Chamar os métdos manualmente como:   $user->setiduser($data["iduser"]) não é produtivo. Então na classe 
 * model criamos o método setData() que vai fazer isso dinamicamente. Ele verifica os dados q estão vindo do 
 * banco, conta quantos campos estão vindo e cria uma variável para cada campo com seus respectivos nomes
 *  e valores. O setData recebe um array com os valores.
 * 
 * $inadmin =true - > verifica se é um usuário da administração
 */
?>