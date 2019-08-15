<?php 
include_once "../Database/database.php";
include_once "../Model/PainelDAO.php";

// Arquivo que quando requisitado, atualiza os acessos e cria um novo JSON dos dados do banco de dados.
$db = new DataBase();
$dao = new PainelDAO($db);
if (@$_REQUEST["id"]) {   
  $id = $_REQUEST['id'];
  $dao->atualizarQtdeAcessos($id); 
}
$dao->criarJSON();

?>
