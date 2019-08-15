<?php
include_once "../Database/database.php";
$db = new Database();
$acao = $_POST['acao'];
@$dt_nascimento  = str_replace("/","-",$_POST['nascimento']);
@$data_partes = explode("-", $dt_nascimento);
@$ano = $data_partes[2];
@$mes = $data_partes[1];
@$dia = $data_partes[0];

// Funções de Adicionar Usuário//  Resetar Senha // Validar matrícula e data de nascimento.
  if($acao == "adicionar"){ // Adicionar novo usuário.
      if(strlen($_POST['nascimento']) < 10){
        echo false;
      } else {
        $nome = $_POST['nome'];
        $login= $_POST['matricula'];
        $senha= $_POST['senha'];
        $senhacrypt = md5($senha);
        $sql_jaExiste = "select * from lista_funci where matr_funci = '".$login."'";
        $res = mysqli_query($db->getConection(), $sql_jaExiste);
        $num = mysqli_num_rows($res);
        if (empty ($nome) || empty ($login) || $num > 0){
          echo false;
        } else {
          $sql_add_usuario = "insert into lista_funci (Nome, matr_funci, tipo_user, senha, dt_nascimento) values ('".$nome."', '".$login."', 0, '".$senhacrypt."', '".$ano."-".$mes."-".$dia."')";
          $res = mysqli_query($db->getConection(),$sql_add_usuario);
          if($res){
            echo true;
          }
        }
      }
      
  }

  if($acao == "resetar"){ // Resetar senha do usuário.
    $matricula = $_POST['matricula'];
    $senha = $_POST['nova_senha'];
    $senhacrypt = md5($senha);
    $sql = "update lista_funci set senha = '".$senhacrypt."' where matr_funci = '".$matricula."'";
    $res = mysqli_query($db->getConection(), $sql);
    echo ($res ? true : false);
  }

  if($acao == "validar"){ // Validar matrícula e data de nascimento do usuário.

    $matricula = $_POST['matricula'];
    if(strlen($_POST['nascimento']) < 10){
      echo false;
    }else {
      $sql = "select * from lista_funci where matr_funci = '".$matricula."' and dt_nascimento = '".$ano."-".$mes."-".$dia."'";
      $res = mysqli_query($db->getConection(), $sql);
      $num = mysqli_num_rows($res);
      if($res && $num > 0){
        echo "<div class='form-group'>
                <label for='senha'><strong>Nova senha</strong></label>
                <input type='password' class='form-control' name='nova_senha' required>
                <input type='hidden' name='matricula' value='".$matricula."'>
                <input type='hidden' name='acao' value='resetar'>
              </div>";
      }else{
        echo false;
      }
    }
  }
?>