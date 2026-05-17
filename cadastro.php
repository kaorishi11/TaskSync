<?php
session_start();
require 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $foto = null;


    $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->bind_param('s', $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Este e-mail já está cadastrado!'); window.history.back();</script>";
        exit;
    }

    if (!empty($_FILES['foto']['name'])) {
        $pasta = 'uploads/';
        if (!is_dir($pasta)) mkdir($pasta, 0777, true);

        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif'])) {
            $foto = $pasta . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
        } else {
            $erro = 'Tipo de arquivo inválido. Apenas JPG, PNG ou GIF.';
        }
    }

    if (!$erro) {
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, foto) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $nome, $email, $senha, $foto);
        if ($stmt->execute()) {
            header('Location: index.php?msg=conta_criada');
            exit;
        } else {
            $erro = 'Erro ao cadastrar: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
</head>
<body>
    <h1>Cadastro</h1>
    <form action="process_cadastro.php" method="POST">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required><br><br>

        <input type="submit" value="Cadastrar">
    </form>
    <p>Já tem uma conta? <a href="index.php">Faça login aqui</a></p>
</body>
</html>