<?php
require '../php/session_auth.php';
require '../php/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);

    if (!empty($titulo) && !empty($descricao)) {
        $db = new db();
        $conexao = $db->conecta_mysql();

        $sql = "INSERT INTO noticias (titulo, descricao, dataPublicacao) VALUES (?, ?, NOW())";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", $titulo, $descricao);

        if ($stmt->execute()) {
            echo "<script>alert('Notícia publicada com sucesso!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Erro ao publicar notícia.');</script>";
        }

        $stmt->close();
        $conexao->close();
    } else {
        echo "<script>alert('Preencha todos os campos obrigatórios.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Criar Notícia | FaVote</title>
    <link rel="icon" href="../Images/iconlogoFaVote.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
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
            width: 400px;
            padding: 30px 50px 30px 30px;
            box-shadow: 0 0 10px rgb(136, 136, 136);
        }

        .modal-content h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .modal-content input,
        .modal-content textarea,
        .modal-content select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 12px;
            border-radius: 10px;
            border: 2px solid #000;
            font-size: 14px;
            resize: vertical;
        }

        .btn-concluir {
            background-color: #d60e0e;
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            margin-top: 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
        }

        .close-btn {
            position: absolute;
            margin-left: 19%;
            background-color: #d4d4d4;
            color: #383838;
            border: none;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 1.5em;
            cursor: pointer;
            transition: background-color 0.6s ease, transform 0.6s ease;
        }

        .close-btn:hover {
            background-color: #eea7a7;
            transform: scale(1.05);
        }

        .atention-card {
            font-size: 12px;
            font-weight: bold;
            text-align: justify;
        }
    </style>
</head>

<body>
    <div id="modal-editar" class="modal">
        <div class="modal-content">
            <span onclick="history.back()" class="close-btn">✖</span>
            <h2>Criar Notícia</h2>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" required>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao" class="form-control" rows="3" maxlength="150" required></textarea>
                </div>

            



                <div class="atention-card">
                    <p>⚠️ A notícia será visível para todos os usuários da instituição.</p>
                </div>

                <button type="submit" class="btn-concluir">PUBLICAR NOTÍCIA</button>
            </form>
        </div>
    </div>
</body>

</html>