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
</head>

<body>
    <div class="container" id="container">
        <div class="background"></div>
        <div class="form-container">            
            <form class="form-box login" id="loginForm">
                <h2>Fazer login</h2>

                <label>E-Mail:</label>
                <input type="email" placeholder="Ex: joao.silva@fatec.sp.gov.br" />

                <label>Senha:</label>
                <input type="password" placeholder="Digite sua senha" />

                <div class="checkbox">
                    <input type="checkbox" id="keepLogged" />
                    <label for="keepLogged">Mantenha-me conectado</label>
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

            <form class="form-box register" id="registerForm">
                <h2>Cadastre-se</h2>

                <label>Nome de usuário:</label>
                <input type="text" placeholder="Digite seu nome" />

                <label>E-Mail Institucional (apenas FATEC):</label>
                <input type="email" placeholder="email@fatec.sp.gov.br" />

                <label>RA:</label>
                <input type="text" maxlength="13" placeholder="0000000000000" title="Digite exatamente 13 números" />

                <label>Senha:</label>
                <input type="password" placeholder="Crie uma senha" />

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
            e.preventDefault();

            const username = this.querySelector('input[type="email"]').value.trim();
            const password = this.querySelector('input[type="password"]').value.trim();

            if (!username || !password) {
                alert('Por favor, preencha todos os campos.');
                return;
            }
            if (username === "admin@fatec.sp.gov.br") {
                if(password === "admin"){
                window.location.href = 'Admin/Pages/home.php';
                }
            }
            else {
                window.location.href = 'Pages/home.php';
            }



        });

        document.getElementById('registerForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const inputs = this.querySelectorAll('input, select');
            const username = inputs[0].value.trim();
            const email = inputs[1].value.trim();
            const cpf = document.getElementById('cpf').value.trim();
            const password = inputs[5].value.trim();

            const emailValido = /^[a-zA-Z0-9._%+-]+@fatec\.sp\.gov\.br$/.test(email);
            const cpfValido = /^\d{11}$/.test(cpf);


            if (!username || !email || !cpf || !password) {
                alert('Por favor, preencha todos os campos obrigatórios.');
                return;
            }

            if (!emailValido) {
                alert('O email deve ser institucional (@fatec.sp.gov.br).');
                return;
            }

            if (!cpfValido) {
                alert('CPF inválido. Use 11 dígitos.');
                return;
            }

            if (!termsAccepted) {
                alert('Você deve aceitar os Termos de Contrato.');
                return;
            }
            window.location.href = 'Pages/verificacao.php';
        });


    </script>


</body>

</html>