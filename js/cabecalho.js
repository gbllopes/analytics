    //----------       MÁSCARA DA MATRÍCULA          --------
$(document).ready(function(){
    $(function(){
        mascaraMatricula();
    })

    isLogged = "<?php echo $isLoggedIn ?>"
    if (!isLogged){
        $(".btnConsultar").css("left", "12px")
    }
    
    //  -----       TOLLTIP DO ASSUNTO      ------
    function tooltipMouse(){
        $('.mouse').tooltip({
            
            position: {
                my: "left top",
                at: "left-180 top",
                using: function( position, feedback ) {
                  $( this ).css( position );
                  $( "<div>" )
                    
                    .addClass( feedback.vertical )
                    .addClass( feedback.horizontal )
                    .appendTo( this );
                }
              }
        });
        $(".mouse" ).tooltip({
            show: {
              effect: "slideDown",
              delay: 250
            }
          });
    }
    // -------      EVENTO DE VOLTAR AO TOPO DA PÁGINA    -----------
    $(document).on("click", "#topo",function() {
        $("#demandas_modal, #metadados_modal").animate({ scrollTop: 0 }, "slow");
        return false;
    });
    
      // -----        MENU TOPBAR        ------
   $('.cl').mouseover(function(){
        $(this).css("color", "rgb(0,56,168)");
        $()
    })
    $('.cl').mouseout(function(){
        $(this).css("color", "#007bff");
    }) 

    $(".dropdown").mouseover(function(){
        $(".dropdown-menu").addClass('show');
    })
    
    $('.dropdown-menu').mouseout(function() {
        $('.dropdown-menu').removeClass('show');
    }) 
//   -------------  MODAL DE CONSULTA DE DEMANDAS ---------- //  

    
    $(document).on('click', "#modalConsultar, #recentes",function(){
            $("#ajaxBusca, #recentes").show(); 
            $.post('../analytics/action/demandas.php',{
                data : $(this).data('paginacao'),
            }, function(data){ 
                $('#tabelaDemandas').html(data);
                $('#ajaxBusca').trigger("reset");
                tooltipMouse();
            })           
        })

//  -------------- MODAL DE METADADOS ---------------- //
        $("#modalMetadados, #recentes").on('click', function(){
            $("#ajaxBusca, #recentes").show(); 
            listarMetadados();
        })
    //  -------       PAGINAÇÃO DA CONSULTA       ---------   

        $(document.body).on('click', '#paginacao', function(){
            pagina = $(this).data('paginacao') 
            busca  = $(this).data('busca')
            coluna_filtro = $("#paginacao").data("filtro");
            dado = $("#abrir, #editar").data("dado")
            filtro = filtrarDadoColuna(dado);
            if($(this).data('acao') == "demanda"){
                $.post('../analytics/action/demandas.php',{
                    pagina : pagina,
                    busca  : busca,
                    orderby : $("#status").data("acao"),
                    filtro : filtro,
                    coluna_filtro : coluna_filtro,
                    dado : dado, 
                }, function(data){
                    $('.modal').animate({scrollTop:0}, 'fast');
                    $('#tabelaDemandas').html(data);
                    tooltipMouse();
                })
            } else {
                $.post('../analytics/action/metadados.php',{
                    pagina : pagina,
                    busca  : busca,
                }, function(data){
                    $('.modal').animate({scrollTop:0}, 'fast');
                    $('#tabelaMetadados').html(data);
                    tooltipMouse();
                })
            } 
        })

    //  -------         SUBMIT DO CAMPO DE BUSCA        -----------
        $('#ajaxBusca, #metadados').submit(function(e) {
            e.preventDefault();  
                var dados = $(this).serialize();    
            if($('#demandas_modal').hasClass('show')){
                $.ajax({
                    url: '../analytics/action/demandas.php',
                    type: 'POST',
                    data: dados,
                    success: function(data) {
                        $('#tabelaDemandas').html(data);
                        tooltipMouse();                
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
                return false;
            } 
            if ($('#metadados_modal').hasClass('show')){
                $.ajax({
                    url: '../analytics/action/metadados.php',
                    type: 'POST',
                    data: dados,
                    success: function(data) {
                        $('#tabelaMetadados').html(data);              
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
                return false;
            }
        });
            $("#resultado").hide();
        // -----------      SUBMIT DE CADASTRO DO USUÁRIO   ---------------
        $(document).on("submit", "#addUsuario", function(e) {
            e.preventDefault();  
                var dados = $(this).serialize();
            $.ajax({
                url: '../analytics/login/Usuario.php',
                type: 'POST',
                data: dados,
                success: function(data) {                       
                    classe = null;
                    if(data == 1) {
                        msg = "Adicionado com sucesso.";
                        classe = "alert alert-success";
                    }else{
                        msg = "Usuário já cadastrado ou matrícula/senha incorretas.";
                        classe = "alert alert-danger";
                    }
                    $("#modalUsuarioBody").append("<div id='resultado' role='alert' class=''></div>")
                    $("#resultado").removeClass();
                    $("#resultado").addClass(classe);
                    $("#resultado").show();
                    $("#resultado").html(msg);
                    setTimeout(function () {  
                        if(classe == "alert alert-success"){
                            $("#modalUsuario").modal("hide");
                        }  
                        $("#resultado").remove();
                    }, 2500);
                    $('#addUsuario').trigger("reset");
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
            return false;
            });        
        //  -----------     AUTENTICAÇÃO DE LOGIN   ---------------
            $('#ajax_auth').on("submit", function(e) {
                $('#resultadoAuth').hide();
                e.preventDefault();  
                    var dados = $(this).serialize();
                $.ajax({
                    url: '../analytics/login/auth.php',
                    type: 'POST',
                    data: dados,
                    success: function(data) { 
                    if(data == true){
                        location.reload();
                    } else { 
                        $("#resultadoAuth").addClass("alert alert-danger")
                        $("#resultadoAuth").show();
                        $("#resultadoAuth").html("Login ou Senha inválidos.");
                        setTimeout(function () {  
                            $("#resultadoAuth").hide();  
                        }, 2000);
                        $('#ajax_auth').trigger("reset");
                    }                     
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
                return false;
            });  
        // --------------- AJAX DE ORDENAÇÃO DA TABELA DEMANDAS ------------------
        $(document).on("mouseover", "#status, #filtroResponsavel, #filtroDivisao, #filtroClassificacao", function(){
            $(this).css("cursor","pointer");
        })
        $(document).on("click", "#status", function(){
            dado = $("#abrir, #editar").data("dado");
            coluna_filtro = $("#abrir, #editar").data("filtro")
            busca = $("#paginacao").data("busca");
            if(!$("#status").data("acao") || $("#status").data("acao") == "num_demanda"){
                orderby = "entregue";
            }
            if($("#status").data("acao") == "entregue"){
                orderby = "num_demanda";
            }
            $.post('./action/demandas.php' , {
                orderby : orderby,
                dado : dado,
                filtro : filtrarDadoColuna(dado),
                coluna_filtro : coluna_filtro,
                busca : busca,
            },function(data){
                $("#tabelaDemandas").html(data); 
                tooltipMouse();         
            })
        }) 
        // ------------     AÇÕES DOS BOTÕES DA TABELA DE DEMANDAS   ---------------
        $(document.body).on("click", "#abrir, #editar", function(){
            $(".close").click(function(){
                aumentarModal();
            })
            acao    = $(this).attr('id');
            pagina  = $('.pagination').find(".active").data("paginacao");
            busca   = $('.pagination').find(".active").data("busca");
            demanda = $(this).data("demanda");
            orderby = $("#status").data("acao");
            dado    = $(this).data("dado")
            filtro  = filtrarDadoColuna(dado);
            coluna_filtro = $("#abrir, #editar").data("filtro");
            $.post("./action/subformularios_demandas.php", {
                pagina  : pagina,
                acao    : acao,
                busca   : busca,
                demanda : demanda, 
            },function (data){
                
                $("#ajaxBusca, #recentes").hide();
                $("#tabelaDemandas").html(data);
                if(acao == "abrir"){                  
                    $("#voltarAbrir").click(function(){
                        $.post("./action/demandas.php", {
                            pagina : pagina,
                            busca  : busca,
                            orderby : orderby,
                            dado : dado,
                            filtro: filtro,
                            coluna_filtro : coluna_filtro
                        },function(data){
                            $("#ajaxBusca, #recentes").show();
                            $('#tabelaDemandas').html(data)
                            tooltipMouse();
                        }
                        )
                    })     
                }
                if(acao == "editar"){
                    diminuirModal();
                    $( "#datepicker" ).datepicker({
                        dateFormat: 'dd/mm/yy',
                        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                        nextText: 'Próximo',
                        prevText: 'Anterior'
                      });
                    $("#voltarEditar").click(function(){
                        $.post("./action/demandas.php", {
                            pagina : pagina,
                            busca  : busca,
                            orderby : orderby,
                            dado: dado,
                            filtro : filtro,
                            coluna_filtro : coluna_filtro
                        },function(data){
                            aumentarModal();
                            $("#ajaxBusca, #recentes").show();
                            $('#tabelaDemandas').html(data)
                            tooltipMouse();
                        }
                        )
                    })
                }
            })
        })

        //----------------------        SUBMIT DE EDICAO DE DEMANDA      ----------------------------
        $(document).on("submit", "#form_edit_demanda",function(e) {
            $('#result').hide();
            e.preventDefault();  
                var dados = $(this).serialize();
            $.ajax({
                url: './action/editar.php',
                type: 'POST',
                acao: 'editar_demanda',
                data: dados,
                success: function(data) {
                    if(data == true){
                        $('#result').addClass("alert alert-success")
                        $('#result').text("Editado com sucesso")
                    }else{
                        $('#result').addClass("alert alert-danger")
                        $('#result').text("Erro ao editar. Por favor, tente novamente.")
                    } 
                    $('#result').show(); 
                    setTimeout(function () {
                        $("#result").hide();
                        $("#result").removeClass();
                    }, 2500);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
            return false;
        });

        // ----------       AÇÕES DO MODAL DE METADADOS  ----------  
            $('#metadados_modal').on('shown.bs.modal', function () {
                $(".close").click(function(){
                    aumentarModal();
                    $('div#menu').show();
                })
                $(document.body).on("click", "#add_metadados, #editar_metadado", function(){ 
                    pagina  = $('.pagination').find(".active").data("paginacao");
                    busca   = $('.pagination').find(".active").data("busca");
                    acao    = $(this).data('acao');
                    if(acao == "editar_metadado"){
                        metadado = $(this).data("metadado");
                    }else {
                        metadado = null; 
                    }            
                        $.post("./action/subformularios_metadados.php", {
                            metadado: metadado,
                            busca:    busca,
                            pagina:   pagina,
                            acao:     acao
                        }, function(data){
                            $('div#menu').hide();
                            diminuirModal();                      
                            $('#tabelaMetadados').html(data);                       
                            $("#voltarEditarMetadado, #voltarAddMetadado").click(function(){
                                $.post("./action/metadados.php", {
                                    pagina : pagina,
                                    busca  : busca
                                },function(data){
                                    aumentarModal();
                                    $("div#menu").show();
                                    $('#tabelaMetadados').html(data)
                                }
                                )
                            })
                            $('#form_edit_metadado , #ajax_form_add_metadados').submit(function(e) {
                                $('#result').hide();
                                e.preventDefault();  
                                    var dados = $(this).serialize();
                                    if($(this).attr('id') == 'ajax_form_add_metadados'){
                                        url = './action/subformularios_metadados.php';
                                        msg1 = "Adicionado";
                                        msg2 = "adicionar";
                                    } else {
                                        url = './action/editar.php';
                                        msg1 = "Editado";
                                        msg2 = "editar";
                                    }
                                    $.ajax({
                                        url: url,
                                        type: 'POST',
                                        data: dados,
                                        success: function(data) {
                                            if(data == true){
                                                $('#result').addClass("alert alert-success")
                                                $('#result').text(msg1 + " com sucesso.")
                                            }else{
                                                $('#result').addClass("alert alert-danger")
                                                $('#result').text("Erro ao "+ msg2 +". Por favor, tente novamente.")
                                            } 
                                            $('#result').show();
                                            $('#ajax_form_add_metadados').trigger(); 
                                            setTimeout(function () {
                                                $("#result").hide();
                                                $("#result").removeClass();
                                            }, 2500);
                                        },
                                        error: function(xhr, status, error) {
                                            alert(xhr.responseText);
                                        }
                                    });
                                    return false;
                            });                    
                        })  
                })
                $(document.body).on("click", "#excluir_metadado", function(){
                    confirmacao = confirm("Tem certeza que deseja excluir este metadado?");
                    if(confirmacao){
                        $.post("./action/excluir_metadado.php", {
                            id_metadado : $(this).data("metadado")  
                        }, function(){
                            listarMetadados();
                        })
                    }
                })
                })
                // --- -------------- Modal Usuário  -- ------------
                
                    $(document).on("click", "#resetarSenha, #novoUsuario", function(){
                        if($(this).attr('id') == "resetarSenha"){
                            $("#tituloModal").text("Redefinir senha");
                            $('form.form_usuario').attr("id","resetSenha");
                            $("#modalUsuarioBody").html('<div class="form-group">'+
                                                        '<label for="matricula"><strong>Matrícula</strong></label>'+
                                                        '<input type="text" class="form-control mascara" name="matricula" required>'+
                                                        '</div>'+
                                                        '<div class="form-group">'+
                                                            '<label for="senha"><strong>Data de nascimento</strong></label>'+
                                                            '<input id="mascara2" type="text" class="form-control" name="nascimento" required>'+
                                                            '<input type="hidden" name="acao" value="validar"'+
                                                        '</div>');                                                 
                        }
                        if($(this).attr('id') == "novoUsuario"){
                            
                            $("#tituloModal").text("Novo usuário");
                            $('form.form_usuario').attr("id", "addUsuario");
                            $("#modalUsuarioBody").html('<div class="form-group">'+
                                                            '<label for="nome"><strong>Nome</strong></label>'+
                                                            '<input type="text" class="form-control" name="nome" required>'+
                                                        '</div>'+
                                                        '<div class="form-group">'+
                                                            '<label for="matricula"><strong>Matrícula</strong></label>'+
                                                            '<input type="text" class="form-control mascara" name="matricula" required>'+
                                                        '</div>'+
                                                        '<div class="form-group">'+
                                                            '<label for="senha"><strong>Senha</strong></label>'+
                                                            '<input type="password" class="form-control" name="senha" required>'+
                                                        '</div>'+
                                                        '<div class="form-group">'+
                                                            '<label for="senha"><strong>Data de nascimento</strong></label>'+
                                                            '<input id="mascara2" type="text" class="form-control" name="nascimento" required>'+
                                                            '<input type="hidden" name="acao" value="adicionar"'+
                                                        '</div>');     
                        }
                        mascaraMatricula();
                        mascaraData();            
                    })
                   // -------------------                    resetar Senha do usuário       ----------------------------- 
                    $(document).on("submit", "#resetSenha, #resetarSenhaUsuario", function(e){
                        e.preventDefault();  
                        var dados = $(this).serialize();
                        $.ajax({
                            url: '../analytics/login/Usuario.php',
                            type: 'POST',
                            data: dados,
                            success: function(data) { 
                                e.preventDefault
                                if (data != ""){ 
                                    if($("form.form_usuario").attr("id") == "resetSenha"){
                                        $("#modalUsuarioBody").html(data);
                                        $("form.form_usuario").attr("id", "resetarSenhaUsuario");
                                    }else{
                                        $("#modalUsuarioBody").append('<div class="alert alert-success resultado" role="alert">Senha alterada com sucesso.</div>');
                                    }        
                                } else {
                                    if($("form.form_usuario").attr("id") == "resetSenha"){
                                        msg = "Usuário não encontrado. Por favor, realize o cadastro.";
                                    } else {
                                        msg = "Erro ao alterar sua senha. Por favor, tente novamente";
                                    }  
                                    $("#modalUsuarioBody").append('<div class="alert alert-danger resultado" role="alert">'+ msg + '</div>');
                                }
                                $('form.form_usuario').trigger("reset");
                                setTimeout(function () {  
                                    if($(".resultado").attr("class") == "alert alert-success resultado"){
                                        $("#modalUsuario").modal("hide");
                                    } 
                                    $(".resultado").remove();
                                }, 2000);
                                
                            }    
                        })
                    })     
                
                // --------------------        Filtro por Responsavel/Divisao ----------------
                $(document).on("click", "#filtro_option_resp, #semFiltro, #filtro_option_div, #filtro_option_class", function(){
                    if($(this).text() !=  "Sem filtro"){
                        filtro = $(this).text()
                    }else{
                        filtro = "";
                    }
                    $.post("./action/demandas.php" ,{
                        pagina : 1,
                        filtro : filtro,
                        coluna_filtro : getColuna($(this).attr("id")),
                        dado : $(this).closest('select').attr('id')
                    },function(data){
                        $("#tabelaDemandas").html(data);
                        tooltipMouse();
                    })
                }) 
                
                //    -----------------          Restruturação modal ---------------
                function diminuirModal(){
                    $(".modal-lg").removeAttr("style");
                }
                
                function aumentarModal(){
                    $(".modal-lg").css("max-width","1650px")   
                }

                function listarMetadados(){
                    $.post('../analytics/action/metadados.php',{
                        data : $(this).data('paginacao'),
                    }, function(data){ 
                        $('#tabelaMetadados').html(data);
                        tooltipMouse();
                        $('#ajaxBusca').trigger("reset");
                    })    
                }
                 
                // -----        MASCARA DA MATRÍCULA     -------
                function mascaraMatricula(){
                    $('.mascara').mask('F0000000', {
                        placeholder:'F_______'
                    }) 
                }
                
                function mascaraData(){
                    $('#mascara2').mask('00/00/0000', {
                        placeholder:'__/__/____'
                    })
                }
                
                // -----------------   Manipulação de colunas com filtros ----------------------
                function filtrarDadoColuna(filtro){
                    filtro = filtro
                    switch(filtro){
                        case "filtroResponsavel":
                            return $("#filtroResponsavel").data("dado");;
                            break;
                        case "filtroDivisao" :
                            return $("#filtroDivisao").data("dado");
                            break;
                        case "filtroClassificacao" :
                            return $("#filtroClassificacao").data("dado");    
                        default:
                            return "";    
                    }
                }

                function getColuna(coluna_filtro){
                    coluna = coluna_filtro;
                    switch(coluna){
                        case "filtro_option_resp" :
                            return "responsavel";
                            break;
                        case "filtro_option_div":
                            return "divisao";
                            break;
                        case "filtro_option_class":
                            return "classificacao";
                            break;        
                        default:
                            return "";    
                    }
                     
                }

    })