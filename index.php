<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id_usuario'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_foto'] = $usuario['foto'];

        header('Location: gerenciamento.php');
        exit;
    } else {
        $erro = 'Email ou senha inválidos';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #eef5ea;
            font-family: 'Segoe UI', system-ui, -apple-system, 'Roboto', sans-serif;
            padding: 20px;
        }

        .card {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: linear-gradient(300deg, #809289, #C4D4A9);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
        }

        .login-form {
            flex: 1;
            padding: 48px 44px;
        }

        .login-form h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #2d4a3a;
            margin-bottom: 8px;
        }

        .login-form .sub {
            color: #3a5a4a;
            margin-bottom: 32px;
            font-size: 0.9rem;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2d4a3a;
            font-size: 0.85rem;
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #d4e0d4;
            border-radius: 16px;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s;
            background: #fefefe;
        }

        .input-group input:focus {
            border-color: #5a8a6a;
            box-shadow: 0 0 0 3px rgba(90, 138, 106, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #2d4a3a;
            color: white;
            border: none;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 8px;
        }

        .btn-login:hover {
            background: #4a7a5a;
            transform: translateY(-1px);
        }

        .register-link {
            text-align: center;
            margin-top: 24px;
            color: #2d4a3a;
        }

        .register-link a {
            color: #2d4a3a;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px 16px;
            border-radius: 16px;
            margin-bottom: 24px;
            font-size: 0.85rem;
            border-left: 4px solid #b91c1c;
        }

        .hero {
            flex: 1;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 48px 32px;
        }

        .hero img {
            width: 160px;
            margin-bottom: 24px;
        }

        .hero p {
            color: #5a7a6a;
            max-width: 260px;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        @media (max-width: 750px) {
            .card {
                flex-direction: column;
                max-width: 450px;
            }
            .login-form, .hero {
                padding: 36px 28px;
            }
            .hero img {
                width: 120px;
            }
        }
    </style>
</head>
<body>
<div class="card">
    <div class="login-form">
        <h1>Acessar conta</h1>
        <p class="sub">Digite suas credenciais para continuar</p>

        <?php if(isset($erro)): ?>
            <div class="error-message"><?= $erro ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>E-mail</label>
                <input type="email" name="email" placeholder="seu@email.com" required>
            </div>
            <div class="input-group">
                <label>Senha</label>
                <input type="password" name="senha" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>

        <div class="register-link">
            Não tem conta? <a href="cadastro.php">Criar conta</a>
        </div>
    </div>

    <div class="hero">
        <img src="images/logo.png" alt="TaskSync Solutions">
        <p>Organize suas tarefas com fluidez e clareza.</p>
    </div>
</div>
</body>
</html>