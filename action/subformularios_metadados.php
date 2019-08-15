<?php
include_once '../Database/database.php';
$db = new Database();

// Recebe informações do ajax e seta nas variáveis. 

@$pagina   = $_POST['pagina'];
@$busca    = $_POST['busca'];
$acao     = $_POST['acao'];

// Com base na variavel ação, realiza uma das operações a seguir.

if ($acao == "add_metadados"){
 @$biblioteca = $_POST['biblioteca'];
 @$table_name = $_POST['table_name'];
 @$name       = $_POST['name'];
 @$tipo       = $_POST['tipo'];
 @$tamanho     = $_POST['tamanho'];
 @$format     = $_POST['format'];
 @$informat   = $_POST['informat'];
 @$label      = $_POST['label'];

 // Verifica se existe dados nas variáveis, caso não tenha, exibe o formulário de cadastro de metadado. Caso tenha, adiciona no banco de dados um novo metadado. 

    if(empty($biblioteca) && empty($table_name) && empty($name) && empty($tipo) && empty($format) && empty($informat) && empty($label)){
        echo '<div class="row">
                <div id="add_met" class="border border-secondary rounded sub-formularios">
                    <form id="ajax_form_add_metadados" action="" method="">
                        <div class="form-group>
                            <label for="biblioteca">Biblioteca:</label>
                            <input class="form-control" name="biblioteca" required>
                        </div>
                        <div class="form-group">
                            <label for="nome_tabela">Nome da tabela:</label>
                            <input class="form-control" name="table_name" required>
                        <div class="form-group>
                            <label for="name">Nome do metadado:</label>
                            <input class="form-control" name="name" required>
                        </div> 
                        <div class="form-group>
                            <label for="tipo">Tipo:</label>
                            <input class="form-control" name="tipo" required>
                        </div>
                        <div class="form-group>
                            <label for="tamanho">Tamanho:</label>
                            <input type="number" class="form-control" name="tamanho" required>
                        </div>
                        <div class="form-group>
                            <label for="formato">Formato:</label>
                            <input class="form-control" name="format" required>
                        </div>
                        <div class="form-group>
                            <label for="informat">Informat:</label>
                            <input class="form-control" name="informat" required>
                        </div>
                        <div class="form-group>
                            <label for="label">Conceito:</label>
                            <input class="form-control" name="label" required>
                        </div> 
                        <input type="hidden" name="acao" value="add_metadados">     
                        <div class="w-100 mt-2 border border-sencondary"></div>
                        <div id="result" class="mt-2" role="alert"></div>
                        <div class="mt-2"> 
                            <button id="voltarAddMetadado" class="btn btn-danger float-right" type="button">Voltar</button>
                            <button class="btn btn-success float-right mr-3" type="submit">Enviar</button>
                        </div>
                    </form> 
                </div>                       
        </div>';
        } else {
            $sql = "INSERT INTO metadados (library, table_name, name, type, length, format, informat, label) values ( '".$biblioteca."', '".$table_name."', '".$name."', '".$tipo."', ".$tamanho." , '".$format."', '".$informat."', '".utf8_decode($label)."')";
            $res = mysqli_query($db->getConection(), $sql);
            if($res){
                echo true;
            } else {
                echo false . mysqli_error($db->getConection());
            }
        }   

} else {

        // Sendo a ação "editar_metadado", exibe a tela de edição de metadado.
        
        $metadado = $_POST['metadado'];
        $sql = "select id_metadado, library, table_name, name, type, length, label from metadados where id_metadado =".$metadado;
        $resultados = mysqli_query($db->getConection(), $sql);
        foreach($resultados as $r){
            echo '<div id="container" class="row" >
            <div class="border border-secondary rounded sub-formularios">
                <div class="col">            
                    <form id="form_edit_metadado" action="" method="">
                        <div class="form-group row mb-3 mt-3">
                            <label for="biblioteca" class="col-sm-2 col-form-label"><strong>Biblioteca:</strong></label>
                            <div class="col-sm-10">
                                <input name="biblioteca" class="form-control font-weight-bold" value="'.$r['library'].'">
                            </div>    
                        </div>
                        <div class="form-group row">
                            <label for="tabela" class="col-sm-2 col-form-label"><strong>Tabela:</strong></label>
                            <div class="col-sm-10">
                                <input name="tabela" class="form-control font-weight-bold" value="'.$r['table_name'].'" >
                            </div> 
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 col-form-label"><strong>Nome:</strong></label>
                            <div class="col-sm-10">
                                <input name="nome" class="form-control font-weight-bold" value="'.$r['name'].'">
                            </div>
                        </div> 
                        <div class="form-group row">       
                            <label for="tipo" class="col-sm-2 col-form-label"><strong>Tipo:</strong></label>
                            <div class="col-sm-10">
                                <input name="tipo" class="form-control" value="'.$r['type'].'">
                            </div>
                        </div> 
                        <div class="form-group row">   
                            <label for="Tamanho" class="col-sm-2 col-form-label"><strong>Tamanho:</strong></label>
                            <div class="col-sm-10">
                                <input name="tamanho" class="form-control font-weight-bold" value="'.$r['length'].'">
                            </div> 
                        </div>    
                        <div class="form-group row">   
                            <label for="conceito" class="col-sm-2 col-form-label"><strong>Conceito:</strong></label>
                            <div class="col-sm-10">
                                <input name="conceito" class="form-control font-weight-bold" value="'.utf8_encode($r['label']).'">
                            </div> 
                        </div> 
                        <input name="metadado" type="hidden" value="'.$r['id_metadado'].'">
                        <input name="acao" type="hidden" value="editar_metadado">
                        <div class="w-100 mt-2 border border-sencondary"></div> 
                        <div id="result" class="mt-2" role="alert"></div>   
                        <button id="voltarEditarMetadado" type="button" class="btn btn-danger mt-2 mb-2 float-right">Voltar</button>
                        <button type="submit" class="btn btn-success mr-3 mt-2 float-right">Enviar</button>
                    </form>
                    
                </div>     
            </div>
            
        </div>        
        ';
    } 
 
}

?>