<?php
require_once "../config/conexao.php";
require_once "../config/proteger.php";

exigirGrupo(["Administradores", "Bibliotecários"]);

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $idUsuario = $_POST["id_usuario"];
        $idLivro = $_POST["id_livro"];
        $quantidade = (int) $_POST["quantidade"];
        $dataPrevista = $_POST["data_prevista_devolucao"];

        if ($quantidade < 1) {
            throw new Exception("A quantidade deve ser maior que zero.");
        }

        $conexao->beginTransaction();

        // Gera o ID do empréstimo
        $idEmprestimo = $conexao
            ->query("SELECT fn_gerar_id()")
            ->fetchColumn();

        // Registra o empréstimo
        $sqlEmprestimo = "INSERT INTO emprestimos
            (id_emprestimo, id_usuario, data_emprestimo,
             data_prevista_devolucao, status)
            VALUES (?, ?, NOW(), ?, 'ATIVO')";

        $stmtEmprestimo = $conexao->prepare($sqlEmprestimo);

        $stmtEmprestimo->execute([
            $idEmprestimo,
            $idUsuario,
            $dataPrevista
        ]);

        // Registra o livro relacionado ao empréstimo
        $sqlItem = "INSERT INTO itens_emprestimo
            (id_emprestimo, id_livro, quantidade)
            VALUES (?, ?, ?)";

        $stmtItem = $conexao->prepare($sqlItem);

        $stmtItem->execute([
            $idEmprestimo,
            $idLivro,
            $quantidade
        ]);

        $conexao->commit();

        $mensagem = "Empréstimo registrado com sucesso!";
        $tipoMensagem = "sucesso";

    } catch (Exception $erro) {

        if ($conexao->inTransaction()) {
            $conexao->rollBack();
        }

        $mensagem = "Erro ao registrar empréstimo: " . $erro->getMessage();
        $tipoMensagem = "erro";
    }
}

// Busca usuários ativos
$usuarios = $conexao->query(
    "SELECT id_usuario, nome
     FROM usuarios
     WHERE ativo = 1
     ORDER BY nome"
)->fetchAll(PDO::FETCH_ASSOC);

// Busca livros disponíveis
$livros = $conexao->query(
    "SELECT id_livro, titulo, quantidade_disponivel
     FROM livros
     WHERE quantidade_disponivel > 0
     ORDER BY titulo"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Empréstimo</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>

<header>
    <h1>Registrar Empréstimo</h1>
</header>

<nav>
    <a href="../index.php">Início</a>
    <a href="emprestimos.php">Ver empréstimos</a>
</nav>

<main>

    <h2>Novo empréstimo</h2>

    <?php if ($mensagem): ?>
        <p class="<?= $tipoMensagem ?>">
            <?= htmlspecialchars($mensagem) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>Usuário:</label>

        <select name="id_usuario" required>
            <option value="">Selecione um usuário</option>

            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= htmlspecialchars($usuario['id_usuario']) ?>">
                    <?= htmlspecialchars($usuario['nome']) ?>
                </option>
            <?php endforeach; ?>

        </select>

        <label>Livro:</label>

        <select name="id_livro" required>
            <option value="">Selecione um livro</option>

            <?php foreach ($livros as $livro): ?>
                <option value="<?= htmlspecialchars($livro['id_livro']) ?>">
                    <?= htmlspecialchars($livro['titulo']) ?>
                    (Disponíveis: <?= $livro['quantidade_disponivel'] ?>)
                </option>
            <?php endforeach; ?>

        </select>

        <label>Quantidade:</label>

        <input
            type="number"
            name="quantidade"
            min="1"
            value="1"
            required
        >

        <label>Data prevista para devolução:</label>

        <input
            type="date"
            name="data_prevista_devolucao"
            required
        >

        <button type="submit">
            Registrar empréstimo
        </button>

    </form>

</main>

</body>
</html>
