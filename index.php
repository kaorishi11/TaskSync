<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    if ($usuario && password_verify($senha, $usuario['senha'])) {

        $_SESSION['usuario_id'] = $usuario['id_usuario'];
        $_SESSION['usuario_nome'] = $usuario['nome'];

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
    <title>Login</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }
        body{
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f2f2f2;
        }
        .container{
            width: 900px;
            height: 550px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .left{
            width: 50%;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .left h1{
            color: #1f1f1f;
            margin-bottom: 10px;
        }
        .left p{
            color: #777;
            margin-bottom: 30px;
        }
        form{
            display: flex;
            flex-direction: column;
        }
        input{
            padding: 14px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 10px;
            outline: none;
            transition: 0.3s;
        }
        input:focus{
            border-color: #1DB954;
        }
        button{
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #1DB954;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover{
            background: #17a74a;
        }
        .erro{
            color: red;
            margin-bottom: 15px;
        }
        .right{
            width: 50%;
            background: linear-gradient(135deg, #1DB954, #169c46);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px;
        }
        .right img{
            width: 120px;
            margin-bottom: 20px;
        }
        .right h2{
            font-size: 36px;
            margin-bottom: 15px;
        }
        .right p{
            font-size: 18px;
            max-width: 300px;
        }
        @media(max-width: 768px){
            .container{
                flex-direction: column;
                width: 95%;
                height: auto;
            }
            .left,
            .right{
                width: 100%;
            }
            .right{
                padding: 50px 20px;
            }
        }
    </style>
    </head>
    <body>
        <div class="container">
            <div class="left">
                <h1>Login</h1>
                <p>Entre para acessar suas tarefas</p>
                <form method="POST">

                    <?php if(isset($erro)): ?>
                        <div class="erro">
                            <?= $erro ?>
                        </div>
                    <?php endif; ?>

                    <input type="email" name="email" placeholder="Digite seu email" required>
                    <input type="password" name="senha" placeholder="Digite sua senha" required>
                    <button type="submit">Entrar</button>
                </form>
                <p style="margin-top:20px;">
                    Não possui conta?
                    <a href="cadastro.php" style="color:#1DB954;">Cadastre-se</a>
                </p>
            </div>
            <div class="right">
                <img src="../images/logo.png" alt="Logo">
                <h2>Bem-vindo!</h2>
                <p>
                    Organize suas tarefas de forma simples,
                    rápida e eficiente.
                </p>
            </div>
        </div>
    </body>
</html>