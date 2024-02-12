<?php 
    namespace app\controllers;
    use app\module\PostModelo;
    use app\classes\Mensagem;
    use app\classes\Sessao;
    use app\Helpers\Helpers;
    use app\Biblioteca\Upload;

    class PostControlador extends Controlador{

        private ?string $imgsPost=null;
        private ?Mensagem $mensagem=null;

        public function __construct()
        {
            parent::__construct('../app/views/post/');

            $sessao=new Sessao();
            if($sessao->checarSessao('usuarioID')){
            
            }else{
               $this->mensagem=(new Mensagem())->informe("Crie uma conta ou faça login para criar post");
                Helpers::redirecinar('login');
            }

        }
        
        public function Post ():void{
            (new Mensagem())->informe("crie, veja, edite e delete seus posts")->flash();
            echo $this->templete->renderizar('posts.html',[
                'titulo'=>'Blog Tecnologia',
                'id_usuario'=>(new Sessao())->__get('usuarioID')
            ]);
            
        }

        public function criarPost():void{
            
            $titulo=filter_input(INPUT_POST,'titulo',FILTER_SANITIZE_SPECIAL_CHARS);
            $texto=filter_input(INPUT_POST,'texto',FILTER_SANITIZE_SPECIAL_CHARS);
            $tipo=filter_input(INPUT_POST,'tipo',FILTER_SANITIZE_SPECIAL_CHARS);

            if(!empty($_FILES['imgPost'])){
                $upload= new Upload();
                $upload->subpastaArquivo($_FILES['imgPost'],Helpers::slug($titulo),'../../public/uploads/imgPost');
                if($upload->getResultado()){
                    $this->imgsPost=$upload->getResultado();
                }else{
                   $this->mensagem = (new Mensagem())->aletar($upload->getErro());
                }
            }
            
         
            if($titulo && $texto && $tipo){
                $dados=['titulo_post'=>$titulo, 'link_slug'=>Helpers::slug($titulo.'-'.uniqid()),'tb_img_p'=>$this->imgsPost ,'texto'=>$texto,'fk_usuario'=>(new Sessao())->__get('usuarioID'),'fk_tipo'=>$tipo];
                (new PostModelo())->cadastraPost($dados);
                header('Location: ./');
                exit();

            }else{
                $this->mensagem = (new Mensagem())->informe("informe todas as informaçoes");
            }

            $this->mensagem->flash();

            echo $this->templete->renderizar('criarPost.html',[
                'titulo'=>'criarPost',
                'tipos'=>(new PostModelo())->verTipos()
            ]);
        }
        public function editarrPost($id):void{
            $titulo=filter_input(INPUT_POST,'titulo',FILTER_SANITIZE_SPECIAL_CHARS);
            $texto=filter_input(INPUT_POST,'texto',FILTER_SANITIZE_SPECIAL_CHARS);
            $tipo=filter_input(INPUT_POST,'tipo',FILTER_SANITIZE_SPECIAL_CHARS);
           
            if(!empty($_FILES['imgPost'])){
                if($_FILES['imgPost']["name"] !=((new PostModelo())->buscarImg($id)->tb_img_p) && $_FILES['imgPost']["name"]!=""){
                    if (file_exists("../public/uploads/imgPost/".(new PostModelo())->buscarImg($id)->tb_img_p)) {
                        var_dump(file_exists("../public/uploads/imgPost/".(new PostModelo())->buscarImg($id)->tb_img_p));
                        echo("<br>");
                        echo("<br>");
                        unlink("../public/uploads/imgPost/".(new PostModelo())->buscarImg($id)->tb_img_p);
                        $upload= new Upload();
                        $upload->subpastaArquivo($_FILES['imgPost'],Helpers::slug($titulo),'../../public/uploads/imgPost');
                        if($upload->getResultado()){
                            $this->imgsPost=$upload->getResultado();
                        }else{
                            (new Mensagem())->aletar($upload->getErro())->flash();
                        }
                    } 
                }
               
            }

                if($titulo && $texto && $tipo){
                    $a="";
                    var_dump($this->imgsPost);
                    echo("<br>");
                    echo("<br>");
                    if($this->imgsPost!=null){
                        $a=$dados=['titulo_post'=>$titulo,'tb_img_p'=>$this->imgsPost, 'texto'=>$texto,'fk_tipo'=>$tipo];
                    }else{
                        $a=$dados=['titulo_post'=>$titulo, 'texto'=>$texto,'fk_tipo'=>$tipo];
                    }
                  
                    var_dump($a);
                
                    
                    (new PostModelo())->editarPost($a,$id,(new Sessao())->__get('usuarioID'));
                    (new Mensagem())->sucesso("Post editado com sucesso")->flash();
                }else{
                    (new Mensagem())->informe("informe todas as informaçoes")->flash();
                }
            
            
    
            (new Mensagem())->informe("edite seus posts")->flash();
            echo $this->templete->renderizar('criarPost.html',[
                'titulo'=>'Edita o post',
                'tipos'=>(new PostModelo())->verTipos(),
                'post'=>(new PostModelo())->verPost("fk_usuario = ".(new Sessao())->__get('usuarioID')." and id_post = {$id}",false)
            ]);
        }

        public function deletarPost():void{
           (new Mensagem())->informe("delete seus posts")->flash();
            echo $this->templete->renderizar('editarposts.html',[
                'titulo'=>'Delete seus post',
                'texto'=>'Delete seus post',
                'id_usuario'=>(new Sessao())->__get('usuarioID'),
                'posts'=>(new PostModelo())->verPost("fk_usuario = ".(new Sessao())->__get('usuarioID'),true)

            ]);
        }

        public function deletarrPost($id):void{
            (new Mensagem())->informe(" delete seu posts")->flash();
            $deletar=filter_input(INPUT_POST,'deletar',FILTER_SANITIZE_SPECIAL_CHARS);
            
            
            if($deletar==4){

                if (file_exists("../public/uploads/imgPost/".(new PostModelo())->buscarImg($id)->tb_img_p))
                {
                    var_dump(file_exists("../public/uploads/imgPost/".(new PostModelo())->buscarImg($id)->tb_img_p));
                    echo("<br>");
                    echo("<br>");
                    var_dump("../public/uploads/imgPost/".(new PostModelo())->buscarImg($id)->tb_img_p);

                    unlink("../public/uploads/imgPost/".(new PostModelo())->buscarImg($id)->tb_img_p);
                }
                    (new PostModelo())->deletarPost($id);
                    header('Location: /php/projetovBlog/public/post/deletarpost/');
                    exit();
            }
            echo $this->templete->renderizar('deletar.html',[
                'titulo'=>'Deletar Post',
                'tipos'=>(new PostModelo())->verTipos(),
                'post'=>(new PostModelo())->verPost("fk_usuario = ".(new Sessao())->__get('usuarioID')." and id_post = {$id}",false)
            ]);
        }

        public function verPost ():void{
            (new Mensagem())->informe("Veja seus posts")->flash();
             echo $this->templete->renderizar('verposts.html',[
                  'titulo'=>'veja seus posts',
                  'id_usuario'=>(new Sessao())->__get('usuarioID'),
                  'posts'=>(new PostModelo())->verPost("fk_usuario = ".(new Sessao())->__get('usuarioID'),true)
              ]);
              
          }
        public function EditarPost ():void{
            (new Mensagem())->informe("Edite seus posts")->flash();
              echo $this->templete->renderizar('editarposts.html',[
                  'titulo'=>'Edite seus post',
                  'texto'=>'Edite seus post',
                  'id_usuario'=>(new Sessao())->__get('usuarioID'),
                  'posts'=>(new PostModelo())->verPost("fk_usuario = ".(new Sessao())->__get('usuarioID'),true)

              ]);
              
        }
    }
?>