<?php 
    namespace app\module;
    use PDOException;
    use app\database\Conexao;
    use Exception;
    use app\classes\Mensagem;


   class Modelo{
    protected $dados;
    protected $query;
    protected $erro;
    protected $parametro;
    protected $table;
    protected $ordem;
    protected $limite;
    protected $offset;
    protected $mensagem;
    protected $id;

    public function __construct(string $table)
    {
        $this->table=$table;
        $this->mensagem=new Mensagem();
    }

    public function buscarColuna(?string $termos=null,?string $parametro=null,?string $colunas="*", ?string $tabela=""){
        try{
            if($termos){
                $this->query="select {$colunas} from ".$this->table." ". $tabela." where {$termos}";
                // var_dump($this->query);
                parse_str($parametro, $this->parametro);
                return $this;
            }
    
            $this->query="select {$colunas} from ".$this->table;
            
            return $this;

        }catch(PDOException $e){
            var_dump($e);
        }
    }

    public function ordem(string $ordem){
        $this->ordem= "order by {$ordem}"; 
        return $this;
    }

    public function limite(string $limite){
        $this->limite= " limite {$limite}";
        return $this;
    }

    public function offset(string $offset){
        $this->offset= " offset {$offset}";
        return $this;
    }
    public function erro(){
        
        return $this->erro;
    }
    public function mensagem(){
        
        return $this->mensagem;
    }

    public function resultado(bool $todos=false){
        try {
            $stmt=Conexao::getInstancia()->prepare($this->query.$this->ordem);
            $stmt->execute($this->parametro);

            if(!$stmt->rowCount()){
                return null;
            }
            if($todos){
                return $stmt->fetchAll();
            }else{
                return $stmt->fetchObject();
            }
        } catch (PDOException $e) {
            $this->erro=$e;
            return null;
        }
    }

    protected function cadastrarDados(array $dados){
        try {
            $colunas=implode(', ',array_keys($dados));
            $valores=implode(',:',array_keys($dados));
            $query="insert into ".$this->table." ({$colunas}) VALUES (:{$valores});";
            // var_dump($query);
            $stmt=Conexao::getInstancia()->prepare($query);
            $stmt->execute($dados);
            return Conexao::getInstancia()->lastInsertId();
        } catch (PDOException $e) {
            $this->erro=$e;
            return null;
        }
    }

    protected function editarDados(array $dados, string $parametro="id=0"){
        try{
            $set=[];
            foreach($dados as $chave => $valor){
                $set[]= "$chave = :$chave";
            }
            $set=implode(', ',$set);
            $query="UPDATE ". $this->table." SET {$set} WHERE $parametro;";
            // var_dump($query);
            $stmt=Conexao::getInstancia()->prepare($query);
            $stmt->execute($dados);
            return true;
        }catch(Exception $e){

        }
        
        
    }
    // id_post= 4

    protected function deletarDados(string $termo){
        $query="DELETE FROM ".$this->table." where $termo ";
        $stmt=Conexao::getInstancia()->prepare($query);
        $stmt->execute();
    }

    protected function mlike_deslike($id, $coluna){
       try{
           $query="UPDATE ".$this->table." set $coluna =(SELECT SUM($coluna) FROM tb_posts WHERE $coluna >= 0 and id_post=$id) +1 WHERE id_post=$id;";
           $stmt=Conexao::getInstancia()->prepare($query);
           $stmt->execute();
           return true;
        }catch(PDOException $e){
            return false;
        }
    }

   
}


?>