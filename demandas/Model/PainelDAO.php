<?php
include_once "../Database/database.php";
class PainelDAO{
    private $db;
    public function __construct(DataBase $db){
        $this->db = $db;
    }


    //  funções  //
    // Cria o arquivo JSON contendo os dados que vieram do banco de dados.
    function criarJSON(){
        $sql = "select a.id ,num_demanda,UPPER(divisao), gerencia, assunto from demandas_mining a inner join gerencias b on b.id = a.id_gerencia where entregue like '%Não%'";
        $res = mysqli_query($this->db->getConection(), $sql);
        $num = mysqli_num_rows($res);
        if ($num < 1){
            $json[] = [
                        'id'        =>  0,
                        'key'       =>  0,
                        'gerencia'  =>  0,
                        'value'     =>  0,
                        'divisao'   =>  0,
                        'assunto'   =>  0
                      ];
        }else{
            foreach ($res as $obj) { 
                $json[] = [
                            'id'                => $obj['id'],
                            'key'               => utf8_encode($obj['UPPER(divisao)']),
                            'gerencia'          => utf8_encode($obj['gerencia']),
                            'value'             => 1,
                            'demanda'           => utf8_encode($obj['num_demanda']),
                            'assunto'           => utf8_encode($obj['assunto'])
                        ];         
            }
        }     
        $codificado = json_encode($json, JSON_UNESCAPED_SLASHES); 
        file_put_contents('../Paineis/gerencias.json', $codificado); // Armazena o arquivo com os dados na pasta /Paineis/
        $this->db->encerrarConexao();
    } 
    
}
?>