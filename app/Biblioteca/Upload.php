<?php 
namespace app\Biblioteca;

// classe que possibilita o upload de aquivo no blog

class Upload{
    private ?array $arquivo;
    private ?string $nomeArquivo;
    private ?string $diretorio;
    private ?string $pasta;
    private ?string $resultado=null;
    private ?string $erro=null;
    private ?int $tamanhoArquivo;

    public function getErro(): ?string{
        return $this->erro;
    }

    public function getResultado(): ?string{
        return $this->resultado;
    }

    public function __construct( string $diretorio=null)
    {
        $this->diretorio=$diretorio ?? 'uploads';
        // verificar se o arquivo nao esxite e se o this diretorio não e um diretorio;
        if((!file_exists($this->diretorio)) && (!is_dir($this->diretorio)) ){
            mkdir($this->diretorio,0755);
        }
    }

    public function criarPasta(){
        if((!file_exists($this->diretorio.DIRECTORY_SEPARATOR.$this->pasta)) && (!is_dir($this->diretorio.DIRECTORY_SEPARATOR.$this->pasta)) ){
            mkdir($this->diretorio.DIRECTORY_SEPARATOR.$this->pasta,0755);
        
        }
    }

  
    public function subpastaArquivo(array $arquivo, string $nome=null,string $nomePasta=null,int $tamanhoArquivo=null){

        $this->arquivo=$arquivo;

        $this->nomeArquivo=$nome ?? pathinfo($this->arquivo['name'],PATHINFO_FILENAME);

        $this->pasta = $nomePasta ?? 'arquivos';
        $extensao=pathinfo($this->arquivo['name'],PATHINFO_EXTENSION);

        $this->tamanhoArquivo= $tamanhoArquivo ?? 2 ;
          
        $extensoesValidas=['pdf','png','jpg'];
        $tiposValidos=[
            'application/pdf',
            'text/plain',
            'image/png',
            'image/jpg',
            'image/jpeg'
        ];
          // verificar o tipo e as extensoes validas no projeto
        if(!in_array($extensao,$extensoesValidas)){

            $this->erro='extenção não permitida! você só pode fazer upload de arquivos tipo .'.implode(' .',$extensoesValidas);

        }else if (!in_array($this->arquivo['type'],$tiposValidos)){
            var_dump($this->arquivo['type']);
            $this->erro='type! você só pode fazer upload de arquivos tipo .'.implode(' .',$extensoesValidas);

        }elseif($this->arquivo['size']>$this->tamanhoArquivo*(1024*1024)){
            $this->erro='Tamanho do arquivo incopativel a capacidade de seu armazenamento'. $this->tamanhoArquivo."mb seu arquivo é de {$this->arquivo['size']} mb ";
        
        }else{
            $this->criarPasta();
            $this->renomearAquivo();
            $this->moverArquivo();
        }

    }

    private function renomearAquivo():void{
        $arquivo = $this->nomeArquivo.strrchr($this->arquivo['name'],'.');
        if(file_exists($this->diretorio.DIRECTORY_SEPARATOR.$this->pasta.DIRECTORY_SEPARATOR.$arquivo)){
            $arquivo = $this->nomeArquivo.'-'.uniqid().strrchr($this->arquivo['name'],'.');
        }

        $this->nomeArquivo=$arquivo;
    }

    private function moverArquivo():void{
        if(move_uploaded_file($this->arquivo['tmp_name'],$this->diretorio.DIRECTORY_SEPARATOR.$this->pasta.DIRECTORY_SEPARATOR.$this->nomeArquivo)){
            $this->resultado=$this->nomeArquivo;
            $this->erro=null;
        }else{
            $this->resultado=null;
            $this->erro='erro ao enviar arquivo';
        }
    }

}
?>