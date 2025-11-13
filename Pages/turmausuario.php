<?php
require '../php/session_auth.php';
include '../php/config.php';

// === AÇÕES PHP DE EDIÇÃO OU EXCLUSÃO ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao'])) {
    header('Content-Type: text/plain; charset=utf-8'); // evita retorno HTML

    // === EDITAR USUÁRIO ===
    if ($_POST['acao'] === 'editar') {
        $id = $_POST['id'] ?? null;
        $nome = $_POST['nome'] ?? null;
        $email = $_POST['email'] ?? null;

        if ($id && $nome && $email) {
            $stmt = $conexao->prepare("UPDATE alunos SET nome = ?, email_institucional = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nome, $email, $id);
            echo $stmt->execute() ? "Usuário atualizado com sucesso!" : "Erro ao atualizar usuário.";
        } else {
            echo "Preencha todos os campos.";
        }
        exit;
    }

    // === EXCLUIR USUÁRIO ===
    if ($_POST['acao'] === 'excluir') {
        $id = $_POST['id'] ?? null;
        if ($id) {
            // 1️⃣ Obter o RA do aluno antes de excluir
            $raAluno = null;
            $res = $conexao->prepare("SELECT ra FROM alunos WHERE id = ?");
            $res->bind_param("i", $id);
            $res->execute();
            $res->bind_result($raAluno);
            $res->fetch();
            $res->close();

            if ($raAluno) {
                // 2️⃣ Excluir votos relacionados a esse aluno (como eleitor)
                $stmt = $conexao->prepare("DELETE FROM votos WHERE aluno_ra = ?");
                $stmt->bind_param("s", $raAluno);
                $stmt->execute();
                $stmt->close();

                // 3️⃣ Excluir votos relacionados a esse aluno (como candidato)
                $stmt = $conexao->prepare("DELETE FROM votos WHERE candidato_id IN (SELECT id FROM candidatos WHERE aluno_ra = ?)");
                $stmt->bind_param("s", $raAluno);
                $stmt->execute();
                $stmt->close();

                // 4️⃣ Excluir o candidato (se existir)
                $stmt = $conexao->prepare("DELETE FROM candidatos WHERE aluno_ra = ?");
                $stmt->bind_param("s", $raAluno);
                $stmt->execute();
                $stmt->close();

                // 5️⃣ Agora sim, excluir o aluno
                $stmt = $conexao->prepare("DELETE FROM alunos WHERE id = ?");
                $stmt->bind_param("i", $id);
                $ok = $stmt->execute();
                $stmt->close();

                echo $ok ? "Usuário (e candidatura, se havia) excluído com sucesso!" : "Erro ao excluir usuário.";
            } else {
                echo "Aluno não encontrado.";
            }
        } else {
            echo "ID inválido.";
        }
        exit;
    }
}


// === CONSULTA USUÁRIOS ===
$sqlUsuarios = "
    SELECT 
        a.id AS aluno_id,
        a.ra,
        a.nome,
        a.email_institucional,
        c.nome AS curso_nome,
        s.nome AS semestre_nome
    FROM alunos a
    LEFT JOIN turmas t ON a.turma_id = t.id
    LEFT JOIN cursos c ON t.curso_id = c.id
    LEFT JOIN semestres s ON t.semestre_id = s.id
";
$resultUsuarios = $conexao->query($sqlUsuarios);

// === CONSULTA TURMAS ===
$sqlTurmas = "
    SELECT 
        t.id AS turma_id,
        c.id AS curso_id,
        c.nome AS curso_nome,
        s.nome AS semestre_nome,
        (SELECT COUNT(*) FROM alunos a WHERE a.turma_id = t.id) AS qtd_alunos
    FROM turmas t
    LEFT JOIN cursos c ON t.curso_id = c.id
    LEFT JOIN semestres s ON t.semestre_id = s.id
";
$resultTurmas = $conexao->query($sqlTurmas);
?>




<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard | FaVote</title>
    <link rel="stylesheet" href="../Styles/dashboard.css">
    <link rel="icon" href="../Images/logoFaVote.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <style>
        .tableSave-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 4px 8px;
            cursor: pointer;
            border-radius: 4px;
        }

        .tableCancel-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 4px 8px;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
</head>


