<?php 
    namespace app\classes;
    use app\classes\Mensagem;
    // aula 90
    class Sessao{
        public function __construct()
        {
            if(!session_id()){
                
                session_start();
            }
        }

        public function criarSessao(string $key, mixed $valor):Sessao{
            $_SESSION[$key]=(is_array($valor) ? (object) $valor : $valor);

            return $this;
        }
        public function limparSessao(string $chave):Sessao{
            unset($_SESSION[$chave]);

            return $this;
        }
        public function carregarSessao(): ? object{
            return (object) $_SESSION;
        }
        public function checarSessao( string $chave):bool{
           return isset($_SESSION[$chave]);
        }
        public function deletarSessao():Sessao{
            session_destroy();
            return $this;
        }

        // public function verSessao( string $chave){
        //     return $_SESSION[$chave];
        // }

        public function __get($atributo)
        {
            if(!empty($_SESSION[$atributo])){
                return $_SESSION[$atributo];
            }
        }
        
        public function flash(): ?Mensagem{
            if($this->checarSessao('flash')){
                $flash= $this->flash;
                $this->limparSessao('flash');
                return $flash;
            }
            return null;
        }


    }
?>