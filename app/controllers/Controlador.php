<?php 

namespace app\controllers;

use app\classes\Mensagem;
use app\Suporte\Template;

class Controlador{

    protected Template $templete;
    
    public function __construct(string $diretorio)
    {
        $this->templete = new Template($diretorio);
    }
}

?>