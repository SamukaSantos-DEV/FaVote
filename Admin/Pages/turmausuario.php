<?php

include '../../php/config.php';

$sqlUsuarios = "
    SELECT 
        a.id AS aluno_id,
        a.ra,
        a.nome,
        a.email_institucional,
        a.cpf,
        c.nome AS curso_nome,
        s.nome AS semestre_nome
    FROM alunos a
    LEFT JOIN turmas t ON a.turma_id = t.id
    LEFT JOIN cursos c ON t.curso_id = c.id
    LEFT JOIN semestres s ON t.semestre_id = s.id
";

$resultUsuarios = $conexao->query($sqlUsuarios);

$sqlTurmas = "
    SELECT 
        t.id AS turma_id,
        c.id AS curso_id,
        c.nome AS curso_nome,
        s.nome AS semestre_nome,
        COUNT(a.id) AS qtd_alunos
    FROM turmas t
    LEFT JOIN cursos c ON t.curso_id = c.id
    LEFT JOIN semestres s ON t.semestre_id = s.id
    LEFT JOIN alunos a ON a.turma_id = t.id
    GROUP BY t.id
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
                <strong>Usuário Administrador</strong>
                <p>FATEC “Dr. Ogari de Castro Pacheco”</p>
                <strong>
                    <p>DSM (N)</p>
                </strong>
                <p>1º Semestre</p>

                    <div class="editar">
                    <a href="editardados.php">Editar dados<i class="fa-solid fa-pen-to-square" style="margin-left: 7px;"></i></a>
                </div>



                
                <div class="sair">
                    <a href="../../login.php">Sair<i style="margin-left: 5px;" class="fa-solid fa-right-from-bracket"></i></a>
                </div>
            </div>
        </div>

    </header>

<script>
    window.addEventListener('scroll', function () {
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
                        <th>CPF</th>
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
                            <td><?php echo htmlspecialchars($usuario['cpf']); ?></td>
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
                    <tr><td colspan="7">Nenhum usuário encontrado.</td></tr>
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
                        <th>Qtd de Alunos Relacionados</th>
                        <th>Semestre</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="turmas-tbody">
                <?php if ($resultTurmas->num_rows > 0): ?>
                    <?php foreach ($resultTurmas as $turma): ?>
                        <?php $idFormatado = str_pad($turma['turma_id'], 2, '0', STR_PAD_LEFT); ?>
                        <tr>
                            <td><?php echo $idFormatado; ?></td>
                            <td><?php echo htmlspecialchars($turma['curso_nome']); ?></td>
                            <td><?php echo $turma['qtd_alunos']; ?></td>
                            <td><?php echo htmlspecialchars($turma['semestre_nome']); ?></td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn tableEdit-btn" title="Editar" onclick="editarTurma('<?php echo $turma['turma_id']; ?>')">✎</button>
                                    <button class="action-btn tabledelete-btn" title="Excluir" onclick="excluirTurma('<?php echo $turma['turma_id']; ?>')">🗑</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">Nenhuma turma encontrada.</td></tr>
                <?php endif; ?>
            </tbody>
            </table>
        </div>
    </div>



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