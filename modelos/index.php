<?php
    @$auth = $_GET['auth']; 
    include_once "../modelos/Database/database.php";
    $db = new Database();
?>

    <!DOCTYPE html>
    <html>

    <head>
        <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="shortcut icon" href="img/bb.ico" />
        <link rel="stylesheet" href="css/layout.css" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
        <script type="text/javascript" src="javascript/d3/d3.v3.min.js"></script>
        <!-- link alternativo <script src="http://d3js.org/d3.v3.min.js"></script> -->
        <script type="text/javascript" src="javascript/treemap.js"></script>
        <meta charset="utf-8">
        <title>Painéis Analytics</title>
    </head>

    <body style="background:#ececec">
        <div style="width:99.5%">
            <div id="chart"></div>
            <div id="info"></div>
        </div>
        <!-- Modal de adição -->
        <div class="modal fade" id="modalRegistro" role="dialog" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog">
                <form action="" method="" class="modal-content" id="ajax_form_add">
                    <div class="modal-header">
                        <h2 class="modal-title" style="font-size:40px" align="center"><strong>Adicionar painel</strong></h2>
                    </div>
                    <div class="modal-body" style="padding:25px">
                        <div id="required"><strong>*</strong> campo obrigatório</div>
                        <div class="form-group">
                            <label for="categoria">Categoria</label>
                            <select id="categoria" class="form-control" name="categoria" required>
                                <option id="disponivel" value="">Selecione o tipo</option>
                                <?php
                                    $sql = "select distinct categoria from paineis";
                                    $res = mysqli_query($db->getConection(), $sql);
                                    foreach($res as $r){
                                        echo '<option id="disponivel" value="'.$r['categoria'].'">'.$r['categoria'].'</option>';
                                    }
                                    $db->encerrarConexao();
                                ?>
                                <option id="novaCategoria" value="nova_categoria">Adicionar Categoria</option>
                            </select>
                        </div>
                        <div id="campoCategoria" class="form-group">
                            <label for="categoria">Nova categoria</label>
							<div id="required"><strong>*</strong> campo obrigatório</div>
                            <input type="text" class="form-control" name="nova_categoria">
                        </div>
                        <div class="form-group">
                            <label for="assunto">Assunto</label>
                            <div id="required"><strong>*</strong> campo obrigatório</div>
                            <input type="text" class="form-control" name="assunto" autocomplete="off" required/>
                        </div>
                        <div class="form-group">
                            <label for="Link">Link</label>
                            <div id="required"><strong>*</strong> campo obrigatório</div>
                            <textarea type="text" class="form-control width-max"  rows="3" name="link" autocomplete="off" required></textarea>
                        </div>
                        <div class="form-group">
                        <label for="descricao">Descrição</label>
                            <div id="required"><strong>*</strong> campo obrigatório</div>
                            <textarea type="text" class="form-control width-max" rows="3" name="descricao" autocomplete="off" required></textarea>
                        </div>
                        <div id="result" style="margin-top:10px" role="alert"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <button id="add" type="button" class="btn btn-danger" data-dismiss="modal">Sair</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal de edição -->
        <div class="modal fade" id="modalEdicao" role="dialog" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog">
                <form action="" method="" class="modal-content" id="ajax_form_edit">
                    <div class="modal-header">
                        <h2 class="modal-title" style="font-size:40px" align="center"><strong>Editar painel</strong></h2>
                    </div>
                    <div id="body_edit" class="modal-body" >
                        <div class="form-group">
                            <div id="required"><strong>*</strong> campo obrigatório</div>
                            <select id="slct" class="form-control" name="select_painel"></select>
                        </div>
                        <div id="table_" style="width:100%">
                            <div class="form-group">
                                <label for="assunto">Assunto</label>
                                <input class="form-control" name="assunto" autocomplete="off" />
                            </div>
                            <div class="form-group">
                                <label for="desc">Descrição</label>
                                <textarea class="form-control width-max" name="descricao" autocomplete="off" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="link">Link</label>
                                <textarea class="form-control width-max" name="link" autocomplete="off" rows="3"></textarea>
                            </div>
                            <div id="result2" role="alert"></div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button id="altBtn" style="display: none;" type="submit" class="btn btn-success">Alterar</button>
                        <button id="edit" type="button" class="btn btn-danger" data-dismiss="modal">Sair</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Modal de exclusão -->
        <div class="modal fade" id="modalDelete" role="dialog" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog">
                <form action="" method="" class="modal-content" id="ajax_form_del">
                <div class="modal-header">
                        <h2 class="modal-title" style="font-size:40px" align="center"><strong>Excluir painel</strong></h2>
                    </div>
                    <div id="body_delete" class="modal-body" style="padding:25px;">
                        <div class="form-group">
                            <div id="required"><strong>*</strong> campo obrigatório</div>
                            <select id="slct_del" class="form-control" name="select_painel_del"></select>
                        </div>
                        <div id="result3" role="alert"></div>
                    </div>
                    <div class="modal-footer">
                        <button id="delBtn" style="display: none;" type="submit" class="btn btn-success">Excluir</button>
                        <button id="del" type="button" class="btn btn-danger" data-dismiss="modal">Sair</button>
                    </div>
                </form>

            </div>
        </div>
    </body>

    </html>