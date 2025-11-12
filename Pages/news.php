<?php
require '../php/session_auth.php';
require '../php/config.php';

date_default_timezone_set('America/Sao_Paulo');

// Criar instância do banco e conectar
$db = new db();
$conexao = $db->conecta_mysql();

// Buscar todas as notícias ordenadas pela data (mais recentes primeiro)
$sql = "SELECT id, titulo, descricao, dataPublicacao FROM noticias ORDER BY dataPublicacao DESC";
$result = $conexao->query($sql);

// Função para classificar notícias conforme a data
function classificarNoticia($dataPublicacao) {
    $agora = new DateTime();
    $data = new DateTime($dataPublicacao);
    $intervaloDias = $agora->diff($data)->days;

    if ($intervaloDias <= 7) return 'esta-semana';

    // Cópia do objeto atual para evitar alteração permanente
    $agoraMesAtual = new DateTime();
    $mesPassado = (new DateTime())->modify('-1 month');

    if ($data->format('m') == $agoraMesAtual->format('m')) return 'este-mes';
    if ($data->format('m') == $mesPassado->format('m')) return 'mes-passado';
    return 'antigas';
}

// Inicializar os arrays das seções
$noticias = [
    'esta-semana' => [],
    'este-mes' => [],
    'mes-passado' => [],
    'antigas' => []
];

// Preencher os arrays conforme a data
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categoria = classificarNoticia($row['dataPublicacao']);
        $noticias[$categoria][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notícias | FaVote</title>
    <link rel="stylesheet" href="../Styles/news.css">
    <link rel="icon" href="../Images/iconlogoFaVote.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<header class="header">
    <div class="logo"><img src="../Images/logofatec.png" width="190"></div>
    <nav class="nav">
        <a href="home.php">Home</a>
        <a href="eleAtive.php">Eleições Ativas</a>
        <a href="news.php" class="active">Notícias</a>
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
                <a href="../php/logout.php">Sair<i style="margin-left: 5px;" class="fa-solid fa-right-from-bracket"></i></a>
            </div>
        </div>
    </div>
</header>

<div class="noticias-container">
    <main class="conteudo-noticias">
        <?php
        $secoes = [
            'esta-semana' => 'Esta semana',
            'este-mes' => 'Este mês',
            'mes-passado' => 'Mês passado',
            'antigas' => 'Antigas'
        ];

        foreach ($secoes as $key => $titulo) {
            echo "<section id='{$key}'>";
            echo "<h2>{$titulo}</h2>";
            echo "<div class='noticias-grid'>";

            if (!empty($noticias[$key])) {
                foreach ($noticias[$key] as $n) {
                    $dataFormatada = date('d/m/Y \à\s H:i', strtotime($n['dataPublicacao']));
                    echo "<div class='noticia-card'>";
                    echo "<h3>" . htmlspecialchars($n['titulo']) . "</h3>";
                    echo "<p>" . nl2br(htmlspecialchars($n['descricao'])) . "</p>";
                    echo "<p class='publicado'>Publicado em: {$dataFormatada}</p>";
                    echo "</div>";
                }
            } else {
                echo "<p style='color: #666;'>Nenhuma notícia encontrada.</p>";
            }

            echo "</div></section>";
        }
        ?>
    </main>
</div>

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
                    <li><a href="https://www.instagram.com/fatecdeitapira?igsh=MWUzNXMzcWNhZzB4Ng==" target="_blank">Instagram</a></li>
                    <li><a href="https://www.facebook.com/share/16Y3jKo71m/" target="_blank">Facebook</a></li>
                    <li><a href="https://www.youtube.com/@fatecdeitapiraogaridecastr2131" target="_blank">Youtube</a></li>
                    <li><a href="https://www.linkedin.com/school/faculdade-estadual-de-tecnologia-de-itapira-ogari-de-castro-pacheco/about/" target="_blank">Linkedin</a></li>
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
