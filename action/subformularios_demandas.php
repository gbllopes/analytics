<?php
include_once '../Database/database.php';
$db = new Database();

// Recebe informações do ajax e seta nas variáveis. 

@$pagina = $_POST['pagina'];  
@$busca  = $_POST['busca'];
$acao   = $_POST['acao'];
$demanda = $_POST['demanda'];

$sql = "select assunto, num_demanda, demandante, divisao, necessidades, condicoes, campos_resultados, responsavel, matricula, fone_ramal, entregue, data_entrega_txt from demandas_mining where num_demanda= ".$demanda;
$res = mysqli_query($db->getConection(), $sql);

foreach($res as $r){

    // Caso a ação for abrir, exibe tela de análise da demanda para o usuário.

    if($acao == "abrir"){
        echo '<table class="table table-responsive-sm border-left border-right">
                    <tbody>
                        <tr>
                            <th style="background:#CCCCCC" class="text-center" colspan="2">. : Relatório de Demanda : .</th>
                        </tr>
                        <tr><td class="border-0"></td><td class="border-0"></td></tr>
                        <tr><td class="border-0"></td><td class="border-0"></td></tr>
                        <tr>
                            <td class="font-weight-bold td-abrir">Assunto</td>
                            <td class="w-80 border border-secondary" bgcolor="#ececec">'.utf8_encode($r['assunto']).'</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold td-abrir">Nº da demanda</td>
                            <td class="w-80 border border-secondary" bgcolor="#ececec">'.$r['num_demanda'].'</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Demandante</td>
                            <td class="border border-secondary" bgcolor="#ececec">'.utf8_encode($r["demandante"]).'</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Divisão</td>
                            <td class="border border-secondary" bgcolor="#ececec">'.utf8_encode($r['divisao']).'</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Necessidades</td>
                            <td class="border border-secondary" bgcolor="#ececec">'.utf8_encode($r['necessidades']).'</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Condições</td>
                            <td class="border border-secondary" bgcolor="#ececec">'.utf8_encode($r['condicoes']).'</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Campos/Resultados</td>
                            <td class="border border-secondary" bgcolor="#ececec">'.utf8_encode($r['campos_resultados']).'</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Responsável</td>
                            <td class="border border-secondary" bgcolor="#ececec">'.utf8_encode($r['responsavel']).'</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold border-bottom">Status</td>
                            <td class="border border-secondary" bgcolor="#ececec">'.($r['entregue']=="Sim" ? "Entregue": "Não entregue").'</td>
                        </tr>';
                        if($r['entregue'] == "Sim"){
                            $data = str_replace("-", "/", $r['data_entrega_txt']);
                            echo '<tr>
                                        <td class="font-weight-bold border-bottom">Data da entrega</td>
                                        <td class="border border-secondary" bgcolor="#ececec">'.$data.'</td>
                                 </tr>';
                        }
                        echo '
                        <tr><td class="border-0"></td><td class="border-0"></td></tr>
                        <tr><td class="border-0"></td><td class="border-0"></td></tr>
                        <tr>
                            <th style="background:#CCCCCC" class="text-center" colspan="2"></th>
                        </tr>
                    </tbody>
            </table>
            <div id="alternar"><button id="voltarAbrir" class="btn btn-primary float-right">Voltar</button></div>';
    }

    // Caso a ação for editar, exibe tela de edição de demanda para o usuário.
    
    if($acao == "editar"){
        echo ' <div id="container" class="row" >
                    <div class="rounded border border-secondary sub-formularios" style="width:90%">
                        <div class="col">            
                            <form id="form_edit_demanda" action="" method="">
                                <div class="form-group row mb-0 mt-3">
                                    <label for="id" class="col-sm-2 col-form-label"><strong>Nº da demanda:</strong></label>
                                    <div class="col-sm-10">
                                        <input name="demanda" class="form-control font-weight-bold" value="'.utf8_encode($r['num_demanda']).'" readonly>
                                    </div>    
                                </div>
                                <div class="form-group row">
                                    <label for="demandante" class="col-sm-2 col-form-label"><strong>Demandante:</strong></label>
                                    <div class="col-sm-10">
                                        <input class="form-control font-weight-bold" value="'.utf8_encode($r['demandante']).'" readonly>
                                    </div> 
                                </div>
                                <div class="form-group row">
                                    <label for="matricula" class="col-sm-2 col-form-label"><strong>Matrícula:</strong></label>
                                    <div class="col-sm-10">
                                        <input class="form-control font-weight-bold" value="'.utf8_encode($r['matricula']).'" readonly>
                                    </div>
                                </div> 
                                <div class="form-group row">       
                                    <label for="ramal" class="col-sm-2 col-form-label"><strong>Ramal:</strong></label>
                                    <div class="col-sm-10">
                                        <input name="fone_ramal" class="form-control" value="'.utf8_encode($r['fone_ramal']).'" required>
                                    </div>
                                </div> 
                                <div class="form-group row">   
                                    <label for="assunto" class="col-sm-2 col-form-label"><strong>Assunto:</strong></label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control ng" name="assunto" required>'.utf8_encode($r['assunto']).'</textarea>
                                    </div> 
                                </div>
                                <div class="form-group row">   
                                    <label for="assunto" class="col-sm-2 col-form-label"><strong>Campos/<br>Resultados:</strong></label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control ng" name="campos_resultados" required>'.utf8_encode($r['campos_resultados']).'</textarea>
                                    </div> 
                                </div>
                                <div class="form-group row">   
                                    <label for="assunto" class="col-sm-2 col-form-label"><strong>Necessidades:</strong></label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control ng" name="necessidades" required>'.utf8_encode($r['necessidades']).'</textarea>
                                    </div> 
                                </div>
                                <div class="form-group row">   
                                    <label for="assunto" class="col-sm-2 col-form-label"><strong>Condições:</strong></label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control ng" name="condicoes" required>'.utf8_encode($r['condicoes']).'</textarea>
                                    </div> 
                                </div>
                                <div class="form-group row">
                                    <label for="assunto" class="col-sm-2 col-form-label"><strong>Classificação</strong></label>
                                    <div class="col-sm-10">
                                        <select id="editSelect" name="classificacao" class="form-control">
                                            <option value="">Reclassifique esta demanda(opcional)</option>
                                            <option class="ng" value="Missing">Não Classificar</option>
                                            <option class="ng" value="Importante">Importante</option>
                                            <option class="ng" value="Urgente">Urgente</option>
                                        </select>  
                                    </div>      
                                </div>    
                                <div class="form-group row">
                                    <label for="responsavel" class="col-sm-2 col-form-label"><strong>Responsavel:</strong></label>
                                    <div class="col-sm-10">
                                        <select name="responsavel" id="editSelect" class="form-control">
                                            <option value="">Escolha o responsável por esta demanda</option>';
        
                                                $sql = "select nome_responsavel from responsaveis";
                                                $resultados = mysqli_query($db->getConection(), $sql);
                                                foreach($resultados as $res ){
                                                    echo '<option class="ng" value="'.$res['nome_responsavel'].'">'.$res['nome_responsavel'].'</option>';
                                                }
                                        echo '
                                        </select>
                                    </div>
                                </div> 
                                <div class="form-group row mb-3">   
                                    <label for="dt_entrega" class="col-sm-2 col-form-label"><strong>Data da Entrega:</strong></label>
                                    <div class="col-sm-10">
                                        <input name="dt_entrega" id="datepicker" class="form-control" style="width:7em" autocomplete="off" readonly>
                                    </div>
                                </div> 
                                <input name="acao" type="hidden" value="editar_demanda">
                                <div class="w-100 mt-2 border border-sencondary"></div>
                                <div class="mt-2" id="result" role="alert"></div>     
                                <button type="button" id="voltarEditar" class="btn btn-danger float-right mt-2 mb-2">Sair</button>
                                <button type="submit" class="btn btn-success float-right mt-2 mr-3">Enviar</button>
                            </form>
                        </div>     
                    </div>                
                </div>        
             ';
    }
}
?>