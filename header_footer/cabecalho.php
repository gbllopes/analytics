<?php
	// verifica se o usuário está logado no sistema.
	session_start();
	$isLoggedIn = (isset($_SESSION["login"]) != NULL) ? true : false; 
?>	
<!DOCTYPE html>
<html lang="en">
   <head>
      <title>DIMEP</title>
	  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	  <!--<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script> 
	  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script> -->
	  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css">
	  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	  <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
	  <script type="text/javascript" src="js/cabecalho.js"></script>
	  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
      <link rel="stylesheet" type="text/css" href="javascript/calendario/_style/jquery.click-calendario-1.0.css">
      <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous"> 
	  <link rel="stylesheet" href="CSS/login.css">
	  <link rel="stylesheet" href="CSS/style.css">
	  
	  <div class="navbar navbar-expand-lg bg-yellow fixed-top">
		<div class="collapse navbar-collapse" id="navbarSupportedContent" style="padding:15px 0px 0px 0px">
			<ul class="navbar-nav">
				<div class="header item"><img src="./img/bblogo.jpg" ></div>
			</ul>
			<ul class="dimep-df navbar-nav">
				<li class="nav-item font-weight-bold text-blue" style="font-size:50px">DIMEP DF - Analytics</li>
			</ul>
			<ul class="loghover mt-1 mb-3 mr-2 list-inline">
					<li class="list-inline-item itens" id="menu"><a href="" id="modalMetadados" class="nav-link" data-toggle="modal" data-target="#metadados_modal"><strong>METADADOS</strong></a></li>
				<?php if($isLoggedIn){ ?>			
					<li class="list-inline-item navbar-item active" style="position: relative;top: 1px;" title="Sair">
					<a class="navbar-item" href="http://localhost/analytics/login/destroy.php"><i class="fas fa-2x fa-sign-out-alt"></i></a>
					</li>
				<?php } else { ?>	
					<li class="list-inline-item navbar-item active dropdown" title="Login" >
						<a class="dropdown-toggle" href="" type="hidden" data-toggle='dropdown' ><i class="fas fa-2x fa-user-check"></i></a>
						<div class="dropdown-menu" style="padding: 15px; padding-bottom: 10px;">
							<form id="ajax_auth" action="" method="" accept-charset="UTF-8">
								<div class="ui form">
									<div class="field">
										<label>Matrícula</label>
										<div class="ui left icon input">
											<input name="login" class="mascara" type="text" autocomplete="off" required/>
											<i class="user icon"></i>
										</div>
									</div>
									<div class="field">
										<label>Senha</label>
										<div class="ui left icon input">
											<input name="senha" type="password" autocomplete="off"/> 
											<i class="lock icon"></i>
										</div>
									</div>	
									<input type="submit" class="ui blue submit button" style="width:100%" value="Entrar"/>
									<div class="field">
										<div id="resetarSenha" class="form" style="margin:0 auto"><a data-toggle="modal" data-target="#modalUsuario" href="#">Esqueci minha senha</a></div>
										<div id="novoUsuario" class="form">Novo usuário? <a data-toggle="modal" data-target="#modalUsuario" href="#">Cadastre-se</a></div>
										<div><div id="resultadoAuth" class="mt-2 p-1" role="alert"></div></div>
									</div>
								</div>
							</form>
									<!--<form id="ajax_auth" action="" class="form-horizontal"  method="" accept-charset="UTF-8">
										<input class="form-control login mascara" type="text" name="login" placeholder="Matrícula" required autocomplete="off"/>
										<input class="form-control login" type="password" name="senha" placeholder="Senha" required autocomplete="off"/>
										<input class="btn btn-primary w-100 mb-1" type="submit" name="submit" value="Entrar" />
										<div id="resultadoAuth" class="mt-2 p-1" role="alert"></div>
										<div id="resetarSenha" class="form" style="margin:0 auto"><a data-toggle="modal" data-target="#modalUsuario" href="#">Esqueci minha senha</a></div>
										<div id="novoUsuario" class="form">Novo usuário? <a data-toggle="modal" data-target="#modalUsuario" href="#">Cadastre-se</a></div>
									</form> -->

						</div>
					</li>		
				<?php } ?>
			</ul>		
		</div> 
	</div>			
	</head>
   <body>

   <!-- Modal de Cadastro ou alteração de senha do usuário. --> 
   <div class="modal" tabindex="-1" role="dialog" id="modalUsuario"> 
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-center" style="margin: 0 auto"><strong id="tituloModal"></strong></h5>
				</div>
				<form class="form_usuario" action="" method ="" >
					<div id="modalUsuarioBody" class="modal-body"></div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Enviar</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Sair</button>
					</div>
				</form>	
			</div>
		</div>
	</div>
				
      <!-- ===================================== END HEADER ===================================== -->
</body>
</html>