    <?php 
        include "./Database/database.php";
        $db = new Database();
    ?>    
    <!DOCTYPE html>
    <html>
    <head>
        
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="shortcut icon" href="img/analytics.png" />
        <link rel="stylesheet" href="css/layout.css" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
        <script type="text/javascript" src="javascript/d3/d3.v3.min.js"></script> 
        <script type="text/javascript" src="javascript/treemap.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="javascript/calendario/_style/jquery.click-calendario-1.0.css">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        
        <meta charset="utf-8">
        <title>Demandas Analytics</title>
    </head>

    <body style="background:#ececec">
        <div id="grafico" style="width:99.5%">
            <div id="chart"></div>
            <div id="info" style="display:none"></div>
        </div>
        <!-- Modal de adição -->
        <div class="modal fade" id="modalRegistro" role="dialog" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog">
                <form action="" method="" class="modal-content" id="ajax_form_add">
                    <div class="modal-header" style="padding:10px !important">
                        <h2 class="modal-title" style="font-size:40px" align="center"><strong>Adicionar demanda</strong></h2>
                    </div>
                    <div class="modal-body" style="padding:25px">
                        <div class="form-group">
                            <label for="demandante">Demandante</label>
                            <input class="form-control" name="demandante" type="text" required>
                        </div>  
                        <div class="form-group">
                            <label for="matricula">Matrícula</label>
                            <input class="form-control" id="matr" name="matricula" type="text" required>
                        </div> 
                        <div class="form-group">
                            <label for="fone_ramal">Fone/Ramal</label>
                            <input class="form-control" name="fone_ramal" type="text" required>
                        </div> 
                        <div class="form-group">
                            <label for="divisao">Divisão</label>
                            <select class="form-control" name="divisao" required>
                                <option value="">Informe sua divisão</option>
                                <?php
                                    $sql = "select id_divisao, divisao from divisoes order by divisao asc";
                                    $resultados = mysqli_query($db->getConection(), $sql);
                                    foreach($resultados as $res){
                                        echo '<option value="'.$res['id_divisao'].'">'.$res['divisao'].'</option>';
                                    }
                                ?>
                            </select>
                    </div>
                    <div class="form-group">
                        <label for="assunto">Assunto</label>
                        <input class="form-control" name="assunto" type="text" required>
                    </div>
                    <div class="form-row">
                    <div class="form-group col-sm-4">
                            <label for="Classificação">Classificação</label>
                            <div class="radio">     
                                <label><input type="radio" name="classificacao" value="">Não Classificar</label>
                            </div>
                            <div class="radio">     
                                <label><input type="radio" name="classificacao" value="Importante">Importante</label>
                            </div>
                            <div class="radio"> 
                                <label><input type="radio" name="classificacao" value="Urgente" required>Urgente</label>
                            </div>
                    </div>
                    <div class="form-group col-sm-8" id="responsavel">
                    <label for="responsavel">Responsável</label>
                            <select name="responsavel" class="form-control">
                                <option value="">Informe um responsável por esta demanda</option>
                                <?php
                                    $sql = "select nome_responsavel from responsaveis";
                                    $resutados = mysqli_query($db->getConection(), $sql);
                                    foreach($resutados as $res){
                                        echo '<option value="'.$res['nome_responsavel'].'">'.$res['nome_responsavel'].'</option>';
                                    }
                                ?>
                            </select>
                    </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4" style="float: right;margin-top: 56px;">
                            <label for="necessidades">Necessidades</label>
                            <textarea class="form-control width-max" name="necessidades" rows="3" required></textarea>
                        </div>
                        <div class="col-sm-4">
                            <label for="condicoes">Condições</label>
                            <textarea class="form-control width-max" name="condicoes"rows="3" required></textarea>
                        </div>
                        <div class="col-sm-4">
                            <label for="campos_resultados">Campos/Resultados</label>
                            <textarea class="form-control width-max" name="campos_resultados" rows="3" required></textarea>
                        </div> 
                    </div>
                    <div id="resultDem" role="alert" style="margin-top:5px"></div> 
                    <div class="modal-footer" style="margin-top:5px">
                        <button type="submit" class="btn btn-success">Adicionar</button>
                        <button id="add" type="button" class="btn btn-danger" data-dismiss="modal">Sair</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- modal de CONSULTA/EDIÇÃO de demandas NO GRÁFICO -->
    <div class="modal fade bd-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" >
            <div class="modal-dialog" role="document" style="width:95%;margin-top:70px">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    </button>
                </div>
                <div class="modal-body" id="tabelaDemandas">                   
                </div>
                <div class="modal-footer">
                    <button id="fecharRelatorio" type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                </div>
                </div>
            </div>
        </div>    
    </body>

    </html>