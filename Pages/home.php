<?php
require '../php/session_auth.php';
require '../php/config.php';

$conexao = $db->conecta_mysql();

$query_finalizar = $conexao->prepare("UPDATE eleicoes SET ativa = 0 WHERE ativa = 1 AND data_fim <= NOW()");
$query_finalizar->execute();
$query_finalizar->close();
$query_finalizar = $conexao->prepare("UPDATE eleicoes SET ativa = 1 WHERE ativa = 0 AND data_fim >= NOW()");
$query_finalizar->execute();
$query_finalizar->close();

$ra_usuario = $_SESSION['user_ra'] ?? null;

$turma_id = null;
if ($ra_usuario) {
    $query_turma = $conexao->prepare("
        SELECT a.turma_id, c.nome AS curso_nome, s.nome AS semestre_nome
        FROM alunos a
        INNER JOIN turmas t ON t.id = a.turma_id
        INNER JOIN cursos c ON c.id = t.curso_id
        INNER JOIN semestres s ON s.id = t.semestre_id
        WHERE a.ra = ?
    ");
    $query_turma->bind_param("s", $ra_usuario);
    $query_turma->execute();
    $resultado = $query_turma->get_result();
    if ($resultado->num_rows > 0) {
        $dados_turma = $resultado->fetch_assoc();
        $turma_id = $dados_turma['turma_id'];
        $_SESSION['curso_nome'] = $dados_turma['curso_nome'];
        $_SESSION['semestre_nome'] = $dados_turma['semestre_nome'];
    }
    $query_turma->close();
}

$eleicao = null;
$periodo_candidatura_aberto = false;
if ($turma_id) {
    $query_eleicao = $conexao->prepare("SELECT * FROM eleicoes WHERE turma_id = ? AND ativa = 1 ORDER BY data_inicio DESC LIMIT 1");
    $query_eleicao->bind_param("i", $turma_id);
    $query_eleicao->execute();
    $resultado_eleicao = $query_eleicao->get_result();
    if ($resultado_eleicao->num_rows > 0) {
        $eleicao = $resultado_eleicao->fetch_assoc();

        // =========================================================================
        // 2. LÓGICA DE PERÍODO DE CANDIDATURA (7 DIAS) 🗓️ (Refatorada para maior segurança)
        // =========================================================================
        // Cria um objeto DateTime para a data de início
        $data_inicio = new DateTime($eleicao['data_inicio']);

        // Cria um novo objeto DateTime para o limite (Data Início + 7 dias)
        // Usamos uma nova instância para não modificar $data_inicio
        $data_limite_candidatura = (new DateTime($eleicao['data_inicio']))->modify('+7 days');
        $agora = new DateTime();

        // Verifica se a data/hora atual é anterior à data limite de candidatura
        if ($agora < $data_limite_candidatura) {
            $periodo_candidatura_aberto = true;
        }
        // =========================================================================

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

// =========================================================================
// LÓGICA PARA BUSCAR AS 3 ÚLTIMAS ELEIÇÕES E SEUS 2 MAIS VOTADOS
// =========================================================================

$ultimas_eleicoes_com_vencedores = [];

// 1. Busca as 3 eleições finalizadas mais recentes e seus dados de turma (ativa = 0)
$sql_ultimas_eleicoes = "
    SELECT e.id, e.titulo, e.data_fim, c.nome AS curso_nome, s.nome AS semestre_nome
    FROM eleicoes e
    INNER JOIN turmas t ON t.id = e.turma_id
    INNER JOIN cursos c ON c.id = t.curso_id
    INNER JOIN semestres s ON s.id = t.semestre_id    
    WHERE e.ativa = 0
    ORDER BY e.data_fim DESC
    LIMIT 3
";
$result_eleicoes = $conexao->query($sql_ultimas_eleicoes);

if ($result_eleicoes) {
    while ($e = $result_eleicoes->fetch_assoc()) {
        $eleicao_id = (int)$e['id'];
        $vencedores = [];

        // 2. Para cada eleição, busca os 2 candidatos mais votados
        $sql_vencedores = "
            SELECT a.nome AS candidato,
                   COUNT(*) AS total
            FROM votos v
            INNER JOIN candidatos c ON c.id = v.candidato_id
            INNER JOIN alunos a ON a.ra = c.aluno_ra
            WHERE v.eleicao_id = ? 
            AND c.id <> 1 
            GROUP BY v.candidato_id, a.nome
            ORDER BY total DESC
            LIMIT 2
        ";

        $stmt_vencedores = $conexao->prepare($sql_vencedores);
        if ($stmt_vencedores) {
            $stmt_vencedores->bind_param("i", $eleicao_id);
            $stmt_vencedores->execute();
            $result_vencedores = $stmt_vencedores->get_result();
            while ($v = $result_vencedores->fetch_assoc()) {
                $vencedores[] = $v;
            }
            $stmt_vencedores->close();
        }

        // Armazena a eleição e seus vencedores (pode ser 1 ou 2)
        $e['vencedores'] = $vencedores;
        $ultimas_eleicoes_com_vencedores[] = $e;
    }
}

$conexao->close(); // Fecha a conexão após todas as operações de banco
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
            <?php $emailLogado = $_SESSION['user_email'] ?? null; ?>
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
                <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuário'); ?></strong>
                <p>FATEC “Dr. Ogari de Castro Pacheco”</p>
                <?php if ($emailLogado !== 'admin@fatec.sp.gov.br'): ?>
                    <strong>
                        <p><?php echo htmlspecialchars($_SESSION['curso_nome'] ?? 'Curso Não Informado'); ?></p>
                    </strong>
                    <p><?php echo htmlspecialchars($_SESSION['semestre_nome'] ?? 'Semestre Não Informado'); ?></p>
                <?php endif; ?>
                <div class="sair">
                    <a href="../php/logout.php">Sair<i style="margin-left: 5px;"
                            class="fa-solid fa-right-from-bracket"></i></a>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">

        <?php if (!$periodo_candidatura_aberto && $eleicao): ?>
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
        <?php endif; ?>

        <section class="news-votes">
            <div class="news">
                <div class="section-header">
                    <h2>Notícias</h2>
                    <a href="news.php">Ver mais ➜</a>
                </div>

                <?php
                if ($eleicao) {
                    if (!$jaCandidato && $periodo_candidatura_aberto):
                ?>
                        <div class="news-card special-card"
                            onclick="window.location.href='querocandidatar.php?id=<?php echo urlencode($eleicao['id']); ?>'">
                            <h3>Quero me candidatar!</h3>
                            <p>Participe da eleição ativa da sua turma. Mostre suas ideias e concorra como representante!</p>
                            <p style="color: crimson;">O período de candidatura é de 7 dias após o início da eleição.</p>
                            <p style="text-decoration: underline dotted black 2px;"><strong>Clique aqui para se inscrever</strong></p>
                        </div>

                    <?php
                    elseif (!$jaCandidato && !$periodo_candidatura_aberto):
                    ?>
                        <div class="news-card special-card">
                            <h3>Período de Candidatura Encerrado</h3>
                            <p>O prazo de 7 dias para se candidatar à eleição <?php echo htmlspecialchars($eleicao['titulo']); ?> já se encerrou.</p>
                            <p><strong>Aguarde a próxima eleição.</strong></p>
                        </div>

                    <?php
                    elseif ($jaCandidato):
                    ?>
                        <div class="news-card special-card">
                            <h3>Você já se candidatou!</h3>
                            <p>Boa sorte! Aguarde o início da votação da sua turma.</p>
                            <small><strong>Eleição:</strong> <?php echo htmlspecialchars($eleicao['titulo']); ?></small>
                        </div>
                <?php
                    endif;
                }
                // Se não houver eleição ativa, não exibe o card de candidatura.
                ?>

                <?php if (count($noticias) > 0): ?>
                    <?php foreach ($noticias as $n): ?>
                        <div class="news-card">
                            <h3><?php echo htmlspecialchars($n['titulo']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($n['descricao'])); ?></p>
                            <small><strong>Publicado em:</strong>
                                <?php echo date('d/m/Y \à\s H:i', strtotime($n['dataPublicacao'])); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Nenhuma notícia publicada ainda.</p>
                <?php endif; ?>
            </div>

            <div class="votes">
                <div class="section-header">
                    <h2>Últimas votações</h2>
                    <a href="elePassa.php">Ver mais ➜</a>
                </div>


                <?php if (!empty($ultimas_eleicoes_com_vencedores)): ?>
                    <?php foreach ($ultimas_eleicoes_com_vencedores as $eleicao_result): ?>
                        <?php

                        $turma_nome = htmlspecialchars($eleicao_result['curso_nome']) . ' - ' . htmlspecialchars($eleicao_result['semestre_nome']);
                        $data_fim = date('d/m/Y', strtotime($eleicao_result['data_fim']));
                        $vencedores = $eleicao_result['vencedores'];

                        // Define o nome do vencedor principal ou mensagem de empate/vazio
                        $primeiro_colocado = "Nenhum Vencedor";
                        if (isset($vencedores[0])) {
                            $primeiro_colocado = htmlspecialchars(strtoupper($vencedores[0]['candidato']));
                        }
                        ?>

                        <div class="vote-result">
                            <h3 class="titulo-eleicao-recente">
                                <?= htmlspecialchars(strtoupper($eleicao_result['titulo'])) ?>
                            </h3>

                            <small class="turma-nome"><?= $turma_nome ?></small>

                            <p style="margin-top: 10px;">Finalizado em: <strong class="badge-date"><?= $data_fim ?></strong></p>

                            <p class="vencedores">Vencedor:<strong> <?= $primeiro_colocado ?></strong></p>



                            <?php if (isset($vencedores[1])): ?>
                                <p class="segundo-colocado">
                                    Vice: <strong><?= htmlspecialchars(strtoupper($vencedores[1]['candidato'])) ?></strong>
                                </p>
                            <?php elseif (count($vencedores) > 0 && count($vencedores) < 2): ?>
                                <p class="segundo-colocado">Vencedor Único</p>
                            <?php endif; ?>

                            <img src="../Images/taça.png" width="144" class="taca">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="sem-votacoes">Nenhuma eleição passada encontrada.</p>
                <?php endif; ?>
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
                        <li><a href="https://www.instagram.com/fatecdeitapira" target="_blank" rel="noopener noreferrer">Instagram</a></li>
                        <li><a href="https://www.facebook.com/share/16Y3jKo71m/" target="_blank" rel="noopener noreferrer">Facebook</a></li>
                        <li><a href="https://www.youtube.com/@fatecdeitapiraogaridecastr2131" target="_blank" rel="noopener noreferrer">YouTube</a></li>
                        <li><a href="https://www.linkedin.com/school/faculdade-estadual-de-tecnologia-de-itapira-ogari-de-castro-pacheco/about/" target="_blank" rel="noopener noreferrer">LinkedIn</a></li>
                        <li><a href="https://fatecitapira.cps.sp.gov.br/" target="_blank" rel="noopener noreferrer">Site Fatec</a></li>
                    </ul>
                </div>
                <div>
                    <h4>INTEGRANTES</h4>
                    <ul>
                        <li>João Paulo Gomes</li>
                        <li>João Pedro Baradeli Pavan</li>
                        <li>Pedro Henrique Cavenaghi dos Santos</li>
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