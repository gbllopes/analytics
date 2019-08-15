<?php
include_once '../Database/database.php';
header("Content-type: text/html; charset=utf-8"); 
$db = new Database();
//Inicia a sessão e verifica se o usuário está logado.
session_start();
$isLoggedIn = (isset($_SESSION["login"]) != NULL) ? true : false;
$limite = 25;
$max_links = 20;
$pagina = (isset($_POST['pagina']))? $_POST['pagina'] : 1;
$inicio = ($limite * $pagina) - $limite; 

// Seta os valores de cada parametro passado pelo ajax.
@$busca = $_POST['busca'];
@$colFiltro = $_POST['coluna_filtro'];
@$getFiltro = $_POST['filtro'];
@$filtro = ($_POST['filtro'] ? "where ".$colFiltro." like '%".$getFiltro."%' " : " ");
@$dado = $_POST['dado'];
if(@$_POST['orderby'] != "num_demanda" && (!empty($_POST['orderby']))){
  if($_POST['orderby'] == "entregue"){
    $orderby = " order by entregue asc";
  }
  if($_POST['orderby'] == "classificacao"){
    $orderby = " order by classificacao desc";
  } 
} else {
  $orderby = "order by num_demanda desc";
}
// É verificado se o usuário realizou a busca. Logo após é criada a query com base na requisição. 
if (!empty($busca)){
  $sql = "select * from demandas_mining where num_demanda like '%".$busca."%' or demandante like '%".$busca."%' or responsavel like '".$busca."' or divisao like '%".$busca."%' or assunto like '%".$busca."%' or data_registro_txt like '%".$busca."%' or classificacao like '%".$busca."%'";
  $result = mysqli_query($db->getConection(), $sql);
  $sql2 = "select num_demanda, demandante, responsavel, divisao, assunto,data_registro_txt, entregue, classificacao from demandas_mining where num_demanda like '%".$busca."%' or demandante like '%".$busca."%' or divisao like '%".$busca."%' or assunto like '%".$busca."%' or data_registro_txt like '%".$busca."%' or classificacao like '%".$busca."%' ".$orderby." limit ".$inicio.", ".$limite;
  $result2 = mysqli_query($db->getConection(), $sql2);
  $num = mysqli_num_rows($result);
  $totalPaginas = ceil($num/$limite);
}else{
  $sql = "select * from demandas_mining ".$filtro;
  $result = mysqli_query($db->getConection(), $sql);
  $sql2 = "select num_demanda, demandante, responsavel, divisao, assunto,data_registro_txt, entregue, classificacao from demandas_mining ".$filtro.$orderby." limit ".$inicio.", ".$limite;
  $result2 = mysqli_query($db->getConection(), $sql2);
  $num = mysqli_num_rows($result);
  $totalPaginas = ceil($num/$limite);
}
// É definido o número de páginas laterais a página atual do usuário. 
$links_laterais = ceil($max_links / 2); 
$inicio = $pagina - $links_laterais;
$limite = $pagina + $links_laterais;

