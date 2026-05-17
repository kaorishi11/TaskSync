<?php
session_start();
require 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $foto = null;

    $check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $check->bind_param('s', $email);
    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $erro = 'Este e-mail já está cadastrado!';
    }

    if (!$erro && !empty($_FILES['foto']['name'])) {
        $pasta = 'uploads/';
        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {

            $foto = $pasta . time() . '_' . uniqid() . '.' . $ext;

            move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
        } else {
            $erro = 'Tipo de arquivo inválido!';
        }
    }

    if (!$erro) {
        $stmt = $conn->prepare("
            INSERT INTO usuarios (nome, email, senha, foto)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('ssss', $nome, $email, $senha, $foto);
        if ($stmt->execute()) {
            header('Location: index.php?msg=conta_criada');
            exit;
        } else {
            $erro = 'Erro ao cadastrar!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family: Arial, Helvetica, sans-serif;
        }
        body{
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:#f2f2f2;
        }
        .container{
            width:950px;
            height:600px;
            background:white;
            border-radius:20px;
            overflow:hidden;
            display:flex;
            box-shadow:0 10px 30px rgba(0,0,0,0.2);
        }
        .left{
            width:50%;
            background:linear-gradient(135deg, #1DB954, #169c46);
            color:white;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            text-align:center;
            padding:40px;
        }
        .left img{
            width:130px;
            margin-bottom:20px;
        }
        .left h1{
            font-size:38px;
            margin-bottom:15px;
        }
        .left p{
            font-size:18px;
            max-width:300px;
        }
        .right{
            width:50%;
            padding:50px;
            display:flex;
            flex-direction:column;
            justify-content:center;
        }
        .right h2{
            margin-bottom:10px;
            color:#222;
        }
        .right .sub{
            color:#777;
            margin-bottom:25px;
        }
        form{
            display:flex;
            flex-direction:column;
        }
        input{
            padding:14px;
            margin-bottom:15px;
            border:1px solid #ccc;
            border-radius:10px;
            outline:none;
            transition:0.3s;
        }
        input:focus{
            border-color:#1DB954;
        }
        input[type="file"]{
            padding:10px;
        }
        button{
            padding:14px;
            border:none;
            border-radius:10px;
            background:#1DB954;
            color:white;
            font-size:16px;
            cursor:pointer;
            transition:0.3s;
        }
        button:hover{
            background:#169c46;
        }
        .erro{
            background:#ffdede;
            color:#d8000c;
            padding:12px;
            border-radius:10px;
            margin-bottom:15px;
        }
        .login-link{
            margin-top:20px;
            text-align:center;
        }
        .login-link a{
            color:#1DB954;
            text-decoration:none;
            font-weight:bold;
        }
        @media(max-width: 768px){
            .container{
                flex-direction:column;
                width:95%;
                height:auto;
            }
            .left,
            .right{
                width:100%;
            }
            .left{
                padding:50px 20px;
            }
        }
    </style>
    </head>
    <body>
    <div class="container">
        <div class="left">
            <img src="../images/logo.png" alt="Logo">
            <h1>Bem-vindo!</h1>
            <p>Crie sua conta e organize suas tarefas com facilidade e rapidez.</p>
        </div>
        <div class="right">
            <h2>Criar Conta</h2>
            <p class="sub">Preencha os dados abaixo</p>
            <?php if($erro): ?>
                <div class="erro"><?= $erro ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input
                    type="text"
                    name="nome"
                    placeholder="Digite seu nome"
                    required
                >
                <input
                    type="email"
                    name="email"
                    placeholder="Digite seu email"
                    required
                >
                <input
                    type="password"
                    name="senha"
                    placeholder="Digite sua senha"
                    required
                >
                <input
                    type="file"
                    name="foto"
                    accept="image/*"
                >
                <button type="submit">Cadastrar</button>
            </form>
            <div class="login-link">Já possui conta?<a href="index.php">Fazer login</a></div>
        </div>
    </div>
    </body>
</html>