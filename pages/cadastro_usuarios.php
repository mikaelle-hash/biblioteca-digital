<?php
require_once "../config/conexao.php";

require_once "../config/proteger.php";

exigirGrupo(["Administradores", "Bibliotecários"]);

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {
        $nome = trim($_POST["nome"]);
        $email = trim($_POST["email"]);
        $senha = $_POST["senha"];
        $idGrupo = $_POST["id_grupo"];

        if (strlen($senha) < 8) {
            throw new Exception("A senha deve ter pelo menos 8 caracteres.");
        }

        // Protege a senha utilizando hash
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Gera o identificador do usuário
        $idUsuario = $conexao
            ->query("SELECT fn_gerar_id()")
            ->fetchColumn();

        // Insere o novo usuário
        $sql = "INSERT INTO usuarios
                (id_usuario, id_grupo, nome, email, senha_hash, ativo)
                VALUES (?, ?, ?, ?, ?, 1)";

        $stmt = $conexao->prepare($sql);

        $stmt->execute([
            $idUsuario,
            $idGrupo,
            $nome,
            $email,
            $senhaHash
        ]);

        $mensagem = "Usuário cadastrado com sucesso!";
        $tipoMensagem = "sucesso";

    } catch (PDOException $erro) {

        if ($erro->getCode() == "23000") {
            $mensagem = "Este e-mail já está cadastrado.";
        } else {
            $mensagem = "Erro ao cadastrar usuário: " . $erro->getMessage();
        }

        $tipoMensagem = "erro";

    } catch (Exception $erro) {

        $mensagem = $erro->getMessage();
        $tipoMensagem = "erro";
    }
}

// Busca os grupos disponíveis
$grupos = $conexao->query(
    "SELECT id_grupo, nome
     FROM grupos_usuarios
     ORDER BY nome"
)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro de Usuários</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>

<header>
    <h1>Cadastro de Usuários</h1>
</header>

<nav>
    <a href="../index.php">Início</a>
    <a href="usuarios.php">Ver usuários</a>
</nav>

<main>

    <h2>Novo usuário</h2>

    <?php if ($mensagem): ?>
        <p class="<?= $tipoMensagem ?>">
            <?= htmlspecialchars($mensagem) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>Nome completo:</label>

        <input
            type="text"
            name="nome"
            required
        >

        <label>E-mail:</label>

        <input
            type="email"
            name="email"
            required
        >

        <label>Senha:</label>

        <input
            type="password"
            name="senha"
            minlength="8"
            required
        >

        <label>Grupo de usuário:</label>

        <select name="id_grupo" required>

            <option value="">Selecione um grupo</option>

            <?php foreach ($grupos as $grupo): ?>

                <option value="<?= htmlspecialchars($grupo['id_grupo']) ?>">
                    <?= htmlspecialchars($grupo['nome']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <button type="submit">
            Cadastrar usuário
        </button>

    </form>

</main>

</body>
</html>
