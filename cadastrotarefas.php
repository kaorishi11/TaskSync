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
    <title>Cadastro de Tarefa</title>
</head>
<body>
    <h1>Cadastro de Tarefa</h1>
    <form action="salvartarefa.php" method="POST">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" required>
        <br>
        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao"></textarea>
        <br>
        <label for="prioridade">Prioridade:</label>
        <select id="prioridade" name="prioridade">
            <option value="baixa">Baixa</option>
            <option value="media">Média</option>
            <option value="alta">Alta</option>
        </select>
        <br>
        <input type="submit" value="Cadastrar">
    </form>
    <a href="gerenciamento.php">Voltar</a>
</body>
</html>