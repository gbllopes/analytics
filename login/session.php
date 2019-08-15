<?php

//inicia a sessão e retorna a matrícula do usuário.
session_start();
echo @$_SESSION["login"];

?>