<body>
    <header class="header">
        <div class="logo"><img src="../Images/logofatec.png" width="190"></div>


        <nav class="nav">
            <a href="home.php">Home</a>
            <a href="eleAtive.php">Eleições Ativas</a>
            <a href="news.php">Notícias</a>
            <a href="elePassa.php">Eleições Passadas</a>
            <a href="dashboard.php"
                style="background-color: white; color: brown; padding: 4px 8px; border-radius: 4px; text-decoration: none; transition: background-color 0.6s ease;"
                onmouseover="this.style.backgroundColor='#ccc'" onmouseout="this.style.backgroundColor='white'">
                DASHBOARD
            </a>
        </nav>
        <div class="user-icon">
            <img src="../Images/user.png" width="50" alt="user" />
            <div class="user-popup">
                <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                <p>FATEC “Dr. Ogari de Castro Pacheco”</p>
                <?php

                $emailLogado = $_SESSION['user_email'] ?? null;
                ?>

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

    <script>
        window.addEventListener('scroll', function() {
            const btn = document.querySelector('.btn-close');
            if (window.scrollY > 50) {
                btn.classList.add('scrolled');
            } else {
                btn.classList.remove('scrolled');
            }
        });
    </script>

    <button class="btn-close" onclick="history.back()">➜</button>

    <div class="container" style="margin-top: 5%;">

        <div class="table-header">
            <h2>Usuários</h2>

        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>RA</th>
                        <th>Nome</th>
                        <th>E-Mail</th>
                        <th>Curso</th>
                        <th>Período</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="usuarios-tbody">
                    <?php if ($resultUsuarios->num_rows > 0): ?>
                        <?php foreach ($resultUsuarios as $usuario): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['ra']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email_institucional']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['curso_nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['semestre_nome']); ?></td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn tableEdit-btn" title="Editar" onclick="editarUsuario('<?php echo $usuario['aluno_id']; ?>')">✎</button>
                                        <button class="action-btn tabledelete-btn" title="Excluir" onclick="excluirUsuario('<?php echo $usuario['aluno_id']; ?>')">🗑</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Nenhum usuário encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="container">
        <div class="table-header">
            <h2>Turmas</h2>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Curso</th>
                        <th>Alunos Relacionados</th>
                        <th>Semestre</th>
                    </tr>
                </thead>
                <tbody id="turmas-tbody">
                    <?php
                    if ($resultTurmas && $resultTurmas->num_rows > 0) {
                        while ($turma = $resultTurmas->fetch_assoc()) {
                            // Definir sigla conforme ID do curso
                            $siglaCurso = '';
                            switch ($turma['curso_id']) {
                                case 1:
                                    $siglaCurso = 'DSM';
                                    break;
                                case 2:
                                    $siglaCurso = 'GE';
                                    break;
                                case 3:
                                    $siglaCurso = 'GPI';
                                    break;
                            }

                            // Exemplo: DSM-1, GE-2 etc.
                            $idFormatado = $siglaCurso . '-' . preg_replace('/[^0-9]/', '', $turma['semestre_nome']);

                            echo '<tr>';
                            echo '  <td>' . $idFormatado . '</td>';
                            echo '  <td>' . htmlspecialchars($turma['curso_nome']) . '</td>';
                            echo '  <td>' . $turma['qtd_alunos'] . '</td>';
                            echo '  <td>' . htmlspecialchars($turma['semestre_nome']) . '</td>';

                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="5">Nenhuma turma encontrada.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
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

    <script defer>
        function criarLinhasTurmas() {
            const tbody = document.getElementById('turmas-tbody');

            turmas.forEach(turma => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
            <td>${turma.id}</td>
            <td>${turma.curso}</td>
            <td>${turma.qtdAlunos}</td>
            <td>${turma.semestre}</td>
            <td>
                <div class="actions">
                    <button class="action-btn tableEdit-btn" title="Editar" onclick="editarTurma('${turma.id}')">✎</button>
                    <button class="action-btn tabledelete-btn" title="Excluir" onclick="excluirTurma('${turma.id}')">🗑</button>
                </div>
            </td>
        `;
                tbody.appendChild(tr);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            criarLinhasTabela();
            criarLinhasTurmas();
        });
    </script>


    <script>
        function editarUsuario(id) {
            const row = event.target.closest('tr');
            const cells = row.querySelectorAll('td');
            const ra = cells[0].innerText;
            const nome = cells[1].innerText;
            const email = cells[2].innerText;
            const curso = cells[3].innerText;
            const semestre = cells[4].innerText;

            // Substitui por inputs editáveis
            row.innerHTML = `
        <td><input type="text" value="${ra}" readonly style="background:#eee; border:none; width:90px;"></td>
        <td><input type="text" value="${nome}" id="edit-nome-${id}" style="width:150px;"></td>
        <td><input type="text" value="${email}" id="edit-email-${id}" style="width:200px;"></td>
        <td>${curso}</td>
        <td>${semestre}</td>
        <td>
            <button onclick="salvarEdicao(${id})" class="tableSave-btn">💾</button>
            <button onclick="cancelarEdicao()" class="tableCancel-btn">❌</button>
        </td>`;
        }

        function cancelarEdicao() {
            location.reload();
        }

        function salvarEdicao(id) {
            const nome = document.getElementById(`edit-nome-${id}`).value.trim();
            const email = document.getElementById(`edit-email-${id}`).value.trim();

            if (!nome || !email) return alert("Preencha todos os campos!");

            fetch("", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `acao=editar&id=${id}&nome=${encodeURIComponent(nome)}&email=${encodeURIComponent(email)}`
                })
                .then(res => res.text())
                .then(msg => {
                    alert(msg);
                    location.reload();
                })
                .catch(() => alert("Erro ao salvar alterações."));
        }

        function excluirUsuario(id) {
            if (!confirm("Deseja realmente excluir este usuário?")) return;

            fetch("", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `acao=excluir&id=${id}`
                })
                .then(res => res.text())
                .then(msg => {
                    alert(msg);
                    location.reload();
                })
                .catch(() => alert("Erro ao excluir usuário."));
        }
    </script>



</body>

</html>