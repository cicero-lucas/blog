<?php 
    namespace app\module;
    use app\classes\Mensagem;
    use app\classes\Sessao;
    use app\Helpers\Helpers;
use Exception;

    class UsuarioModelo extends Modelo{
        
        public function __construct()
        {
            parent:: __construct("tb_dados_usuario");
        }

        public function buscarPorEmail(string $email){
            $busca = $this->buscarColuna("emeil_u='$email' ");
            return $busca->resultado();
        }

        public function login(array $dados){

            $usuario=$this->buscarPorEmail($dados['emeil_u']);
            // var_dump($usuario);
            if(!$usuario){
                $this->mensagem->erro("os dados estão incoretos")->flash();
                return false;
                
            }else if(!Helpers::verificarSenha($dados['senha_u'],$usuario->senha_u )){
                $this->mensagem->erro("os dados estão incoretos")->flash();
                return false;
            }
           
            // coloco data e hora do loguin
            $dataHora_login=date('Y-m-d H:i:s');
            $dadosDH=['data_logui'=>$dataHora_login];
            $this->editarDados($dadosDH,"Id_dados = $usuario->Id_dados");

           
            // // criando uma sesão para salvar os dados do loguin aula 105
            (new Sessao ())->criarSessao('usuarioID',$usuario->Id_dados);
            
            $this->mensagem->sucesso(" Bem vindo {$usuario->nome_u}")->flash();

            return true;
        }

        public function cadastraUsuario(array $dados){

            $usuario=$this->buscarPorEmail($dados['emeil_u']);
            if(!empty($usuario)){
                $this->mensagem->aletar("Email já cadastrado tente recuperar a senha")->flash();
                return false;
            }else{
            $a=$this->cadastrarDados($dados);
            if($a){
                $this->mensagem->sucesso("Usuario cadastrado com sucesso")->flash();
                return true;
            }
            }
            
        }

        public function buscarUsuario($dados){
            try{
                $dadosUsuario=$this->buscarColuna('nome_u=\''.$dados["nome_u"].'\' and emeil_u= '.'\''. $dados["emeil_u"].'\''
                )->resultado();
                if($dadosUsuario){
                    $this->mensagem->sucesso("Digite sua nova senha")->flash();
                    (new Sessao ())->criarSessao('idSenha',$dadosUsuario->Id_dados);
                    return true;
                }else{
                    $this->mensagem->erro("Dados incoretos")->flash();
                }
            }catch(Exception $e){

            }

        }


        public function editarSenha($dados,$id){
            $dataHora_login=date('Y-m-d H:i:s');
            $a=$this->editarDados($dados,$id);
            if($a){
                $dadosDH=['data_atualizacao'=>$dataHora_login];
                (new UsuarioModelo())->editarDados($dadosDH, $id);
               var_dump($dadosDH);
                return true;
            }else{
                return false;
            }
        }
    
    }
?>