<?php
session_start();

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;

if ($error) {

    switch ($error) {
        case 'empty_fields':
            $msg = 'Preencha todos os campos para continuar.';
            break;

        case 'invalid_credentials':
            $msg = 'Email ou senha incorretos.';
            break;

        case 'db_error':
            $msg = 'Erro no servidor ao tentar realizar o login.';
            break;

        default:
            $msg = 'Ocorreu um erro inesperado.';
            break;
    }

    echo "<script>alert('$msg');</script>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login/Cadastro</title>
    <link rel="stylesheet" href="Styles/login.css">
    <link rel="icon" href="Images/iconlogoFaVote.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>

<body>
    <div class="container" id="container">
        <div class="background"></div>
        <div class="form-container">

            <?php if ($error): ?>
                <div class="message error">
                    <?php
                    switch ($error) {
                        case 'empty_fields':
                            echo 'Por favor, preencha todos os campos.';
                            break;
                        case 'invalid_email':
                            echo 'O email deve ser institucional (@fatec.sp.gov.br).';
                            break;
                        case 'invalid_ra':
                            echo 'O RA deve conter 13 números.';
                            break;
                        case 'user_exists':
                            echo 'Email ou RA já cadastrado.';
                            break;
                        case 'db_error':
                            echo 'Erro no banco de dados. Tente novamente.';
                            break;
                        case 'invalid_credentials':
                            echo 'Email ou senha incorretos.';
                            break;
                        case 'not_logged_in':
                            echo 'Você precisa fazer login para acessar esta página.';
                            break;
                        default:
                            echo 'Ocorreu um erro desconhecido.';
                    }
                    ?>
                </div>
            <?php endif; ?>
            <?php if ($success == 'registered'): ?>
                <div class="message success">
                    Cadastro realizado com sucesso! Faça o login.
                </div>
            <?php endif; ?>
            <form class="form-box login" id="loginForm" action="php/log.php" method="POST">
                <h2>Fazer login</h2>

                <label>E-Mail:</label>
                <input type="email" name="email_login" placeholder="Ex: joao.silva@fatec.sp.gov.br" />

                <label>Senha:</label>
                <input type="password" id="senha_login" name="senha_login" placeholder="Digite sua senha" />

                <div class="checkbox">
                    <input type="checkbox" id="keepLogged" name="keep_logged" />
                    <label for="keepLogged">Exibir Senha</label>
                </div>




                <div style=" height: 20%;"></div>

                <div class="link">
                    <label style="font-size: 13px" for="terms">Ao fazer login, os <a href="Pages/termos.php"
                            style="text-decoration: none; color: rgb(112, 0, 0);">Termos de
                            Contrato</a> foram
                        aceitos.</label>
                    <div style=" height: 18px;"></div>
                    <span>Não tem Login? <a href="#" style="text-decoration: none; color: rgba(161, 0, 0, 1);"
                            onclick="toggleForm()">Faça um cadastro.</a></span>
                </div>

                <button type="submit">FAZER LOGIN</button>
            </form>

            <form class="form-box register" id="registerForm" action="php/register.php" method="POST">
                <h2>Cadastre-se</h2>

                <label>Nome de usuário:</label>
                <input type="text" name="nome_cadastro" placeholder="Digite seu nome" />

                <label>E-Mail Institucional (apenas FATEC):</label>
                <input type="email" name="email_cadastro" placeholder="email@fatec.sp.gov.br" />

                <label>RA:</label>
                <input type="text" name="ra_cadastro" maxlength="13" placeholder="0000000000000"
                    title="Digite exatamente 13 números" />

                <label>Senha:</label>
                <input type="password" name="senha_cadastro" minlength="6" placeholder="Crie uma senha" required/>

                <label for="terms" style="font-size: 12px">Ao fazer cadastro, os <a href="Pages/termos.php"
                        style="text-decoration: none; margin-top:20px;color: rgb(112, 0, 0);">Termos de
                        Contrato</a> foram
                    aceitos.</label>

                <div style=" height: 10px;"></div>

                <div class="link">
                    <span>Já possui cadastro?<a href="#" style="text-decoration: none; color: rgba(161, 0, 0, 1);"
                            onclick="toggleForm()"> Faça Login.</a></span>
                </div>

                <button type="submit">FINALIZAR CADASTRO</button>
            </form>
        </div>
    </div>

    <script>
        const senhaInput = document.getElementById('senha_login');
        const checkbox = document.getElementById('keepLogged');

        checkbox.addEventListener('change', () => {
            senhaInput.type = checkbox.checked ? 'text' : 'password';
        });
    </script>

    <script>
        const container = document.getElementById('container');

        function toggleForm() {
            container.classList.add('hide-background');
            setTimeout(() => {
                container.classList.toggle('active');
                document.title = container.classList.contains('active') ? 'FaVote | Cadastro' : 'FaVote | Login';
            }, 100);
            setTimeout(() => {
                container.classList.remove('hide-background');
            }, 500);
        }

        document.title = 'Fazer login';

        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const email = this.querySelector('input[name="email_login"]').value.trim();
            const password = this.querySelector('input[name="senha_login"]').value.trim();

            if (!email || !password) {
                alert('Por favor, preencha todos os campos.');
                e.preventDefault();
                return;
            }
        });

        document.getElementById('registerForm').addEventListener('submit', function (e) {
            const username = this.querySelector('input[name="nome_cadastro"]').value.trim();
            const email = this.querySelector('input[name="email_cadastro"]').value.trim();
            const ra = this.querySelector('input[name="ra_cadastro"]').value.trim();
            const password = this.querySelector('input[name="senha_cadastro"]').value.trim();

            const emailValido = /^[a-zA-Z0-9._%+-]+@fatec\.sp\.gov\.br$/.test(email);
            const raValido = /^\d{13}$/.test(ra);

            if (!username || !email || !ra || !password) {
                alert('Por favor, preencha todos os campos obrigatórios.');
                e.preventDefault();
                return;
            }

            if (!emailValido) {
                alert('O email deve ser institucional (@fatec.sp.gov.br).');
                e.preventDefault();
                return;
            }

            if (!raValido) {
                alert('RA inválido. Use 13 dígitos numéricos.');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>

</html>