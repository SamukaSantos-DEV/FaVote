<?php
class db {
    private $hostname = 'localhost';
    private $bancodedados = 'u180877424_favotedb';
    private $usuario = 'u180877424_favotedb';
    private $senha = 'FaVoteDB2025';
    private $mysqli = null;

    public function conecta_mysql() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->mysqli = new mysqli($this->hostname, $this->usuario, $this->senha, $this->bancodedados);

        if ($this->mysqli->connect_errno) {
            die("Falha na conexão: ({$this->mysqli->connect_errno}) {$this->mysqli->connect_error}");
        }

        $this->mysqli->set_charset("utf8mb4");

        return $this->mysqli;
    }
}

$db = new db();
$conexao = $db->conecta_mysql();
?>
