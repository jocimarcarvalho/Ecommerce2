<?php

namespace Lexter;

use Rain\Tpl;

class Page {
    private $tpl;

    private $options = [];
    private $defaults = [
        "header"=>true,
        "footer"=>true,
        "data"=>[]
    ];

    //O construtor será chamado assim que a classe for instanciada
    public function __construct($opts = array(), $tpl_dir = "\/views\/"){

        $this->options = array_merge($this->defaults, $opts); // Se vier algo no parâametro, sobrecreve o padrão
        
        //o array_merge vai meclar as informações do dois arrays e guardar no options
        //Esse método construct também é usado pela classe PageAdmin, por herança. Então en seu construtor foi
        //criado uma variável que recebe o diretório a ser apontando quando uma das classes chamar.
        // Se a classe PageAdmin chamar, ela passa o parâmetro dela, caso contrário o método usará por padrão
        //o caminho das views.

        // config
	    $config = array(
                        "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"] . $tpl_dir,
                         "cache_dir"     => $_SERVER["DOCUMENT_ROOT"] ."\/views-cache\/",
                          "debug"         => false // set to false to improve the speed
                 );

        Tpl::configure( $config );

        $this->tpl = new Tpl;

        $this->setData($this->options["data"] );

        if ($this->options["header"] === true) $this->tpl->draw("header");
         //será criado em todas as páginas caso o 'header' contenha a flag 'true' - estará na pasta 'view'

    }


    ///////////////////////////////////////////////////////////////
    //Médoto responsável por inserir os dados no template

    private function setData($data = array())

    {
        foreach($data as $key => $value)
            {
                $this->tpl->assign($key, $value);
            } 

    }

/////////////////////////////////////////////////////////////////////
    //Este médotod renderiza o corpo da página, q no nosso caso é o conteúdo do índex

    public function setTpl($name, $data = array(), $returnHTML = false)
    {
        $this->setData($data); //seta os dados no template
       return  $this->tpl->draw($name, $returnHTML);//recebe o nome do template para renderizar

       //o return é para a necessidade de utilizá-lo em outro lugar
    }

    ////////////////////////////////////////////////////////////////////
    // Assim que a classe terminar a execução o método destruct será chamado icluirá o 
    //o footer no template

    public function __destruct(){

        if($this->options["footer"] === true) $this->tpl->draw("footer"); 

        //Carrega o footer caso a opção 'footer' seja passado no construtor com a flag 'true'
    }
}
?>