<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync Solutions</title>
</head>
<header>
    <img src="logo.png" alt="Logo">
    <ul>
        <li><a href="cadastrotarefas.php">Criar Nova Tarefa</a></li>
        <li><a href="perfil.php">Perfil</a></li>
        <li><a href="logout.php">Sair</a></li>
    </ul>
</header>
<body>
    <h1>Gerenciamento</h1>
    <p>Bem-vindo, <?php echo $_SESSION['user_name']; ?>!</p>
    <div>
        <h2>Minhas Tarefas</h2>
        <div class="aFazer">
            <h3>A Fazer</h3>
        </div>
        <div class="fazendo">
            <h3>Fazendo</h3>
        </div>
        <div class="concluido">
            <h3>Concluído</h3>
        </div>
    </div>
</body>
</html>