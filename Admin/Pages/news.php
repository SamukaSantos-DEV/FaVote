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
            <a href="dashboard.php"
                style="background-color: brown; color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; transition: background-color 0.6s ease;"
                onmouseover="this.style.backgroundColor='#631212'" onmouseout="this.style.backgroundColor='brown'">
                DASHBOARD
            </a>
        </nav>
        <div class="user-icon">
            <img src="../Images/user.png" width="50" alt="user" />
            <div class="user-popup">
                <strong>Nome de Usuário</strong>
                <p>FATEC “Dr. Ogari de Castro Pacheco”</p>
                <strong><p>DSM (N)</p></strong>
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


    <div class="noticias-container">

        <main class="conteudo-noticias">
            <section id="esta-semana">
                <h2>Esta semana</h2>
                <div class="noticias-grid">
                    <div class="noticia-card">
                        <h3>ELEIÇÃO DE REPRESENTANTE DO 1º DSM PRORROGADA</h3>
                        <p>A votação do representante de sala do curso de DSM (Noturno) foi prorrogada até o dia
                            15/05/2025.</p>
                        <p class="publicado">Publicado em: 10/05/2025 às 13:42</p>
                    </div>
                    <div class="noticia-card">
                        <h3>ENCERRAMENTO DAS INSCRIÇÕES – GPI</h3>
                        <p>O período de inscrições para os candidatos a representante e vice do curso de GPI termina
                            nesta sexta-feira (17/05).</p>
                        <p class="publicado">Publicado em: 09/05/2025 às 16:10</p>
                    </div>
                </div>
            </section>

            <section id="este-mes">
                <h2>Este mês</h2>
                <div class="noticias-grid">
                    <div class="noticia-card">
                        <h3>CANDIDATOS CONFIRMADOS PARA REPRESENTAÇÃO EM GE</h3>
                        <p>Lista oficial dos candidatos a representante e vice do curso de Gestão Empresarial já está
                            disponível no portal interno da Fatec.</p>
                        <p class="publicado">Publicado em: 05/05/2025 às 10:30</p>
                    </div>
                    <div class="noticia-card">
                        <h3>INÍCIO DAS CAMPANHAS PRESENCIAIS</h3>
                        <p>Está liberado o período de campanhas presenciais para todos os cursos. A comissão reforça o
                            respeito às normas internas.</p>
                        <p class="publicado">Publicado em: 03/05/2025 às 09:00</p>
                    </div>
                </div>
            </section>

            <section id="mes-passado">
                <h2>Mês passado</h2>
                <div class="noticias-grid">
                    <div class="noticia-card">
                        <h3>REUNIÃO COM CANDIDATOS ACONTECEU NA BIBLIOTECA</h3>
                        <p>A comissão eleitoral reuniu os pré-candidatos para esclarecimentos sobre regras da eleição e
                            prazos de campanha.</p>
                        <p class="publicado">Publicado em: 18/04/2025 às 15:12</p>
                    </div>
                    <div class="noticia-card">
                        <h3>LANÇADO EDITAL DE ELEIÇÃO 2025</h3>
                        <p>O edital oficial das eleições de representantes e vices de sala para todos os cursos foi
                            publicado no portal da Fatec.</p>
                        <p class="publicado">Publicado em: 10/04/2025 às 14:00</p>
                    </div>
                </div>
            </section>

            <section id="antigas">
                <h2>Antigas</h2>
                <div class="noticias-grid">
                    <div class="noticia-card">
                        <h3>RESULTADOS DAS ELEIÇÕES 2024</h3>
                        <p>Veja quem foram os representantes eleitos nas eleições anteriores para os cursos de DSM, GPI
                            e GE.</p>
                        <p class="publicado">Publicado em: 15/11/2024 às 11:00</p>
                    </div>
                    <div class="noticia-card">
                        <h3>REGRAS DE PARTICIPAÇÃO NA ELEIÇÃO</h3>
                        <p>Relembre as regras de conduta para campanhas eleitorais dentro da instituição.</p>
                        <p class="publicado">Publicado em: 01/10/2024 às 08:50</p>
                    </div>
                </div>
            </section>
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