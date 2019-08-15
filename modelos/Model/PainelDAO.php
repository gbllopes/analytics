<?php
include_once "../Database/database.php";
class PainelDAO{
    private $db;
    public function __construct(DataBase $db){
        $this->db = $db;
    }


    //  funções  //
    // Verifica se o LINK já encontra-se no banco de dados.
    function verificarLink($link) {
        $sql = "select * from paineis where link ='".$link."'";
        $res = mysqli_query($this->db->getConection(),$sql);
        $num = mysqli_num_rows($res);
    return $num;
    }

    //  Adiciona mais um click ao id no banco de dados.
    function atualizarQtdeAcessos($id){
        $sql = "update paineis
                set qtde_acessos = qtde_acessos + 1
                where id_painel = ".$id;
        $res = mysqli_query($this->db->getConection(), $sql);
    }
    
    // Adiciona novo painel de modelo no banco de dados.
    function adicionarPainel($categoria, $assunto,$link,$desc){
        $num = $this->verificarLink($link);
        if($num == 0){
            $sql = "insert into paineis
             (categoria, assunto, qtde_acessos, link, descricao)
              values ('".$categoria."', '".$assunto."', 1, '".$link."', '".$desc."')";
             $res = mysqli_query($this->db->getConection(), $sql);
             if ($res){
                 return "success";
             }
             
        }
    }

    // Edita um painel com novas informações no banco de dados.
    function editarPainel($id, $assunto, $desc,$link){
        if($assunto == null && $desc == null && $link == null){
            return "fail";
        }else{ 
            $sql = "update paineis set ";
            if($assunto != null && $desc != null && $link != null){
                $sql .= "assunto = '".$assunto."', descricao = '".$desc."', link = '".$link."'"; 
            }else{       
                if($assunto){
                    if($desc == null && $link == null){
                        $sql .= "assunto = '".$assunto."'"; 
                    }   
                    if($desc){
                        $sql .= "assunto = '".$assunto."',"; 
                    }
                    if($link){
                        $sql .= "assunto = '".$assunto."',";
                    }
                }
                if($desc){
                    if($assunto == null && $link == null){
                        $sql .= "descricao = '".$desc."'";
                    }
                    if($assunto){
                        $sql .= " descricao = '".$desc."'";
                    }
                    if($link){   
                        $sql .= "descricao = '".$desc."',";
                    }  
                }
                if($link){
                    if($assunto == null && $desc == null){
                        $sql .= "link = '".$link."'"; 
                    }
                    if($assunto){
                        $sql .= " link = '".$link."'";
                    }
                    if($desc){
                        $sql .= " link = '".$link."'";
                    }
                }
            }
            $sql .= " where id_painel = ".$id;
            $res = mysqli_query($this->db->getConection(), $sql);
            if($res){
                return "success";
            }
        }
    }

    // Lista painel de modelos ao usuário
    function listarPainel(){
        $sql = "select id_painel, assunto from paineis";
        $res = mysqli_query($this->db->getConection(), $sql);
        return $res;
    }

    // Exclui um modelo do painel de modelos com base no id.
    function excluirPainel($id){
        $sql = "delete from paineis where id_painel = ".$id;
        $res = mysqli_query($this->db->getConection(), $sql);
        if($res){
            echo true;
        } 
    }

    // Cria um novo arquivo JSON com os dados do banco de dados.
    function criarJSON(){
        $sql = "select * from paineis";
        $res = mysqli_query($this->db->getConection(), $sql);
        $num = mysqli_num_rows($res);
        if ($num < 1){
            $json[] = [
                'id'        =>  0,
                'key'       =>  0,
                'gerencia'  =>  0,
                'value'     =>  0
              ];
        }else{
            foreach ($res as $obj) {  
                $json[] = [
                            'id'                 =>  $obj['id_painel'],
                            'key'                =>  utf8_encode($obj['assunto']),
                            'categoria'          =>  utf8_encode($obj['categoria']),
                            'value'              =>  (int)$obj['qtde_acessos'] + 1000,
                            'link'               =>  utf8_encode($obj['link']),
                            'descricao'          =>  utf8_encode($obj['descricao']), 
                        ];
                    
            } 
        }        
        $codificado = json_encode($json, JSON_UNESCAPED_SLASHES); 
        file_put_contents('../Paineis/paineis.json', $codificado); // armazena o novo arquivo na pasta /Paineis/
    } 
    
    function criarJSON_QTDE_POR_CATEGORIA(){
        $sql = "select categoria, count(categoria) as qtde_por_categoria from paineis  group by categoria";
        $resultados = mysqli_query($this->db->getConection(), $sql);
        foreach ($resultados as $res){
            $jsonQtde_Por_Categoria[] = [
                                        'categoria' => utf8_encode($res['categoria']),
                                        'QTDE'      => (int)$res['qtde_por_categoria'],
            ];
        }
        $codificado = json_encode($jsonQtde_Por_Categoria, JSON_UNESCAPED_SLASHES);
        file_put_contents('../Paineis/qtde_por_categoria.json', $codificado); // armazena o novo arquivo na pasta /Paineis/ */
    }
}
?>