<html>
	<head>
		  <link rel="shortcut icon" href="img/analytics.png" />
		  <meta charset="utf-8">
		  
	</head>
   <body style="background:#ececec;heigth:80%">
      <?php
         include_once ("../analytics/header_footer/cabecalho.php");
		 include_once ('../analytics/Database/database.php');	
				 $isLoggedIn = (isset($_SESSION["login"]) != NULL) ? true : false;
		 define( 'BASEPATH', __DIR__ . "index.php" );
         ?>			
       <style>
			 body{
				 padding-top:21.7px;
				 font-family: Arial, Helvetica, sans-serif !important;
			 }		
		</style>	
		<!-- Cria 2 divs que armazenam os gráficos -->
			<div class="d-flex" style="position:relative;height: 800px;">
				<div class="col-sm-6 p-0">
						<button class="btn btn-warning btnConsultar" id="modalConsultar" data-paginacao="1" data-toggle="modal" data-target=".bd-example-modal-lg" href="#" title="Consultar demandas"><i class="fas fa-search"></i></button>
                        <embed src="../analytics/demandas/index.php" style="width:100%; height:800px;">      
				</div>
				<div class="col-sm-6 p-0">
                        <embed src="../analytics/modelos/index.php" style="width:100%;height:800px">                  
				</div>
			</div>
      <!-- ===================================== MODAL CONSULTAR DEMANDAS ===================================== -->
				<div class="modal fade bd-example-modal-lg" id="demandas_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg" style="max-width:1650px;">
						<div class="modal-content">
							<div class="modal-header">
							<h2 class="modal-title text-center " style="margin: 0 auto"><strong>Demandas</strong></h2>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Fechar">
								<i class="fa fa-window-close" style="color:red" aria-hidden="true"></i>
        			</button>
							</div>
							<div id="menu" class="mt-2">
							<form id="ajaxBusca" action="" type="" class="buscaInput">
								<div class="ui action input">
									<input class="w-100" name="busca" placeholder="Procure por divisões, demandantes, etc..." type="text" style="font-size: 15px">
									<button id="enviar" type="submit" class="ui icon button">
										<i class="search icon"></i>
									</button>
								</div>
							</form>
							<button id="recentes" class="btn btn-primary" data-paginacao="1" >Recentes</button>
							</div>
							<div id="tabelaDemandas" class="modal-body"> 
							</div>						
						</div>
					</div>
				</div>
	<!-- ===================================== MODAL CONSULTAR METADADOS ===================================== -->
				<div class="modal fade bd-example-modal-lg" id="metadados_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg" style="max-width:1650px;">
						<div class="modal-content">
							<div class="modal-header">
							<h2 class="modal-title text-center" style="margin:0 auto"><strong>Metadados</strong></h2>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Fechar">
								<i class="fa fa-window-close" style="color:red" aria-hidden="true"></i>
        			</button>
							</div>
							<div id="menu" class="mt-2">
							<form id="ajaxBusca" action="" type="" class="buscaInput">
								<div class="ui action input">
									<input class="w-100" name="busca" placeholder="Procure por bibliotecas, tabelas, etc..." type="text" style="font-size: 15px">
									<button id="enviar" type="submit" class="ui icon button">
										<i class="search icon"></i>
									</button>
								</div>
							</form>
							<button id="recentes" class="btn btn-primary" data-paginacao="1" >Recentes</button>
							<?php
							if($isLoggedIn){ ?>
								<button id="add_metadados" class="btn btn-primary float-right mr-4 pl-4 pr-4" title="Adicionar metadados" data-acao="add_metadados"><i class="fas fa-plus"></i></button>
							<?php } ?>
								</div>
							<div id="tabelaMetadados" class="modal-body"> 
							</div>						
						</div>
					</div>
				</div>
   </body>
   <?php include_once "../analytics/header_footer/rodape.php";?>
</html>

