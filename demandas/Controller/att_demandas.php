<?php 
include_once "../Database/database.php";
include_once "../Model/PainelDAO.php";

$db = new DataBase();
$dao = new PainelDAO($db);

// Arquivo que Atualiza o JSON dos dados do painel de demandas.

$dao->criarJSON();

?>
