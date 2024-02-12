<?php 
namespace app\database;
use PDO;
use PDOException;

class Conexao{
    private static $instancia;

    public static function getInstancia():PDO{
        if(empty(self::$instancia)){
            try{
                self::$instancia = new PDO('mysql:host='.DB_HOST.';port='.DB_PORTA.';dbname='.
                    DB_NOME, DB_USUARIO, DB_SENHA, [

                        //  garante que o charset do PDO seja o mesmo do banco de dados PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",

                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",

                        //todo erro atraves do PDO será uma exeção  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                        // Converte qualquer resultado em um objeto anonimo; PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,

                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,

                        // Garante que o mesmo nime das colunas do banco de dados seja utlizado PDO::ATTR_CASE=>PDO::CASE_NATURAL.

                        PDO::ATTR_CASE=>PDO::CASE_NATURAL
                    ]);
            }catch(PDOException $e){
                var_dump($e->getMessage());
            }
        }

        return self::$instancia;
    }

    protected function __construct()
    {
        
    }

    private function __clone():void
    {
        
    }
}

?>