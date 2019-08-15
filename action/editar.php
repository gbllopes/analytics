<?php
include_once '../Database/database.php';
$db = new Database();

/*  Recebe a ação do usuário por ajax, seta os dados nas variáveis e realiza a ação.
    OBS: caso ação  nao for "editar_demanda" será "editar_metadado".                  */
$acao = $_POST['acao'];

if($acao == "editar_demanda"){
    $responsavel            =   mysqli_real_escape_string($db->getConection(), utf8_decode($_POST['responsavel']));
    $fone_ramal             =   mysqli_real_escape_string($db->getConection(), utf8_decode($_POST['fone_ramal']));
    $data_txt               =   mysqli_real_escape_string($db->getConection(), utf8_decode($_POST['dt_entrega']));
    $demanda                =   mysqli_real_escape_string($db->getConection(), utf8_decode($_POST['demanda'])); 
    $assunto                =   mysqli_real_escape_string($db->getConection(), utf8_decode($_POST['assunto']));
    $necessidades           =   mysqli_real_escape_string($db->getConection(), utf8_decode($_POST['necessidades']));
    $campos_resultados      =   mysqli_real_escape_string($db->getConection(), utf8_decode($_POST['campos_resultados']));
    $condicoes              =   mysqli_real_escape_string($db->getConection(), utf8_decode($_POST['condicoes']));
    $data = str_replace("/", "-", $data_txt);
    if(!empty($_POST['classificacao'])){
        $classificacao = $_POST['classificacao'];
        $classificacao = ($classificacao == "Missing" ? $classificacao = ", classificacao = null" : ", classificacao = '".$classificacao."'");
    }
    if(empty($fone_ramal) || empty($assunto) || empty($campos_resultados) || empty($condicoes)){
        echo "oi";
    } else { 
            $sql = "update demandas_mining set ".($responsavel ? "responsavel = '".$responsavel."'," : "")." fone_ramal = '".$fone_ramal."', assunto = '".$assunto."', necessidades = '".$necessidades."', condicoes ='".$condicoes."',
                    campos_resultados ='".$campos_resultados."'".($data_txt ? ", data_entrega_txt = '".$data_txt."', entregue = 'Sim', classificacao = null" : "").(@$classificacao ? $classificacao : "")." where num_demanda = ".$demanda;    
            $res = mysqli_query($db->getConection(), $sql);
            if($res){
                echo true;
            }else{
                echo mysqli_error($db->getConection());
            }

    }
} else {
    $biblioteca = $_POST['biblioteca'];
    $tabela     = $_POST['tabela'];
    $nome       = $_POST['nome'];
    $tipo       = $_POST['tipo'];
    $tamanho    = $_POST['tamanho'];
    $conceito   = $_POST['conceito'];
    $metadado   = utf8_decode($_POST['metadado']);

    if($biblioteca == null && $tabela == null && $nome == null && $tipo == null && $tamanho == null && $conceito == null){
        echo false;
    } else {
        $sql = "update metadados set library = '".$biblioteca."', table_name = '".$tabela."', name = '".$nome."', type = '".$tipo."', length = ".$tamanho.", label = '".$conceito."' where id_metadado = ".$metadado;
        $res = mysqli_query($db->getConection(), $sql);
        if($res){
            echo true;
        }
    }
}    