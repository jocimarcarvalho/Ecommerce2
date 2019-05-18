<?php

namespace Lexter\Model;

use \Lexter\DB\Sql;
use \Lexter\Model;
use Lexter\Mailer;

class User extends Model{

    const SESSION = "User";
    const SECRET = "LexterPHP_Secret";  //chave que vai cirptografar e descriptografar o link

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


    public static function listAll()
    {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");

        //Cono a talela users extende a tabela persons, para buscar os dados completos temos que fazer
        // um inner join, podemos usar o USING para inserir um campo em comum.
        //Agora é só inserir a variável no template( setTpl())
    }


    public function save()
    {
        $sql = new Sql();
       
        //os campos abaixo serão geranos automaticamente pelo setdata() na classe Model

       $result =  $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            "desperson"=>$this->getdesperson(),
            "deslogin"=>$this->getdeslogin(),
            "despassword"=>$this->getdespassword(),
            "desemail"=>$this->getdesemail(),
            "nrphone"=>$this->getnrphone(),
            "inadmin"=>$this->getinadmin()
        ));
      
        //Só precisaremos da primeira linha do resultado e setaremos no proprio setData para uso futuro

        $this->setData($result);
    }


    public function get($iduser)
    {
        $sql = new Sql();

        $results  = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
            ":iduser"=>$iduser
        ));

        $this->setData($results[0]);
    }


    public function update()
    {
        $sql = new Sql();
       
        //os campos abaixo serão geranos automaticamente pelo setdata() na classe Model

       $result =  $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            "iduser"=>$this->getiduser(),
            "desperson"=>$this->getdesperson(),
            "deslogin"=>$this->getdeslogin(),
            "despassword"=>$this->getdespassword(),
            "desemail"=>$this->getdesemail(),
            "nrphone"=>$this->getnrphone(),
            "inadmin"=>$this->getinadmin()
        )); 
        
        $this->setData($result[0]);
    }


    public function delete()
    {
        $sql = new Sql();

        $sql->query("CALL sp_users_delete(:iduser)", array(
            ":iduser"=>$this->getiduser()
        ));
    }



    public static function getForgot($email){

        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) 
        WHERE  a.desemail = :email", array(":email"=>$email));
    

        if(count($results) === 0)           //Verificando se o email digitado existe
        {
            throw new \Exception("Não foi possível recuperar a senha 1");
        }else{

            $data = $results[0];

            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
                ":iduser"=>$data["iduser"],
                ":desip"=>$_SERVER["REMOTE_ADDR"]
            ));

            // Verificando e o results2 foi criado

            if(count($results2) === 0)           //Verificando se o email digitado existe
            {
                throw new \Exception("Não foi possível recuperar a senha");
            }else {
                $dataRecovery = $results2[0];
                
                //A procedure retorna um idrecovery. Então temos que pegar esse id, criptografá-lo
                //e enviar para o email do usuário como um link de recuperação de senha.
                //O link vai chegar para o usuário em base64. 

                $code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET,
                                        $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));

                                       

                //Link que será enviado para o usuário com o código de redefiniçãode senha

                $link = "http://www.sitelexter.com.br/adm/forgot/reset?code=$code";

                $mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir senha da JC Imports", "forgot", 
                        array(
                            "name"=>$data["desperson"],
                            "link"=>$link
                        ));

                $mailer->send();

                return $data; 
            }
        }
    }



    public static function validForgotDecrypt($code){
        $base64d = base64_decode($code);

        $idrecovery = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $base64d, MCRYPT_MODE_ECB);

        $sql = new Sql();

        $results = $sql->select("SELECT * 
        FROM  tb_userspasswordsrecoveries a 
        INNER JOIN tb_users b USING(iduser) 
        INNER JOIN tb_persons c USING(idperson) 
        WHERE 
        a.idrecovery = :idrecovery 
        AND 
        a.dtrecovery IS NULL 
        AND 
        DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();",
        array(
            ":idrecovery"=>$idrecovery
        ));

        if(count($results) === 0)
        {
            throw new \Exception("Não foi possível recuperar a senha!");
        }else
            {
                 return $results[0];
            }
    }


    public static function setForgotUsed($idrecovery)
    {
        $sql = new Sql();
        $sql->query("UPDATE rb_userspasswordsrecoveries SET dtrecovery = NOW()  WHERE idrecovrey = :idrecovery",
        array(
            ":idrecovery"=>$idrecovery
        ));
    }


    public function setPassword($password)
    {
        $sql = new Sql();

        $sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser",
        array(
            ":password"=>$password,
            ":iduser"=>$this->getiduser()
        ));
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