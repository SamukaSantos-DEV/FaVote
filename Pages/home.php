<?php
require '../php/session_auth.php';
require '../php/config.php';

$conexao = $db->conecta_mysql();

// Pegando RA do usuário logado
$ra_usuario = $_SESSION['user_ra'] ?? null;

// Pega turma_id do aluno
$turma_id = null;
if ($ra_usuario) {
    $query_turma = $conexao->prepare("SELECT turma_id FROM alunos WHERE ra = ?");
    $query_turma->bind_param("s", $ra_usuario);
    $query_turma->execute();
    $resultado = $query_turma->get_result();
    if ($resultado->num_rows > 0) {
        $turma_id = $resultado->fetch_assoc()['turma_id'];
    }
    $query_turma->close();
}

// Busca a eleição ativa da turma do aluno
$eleicao = null;
if ($turma_id) {
    $query_eleicao = $conexao->prepare("SELECT * FROM eleicoes WHERE turma_id = ? AND ativa = 1 ORDER BY data_inicio DESC LIMIT 1");
    $query_eleicao->bind_param("i", $turma_id);
    $query_eleicao->execute();
    $resultado_eleicao = $query_eleicao->get_result();
    if ($resultado_eleicao->num_rows > 0) {
        $eleicao = $resultado_eleicao->fetch_assoc();
    }
    $query_eleicao->close();
}

// Verifica se o aluno já é candidato na eleição ativa
$jaCandidato = false;
if ($eleicao && $ra_usuario) {
    $query_candidato = $conexao->prepare("SELECT id FROM candidatos WHERE aluno_ra = ? AND eleicao_id = ?");
    $query_candidato->bind_param("si", $ra_usuario, $eleicao['id']);
    $query_candidato->execute();
    $resultado_candidato = $query_candidato->get_result();
    if ($resultado_candidato->num_rows > 0) {
        $jaCandidato = true;
    }
    $query_candidato->close();
}

// Busca as últimas notícias
$noticias = [];
$query_noticias = $conexao->query("SELECT * FROM noticias ORDER BY dataPublicacao DESC LIMIT 2");
while ($row = $query_noticias->fetch_assoc()) {
    $noticias[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home | FaVote</title>
    <link rel="stylesheet" href="../Styles/home.css">
    <link rel="icon" href="../Images/iconlogoFaVote.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <header class="header">
        <div class="logo"><img src="../Images/logofatec.png" width="190"></div>

        <nav class="nav">
            <a href="home.php" class="active">Home</a>
            <a href="eleAtive.php">Eleições Ativas</a>
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
                <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
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
        <!-- ELEIÇÃO PRINCIPAL -->
        <?php if ($eleicao): ?>
            <a href="eleAtive.php" style="text-decoration: none;">
                <section class="main-vote">
                    <div class="vote-box">
                        <span class="badge">PRINCIPAL VOTAÇÃO EM ANDAMENTO</span>
                        <h1><?php echo htmlspecialchars($eleicao['titulo']); ?></h1>
                        <p><?php echo htmlspecialchars($eleicao['descricao']); ?></p>
                        <p>
                            <strong>Início:</strong> <?php echo date('d/m/Y H:i', strtotime($eleicao['data_inicio'])); ?>
                            &nbsp;
                            <strong>Fim:</strong> <?php echo date('d/m/Y H:i', strtotime($eleicao['data_fim'])); ?>
                        </p>
                    </div>
                    <div class="vote-img">
                        <img src="../Images/imgAlvo.png" width="330">
                    </div>
                </section>
            </a>
        <?php else: ?>

        <?php endif; ?>

        <!-- NOTÍCIAS -->
        <section class="news-votes">
            <div class="news">
                <div style="display: flex; justify-content: space-between;">
                    <h2>Notícias</h2>
                    <a href="news.php">Ver mais ➜</a>
                </div>

                <?php if ($eleicao && !$jaCandidato): ?>
                    <a href="querocandidatar.php?id=<?php echo urlencode($eleicao['id']); ?>" class="news-card special-card"
                        style="text-decoration: none; color: inherit;">
                        <h3>Quero me candidatar!</h3>
                        <p>Participe da eleição ativa da sua turma. Mostre suas ideias e concorra como representante!</p>
                        <br>
                        <small><strong>Clique aqui para se inscrever</strong></small>
                    </a>
                <?php elseif ($eleicao && $jaCandidato): ?>
                    <div class="news-card special-card" style="background-color: #e6ffe6; border-left: 5px solid #4caf50;">
                        <h3>Você já se candidatou!</h3>
                        <p>Boa sorte! Aguarde o início da votação da sua turma.</p><br>
                        <small><strong>Eleição:</strong> <?php echo htmlspecialchars($eleicao['titulo']); ?></small>
                    </div>
                <?php endif; ?>

                <?php if (count($noticias) > 0): ?>
                    <?php foreach ($noticias as $n): ?>
                        <div class="news-card">
                            <h3><?php echo htmlspecialchars($n['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($n['descricao']); ?></p><br>
                            <small><strong>Publicado em:</strong>
                                <?php echo date('d/m/Y \à\s H:i', strtotime($n['dataPublicacao'])); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Nenhuma notícia publicada ainda.</p>
                <?php endif; ?>

            </div>

            <!-- ÚLTIMAS VOTAÇÕES -->
            <div class="votes">
                <div style="display: flex; justify-content: space-between;">
                    <h2>Últimas votações</h2>
                    <a href="elePassa.php">Ver mais ➜</a>
                </div>
                <div class="vote-result">
                    <h3>VICTOR LUIZ RODRIGUES</h3><br>
                    Representante 2º DSM<br>
                    <small>Eleito em: <strong class="badge-date">05/05/2025</strong></small>
                    <img src="../Images/taça.png" width="144" class="taca1">
                </div>
                <div class="vote-result">
                    <h3>RAFAEL MORAES ALMEIDA</h3><br>
                    Representante 4º DSM<br>
                    <small>Eleito em: <strong class="badge-date">03/05/2025</strong></small>
                    <img src="../Images/taça.png" width="144" class="taca2">
                </div>
                <div class="vote-result">
                    <h3>RODRIGO POLASTRO</h3><br>
                    Representante 3º DSM<br>
                    <small>Eleito em: <strong class="badge-date">01/05/2025</strong></small>
                    <img src="../Images/taça.png" width="144" class="taca3">
                </div>
            </div>
        </section>
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
                        <li><a href="elePassa.php">Eleições Passadas</a></li>
                        <li><a href="termos.php">Termos de Contrato</a></li>
                    </ul>
                </div>
                <div>
                    <h4>REDES</h4>
                    <ul>
                        <li><a href="https://www.instagram.com/fatecdeitapira" target="_blank">Instagram</a></li>
                        <li><a href="https://www.facebook.com/share/16Y3jKo71m/" target="_blank">Facebook</a></li>
                        <li><a href="https://www.youtube.com/@fatecdeitapiraogaridecastr2131"
                                target="_blank">YouTube</a></li>
                        <li><a href="https://www.linkedin.com/school/faculdade-estadual-de-tecnologia-de-itapira-ogari-de-castro-pacheco/about/"
                                target="_blank">LinkedIn</a></li>
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