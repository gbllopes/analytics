<?php
include_once "../Database/database.php";
include_once "../Model/PainelDAO.php";

// Arquivo que salva ou edita um painel no banco de dados.

    $db = new DataBase();
    $dao = new PainelDAO($db);
    @$categoria = utf8_decode($_POST['categoria']);
    @$assunto   = utf8_decode($_POST['assunto']);
    @$link      = utf8_decode($_POST['link']);
    @$desc      = utf8_decode($_POST['descricao']);
    @$id        = $_POST['select_painel'];
    
    
    if($categoria == "nova_categoria"){
        $categoria = strtoupper($_POST['nova_categoria']);
    }
    
    if(@$id == null && @$categoria && @$assunto && @$link && @$desc ){
        
        if($dao->adicionarPainel($categoria, $assunto, $link, $desc) == "success"){
            $dao->criarJSON_QTDE_POR_CATEGORIA();
            echo "success";
        }
        
    }else{
        
        if($dao->editarPainel($id, $assunto, $desc, $link) == "success"){
            echo "success";
        }
    } 

?>