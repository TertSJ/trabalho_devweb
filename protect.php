<?php
    if(!isset($_SESSION)){
        session_start();
    }

    if(!isset($_SESSION['id'])){
        die("Acesso negado. Por favor, <a href='login.php'>Faça login</a> para continuar.");
    }

?>