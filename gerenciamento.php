<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$idUsuario = $_SESSION['usuario_id'];

$stmt = $conexao->prepare("SELECT * FROM tarefas WHERE id_usuario = ? ORDER BY id_tarefa DESC");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();

$tarefasAFazer = [];
$tarefasFazendo = [];
$tarefasConcluido = [];

while ($tarefa = $resultado->fetch_assoc()) {
    if ($tarefa['status'] == 'a fazer') $tarefasAFazer[] = $tarefa;
    elseif ($tarefa['status'] == 'fazendo') $tarefasFazendo[] = $tarefa;
    elseif ($tarefa['status'] == 'concluido') $tarefasConcluido[] = $tarefa;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, 'Roboto', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: #eef5ea;
        }

        .sidebar {
            width: 280px;
            background: white;
            padding: 32px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid #d4e0d4;
        }

        .logo {
            text-align: center;
        }

        .logo img {
            width: 150px;
            margin-bottom: 12px;
        }

        .logo h2 {
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            color: #2d4a3a;
            margin-top: 8px;
        }

        .menu {
            margin-top: 48px;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            background: #eef5ea;
            color: #2d4a3a;
            padding: 14px 18px;
            border-radius: 20px;
            margin-bottom: 16px;
            transition: 0.2s;
            font-weight: 500;
        }

        .menu a:hover {
            background: linear-gradient(300deg, #809289, #C4D4A9);
            color: #2d4a3a;
        }

        .profile {
            margin-bottom: 20px;
        }

        .profile a {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #eef5ea;
            padding: 12px 16px;
            border-radius: 28px;
            text-decoration: none;
            color: #2d4a3a;
        }

        .profile a:hover {
            background: linear-gradient(300deg, #809289, #C4D4A9);
            color: #2d4a3a;
        }

        .profile img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #809289;
        }

        .logout a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #eef5ea;
            text-decoration: none;
            color: #8a5a5a;
            padding: 12px;
            border-radius: 40px;
            font-weight: 500;
        }

        .logout a:hover {
            background: #c2412c;
            color: white;
        }

        .main {
            flex: 1;
            padding: 32px 36px;
            overflow-x: auto;
        }

        .main h1 {
            font-size: 2rem;
            color: #2d4a3a;
            margin-bottom: 32px;
            font-weight: 700;
        }

        .columns {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .column {
            background: white;
            border-radius: 28px;
            padding: 20px;
            min-height: 550px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #d4e0d4;
        }

        .column h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 3px solid #809289;
            display: inline-block;
            color: #2d4a3a;
        }

        .card {
            background: #fefef8;
            border-radius: 20px;
            padding: 18px;
            margin-bottom: 18px;
            border: 1px solid #e2ece2;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }

        .card h3 {
            font-size: 1.2rem;
            color: #2d4a3a;
            margin-bottom: 8px;
        }

        .card p {
            color: #5a7a6a;
            font-size: 0.9rem;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .prioridade {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .baixa { background: #e0f2e5; color: #1e6f3f; }
        .media { background: #fff0db; color: #b45309; }
        .alta { background: #fee2e2; color: #b91c1c; }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .actions a {
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: 0.2s;
        }

        .editar {
            background: #809289;
            color: white;
        }

        .excluir {
            background: #c2412c;
            color: white;
        }

        .actions a:hover {
            filter: brightness(0.9);
            transform: translateY(-1px);
        }

        @media (max-width: 1000px) {
            .sidebar { width: 240px; }
            .columns { grid-template-columns: 1fr; gap: 20px; }
            .main { padding: 24px; }
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div>
        <div class="logo">
            <img src="images/logo.png" alt="TaskSync">
        </div>
        <div class="menu">
            <a href="cadastrotarefas.php"><i class="fa-solid fa-plus"></i> Nova Tarefa</a>
        </div>
    </div>
    <div>
        <div class="profile">
            <a href="perfil.php">
                <?php if(!empty($_SESSION['usuario_foto'])): ?>
                    <img src="<?= $_SESSION['usuario_foto'] ?>">
                <?php else: ?>
                    <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png">
                <?php endif; ?>
                <span><?= $_SESSION['usuario_nome'] ?></span>
            </a>
        </div>
        <div class="logout">
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
        </div>
    </div>
</div>
<div class="main">
    <h1>Gerenciamento</h1>
    <div class="columns">
        <div class="column">
            <h2>A Fazer</h2>
            <?php foreach($tarefasAFazer as $tarefa): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($tarefa['setor']) ?></h3>
                    <p><?= htmlspecialchars($tarefa['descricao']) ?></p>
                    <span class="prioridade <?= $tarefa['prioridade'] ?>"><?= ucfirst($tarefa['prioridade']) ?></span>
                    <div class="actions">
                        <a class="editar" href="editar.php?id=<?= $tarefa['id_tarefa'] ?>">Editar</a>
                        <a class="excluir" href="excluir.php?id=<?= $tarefa['id_tarefa'] ?>">Excluir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="column">
            <h2>Fazendo</h2>
            <?php foreach($tarefasFazendo as $tarefa): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($tarefa['setor']) ?></h3>
                    <p><?= htmlspecialchars($tarefa['descricao']) ?></p>
                    <span class="prioridade <?= $tarefa['prioridade'] ?>"><?= ucfirst($tarefa['prioridade']) ?></span>
                    <div class="actions">
                        <a class="editar" href="editar.php?id=<?= $tarefa['id_tarefa'] ?>">Editar</a>
                        <a class="excluir" href="excluir.php?id=<?= $tarefa['id_tarefa'] ?>">Excluir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="column">
            <h2>Concluído</h2>
            <?php foreach($tarefasConcluido as $tarefa): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($tarefa['setor']) ?></h3>
                    <p><?= htmlspecialchars($tarefa['descricao']) ?></p>
                    <span class="prioridade <?= $tarefa['prioridade'] ?>"><?= ucfirst($tarefa['prioridade']) ?></span>
                    <div class="actions">
                        <a class="editar" href="editar.php?id=<?= $tarefa['id_tarefa'] ?>">Editar</a>
                        <a class="excluir" href="excluir.php?id=<?= $tarefa['id_tarefa'] ?>">Excluir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</body>
</html>