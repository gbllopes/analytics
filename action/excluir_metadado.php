<?php
include_once '../Database/database.php';
$db = new Database();
// Cria a query de exclusão com base no id do metadado que o usuário quer excluir.
$id = $_POST['id_metadado'];
echo "sucesso";
$sql = "delete from metadados where id_metadado = ".$id;
$res = mysqli_query($db->getConection(), $sql);
?>