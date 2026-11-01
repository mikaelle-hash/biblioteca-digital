<?php
require_once "../config/conexao.php";
require_once "../config/proteger.php";

exigirGrupo(["Administradores", "Bibliotecários"]);

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $idEmprestimo = $_POST["id_emprestimo"];

        $conexao->beginTransaction();

        $sql = "UPDATE emprestimos
                SET status = 'DEVOLVIDO',
                    data_devolucao = NOW()
                WHERE id_emprestimo = ?
                AND status = 'ATIVO'";

        $stmt = $conexao->prepare($sql);
        $stmt->execute([$idEmprestimo]);

        if ($stmt->rowCount() === 0) {
            throw new Exception(
                "Empréstimo não encontrado ou já devolvido."
            );
        }

        $conexao->commit();

        $mensagem = "Devolução registrada com sucesso!";
        $tipoMensagem = "sucesso";

    } catch (Exception $erro) {

        if ($conexao->inTransaction()) {
            $conexao->rollBack();
        }

        $mensagem = "Erro ao registrar devolução: "
                    . $erro->getMessage();

        $tipoMensagem = "erro";
    }
}

// Consulta empréstimos ativos
$sql = "SELECT id_emprestimo, usuario,
               data_emprestimo,
               data_prevista_devolucao,
               status
        FROM vw_emprestimos_ativos
        ORDER BY data_emprestimo DESC";

$emprestimos = $conexao->query($sql)
                       ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devolução de Livros</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>

<header>
    <h1>Devolução de Livros</h1>
</header>

<nav>
    <a href="../index.php">Início</a>
    <a href="emprestimos.php">Ver empréstimos</a>
</nav>

<main>

    <h2>Registrar devolução</h2>

    <?php if ($mensagem): ?>
        <p class="<?= $tipoMensagem ?>">
            <?= htmlspecialchars($mensagem) ?>
        </p>
    <?php endif; ?>

    <?php if (count($emprestimos) > 0): ?>

        <form method="POST"
              onsubmit="return confirm('Confirmar a devolução deste empréstimo?')">

            <label>Selecione o empréstimo:</label>

            <select name="id_emprestimo" required>

                <option value="">Selecione um empréstimo</option>

                <?php foreach ($emprestimos as $emprestimo): ?>

                    <option value="<?= htmlspecialchars($emprestimo['id_emprestimo']) ?>">

                        <?= htmlspecialchars($emprestimo['usuario']) ?>

                        - Empréstimo:
                        <?= htmlspecialchars($emprestimo['id_emprestimo']) ?>

                        - Devolução prevista:
                        <?= htmlspecialchars($emprestimo['data_prevista_devolucao']) ?>

                    </option>

                <?php endforeach; ?>

            </select>

            <button type="submit">
                Confirmar devolução
            </button>

        </form>

    <?php else: ?>

        <p>Não existem empréstimos ativos para devolver.</p>

    <?php endif; ?>

</main>

</body>
</html>
