<?php
require '../php/session_auth.php';
require '../php/config.php';

// Garante que o campo turma_id exista antes de usar
$turmaUsuario = isset($_SESSION['turma_id']) ? $_SESSION['turma_id'] : 0;

// Busca eleições ativas da turma do usuário
$sqlAtivas = "
SELECT 
    e.*, 
    c.nome AS curso_nome, 
    s.nome AS semestre_nome
FROM eleicoes e
INNER JOIN turmas t ON e.turma_id = t.id
INNER JOIN cursos c ON t.curso_id = c.id
INNER JOIN semestres s ON t.semestre_id = s.id
WHERE e.ativa = 1 AND e.turma_id = ?
";
$stmtAtivas = $conexao->prepare($sqlAtivas);
$stmtAtivas->bind_param("i", $turmaUsuario);
$stmtAtivas->execute();
$resultAtivas = $stmtAtivas->get_result();

// Busca eleições bloqueadas (ou de outras turmas)
$sqlBloqueadas = "
SELECT 
    e.*, 
    c.nome AS curso_nome, 
    s.nome AS semestre_nome
FROM eleicoes e
INNER JOIN turmas t ON e.turma_id = t.id
INNER JOIN cursos c ON t.curso_id = c.id
INNER JOIN semestres s ON t.semestre_id = s.id
WHERE e.ativa = 0 OR e.turma_id != ?
";
$stmtBloqueadas = $conexao->prepare($sqlBloqueadas);
$stmtBloqueadas->bind_param("i", $turmaUsuario);
$stmtBloqueadas->execute();
$resultBloqueadas = $stmtBloqueadas->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Eleições 🕢 | FaVote</title>
    <link rel="stylesheet" href="../Styles/eleAtive.css">

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

                <p>FATEC “Dr. Ogari de Castro Pacheco”</p>
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
        <h2>Eleições ativas para seu usuário</h2>

        <?php if ($resultAtivas->num_rows > 0): ?>
            <?php while ($eleicao = $resultAtivas->fetch_assoc()): ?>
                <a href="votacao.php?id=<?php echo $eleicao['id']; ?>" style="text-decoration: none;">
                    <section class="main-vote">
                        <div class="vote-box">
                            <h1><?php echo strtoupper($eleicao['titulo']); ?></h1><br>
                            <h2 style="margin-top: -35px; margin-bottom: 12px; font-size: 24px;">
                                <?php echo $eleicao['curso_nome'] . " (" . $eleicao['semestre_nome'] . ")"; ?></h1>
                            </h2>
                            <p><?php echo htmlspecialchars($eleicao['descricao']); ?></p><br>
                            <small>
                                <p>
                                    <strong>Início:</strong>
                                    <?php echo date('d/m/Y H:i', strtotime($eleicao['data_inicio'])); ?>
                                    &nbsp;
                                    <strong>Fim:</strong> <?php echo date('d/m/Y H:i', strtotime($eleicao['data_fim'])); ?>
                                </p>
                            </small>
                        </div>
                        <div class="vote-img">
                            <img src="../Images/imgAlvo.png" width="310">
                        </div>
                    </section>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: gray;">Nenhuma eleição ativa disponível para sua turma.</p>
        <?php endif; ?>

        <h2 style="color: gray; border-bottom: 3px solid gray;">Eleições bloqueadas para seu usuário</h2>

        <?php if ($resultBloqueadas->num_rows > 0): ?>
            <?php while ($eleicao = $resultBloqueadas->fetch_assoc()): ?>
                <section class="main-vote2">
                    <div class="vote-box">
                        <h1><?php echo strtoupper($eleicao['titulo']); ?></h1><br>
                        <h3 style="margin-top: -35px; margin-bottom: 12px; font-size: 24px;">
                            <?php echo $eleicao['curso_nome'] . " (" . $eleicao['semestre_nome'] . ")"; ?></h3>
                        <p><?php echo htmlspecialchars($eleicao['descricao']); ?></p><br>
                        <small>
                            <p>
                                <strong>Início:</strong> <?php echo date('d/m/Y H:i', strtotime($eleicao['data_inicio'])); ?>
                                &nbsp;
                                <strong>Fim:</strong> <?php echo date('d/m/Y H:i', strtotime($eleicao['data_fim'])); ?>
                            </p>
                        </small>
                    </div>
                    <div class="vote-img2">
                        <img style="margin-top: 5px;" src="../Images/imgAlvo2.png" width="310">
                    </div>
                </section>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: gray;">Nenhuma eleição bloqueada encontrada.</p>
        <?php endif; ?>
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