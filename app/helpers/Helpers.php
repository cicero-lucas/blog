<?php 
    namespace app\Helpers;
    use app\classes\Sessao;
use Exception;

    class Helpers{

        public static function redirecinar(string $url=null){
            try{
              
            $local = ($url!=0 ? 'http://127.0.0.1/php/projetovBlog/public/'.$url:'http://127.0.0.1/php/projetovBlog/public/erro');
            header("Location: {$local}");
            exit();
            
            }catch(Exception $e){

            }
        }

        public static function url(string $url=null):string{

            $servido= filter_input(INPUT_SERVER,'SERVER_NAME');

            $ambiente=($servido =='localhost'?URL_DESENVOLVIMENTO:URL_PRODUCAO);

            if(str_starts_with($url,'/')){
                return $ambiente.$url;
            }

            return $ambiente.'/'.$url;
        }

        
        public static function slug(string $slug): string {
            // Substitui os espaços por hífens
            $slug = str_replace(' ', '-', $slug);
            // Converte os caracteres para minúsculos
            $slug = mb_strtolower($slug, 'UTF-8');
            // Remove os acentos e outros símbolos
            $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
            // Retorna o slug
            return $slug;
          }

        public static function flash(): ?string{
            // cria uma intancia de uma sessão

            $sessao= new Sessao();

            if($flash = $sessao->flash()){
                echo $flash;
            }

            return null;

        }

        public static function validarSenha(string $senha):bool{
            
            if(mb_strlen($senha)>8 && mb_strlen($senha)<=16){
                return true;
            }

            return false;
        }

        public static function criptografarSenha(string $senha):string{
            return password_hash($senha,PASSWORD_DEFAULT,['cost'=>10]);
        }

        public static function verificarSenha(string $senha, string $hashdba):bool{
            if(password_verify($senha,$hashdba)){
                return true;
            }
            return false;
        }

        function remover_espacos($string) {
           
            $string_sem_espacos = str_replace(" ", "", $string);
        
            return $string_sem_espacos;
        }
    }


?>