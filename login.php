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
            
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            header("Location: index.php");
        
        }else{
            $erro_login = "Falha ao logar! E-mail ou senha incorretos.";
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Controle Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-wallet fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold">Controle Financeiro</h3>
                            <p class="text-muted">Acesse sua conta</p>
                        </div>
                        
                        <?php if(isset($erro_login)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $erro_login; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Senha
                                </label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Entrar
                            </button>
                            
                            <div class="text-center">
                                <p class="mb-0">Não tem uma conta? 
                                    <a href="cadastro.php" class="text-decoration-none">Cadastre-se</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>