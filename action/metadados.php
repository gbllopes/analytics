<?php
include_once '../Database/database.php';
$db = new Database();
session_start();
$isLoggedIn = (isset($_SESSION["login"]) != NULL) ? true : false;
$limite = 25;
$max_links = 20;
$pagina = (isset($_POST['pagina']))? $_POST['pagina'] : 1;
$inicio = ($limite * $pagina) - $limite; 
@$busca = $_POST['busca'];
if (isset($busca)){
  $sql = "select * from metadados where library like '%".$busca."%' or table_name like '%".$busca."%' or name like '%".$busca."%' or type like '%".$busca."%' or length like '%".$busca."%' or informat like '%".$busca."%' or label like '%".$busca."%' or conceito like '%".$busca."%'";
  $result = mysqli_query($db->getConection(), $sql);
  $sql2 = "select * from metadados where library like '%".$busca."%' or table_name like '%".$busca."%' or name like '%".$busca."%' or type like '%".$busca."%' or length like '%".$busca."%' or informat like '%".$busca."%' or label like '%".$busca."%' or conceito like '%".$busca."%' order by id_metadado desc limit ".$inicio.", ".$limite;
  $result2 = mysqli_query($db->getConection(), $sql2);
  $num = mysqli_num_rows($result);
  $totalPaginas = ceil($num/$limite);
}else{
  $sql = "select * from metadados";
  $result = mysqli_query($db->getConection(), $sql);
  $sql2 = "select * from metadados order by id_metadado desc limit ".$inicio.", ".$limite;
  $result2 = mysqli_query($db->getConection(), $sql2);
  $num = mysqli_num_rows($result);
  $totalPaginas = ceil($num/$limite);
}
$links_laterais = ceil($max_links / 2);
$inicio = $pagina - $links_laterais;
$limite = $pagina + $links_laterais;
echo '
<div class="w-100 menu_button" style="height: 30px;margin-bottom: 5px;">
<strong>Listando página '.$pagina.' de '.$totalPaginas.'.</strong>
</div>  
<table class="table table-hover table-responsive-lg text-center">
<thead class="thead-dark">
  <tr>
    <th scope="col">Biblioteca</th>
    <th scope="col">Tabela</th>
    <th scope="col">Nome</th>
    <th scope="col">Tipo</th>
    <th scope="col">Tamanho</th>
    <th scope="col">Conceito</th>';
    if($isLoggedIn){
    echo  '<th scope="col" colspan="2">Ação</th>';
    }
 echo '     
  </tr>
</thead>
<tbody>';
if ($num == 0){
  echo '<tr>
          <h4>Nenhum resultado encontrado com os dados informados.</h4>
        </tr>';
} else {
        foreach($result2 as $r){
            echo '<tr>
                        <th scope="row" class="align-middle">'.utf8_encode($r['library']).'</th>
                        <td>'.utf8_encode($r['table_name']).'</td>
                        <td class="text-truncate mouse">'.utf8_encode($r['name']).'</td>
                        <td>'.utf8_encode($r['type']).'</td>
                        <td>'.utf8_encode($r['length']).'</td>
                        <td class="mouse quebra-texto" title="'.utf8_encode($r['label']).'">'.utf8_encode($r['label']).'</td>';
                        if($isLoggedIn){
            echo '              
                        <td style="width:10px"><button id="editar_metadado" title="Editar metadado" class="btn btn-warning" data-acao="editar_metadado" data-metadado="'.$r['id_metadado'].'"><i class="fas fa-pencil-alt"></i></button></td>
                        <td style="width:10px"><button id="excluir_metadado" title="Excluir metadado" class="btn btn-danger" data-metadado="'.$r['id_metadado'].'"><i class="far fa-trash-alt"></i></button></td>';
                        }
           echo'             
                </tr>';        
        }  
}
echo '</tbody>
</table>
<div class="modal-footer"><button id="topo" class="btn btn-secondary">Topo da página</button></div>
<nav aria-label="Page navigation">
  <ul class="pagination flex-wrap justify-content-center">';
    if($pagina == 1){
      echo '<li class="page-item disabled">
              <button class="page-link">Anterior</button>
            </li>';
    }else{
      echo '<li class="page-item">
              <button id="paginacao" data-acao="metadados" data-busca="'.$busca.'" class="page-link" data-paginacao="'.($pagina - 1).'">Anterior</button>
            </li>';
    } 
    for ($i=1;$i<=$totalPaginas;$i++){
      if($i >= 1 && $i <= $limite){
        echo '<li id="paginacao" data-acao="metadados" data-busca="'.$busca.'" data-paginacao='.$i.' class="page-item '.($pagina == $i ? 'active' : '').'"><button class="page-link">'.$i.'</button></li>';
      }
    }
    if($pagina != $totalPaginas){
      echo '<li class="page-item"> 
              <button id="paginacao" data-acao="metadados" data-busca="'.$busca.'" class="page-link" data-paginacao="'.($pagina + 1).'">Próxima</button>
            </li>
            </ul>
            </nav>';
    }else{
      echo '<li class="page-item disabled"> 
              <button class="page-link">Próxima</button>
            </li>
            </ul>
            </nav>';
    }
?>