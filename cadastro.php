<?php
require_once "config/conexao.php";

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {
        $nome = trim($_POST["nome"]);
        $email = trim($_POST["email"]);
        $senha = $_POST["senha"];
        $confirmarSenha = $_POST["confirmar_senha"];

        if ($nome === "" || $email === "") {
            throw new Exception("Preencha todos os campos.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Informe um e-mail válido.");
        }

        if (strlen($senha) < 8) {
            throw new Exception("A senha deve ter pelo menos 8 caracteres.");
        }

        if ($senha !== $confirmarSenha) {
            throw new Exception("As senhas não são iguais.");
        }

        // Localiza automaticamente o grupo Leitores
        $consultaGrupo = $conexao->prepare(
            "SELECT id_grupo FROM grupos_usuarios WHERE nome = ?"
        );

        $consultaGrupo->execute(["Leitores"]);
        $grupo = $consultaGrupo->fetch(PDO::FETCH_ASSOC);

        if (!$grupo) {
            throw new Exception("Grupo de leitores não encontrado.");
        }

        // Gera identificador e protege a senha
        $idUsuario = $conexao
            ->query("SELECT fn_gerar_id()")
            ->fetchColumn();

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Cadastra sempre como Leitor
        $sql = "INSERT INTO usuarios
            (id_usuario, id_grupo, nome, email, senha_hash, ativo)
            VALUES (?, ?, ?, ?, ?, 1)";

        $stmt = $conexao->prepare($sql);

        $stmt->execute([
            $idUsuario,
            $grupo["id_grupo"],
            $nome,
            $email,
            $senhaHash
        ]);

        $mensagem = "Conta criada com sucesso! Você já pode entrar.";
        $tipoMensagem = "sucesso";

    } catch (PDOException $erro) {

        if ($erro->getCode() == "23000") {
            $mensagem = "Este e-mail já está cadastrado.";
        } else {
            $mensagem = "Erro ao criar conta. Verifique os dados e tente novamente.";
        }

        $tipoMensagem = "erro";

    } catch (Exception $erro) {

        $mensagem = $erro->getMessage();
        $tipoMensagem = "erro";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta - Biblioteca Digital</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

<header>
    <h1>Biblioteca Digital</h1>
    <p>Crie sua conta gratuitamente</p>
</header>

<main>

    <h2>Criar uma conta</h2>

    <?php if ($mensagem): ?>
        <p class="<?= $tipoMensagem ?>">
            <?= htmlspecialchars($mensagem) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>Nome completo:</label>
        <input type="text" name="nome" required>

        <label>E-mail:</label>
        <input type="email" name="email" required>

        <label>Senha:</label>
        <input type="password" name="senha" minlength="8" required>

        <label>Confirmar senha:</label>
        <input type="password" name="confirmar_senha"
               minlength="8" required>

        <button type="submit">Criar conta</button>

    </form>

    <p>Já possui uma conta?</p>
    <a href="login.php">Entrar no sistema</a>

</main>

<footer>
    <p>Biblioteca Digital - Projeto Acadêmico</p>
</footer>

</body>
</html>
