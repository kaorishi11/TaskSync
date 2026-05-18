<?php
session_start();
require 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $foto = null;

    $check = $conexao->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $check->bind_param('s', $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $erro = 'Este e-mail já está cadastrado.';
    }

    if (!$erro && !empty($_FILES['foto']['name'])) {
        $pasta = 'uploads/';
        if (!is_dir($pasta)) mkdir($pasta, 0777, true);
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $foto = $pasta . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
        } else {
            $erro = 'Formato de imagem inválido (use JPG, PNG ou GIF).';
        }
    }

    if (!$erro) {
        $stmt = $conexao->prepare("INSERT INTO usuarios (nome, email, senha, foto) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $nome, $email, $senha, $foto);
        if ($stmt->execute()) {
            header('Location: index.php?msg=conta_criada');
            exit;
        } else {
            $erro = 'Erro interno ao cadastrar.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Cadastro</title>
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

        .register-form {
            flex: 1;
            padding: 48px 44px;
        }

        .register-form h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #2d4a3a;
            margin-bottom: 8px;
        }

        .register-form .sub {
            color: #3a5a4a;
            margin-bottom: 28px;
            font-size: 0.9rem;
        }

        .input-group {
            margin-bottom: 18px;
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
            padding: 12px 16px;
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

        .input-group input[type="file"] {
            padding: 10px;
        }

        .btn-register {
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

        .btn-register:hover {
            background: #4a7a5a;
            transform: translateY(-1px);
        }

        .login-link {
            text-align: center;
            margin-top: 24px;
            color: #2d4a3a;
        }

        .login-link a {
            color: #2d4a3a;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
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

        @media (max-width: 750px) {
            .card {
                flex-direction: column;
                max-width: 450px;
            }
            .register-form, .hero {
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
    <div class="hero">
        <img src="images/logo.png" alt="TaskSync Solutions">
        <p>Junte-se a nós e simplifique seu dia a dia.</p>
    </div>
    <div class="register-form">
        <h1>Criar conta</h1>
        <p class="sub">Preencha os campos abaixo</p>

        <?php if($erro): ?>
            <div class="error-message"><?= $erro ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label>Nome completo</label>
                <input type="text" name="nome" placeholder="Seu nome" required>
            </div>
            <div class="input-group">
                <label>E-mail</label>
                <input type="email" name="email" placeholder="seu@email.com" required>
            </div>
            <div class="input-group">
                <label>Senha</label>
                <input type="password" name="senha" placeholder="••••••••" required>
            </div>
            <div class="input-group">
                <label>Foto de perfil (opcional)</label>
                <input type="file" name="foto" accept="image/*">
            </div>
            <button type="submit" class="btn-register">Cadastrar</button>
        </form>

        <div class="login-link">
            Já possui conta? <a href="index.php">Fazer login</a>
        </div>
    </div>
</div>
</body>
</html>