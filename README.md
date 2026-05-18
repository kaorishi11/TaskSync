# TaskSync - Sistema de Gerenciamento de Tarefas

O **TaskSync** é uma aplicação web desenvolvida para gerenciamento de tarefas corporativas utilizando o conceito de quadro Kanban.

O sistema permite:

- Cadastro de usuários
- Cadastro de tarefas
- Edição e exclusão de tarefas
- Organização por status:
  - A Fazer
  - Fazendo
  - Concluído
- Alteração de status das tarefas
- Perfil do usuário com foto
- Interface responsiva e moderna

---

# Tecnologias Utilizadas

- HTML5
- CSS3
- JavaScript
- PHP
- MySQL
- XAMPP

---

# Estrutura de Pastas

```bash
TaskSync/
│
├── database/
│   ├── tarefas.sql
│
├── docs/
│   ├── ERDDiagram.png
│   └── UseCaseDiagram.png
│
├── images/
│   └── logo.png
│
├── uploads/
│
├── conexao.php
├── index.php
├── cadastro.php
├── gerenciamento.php
├── cadastrotarefas.php
├── editar.php
├── excluir.php
├── perfil.php
├── logout.php
└── README.md

```
# Banco de Dados

Importe o arquivo:
``
tarefas.sql
``
no **phpMyAdmin** ou outro gerenciador MySQL.

---

# Como Executar o Projeto

## 1 Clonar o repositório

```bash
git clone https://github.com/kaorishi11/TaskSync.git
```

---

## 2 Mover para o XAMPP

Coloque a pasta do projeto dentro de:

```bash
htdocs
```

Exemplo:

```bash
C:\xampp\htdocs\TaskSync
```

---

## 3 Iniciar o servidor

Abra o **XAMPP** e inicie:

- Apache
- MySQL

---

# Como Acessar

Abra no navegador:

```bash
http://localhost/TaskSync
```

---

# Usuários para Teste

```txt
Email: kaorishimada11@gmail.com
Senha: 123456
```

```txt
Email: jamilefranquilim@gmail.com
Senha: 123456
```

---

# Desenvolvedor

- kaorishi11
