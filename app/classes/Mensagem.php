<?php 
    namespace app\classes;
    use app\classes\Sessao;

    class Mensagem{
        private $texto;
        private $css;

        public function __toString() {
            if (isset($this->texto)) {
            return $this->texto;
            } else {
            return "";
            }
        }
        
          

        public function sucesso(string $msg):Mensagem{
            $this->css='class="alert-success"';
            $this->texto="<p class=\"alert-success\">".$this->filtrar($msg)."<p>";
            return $this;
        }
        
        //mensagens aula 92
        public function erro(string $msg):Mensagem{
            $this->css='alert alert-danger';
            $this->texto=" <p class=\"alert-danger\">".$this->filtrar($msg)."<p>";
            return $this;
        }
        //mensagens aula 92
        public function aletar(string $msg):Mensagem{
            $this->css='alert alert-warning';
            $this->texto=" <p class=\"alert-warning\">".$this->filtrar($msg)."<p>";
            return $this;
        }

        public function informe(string $msg):Mensagem{
            $this->css='alert alert-info';
            $this->texto=" <p class=\"alert-info\">".$this->filtrar($msg)."<p>";
            return $this;
        }

        public function filtrar(string $msg){
            return filter_var($msg,FILTER_SANITIZE_SPECIAL_CHARS);
        }
        
        //mensagens aula 92
        public function flash():void{
            (new Sessao())->criarSessao('flash', $this);
        }
    }
?>