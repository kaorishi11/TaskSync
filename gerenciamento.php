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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
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
            transition: all 0.3s ease;
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

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 16px;
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

        .fab-mobile {
            display: none;
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #2d4a3a;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            transition: 0.2s;
        }

        .fab-mobile:hover {
            background: #1e3528;
            transform: scale(1.05);
        }

        .fab-mobile i {
            font-size: 24px;
        }

        .hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            position: absolute;
            top: 24px;
            right: 20px;
            z-index: 1001;
        }

        .hamburger span {
            display: block;
            width: 28px;
            height: 3px;
            background: #2d4a3a;
            margin: 5px 0;
            transition: 0.3s ease;
            border-radius: 2px;
        }

        .hamburger.ativo span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 5px);
        }

        .hamburger.ativo span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.ativo span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -5px);
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
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
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

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #809289;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 0.9rem;
        }

        @media (max-width: 700px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 10px 15px;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                border-right: none;
                border-bottom: 1px solid #d4e0d4;
                position: relative;
                min-height: 70px;
            }

            .logo {
                text-align: left;
            }

            .logo img {
                width: 60px;
            }

            .menu {
                display: none;
            }

            .nav-links {
                display: none;
                position: fixed;
                top: 0;
                right: 0;
                width: 70%;
                max-width: 280px;
                height: 100vh;
                background: white;
                flex-direction: column;
                padding: 80px 24px 24px;
                box-shadow: -4px 0 20px rgba(0,0,0,0.1);
                z-index: 1000;
                gap: 20px;
            }

            .nav-links.ativo {
                display: flex;
            }

            .hamburger {
                display: block;
            }

            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }

            .overlay.ativo {
                display: block;
            }

            .fab-mobile {
                display: flex;
            }

            .main {
                padding: 20px;
            }

            .columns {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .main h1 {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 1000px) and (min-width: 701px) {
            .sidebar {
                width: 220px;
            }

            .columns {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }

            .main {
                padding: 24px;
            }
        }
    </style>
</head>
<body>

<div class="overlay" id="overlay"></div>

<a href="cadastrotarefas.php" class="fab-mobile" id="fabMobile">
    <i class="fa-solid fa-plus"></i>
</a>

<div class="sidebar">
    <div class="logo">
        <img src="images/logo.png" alt="TaskSync">
    </div>
    
    <div class="menu">
        <a href="cadastrotarefas.php"><i class="fa-solid fa-plus"></i> Nova Tarefa</a>
    </div>

    <button class="hamburger" id="hamburgerBtn">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="nav-links" id="mobileMenu">
        <div class="profile">
            <a href="perfil.php">
                <?php if(!empty($_SESSION['usuario_foto'])): ?>
                    <img src="<?= $_SESSION['usuario_foto'] ?>" alt="Foto">
                <?php else: ?>
                    <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Perfil">
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
            <?php if(empty($tarefasAFazer)): ?>
                <div class="empty-state">
                    <i class="fa-regular fa-clock"></i>
                    <p>Nenhuma tarefa pendente</p>
                </div>
            <?php else: ?>
                <?php foreach($tarefasAFazer as $tarefa): ?>
                    <div class="card">
                        <h3><?= htmlspecialchars($tarefa['setor']) ?></h3>
                        <p><?= htmlspecialchars($tarefa['descricao']) ?></p>
                        <span class="prioridade <?= $tarefa['prioridade'] ?>"><?= ucfirst($tarefa['prioridade']) ?></span>
                        <div class="actions">
                            <a class="editar" href="editar.php?id=<?= $tarefa['id_tarefa'] ?>">Editar</a>
                            <a class="excluir" href="excluir.php?id=<?= $tarefa['id_tarefa'] ?>" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')">Excluir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="column">
            <h2>Fazendo</h2>
            <?php if(empty($tarefasFazendo)): ?>
                <div class="empty-state">
                    <i class="fa-regular fa-hourglass-half"></i>
                    <p>Nenhuma tarefa em andamento</p>
                </div>
            <?php else: ?>
                <?php foreach($tarefasFazendo as $tarefa): ?>
                    <div class="card">
                        <h3><?= htmlspecialchars($tarefa['setor']) ?></h3>
                        <p><?= htmlspecialchars($tarefa['descricao']) ?></p>
                        <span class="prioridade <?= $tarefa['prioridade'] ?>"><?= ucfirst($tarefa['prioridade']) ?></span>
                        <div class="actions">
                            <a class="editar" href="editar.php?id=<?= $tarefa['id_tarefa'] ?>">Editar</a>
                            <a class="excluir" href="excluir.php?id=<?= $tarefa['id_tarefa'] ?>" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')">Excluir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="column">
            <h2>Concluído</h2>
            <?php if(empty($tarefasConcluido)): ?>
                <div class="empty-state">
                    <i class="fa-regular fa-circle-check"></i>
                    <p>Nenhuma tarefa concluída</p>
                </div>
            <?php else: ?>
                <?php foreach($tarefasConcluido as $tarefa): ?>
                    <div class="card">
                        <h3><?= htmlspecialchars($tarefa['setor']) ?></h3>
                        <p><?= htmlspecialchars($tarefa['descricao']) ?></p>
                        <span class="prioridade <?= $tarefa['prioridade'] ?>"><?= ucfirst($tarefa['prioridade']) ?></span>
                        <div class="actions">
                            <a class="editar" href="editar.php?id=<?= $tarefa['id_tarefa'] ?>">Editar</a>
                            <a class="excluir" href="excluir.php?id=<?= $tarefa['id_tarefa'] ?>" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')">Excluir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const hamburger = document.getElementById('hamburgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('overlay');

    function toggleMenu() {
        mobileMenu.classList.toggle('ativo');
        overlay.classList.toggle('ativo');
        hamburger.classList.toggle('ativo');
        document.body.style.overflow = mobileMenu.classList.contains('ativo') ? 'hidden' : '';
    }

    hamburger.addEventListener('click', toggleMenu);

    overlay.addEventListener('click', () => {
        mobileMenu.classList.remove('ativo');
        overlay.classList.remove('ativo');
        hamburger.classList.remove('ativo');
        document.body.style.overflow = '';
    });

    const menuLinks = mobileMenu.querySelectorAll('a');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.remove('ativo');
            overlay.classList.remove('ativo');
            hamburger.classList.remove('ativo');
            document.body.style.overflow = '';
        });
    });
</script>
</body>
</html>