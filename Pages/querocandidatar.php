<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Candidatar | FaVote</title>
    <link rel="icon" href="../Images/iconlogoFaVote.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');

        body {
            font-family: 'Poppins';
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(204, 204, 204, 0.3);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }


        .modal-content {
            background-color: #d6d6d6;
            border-radius: 30px;
            width: 450px;
            padding: 30px 50px 30px 30px;
            max-width: 100%;
            box-shadow: 0 0 10px rgb(136, 136, 136);
        }

        .modal-content h2 {
            color: #B60000;
            font-size: 35px;
            text-align: center;
            margin-bottom: 30px;
        }

        .modal-content label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }

        .modal-content input,
        .modal-content textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 2px solid #000;
            font-size: 14px;
        }

        .modal-content select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 2px solid #000;
            font-size: 14px;
        }

        .btn-concluir {
            background-color: #d60e0e;
            color: white;
            border: none;
            padding: 20px;
            width: 107%;
            margin-top: 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 22px;
            cursor: pointer;
        }

        .close-btn {
            margin-left: 21%;
            margin-top: 1.5%;
            position: absolute;
            background-color: #e9e9e9;
            color: #383838;
            border: none;
            padding: 6px 13px;
            border-radius: 10px;
            font-size: 1.5em;
            cursor: pointer;
            transition: background-color 0.6s ease, transform 0.6s ease;
        }

        .close-btn:hover {
            background-color: #eea7a7;
            transform: scale(1.05);

        }
    </style>
</head>

<body>

    <div id="modal-editar" class="modal">

        <div class="modal-content">
            <span onclick="fechar()" class="close-btn">✖</span>
            <h2>CANDIDATAR</h2>

            <label>E-Mail Institucional (apenas FATEC):</label>
            <input type="text" placeholder="itapira@fatec.sp.gov.br" />

            <label>RA:</label>
            <input type="text" maxlength="13" placeholder="0000000000000" title="Digite exatamente 13 números" />

            <div style="display: flex; ">
                <div style="flex: 1;">
                    <label>Curso:</label>
                    <select>
                        <option value="">DSM (N)</option>
                        <option value="">GE (N)</option>
                        <option value="">GPI (N)</option>
                    </select>
                </div>
                <div style="flex: 1; ">
                    <label style="margin-left: 10%;">Semestre:</label>
                    <select style="margin-left: 10%;">
                        <option value="">1° Semestre</option>
                        <option value="">2° Semestre</option>
                        <option value="">3° Semestre</option>
                        <option value="">4° Semestre</option>
                        <option value="">5° Semestre</option>
                        <option value="">6° Semestre</option>
                    </select>
                </div>
            </div>


            <label>Qual o motivo da sua candidatura? Qual a finalidade?</label>
            <textarea style="resize: vertical;" id="descricao" rows="5"></textarea>

            <button class="btn-concluir" onclick="enviarCandidatura()">CANDIDATAR</button>
        </div>
    </div>

    <script>
        function fechar() {
            try {
                if (window.history.length > 1) {
                    history.back();
                } else {
                    window.location.href = "../login.php";
                }
            } catch (e) {
                window.location.href = "../login.php";
            }
        }

        function enviarCandidatura() {
            const ra = document.querySelector('input[placeholder="0000000000000"]').value.trim();
            const curso = document.querySelectorAll('select')[0].value.trim();
            const semestre = document.querySelectorAll('select')[1].value.trim();
            const descricao = document.getElementById("descricao").value.trim();

            if (!ra || !curso || !semestre || !descricao) {
                alert("Por favor, preencha todos os campos antes de se candidatar.");
                return; 
            }

            alert("Candidatura enviada com sucesso!");

            try {
                if (window.history.length > 1) {
                    history.back();
                } else {
                    window.location.href = "../login.php";
                }
            } catch (e) {
                window.location.href = "../login.php";
            }
        }
    </script>




</body>

</html>