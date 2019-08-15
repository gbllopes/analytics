<?php 
include_once "../Database/database.php";
include_once "../Model/PainelDAO.php";

//  Lista os assuntos disponíveis do banco de dados em um select que irá ser gerado.

$db = new DataBase();
$dao = new PainelDAO($db);
$resultados = $dao->listarPainel(); 
echo '<option value="">Escolha um assunto</option>';
foreach($resultados as $data){
    echo "<option value=". $data['id_painel'] .">". utf8_encode($data['assunto']) ."</option>";
}



