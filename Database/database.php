<?php
class DataBase {

    private $host         = "xxxxxxxxxxxxxxxxx";
    private $user         = "xxxxxxxxxxxxxxxxx";
    private $password     = "xxxxxxxxxxxxxxxxx";
    private $db           = "db_mining";
    private $conn         = null;

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
