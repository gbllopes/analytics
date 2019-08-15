    <?php
    //Inicia a sessão
    session_start();
    //Elimina os dados da sessão
    unset($_SESSION['id']);
    unset($_SESSION['nome']);
    unset($_SESSION['login']);
     
    //Encerra a sessão
    session_destroy();
    header("Location: ../index.php");
    ?>