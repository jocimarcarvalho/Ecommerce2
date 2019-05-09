<?php

namespace Lexter;

class PageAdmin extends Page {

    public function __construct($opts = array(), $tpl_dir = "/views/admin/"){
    
    //Como essa classe extende a classe Page, ela pode usar os métodos da mesma. Então podemos usar o parâmetro
    //'parent' passando o construtor da  classe mãe e os parâmetros desta classe

    parent:: __construct($opts, $tpl_dir);
    
    }

}
?>