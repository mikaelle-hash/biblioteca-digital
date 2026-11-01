<?php
require_once "config/proteger.php";

$grupo = $_SESSION["grupo"];
$nome = $_SESSION["nome"];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Biblioteca Digital</title>

    <link rel="stylesheet" href="css/estilo.css">
    <script src="js/script.js" defer></script>
</head>

<body>

<header>
    <h1>Biblioteca Digital</h1>
    <p>Sistema de Gerenciamento de Biblioteca</p>
</header>

<nav>
    <a href="index.php">Início</a>
    <a href="livros.php">Livros</a>

    <?php if ($grupo === "Administradores" || $grupo === "Bibliotecários"): ?>

        <a href="cadastro_livros.php">Cadastrar Livro</a>
        <a href="pages/usuarios.php">Usuários</a>
        <a href="pages/cadastro_usuarios.php">Cadastrar Usuário</a>
        <a href="pages/novo_emprestimo.php">Novo Empréstimo</a>
        <a href="pages/devolucao.php">Devolução</a>

    <?php endif; ?>

    <a href="pages/emprestimos.php">Empréstimos</a>
    <a href="logout.php">Sair</a>
</nav>

<main>

    <h2>Bem-vindo(a), <?= htmlspecialchars($nome) ?>!</h2>

    <p>
        Você está acessando a Biblioteca Digital
        como <strong><?= htmlspecialchars($grupo) ?></strong>.
    </p>

    <p>
        Nosso sistema facilita o gerenciamento de livros,
        usuários e empréstimos.
    </p>

    <p>
        Escolha uma das funcionalidades disponíveis no menu
        para começar.
    </p>

</main>

<footer>
    <p>Biblioteca Digital - Projeto Acadêmico</p>
</footer>

</body>
</html>
