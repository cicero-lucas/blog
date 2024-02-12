<?php 
    namespace app\controllers;
    use app\controllers\Controlador;
    use app\classes\Mensagem;
    use app\Helpers\Helpers;
    use app\module\UsuarioModelo;
    use app\module\PostModelo;
    use app\classes\Sessao;

    class siteControlador extends Controlador {

        private ?Mensagem $mensagem=null;

        public function __construct()
        {
            parent::__construct('../app/views/site/');
        }

        public function index():void{

            echo $this->templete->renderizar('index.html',[
                'titulo'=>'Blog Tecnologia',
                'id_usuario'=>(new Sessao())->__get('usuarioID'),
                'posts'=>(new PostModelo())->buscaPosts(),
                'tipos'=>(new PostModelo())->verTipos()
            ]);
            
        }
        public function erro():void{

            $this->mensagem=(new Mensagem())->aletar("resultado não encontrado !")->flash();

            echo $this->templete->renderizar('erro404.html',[
                'titulo'=>'Pagina não encontrada',
                'id_usuario'=>(new Sessao())->__get('usuarioID')
            ]);
            
        }

        public function buscar():void{

            $busca=filter_input(INPUT_POST,'busca',FILTER_SANITIZE_SPECIAL_CHARS);
            $this->mensagem=(new Mensagem())->aletar("resultado não encontrado !");
            if(!empty($busca)){
                $dados=(new PostModelo())->buscaPosts(" titulo_post LIKE '%$busca%'");
                if(empty($dados)){
                    $this->mensagem->flash();
                }
                   
            }else{
                Helpers::redirecinar('./');
            }

            echo $this->templete->renderizar('buscas.html',[
                'titulo'=>'buscar',
                'id_usuario'=>(new Sessao())->__get('usuarioID'),
                'dados'=>[
                    'posts'=>$dados
                ]
            ]);
        }

        
        public function buscarCategoria($categoria):void{
            
            echo $this->templete->renderizar('buscas.html',[
                'titulo'=>'buscar',
                'dados'=>[
                    'posts'=>(new PostModelo())->buscaCategoria($categoria),
                    'id_usuario'=>(new Sessao())->__get('usuarioID')
                ]
            ]);
        }
        public function sobre():void{
            echo $this->templete->renderizar('sobre.html',[
                'titulo'=>'Sobre o Blog Tecnologia',
                'id_usuario'=>(new Sessao())->__get('usuarioID')
            
            ]);
            
        }
        public function login():void{
            
            $email=filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
            $senha=filter_input(INPUT_POST,'senha',FILTER_SANITIZE_SPECIAL_CHARS);


            if(($email==null || $email=="") ||($senha==null || $senha=="")){
                $this->mensagem=(new Mensagem())->informe("Coloque os dados corretos");
                $this->mensagem->flash();

            }else{
                $dados=["emeil_u"=>$email,"senha_u"=>$senha];
                $a=(new UsuarioModelo())->login($dados);
                if($a){
                    if((new Sessao())->checarSessao('usuarioID')){
                        header('Location: ./');
                        exit();
                    }
                    
                }
            }
       
            echo $this->templete->renderizar('login.html',[
                'titulo'=>'login no blog'
            ]);
            
        }
        public function cadastro():void{
    
            $nome=filter_input(INPUT_POST,'nome',FILTER_SANITIZE_SPECIAL_CHARS);
            $email=filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
            $senha=filter_input(INPUT_POST,'senha',FILTER_SANITIZE_SPECIAL_CHARS);
            $senhaConfimacao=filter_input(INPUT_POST,'senhaConfimacao',FILTER_SANITIZE_SPECIAL_CHARS);

            
            if(($nome==null || $nome=="")||($email==null || $email=="") ||($senha==null || $senha=="")||($senhaConfimacao==null || $senhaConfimacao=="")){

               
            }else{

                if($senha===$senhaConfimacao){
                   $dados=["nome_u"=>$nome,"emeil_u"=>$email,"senha_u"=>Helpers::criptografarSenha($senha)];
                   $a=(new UsuarioModelo())->cadastraUsuario($dados);
                   if($a){
                    header('Location: login');
                    exit();
                }
                   
                }else{
                   (new Mensagem())->informe("Coloque os dados corretos")->flash();
                }
            }
            echo $this->templete->renderizar('cadastro.html',[
                'titulo'=>'cadastro',
                'a'=>'1'
            ]);
            
        }
        public function recuperarSenha():void{
            $a=0;
       
            $nome=filter_input(INPUT_POST,'nome',FILTER_SANITIZE_SPECIAL_CHARS);
            $email=filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
            $senha=filter_input(INPUT_POST,'senha',FILTER_SANITIZE_SPECIAL_CHARS);
            $senhaConfimacao=filter_input(INPUT_POST,'senhaConfimacao',FILTER_SANITIZE_SPECIAL_CHARS);
            if(($nome==null || $nome=="")||($email==null || $email=="")){
                (new Mensagem())->informe("Coloque os dados corretos")->flash();
            }else{
               $dados=['nome_u'=>$nome,'emeil_u'=>$email];
               $d=(new UsuarioModelo)->buscarUsuario($dados);
               if($d){
                   $a=1;
               }else{
                (new mensagem())->aletar("Coloque os dados corretos")->flash();
               }
            }

            if($senha!='' || $senha!=null){
                if($senha===$senhaConfimacao){
                    $dados=["senha_u"=>Helpers::criptografarSenha($senha)];
                 
                    $mudar=(new UsuarioModelo())->editarSenha($dados,'Id_dados= '.(new Sessao())->__get("idSenha"));
                    if($mudar){
                        (new Sessao())->limparSessao("idSenha");
                        Helpers::redirecinar('login');
                    }
                    
                }else{
                    (new mensagem())->aletar("Coloque a nova senha iguais")->flash();
                    $a=1;
                }
            }

            echo $this->templete->renderizar('recuperasenha.html',[
                'titulo'=>'Mudar Senha',
                'a'=>$a
            ]);
            
        }
        public function sair():void{
            $sair=filter_input(INPUT_POST,'sair',FILTER_SANITIZE_NUMBER_INT);
            if($sair==1){
                $sessao=new Sessao();
                $sessao->deletarSessao($sessao->__get('usuarioID'));
                Helpers::redirecinar('./');
                (new Mensagem())->informe("Obrigado ate logo !")->flash();
                
            }
            
            echo $this->templete->renderizar('sair.html',[
                'titulo'=>'Blog Tecnologia',

            ]);
            
        }

        public function like($id):void{
          if((new PostModelo())->like_delisike($id,'c_like')){
            header('Location: ../');
            exit();
          }
            
        }

        public function delike($id):void{
          
            if((new PostModelo())->like_delisike($id,'c_deslike')){
                header('Location: ../');
                exit();
            }
              
        }

        public function verpost($slug):void{
         $post=(new PostModelo)->verpostp($slug);
          echo $this->templete->renderizar('verPost.html',[
            'titulo'=>'ver post',
            'id_usuario'=>(new Sessao())->__get('usuarioID'),
            'post'=>$post
        ]);
        }

    }
?>