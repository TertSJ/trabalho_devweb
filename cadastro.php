<?php
    include 'conexao.php';

    $mensagem = '';
    $tipo_mensagem = '';

    if(isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['password'])){
        $nome = $conexao->real_escape_string($_POST['nome']);
        $email = $conexao->real_escape_string($_POST['email']);
        $password = $conexao->real_escape_string($_POST['password']);
        
        $sql_check = "SELECT * FROM usuarios WHERE email = '$email'";
        $result_check = $conexao->query($sql_check);
        
        if($result_check->num_rows > 0){
            $mensagem = "Este email já está cadastrado!";
            $tipo_mensagem = 'danger';
        } else {
           
            $sql_insert = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$password')";
            
            if($conexao->query($sql_insert)){
                $mensagem = "Cadastro realizado com sucesso! Redirecionando para o login...";
                $tipo_mensagem = 'success';
                header("refresh:2;url=login.php");
            } else {
                $mensagem = "Erro ao cadastrar: " . $conexao->error;
                $tipo_mensagem = 'danger';
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Controle Financeiro</title>
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
                            <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold">Criar Conta</h3>
                            <p class="text-muted">Comece a controlar suas finanças</p>
                        </div>
                        
                        <?php if(!empty($mensagem)): ?>
                            <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show">
                                <i class="fas fa-<?php echo $tipo_mensagem === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                                <?php echo $mensagem; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="nome" class="form-label">
                                    <i class="fas fa-user me-2"></i>Nome Completo
                                </label>
                                <input type="text" class="form-control" id="nome" name="nome" placeholder="Seu nome completo" required>
                            </div>
                            
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
                                <input type="password" class="form-control" id="password" name="password" placeholder="Mínimo 6 caracteres" minlength="6" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-user-plus me-2"></i>Cadastrar
                            </button>
                            
                            <div class="text-center">
                                <p class="mb-0">Já tem uma conta? 
                                    <a href="login.php" class="text-decoration-none">Faça login</a>
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