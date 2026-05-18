<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: gerenciamento.php");
    exit();
}

$id = $_GET['id'];
$idUsuario = $_SESSION['usuario_id'];

$stmt = $conexao->prepare("SELECT * FROM tarefas WHERE id_tarefa = ? AND id_usuario = ?");
$stmt->bind_param("ii", $id, $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header("Location: gerenciamento.php");
    exit();
}
$tarefa = $resultado->fetch_assoc();
$mensagem = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = trim($_POST['descricao']);
    $setor = trim($_POST['setor']);
    $prioridade = $_POST['prioridade'];
    $status = $_POST['status'];

    if (empty($descricao) || empty($setor)) {
        $mensagem = "Preencha todos os campos obrigatórios.";
    } else {
        $update = $conexao->prepare("UPDATE tarefas SET descricao=?, setor=?, prioridade=?, status=? WHERE id_tarefa=? AND id_usuario=?");
        $update->bind_param("ssssii", $descricao, $setor, $prioridade, $status, $id, $idUsuario);
        if ($update->execute()) {
            $sucesso = "Tarefa atualizada com sucesso!";
            $tarefa['descricao'] = $descricao;
            $tarefa['setor'] = $setor;
            $tarefa['prioridade'] = $prioridade;
            $tarefa['status'] = $status;
        } else {
            $mensagem = "Erro ao atualizar tarefa.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Editar Tarefa</title>
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
            max-width: 580px;
            width: 100%;
            background: white;
            border-radius: 32px;
            padding: 44px 40px;
            box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.25);
        }

        .header {
            text-align: center;
            margin-bottom: 32px;
        }

        .header h1 {
            font-size: 1.9rem;
            font-weight: 700;
            color: #2d4a3a;
            margin-bottom: 8px;
        }

        .header p {
            color: #5a7a6a;
            font-size: 0.9rem;
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
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid #e2ece2;
            border-radius: 20px;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s;
            background: #fefefe;
            font-family: inherit;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 110px;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #809289;
            box-shadow: 0 0 0 3px rgba(128, 146, 137, 0.15);
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
            .header h1 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Editar tarefa</h1>
        <p>Altere as informações da sua tarefa</p>
    </div>

    <?php if($sucesso): ?>
        <div class="message success">✓ <?php echo $sucesso; ?></div>
    <?php endif; ?>

    <?php if($mensagem): ?>
        <div class="message error">⚠ <?php echo $mensagem; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Descrição</label>
            <textarea name="descricao" required><?php echo htmlspecialchars($tarefa['descricao']); ?></textarea>
        </div>
        <div class="form-group">
            <label>Setor / Departamento</label>
            <input type="text" name="setor" value="<?php echo htmlspecialchars($tarefa['setor']); ?>" required>
        </div>
        <div class="form-group">
            <label>Prioridade</label>
            <select name="prioridade">
                <option value="baixa" <?php echo ($tarefa['prioridade'] == 'baixa') ? 'selected' : ''; ?>>Baixa</option>
                <option value="media" <?php echo ($tarefa['prioridade'] == 'media') ? 'selected' : ''; ?>>Média</option>
                <option value="alta" <?php echo ($tarefa['prioridade'] == 'alta') ? 'selected' : ''; ?>>Alta</option>
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="a fazer" <?php echo ($tarefa['status'] == 'a fazer') ? 'selected' : ''; ?>>A Fazer</option>
                <option value="fazendo" <?php echo ($tarefa['status'] == 'fazendo') ? 'selected' : ''; ?>>Fazendo</option>
                <option value="concluido" <?php echo ($tarefa['status'] == 'concluido') ? 'selected' : ''; ?>>Concluído</option>
            </select>
        </div>
        <button type="submit" class="btn-save">Salvar alterações</button>
    </form>

    <hr>
    <a href="gerenciamento.php" class="back-link">← Voltar ao painel</a>
</div>
</body>
</html>