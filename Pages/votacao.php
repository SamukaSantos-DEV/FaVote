<?php
require '../php/session_auth.php';
require '../php/config.php';

// 🔹 Pega o ID da eleição via URL
$eleicao_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($eleicao_id <= 0) {
    die("Eleição inválida.");
}

// 🔹 Busca dados da eleição
$sqlEleicao = "SELECT * FROM eleicoes WHERE id = ?";
$stmt = $conexao->prepare($sqlEleicao);
$stmt->bind_param("i", $eleicao_id);
$stmt->execute();
$resultEleicao = $stmt->get_result();
$eleicao = $resultEleicao->fetch_assoc();
if (!$eleicao) {
    die("Eleição não encontrada.");
}

// 🔹 Busca candidatos da eleição
$sqlCandidatos = "
    SELECT c.id AS candidato_id, a.nome AS candidato_nome, c.proposta
    FROM candidatos c
    INNER JOIN alunos a ON c.aluno_ra = a.ra
    WHERE c.eleicao_id = ?
";
$stmt2 = $conexao->prepare($sqlCandidatos);
$stmt2->bind_param("i", $eleicao_id);
$stmt2->execute();
$resultCandidatos = $stmt2->get_result();

// 🔹 Processa o voto
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['candidato_id'])) {
    $candidato_id = (int) $_POST['candidato_id'];
    $aluno_ra = $_SESSION['user_ra'];
    $data_voto = date('Y-m-d H:i:s');

    // Verifica se já votou
    $check = $conexao->prepare("SELECT id FROM votos WHERE eleicao_id = ? AND aluno_ra = ?");
    $check->bind_param("is", $eleicao_id, $aluno_ra);
    $check->execute();
    $resultCheck = $check->get_result();
    if ($resultCheck->num_rows > 0) {
        echo "<script>alert('Você já votou nesta eleição!'); window.location='eleAtive.php';</script>";
        exit();
    }

    // Insere voto
    $sqlVoto = "INSERT INTO votos (eleicao_id, aluno_ra, candidato_id, data_voto) VALUES (?, ?, ?, ?)";
    $stmtVoto = $conexao->prepare($sqlVoto);
    $stmtVoto->bind_param("isis", $eleicao_id, $aluno_ra, $candidato_id, $data_voto);
    $stmtVoto->execute();

    echo "<script>alert('Voto registrado com sucesso!'); window.location='home.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Votação | FaVote</title>
    <link rel="stylesheet" href="../Styles/votacao.css">
    <link rel="icon" href="../Images/iconlogoFaVote.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <header class="header">
        <div class="logo"><img src="../Images/logofatec.png" width="190"></div>
        <nav class="nav">
            <a href="home.php">Home</a>
            <a href="eleAtive.php" class="active">Eleições Ativas</a>
            <a href="news.php">Notícias</a>
            <a href="elePassa.php">Eleições Passadas</a>
            <?php
            $emailLogado = $_SESSION['user_email'] ?? null;
            ?>
            <?php if ($emailLogado === 'admin@fatec.sp.gov.br'): ?>
                <a href="dashboard.php"
                    style="background-color: brown; color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; transition: background-color 0.6s ease;"
                    onmouseover="this.style.backgroundColor='#631212'" onmouseout="this.style.backgroundColor='brown'">
                    DASHBOARD
                </a>
            <?php endif; ?>
        </nav>
        <div class="user-icon">
            <img src="../Images/user.png" width="50" alt="user" />
            <div class="user-popup">

                <strong>
                    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </strong>

                <p>FATEC “Dr. Ogari de Castro Pacheco”</p> <strong>
                    <?php

                    // Supondo que o login salva o e-mail na sessão assim:
                    $emailLogado = $_SESSION['user_email'] ?? null;
                    ?>

                    <!-- ... resto do seu HTML ... -->

                    <?php if ($emailLogado !== 'admin@fatec.sp.gov.br'): ?>
                        <strong>
                            <p><?php echo htmlspecialchars($_SESSION['curso_nome']); ?></p>
                        </strong>
                        <p><?php echo htmlspecialchars($_SESSION['semestre_nome']); ?></p>
                    <?php endif; ?>

                    <div class="sair">
                        <a href="../php/logout.php">Sair<i style="margin-left: 5px;"
                                class="fa-solid fa-right-from-bracket"></i></a>
                    </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container-eleicao">
            <h1>ENVIAR MEU VOTO:</h1>
            <button class="btn-close" onclick="history.back()">✖</button>
        </div>

        <!-- 🔹 Mostra dados da eleição -->
        <h1 class="titulo-eleicao"><?= htmlspecialchars($eleicao['titulo']) ?></h1>
        <p class="subtitulo-eleicao">
            Início: <?= date("d/m/Y H:i", strtotime($eleicao['data_inicio'])) ?> <br>
            Término: <?= date("d/m/Y H:i", strtotime($eleicao['data_fim'])) ?>
        </p>

        <h2 class="titulo-cargo">CANDIDATOS</h2>

        <!-- 🔹 Formulário de votação -->
        <form method="POST">
            <section class="painel-votacao">
                <div class="painel">
                    <div class="lista-candidatos">
                        <?php if ($resultCandidatos->num_rows > 0): ?>
                            <?php while ($cand = $resultCandidatos->fetch_assoc()): ?>
                                <label>
                                    <img src="../Images/user.png" width="20" alt="user" />
                                    <?= htmlspecialchars($cand['candidato_nome']) ?>
                                    <input type="radio" name="candidato_id" value="<?= $cand['candidato_id'] ?>" required>
                                </label>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>Nenhum candidato cadastrado nesta eleição.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <p class="termos">Ao finalizar esse processo eleitoral, você concorda com os
                <a href="termos.php" class="link-termos">Termos e Condições</a>.
            </p>

            <div class="finalizar-container">
                <button type="submit" class="botao-finalizar">FINALIZAR</button>
            </div>
        </form>

    </main>
    <footer class="footer">
        <div class="footer-top">
            <div class="footer-logo">
                <img src="../Images/logoFaVote.png" width="70">
            </div>
            <div class="footer-links">
                <div>
                    <h4>PÁGINAS</h4>
                    <ul>
                        <li><a href="home.php">Home</a></li>
                        <li><a href="eleAtive.php">Eleições Ativas</a></li>
                        <li><a href="news.php">Notícias</a></li>
                        <li><a href="elepassa.php">Eleições Passadas</a></li>
                        <li><a href="termos.php">Termos de Contrato</a></li>
                    </ul>
                </div>
                <div>
                    <h4>REDES</h4>
                    <ul>
                        <li><a href="https://www.instagram.com/fatecdeitapira?igsh=MWUzNXMzcWNhZzB4Ng=="
                                target="_blank">Instagram</a></li>
                        <li><a href="https://www.facebook.com/share/16Y3jKo71m/" target="_blank">Facebook</a></li>
                        <li><a href="https://www.youtube.com/@fatecdeitapiraogaridecastr2131"
                                target="_blank">Youtube</a></li>
                        <li><a href="https://www.linkedin.com/school/faculdade-estadual-de-tecnologia-de-itapira-ogari-de-castro-pacheco/about/"
                                target="_blank">Linkedin</a></li>
                        <li><a href="https://fatecitapira.cps.sp.gov.br/" target="_blank">Site Fatec</a></li>
                    </ul>
                </div>
                <div>
                    <h4>INTEGRANTES</h4>
                    <ul>
                        <li>Graziela Dilany da Silva</li>
                        <li>João Pedro Baradeli Pavan</li>
                        <li>Pedro Henrique Cavenaghi dos Santos</li>
                        <li>Samara Stefani da Silva</li>
                        <li>Samuel Santos Oliveira</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            FaVote - Todos os direitos reservados | 2025
        </div>
    </footer>
</body>

</html>