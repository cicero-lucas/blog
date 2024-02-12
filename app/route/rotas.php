
<?php
require '../vendor/autoload.php';

use app\Helpers\Helpers;
use Pecee\SimpleRouter\SimpleRouter;

try{
    SimpleRouter::setDefaultNamespace('app\controllers');
    // site
    SimpleRouter::get(URL_SITE,'SiteControlador@index');
    SimpleRouter::get(URL_SITE.'erro','SiteControlador@erro');
    
    SimpleRouter::match(['get','post'],URL_SITE.'login','SiteControlador@login');
    SimpleRouter::match(['get','post'],URL_SITE.'cadastro','SiteControlador@cadastro');
    SimpleRouter::match(['get','post'],URL_SITE.'buscar','SiteControlador@buscar');
    SimpleRouter::get(URL_SITE.'buscar/{categoria}','SiteControlador@buscarCategoria');
    SimpleRouter::match(['get','post'],URL_SITE.'recuperar/senha','SiteControlador@recuperarSenha');
    SimpleRouter::get(URL_SITE.'like/{id}','SiteControlador@like');
    SimpleRouter::get(URL_SITE.'deslike/{id}','SiteControlador@delike');
    SimpleRouter::get(URL_SITE.'ver-post/{slug}','SiteControlador@verpost');

    SimpleRouter::get(URL_SITE.'sobre','SiteControlador@sobre');
    SimpleRouter::match(['get','post'],URL_SITE.'sair','SiteControlador@sair');

    // grupo do post
    SimpleRouter::match(['get','post'],URL_POST,'PostControlador@Post');

    SimpleRouter::match(['get','post'],URL_POST.'/criarpost','PostControlador@criarPost'); 
    SimpleRouter::match(['get','post'],URL_POST.'/editarpost','PostControlador@editarPost'); 
    SimpleRouter::match(['get','post'],URL_POST.'/editarpost/{id}','PostControlador@editarrPost'); 
    SimpleRouter::match(['get','post'],URL_POST.'/verpost','PostControlador@verPost'); 
    SimpleRouter::match(['get','post'],URL_POST.'/deletarpost','PostControlador@deletarPost'); 
    SimpleRouter::match(['get','post'],URL_POST.'/deletarrpost/{id}','PostControlador@deletarrPost'); 

    SimpleRouter::start();
}catch(Exception $e){
    Helpers::redirecinar(0);
}

    
?>