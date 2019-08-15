<?php
class DataBase {

    private $host         = "172.17.191.110";
    private $user         = "admin";
    private $password     = "conexao123tigre";
    private $db           = "db_mining";
    private $db2          = "analytics";
    private $conn         = null;
    private $conn2        = null;

    public function __construct(){
        $this->conectarDB();
    }
    
    public function getConection(){
        // Metodo que retorna a conexão do banco de dados quando chamado.
        return $this->conn;
    }
    function conectarDB(){    
        $this->conn = mysqli_connect(
           $this->host,
           $this->user,
           $this->password,
           $this->db);    
    }
    function encerrarConexao(){
        mysqli_close($this->conn);
    }
}   
?>