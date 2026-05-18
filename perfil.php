<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$idUsuario = $_SESSION['usuario_id'];

$stmt = $conexao->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$mensagem = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $foto = $usuario['foto'];

    if (!empty($_FILES['foto']['name'])) {
        $pasta = 'uploads/';
        if (!is_dir($pasta)) mkdir($pasta, 0777, true);
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif'])) {
            $foto = $pasta . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
        } else {
            $mensagem = "Formato inválido. Use JPG, PNG ou GIF.";
        }
    }

    if (empty($mensagem)) {
        $update = $conexao->prepare("UPDATE usuarios SET nome=?, email=?, foto=? WHERE id_usuario=?");
        $update->bind_param("sssi", $nome, $email, $foto, $idUsuario);
        if ($update->execute()) {
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_foto'] = $foto;
            $sucesso = "Perfil atualizado com sucesso!";
            $usuario['nome'] = $nome;
            $usuario['email'] = $email;
            $usuario['foto'] = $foto;
        } else {
            $mensagem = "Erro ao atualizar perfil.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Meu Perfil</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(300deg, #809289, #C4D4A9);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', system-ui, -apple-system, 'Roboto', sans-serif;
            padding: 2rem;
        }

        .container {
            max-width: 560px;
            width: 100%;
            background: white;
            border-radius: 32px;
            padding: 44px 40px;
            box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.25);
            transition: all 0.2s;
        }

        .header {
            text-align: center;
            margin-bottom: 32px;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #2d4a3a;
            margin-bottom: 8px;
        }

        .header p {
            color: #5a7a6a;
            font-size: 0.9rem;
        }

        .profile-image {
            text-align: center;
            margin-bottom: 32px;
            position: relative;
        }

        .profile-image img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
            border: 4px solid #C4D4A9;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d4a3a;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid #e2ece2;
            border-radius: 20px;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s;
            background: #fefefe;
        }

        .form-group input:focus {
            border-color: #809289;
            box-shadow: 0 0 0 3px rgba(128, 146, 137, 0.15);
        }

        .form-group input[type="file"] {
            padding: 12px;
            background: #f8faf8;
        }

        .btn-save {
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
            margin-top: 12px;
        }

        .btn-save:hover {
            background: #4a7a5a;
            transform: translateY(-2px);
        }

        .message {
            padding: 14px 18px;
            border-radius: 20px;
            margin-bottom: 24px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .message.success {
            background: #e0f2e5;
            color: #1e6f3f;
            border-left: 4px solid #2c7a4d;
        }

        .message.error {
            background: #fee2e2;
            color: #b91c1c;
            border-left: 4px solid #b91c1c;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 28px;
            text-decoration: none;
            color: #5a7a6a;
            font-weight: 500;
            transition: 0.2s;
        }

        .back-link:hover {
            color: #2d4a3a;
            text-decoration: underline;
        }

        hr {
            margin: 24px 0 8px;
            border: none;
            height: 1px;
            background: #e2ece2;
        }

        @media (max-width: 560px) {
            .container {
                padding: 32px 24px;
            }
            .profile-image img {
                width: 100px;
                height: 100px;
            }
            .header h1 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Meu perfil</h1>
        <p>Gerencie suas informações pessoais</p>
    </div>

    <?php if($sucesso): ?>
        <div class="message success">✓ <?= $sucesso ?></div>
    <?php endif; ?>

    <?php if($mensagem): ?>
        <div class="message error">⚠ <?= $mensagem ?></div>
    <?php endif; ?>

    <div class="profile-image">
        <?php if($usuario['foto']): ?>
            <img src="<?= $usuario['foto'] ?>" alt="Foto de perfil">
        <?php else: ?>
            <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Avatar padrão">
        <?php endif; ?>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nome completo</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        </div>
        <div class="form-group">
            <label>E-mail</label>
            <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>
        <div class="form-group">
            <label>Foto de perfil</label>
            <input type="file" name="foto" accept="image/*">
        </div>
        <button type="submit" class="btn-save">Salvar alterações</button>
    </form>

    <hr>
    <a href="gerenciamento.php" class="back-link">← Voltar ao painel</a>
</div>
</body>
</html>