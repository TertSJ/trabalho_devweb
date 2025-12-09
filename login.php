<?php
    include 'conexao.php';

    if(isset($_POST['email']) && isset($_POST['password'])){
        $email = $conexao->real_escape_string($_POST['email']);
        $password = $conexao->real_escape_string($_POST['password']);

        $sql_code = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$password'";
        $sql_query = $conexao->query($sql_code) or die("Falha na execução do código SQL: " . $conexao->error);
        $quantidade = $sql_query->num_rows;

        if($quantidade == 1 ){

            $usuario = $sql_query->fetch_assoc();

                if(!isset($_SESSION)){
                session_start();
            }
            header("Location: index.php");
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
        
        }else{
            echo "Falha ao logar! E-mail ou senha incorretos.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Acesse sua conta</h1>
    <form action="" method="post">
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required>
        <br>
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Entrar</button>
</body>
</html>