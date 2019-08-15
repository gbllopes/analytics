

<?php
   include_once '../Database/database.php';
   @include_once '../analytics/login/User.php';
     // Recebemos os dados digitados pelo usuário
    
     $db = new Database(); 
     $login = $_POST['login'];
     $login = strtolower($login);
     $senha = $_POST['senha'];
     $sql = "SELECT id, Nome FROM lista_funci WHERE matr_funci = '{$login}' AND senha = md5('{$senha}') AND tipo_user = '0'";
     $rs = mysqli_query($db->getConection(),$sql);
     $num = mysqli_num_rows($rs);
   //Verificams se alguma linha foi afetada, caso sim retornamos suas informações
     if($num > 0)
     {
     //Retorna os dados do banco
     $rst = mysqli_fetch_array($rs);
     $id = $rst["id"];
     $nome = $rst["Nome"];
   //Inicia a sessão
     session_start();
     //Registra os dados do usuário na sessão
     $_SESSION["id"] = $id;
     $_SESSION["nome"] = $nome;
     $_SESSION["login"] = $login;
     $db->encerrarConexao();
     echo true;
   }else{
     //Encerra a conexão com o banco
     $db->encerrarConexao();
		 echo false;
   }
     ?>

