<?php
class db
{
    private $hostname = 'localhost'; // ou 'localhost'
    private $bancodedados = 'favotedb'; // nome do banco local
    private $usuario = 'root'; // padrão do XAMPP
    private $senha = ''; // XAMPP NÃO usa senha por padrão
    private $mysqli = null;

    public function conecta_mysql()
    {
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