// Retorna ao ajax do jQuery a tabela listando os dados e paginação refeita.
echo '
<div class="mb-1">
<strong>Listando página '.$pagina.' de '.$totalPaginas.'</strong> 
</div> 
<table class="table table-hover table-responsive-lg text-center">
<thead class="thead-dark">
  <tr>
    <th scope="col">Nº demanda</th>
    <th scope="col">Demandante</th>
    <th scope="col" class="th-filtro-width">Responsavel<br>
      <div class="select">    
          <select class="rounded" id="filtroResponsavel" '.(@$colFiltro == "responsavel" ? "data-dado='".$getFiltro."'": "").'>
              <option id="semFiltro">Sem filtro</option>';
              $sql = "select nome_responsavel from responsaveis";
              $responsaveis = mysqli_query($db->getConection(), $sql);
              foreach($responsaveis as $responsavel){
                echo '<option id="filtro_option_resp">'.$responsavel['nome_responsavel'].'</option>';
              }
    echo '    
          </select>
      </div>
    </th>
    <th scope="col" class="th-filtro-width">Divisão<br>
     <div class="select">
        <select class="rounded" id="filtroDivisao" '.(@$colFiltro == "divisao" ? "data-dado='".$getFiltro."'": "").'>
          <option id="semFiltro">Sem filtro</option>';
          $sql = "select divisao from divisoes";
          $divisoes = mysqli_query($db->getConection(), $sql);
          foreach($divisoes as $divisao){
            echo '<option id="filtro_option_div">'.ucfirst(strtolower($divisao['divisao'])).'</option>';
          }
    echo  '  
        </select>
      </div>
    </th>
    <th scope="col">Assunto</th>
    <th scope="col">Data de Registro</th>
    <th scope="col" class="th-filtro-width">Classificação<br>
      <div class="select">
        <select class="rounded" id="filtroClassificacao" '.(@$colFiltro == "classificacao" ?  " data-dado='".$getFiltro."' data-acao='".$_POST['orderby']."'": "").'>
          <option id="semFiltro">Sem filtro</option>';
          $sql = "select DISTINCT classificacao from demandas_mining where classificacao != '' ";
          $classificacoes = mysqli_query($db->getConection(), $sql);
          foreach($classificacoes as $classificacao){
            echo '<option id="filtro_option_class">'.$classificacao['classificacao'].'</option>';
          }
    echo  '
        </select>
      </div>
    </th> 
    <th id="status" data-acao="'.@$_POST['orderby'].'" scope="col">Status<br><div class="bg bg-primary"><span class="span-orderby">Clique p/ ordernar</span><div></th>
    <th scope="col" colspan="2">Ação</th> 
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
                        <th scope="row" class="align-middle" >'.$r['num_demanda'].'</th>
                        <td>'.utf8_encode($r['demandante']).'</td>
                        <td>'.utf8_encode($r['responsavel']).'</td>
                        <td>'.utf8_encode($r['divisao']).'</td>
                        <td id="open-tooltip" class="quebra-texto mouse" title="'.utf8_encode($r['assunto']).'">'.utf8_encode($r['assunto']).'</td>
                        <td>'.utf8_encode($r['data_registro_txt']).'</td>';
                        $cor = null;
                        if($r['classificacao'] == "Urgente"){
                          $cor = "bg-danger";
                        } else if($r['classificacao'] == "Importante"){
                          $cor = "bg-warning";
                        }
                        $class = ($r['classificacao'] ? $r['classificacao'] : "");
             echo       '<td class="'.($r['classificacao'] ? $cor : "").'">'.$class.'</td>';
                        if ($r['entregue'] == "Sim"){
                          echo '<td class="alert-success">Entregue</td>';
                        }else{
                          echo '<td class="alert-danger">Não entregue</td>';
                        }
             echo       '<td><button id="abrir" title="Abrir" class="btn btn-primary" data-demanda="'.$r['num_demanda'].'" data-dado="'.$dado.'" data-filtro="'.$colFiltro.'"><i class="fas fa-vote-yea"></i></button></td>';
                        if($isLoggedIn && $r['entregue'] == "Não"){
                          echo '<td style="border-bottom: 1px solid #dee2e6"><button id="editar" title="Editar" class="btn btn-warning" data-demanda="'.$r['num_demanda'].'" data-dado="'.$dado.'" data-filtro="'.$colFiltro.'"><i class="fas fa-pencil-alt"></i></button></td>';
                        }  
            echo '</tr>';        
        }  
}
echo '</tbody>
</table>
<div class="tooltip"></div>
<div class="modal-footer"><button id="topo" class="btn btn-secondary">Topo da página</button></div>
<div class="ui column centered grid">
<div class="ui buttons">
    <button class="mini ui labeled icon button '.($pagina === 1 ? "disabled": "").'" id="paginacao" data-filtro="'.$colFiltro.'" data-acao="demanda" data-busca="'.$busca.'" class="page-link" data-paginacao="'.($pagina - 1).'">
        <i class="left chevron icon"></i>
        Anterior
    </button>';
    for ($i = $inicio; $i <= $totalPaginas; $i++){
        if($i >= 1 && $i <= $limite){
        echo  '<button class="ui button '.($i == $pagina ? "active" : "").'" id="paginacao" data-filtro="'.$colFiltro.'" data-acao="demanda" data-busca="'.$busca.'" data-paginacao="'.$i.'">'.$i.'</button>';
        }    
    }
    echo '<button id="paginacao" class="ui mini right labeled icon button '.($pagina == $totalPaginas ? "disabled": "").'" data-filtro="'.$colFiltro.'" data-acao="demanda" data-busca="'.$busca.'" class="page-link" data-paginacao="'.($pagina + 1).'">
              Próxima
              <i class="right chevron icon"></i>
          </button> 
</div>
</div>';
?>