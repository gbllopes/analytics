<?php 
include_once "../Database/database.php";
include_once "../Model/PainelDAO.php";

//   Exclui o painel solicitado com base no seu id. 

$db = new DataBase();
$dao = new PainelDAO($db);

$id = $_POST['select_painel_del'];  

$dao->excluirPainel($id);
$dao->criarJSON_QTDE_POR_CATEGORIA();
?>



