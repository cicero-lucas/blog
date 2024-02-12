<?php 
    namespace app\module;
    use app\database\Conexao;
    
    class PostModelo extends Modelo{
        
        public function __construct()
        {
            parent::__construct('tb_posts');
        }

        public function verTipos(){
            $query="SELECT * FROM tb_tipo";
            $stmt=Conexao::getInstancia()->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        public function cadastraPost($dados){
            $a=$this->cadastrarDados($dados);
            if($a){
                $this->mensagem->sucesso("dados cadastrados com sucesso")->flash();
            }else{
                $this->mensagem->erro("erro ao cadastra dados")->flash();
            }
        }

        public function buscaPosts($termos=null, $paramento=null){
            return $this->buscarColuna($termos,$paramento)->resultado(true);
           
        }

        public function verPost(string $termos,$retorno){
            return $this->buscarColuna($termos)->resultado($retorno);
        }
        public function editarPost(array $dados,int $id,$usuario){
            $this->editarDados($dados,"id_post = {$id} and fk_usuario = $usuario");
        }
        public function deletarPost(int $id){
            $this->deletarDados("id_post = $id");
            $this->mensagem->sucesso("Post deletado com sucesso")->flash();
        }

        public function like_delisike($id, $coluna){
            if($this->mlike_deslike($id,$coluna)){
                return true;
            }

            return false;
        }

        public function verpostp($slug){
            $colunas="titulo_post, texto, data_post, pdata_atualizacao, tu.nome_u";
            $tabela2=",tb_dados_usuario tu";
            $termo="link_slug = '$slug' and fk_usuario = tu.Id_dados";
            return $this->buscarColuna($termo,"",$colunas,$tabela2)->resultado();
            
        }

        public function buscaCategoria($categoria){
            $tabela2=",tb_tipo tt";
            $termo="fk_tipo = tt.id_tipo and tt.tipo='$categoria'";
            return $this->buscarColuna($termo,"","*",$tabela2)->resultado(true);
            
        }

        public function buscarImg($id){
          return $this->buscarColuna("id_post = $id",null,'tb_img_p')->resultado();

        }

    }
?>