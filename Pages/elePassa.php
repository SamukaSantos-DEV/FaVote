<?php
// Arquivo: elePassa.php

// Inclui a autenticação de sessão e a configuração de banco de dados
require '../php/session_auth.php';
require '../php/config.php';

$query_finalizar = $conexao->prepare("UPDATE eleicoes SET ativa = 0 WHERE ativa = 1 AND data_fim <= NOW()");
$query_finalizar->execute();
$query_finalizar->close();
$query_finalizar = $conexao->prepare("UPDATE eleicoes SET ativa = 1 WHERE ativa = 0 AND data_fim >= NOW()");
$query_finalizar->execute();
$query_finalizar->close();

// =================================================================
// 1. LÓGICA AJAX PARA BUSCAR APENAS OS CARDS DE ELEIÇÃO
// Esta lógica é executada quando o JavaScript chama este arquivo via AJAX.
// =================================================================

if (isset($_GET['acao']) && $_GET['acao'] === 'fetch') {
    // Busca o ano do filtro
    // O ano pode ser '2025', '2024', '2023' ou vazio ('') se for "Todos os Anos"
    $ano = $_GET['ano'] ?? null;

    // Verifica se o ano é um valor válido para filtrar (não vazio e é numérico de 4 dígitos)
    $temFiltro = !empty($ano) && is_numeric($ano) && strlen($ano) === 4;

    // Constrói a consulta de eleições
    $sqlEleicoes = "
        SELECT e.id, e.titulo, e.descricao, e.data_inicio, e.data_fim AS data_termino,
               c.nome AS curso_nome, s.nome AS semestre_nome
        FROM eleicoes e
        INNER JOIN turmas t ON t.id = e.turma_id
        INNER JOIN cursos c ON c.id = t.curso_id
        INNER JOIN semestres s ON s.id = t.semestre_id
        WHERE e.ativa = 0
    ";

    if ($temFiltro) {
        // Adiciona a condição de ano no filtro
        $sqlEleicoes .= " AND YEAR(e.data_inicio) = ?";
    }

    $sqlEleicoes .= " ORDER BY e.data_inicio DESC";

    $stmtEleicoes = $conexao->prepare($sqlEleicoes);

    if ($temFiltro) {
        $stmtEleicoes->bind_param("s", $ano);
    }

    $stmtEleicoes->execute();
    $resultEleicoes = $stmtEleicoes->get_result();

    $temEleicoesPassadas = false;

    // Início do LOOP de Geração dos Cards (retornados para o JavaScript)
    while ($eleicao = $resultEleicoes->fetch_assoc()):
        $temEleicoesPassadas = true;
        $eleicaoId = (int) $eleicao['id'];
        $turmaFormatada = htmlspecialchars($eleicao['curso_nome']) . ' - ' . htmlspecialchars($eleicao['semestre_nome']);

        // SQL para buscar os 2 mais votados (Lógica de Vencedores)
        $sqlVencedores = "
            SELECT a.nome AS candidato,
                   MAX(v.data_voto) AS data_voto,
                   COUNT(*) AS total
            FROM votos v
            INNER JOIN candidatos c ON c.id = v.candidato_id
            INNER JOIN alunos a ON a.ra = c.aluno_ra
            WHERE v.eleicao_id = ?
            GROUP BY v.candidato_id, a.nome
            ORDER BY total DESC
            LIMIT 2
        ";

        $stmtVencedores = $conexao->prepare($sqlVencedores);
        $stmtVencedores->bind_param("i", $eleicaoId);
        $stmtVencedores->execute();
        $resultVencedores = $stmtVencedores->get_result();
        $vencedores = [];

        while ($row = $resultVencedores->fetch_assoc()) {
            $vencedores[] = $row;
        }

        $stmtVencedores->close();
        ?>
        <div class="eleicao-card">
            <div class="info-eleicao">
                <h3>
                    <?= htmlspecialchars(strtoupper($eleicao['titulo'])) ?><br>
                    <?= $turmaFormatada ?>
                </h3>
                <p><?= htmlspecialchars($eleicao['descricao']) ?></p>
                <div class="datas">
                    <p>
                        <strong>Início:</strong> <?= date('d/m/Y H:i', strtotime($eleicao['data_inicio'])) ?>
                        &nbsp;
                        <strong>Fim:</strong> <?= date('d/m/Y H:i', strtotime($eleicao['data_termino'])) ?>
                    </p>
                </div>
            </div>

            <div class="eleitos">
                <?php if (!empty($vencedores)): ?>
                    <?php foreach ($vencedores as $index => $v): ?>
                        <div class="<?= $index == 0 ? 'eleito' : 'eleito2' ?>">
                            <img class="trofeu" src="../Images/tacabranca.png" alt="Troféu de Eleito">
                            <p class="nome-eleito">
                                <?= htmlspecialchars(strtoupper($v['candidato'])) ?>
                            </p>
                            <p class="data-eleito">
                                Eleito em: <br>
                                <span><?= date('d/m/Y', strtotime($v['data_voto'])) ?></span>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="sem-vencedores">Nenhum voto registrado ou candidato cadastrado.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile;

    $stmtEleicoes->close();

    if (!$temEleicoesPassadas):
        ?>
        <p class="nenhuma-eleicao">Não há eleições passadas registradas no
            momento<?php echo $temFiltro ? " para o ano de " . htmlspecialchars($ano) . "." : "."; ?></p>
    <?php endif;

    $conexao->close();

    // FINALIZA a execução do script PHP aqui para não renderizar o HTML da página inteira
    die();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Eleições Passadas | FaVote</title>
    <link rel="stylesheet" href="../Styles/elePassa.css">
    <link rel="icon" href="../Images/iconlogoFaVote.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>


<body>
    <header class="header">
        <div class="logo"><img src="../Images/logofatec.png" width="190"></div>

        <nav class="nav">
            <a href="home.php">Home</a>
            <a href="eleAtive.php" >Eleições Ativas</a>
            <a href="news.php">Notícias</a>
            <a href="elePassa.php" class="active">Eleições Passadas</a>
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


    <div class="eleicoes-passadas-container">
        <h2 class="titulo-pagina">Eleições Passadas</h2>

        <div class="filtro-ano">
            <label for="filtro">Mostrar todos os processos realizados em:</label>
            <select id="filtro">
                <option value="">Todos os Anos</option>
                <option value="2025">2025</option>
                <option value="2024">2024</option>
                <option value="2023">2023</option>
            </select>
        </div>

        <div id="eleicoes-lista">
        </div>

    </div>


    <footer class="footer">
        <div class="footer-top">
            <div class="footer-logo">
                <img src="../Images/logoFaVote.png" width="70" alt="Logo FaVote">
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
                        <li><a href="https://www.instagram.com/fatecdeitapira?igsh=MWUzNXMzcWNhZzB4Ng==" target="_blank"
                                rel="noopener noreferrer">Instagram</a></li>
                        <li><a href="https://www.facebook.com/share/16Y3jKo71m/" target="_blank"
                                rel="noopener noreferrer">Facebook</a></li>
                        <li><a href="https://www.youtube.com/@fatecdeitapiraogaridecastr2131" target="_blank"
                                rel="noopener noreferrer">Youtube</a></li>
                        <li><a href="https://www.linkedin.com/school/faculdade-estadual-de-tecnologia-de-itapira-ogari-de-castro-pacheco/about/"
                                target="_blank" rel="noopener noreferrer">Linkedin</a></li>
                        <li><a href="https://fatecitapira.cps.sp.gov.br/" target="_blank" rel="noopener noreferrer">Site
                                Fatec</a></li>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filtroSelect = document.getElementById('filtro');
            const listaDiv = document.getElementById('eleicoes-lista');

            // Função que carrega as eleições via AJAX
            function carregarEleicoes(ano) {
                // Adiciona um indicador de carregamento
                listaDiv.innerHTML = '<p class="carregando">Carregando eleições...</p>';

                // CHAMA O PRÓPRIO ARQUIVO, mas com o parâmetro 'acao=fetch'
                // Isto faz com que o PHP execute a lógica de busca (bloco 1) e retorne apenas o HTML dos cards.
                const url = 'elePassa.php?acao=fetch&ano=' + ano;

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro de rede: ' + response.statusText);
                        }
                        return response.text();
                    })
                    .then(html => {
                        // Insere o HTML retornado (apenas os cards) na div da lista
                        listaDiv.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Erro ao carregar eleições:', error);
                        listaDiv.innerHTML = '<p class="erro">Erro ao carregar as eleições. Tente novamente. (' + error.message + ')</p>';
                    });
            }

            // 1. Event Listener: Recarrega a lista quando o filtro muda
            filtroSelect.addEventListener('change', function () {
                const anoSelecionado = this.value;
                carregarEleicoes(anoSelecionado);
            });

            // 2. Carregamento Inicial: Carrega todas as eleições ao carregar a página
            // O valor inicial é vazio ('') (Todos os Anos)
            carregarEleicoes(filtroSelect.value);
        });
    </script>
</body>

</html>