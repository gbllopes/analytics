window.addEventListener('message', function(e) {
    var opts = e.data.opts,
        data = e.data.data;

    return main(opts, data);
});
  

var defaults = {
    margin: {top: 24, right: 0, bottom: 0, left: 0},
    rootname: "TOP",
    format: ",d",
    title: "",
    width: 930,
    height: 650
};

function main(o, data) {
  var root,
      opts = $.extend(true, {}, defaults, o),
      formatNumber = d3.format(opts.format),
      rname = opts.rootname,
      margin = opts.margin,
      theight = 36 + 16;

  $('#chart').width(opts.width).height(opts.height);
  var width = opts.width - margin.left - margin.right,
      height = opts.height - margin.top - margin.bottom - theight,
      transitioning;
  
  var color = d3.scale.category20c();
  
  var x = d3.scale.linear()
      .domain([0, width])
      .range([0, width]);
  
  var y = d3.scale.linear()
      .domain([0, height])
      .range([0, height]);
  
  var treemap = d3.layout.treemap()
      .children(function(d, depth) { return depth ? null : d._children; })
      .sort(function(a, b) { return a.value - b.value; })
      .ratio(height / width * 0.5 * (1 + Math.sqrt(5)))
      .round(false);
  
  var svg = d3.select("#chart").append("svg")
      .attr("width", width + margin.left + margin.right)
      .attr("height", height + margin.bottom + margin.top)
      .style("margin-left", -margin.left + "px")
      .style("margin.right", -margin.right + "px")
    .append("g")
      .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
      .style("shape-rendering", "crispEdges");   
      
  
  var grandparent = svg.append("g")
      .attr("class", "grandparent");
  
  grandparent.append("rect")
      .attr("y", -margin.top)
      .attr("width", width)
      .attr("height", "26");
  
  grandparent.append("text")
      .attr("x", 6)
      .attr("y", 6 - margin.top)
      .attr("dy", ".75em");
      
      
      

    // atualiza os acessos a cada vez que o painel de modelos for carregado.
      $('#add, #edit, #del').click(function(){
        $.ajax({ url: "Controller/att_acessos.php",
                context: document.body,
        });
        $('body').load('index.php'); // atualiza o painel de modelos para gerar novos dados.
    });    

  if (opts.title) {
       // Exibe o título e botões do painel de modelos.
        $("#chart").prepend("<button id='button' class='btn btn-primary bg-blue button' data-toggle='modal' data-target='#modalRegistro' title='Adicionar painel'><i class='fas fa-plus'></i></button>"+
                            "<button id='button' style='margin-left:20px' class='btn btn-success button' data-toggle='modal' data-target='#modalEdicao' title='Editar painel'><i class='far fa-edit'></i></button>"+
                            "<button id='button' style='margin-left:20px' class='btn btn-danger button'  data-toggle='modal' data-target='#modalDelete' title='Excluir painel'><i class='far fa-trash-alt'></i></button>"+
                            "<p class='title'>" + opts.title + "<br><div class='sub-title'>(Área = Quantidade de acessos)</div></p>");
  }
  if (data instanceof Array) {
    root = { key: rname, values: data };
  } else {
    root = data;
  }
    
  initialize(root);
  accumulate(root);
  layout(root);
  console.log(root);
  display(root);
  salvarRegistro();
  listarPainel();
  $('#info, #table_, #campoCategoria').hide();
  $('#altBtn, #delBtn').addClass('invisible')
  isLoggedIn()



  if (window.parent !== window) {
    var myheight = document.documentElement.scrollHeight || document.body.scrollHeight;
    window.parent.postMessage({height: myheight}, '*');
  }

  function initialize(root) {
    root.x = root.y = 0;
    root.dx = width;
    root.dy = height;
    root.depth = 0;
  }

  // Aggregate the values for internal nodes. This is normally done by the
  // treemap layout, but not here because of our custom implementation.
  // We also take a snapshot of the original children (_children) to avoid
  // the children being overwritten when when layout is computed.

  function accumulate(d) {
       return (d._children = d.values)   
            ? d.value = d.values.reduce(function(p, v) { return p + accumulate(v); }, 0)
            : d.value = d.value;
    } 

  // Compute the treemap layout recursively such that each group of siblings
  // uses the same size (1×1) rather than the dimensions of the parent cell.
  // This optimizes the layout for the current zoom state. Note that a wrapper
  // object is created for the parent node for each group of siblings so that
  // the parent’s dimensions are not discarded as we recurse. Since each group
  // of sibling was laid out in 1×1, we must rescale to fit using absolute
  // coordinates. This lets us use a viewport to zoom.
  function layout(d) {
    if (d._children) {
      treemap.nodes({_children: d._children});
      d._children.forEach(function(c) {
        c.x = d.x + c.x * d.dx;
        c.y = d.y + c.y * d.dy;
        c.dx *= d.dx;
        c.dy *= d.dy;
        c.parent = d;
        layout(c);
      });
    }
  }

  function display(d) {
    grandparent
        .datum(d.parent)
        .on("click", transition)
      .select("text")
        .text(name(d))
        .text(function(){ return getTotal(d)});

    var g1 = svg.insert("g", ".grandparent")
        .datum(d)
        .attr("class", "depth");

    var g = g1.selectAll("g")
        .data(d._children)
      .enter().append("g");

    g.filter(function(d) { return d._children; })
        .classed("children", true)
        .on("click", transition);
        

    var children = g.selectAll(".child")
        .data(function(d) { return d._children  || [d]; })
      .enter().append("g");

    children.append("rect")
        .attr("class", "child")
        .call(rect);

    children.append("text")
        .attr("class", "ctext")
        .attr("data-id", function(d){ return d.id})
        .attr("data-link", function(d){ return d.link})
        .text(function(d) { return d.key; })
        .call(text2);

    g.append("rect")
        .attr("class", "parent")
        .call(rect);
    var t = g.append("text")
        .attr("class", "ptext")
        .attr("dy", ".75em")
        .attr("id", function(){
            if(!$(this).prev().prev().children('rect').data("id")){
                $(".ptext").attr("id","textoCategoria")
            } else {
                $(".ptext").attr("id","textoModelo")
            }
        });
    t.append("tspan")
        .text(function(d) { 
            if (d.link != null){               
                return d.descricao;
            }else{
                return d.key;
            }
         });
    t.append("tspan")
        .attr("dy", "1.0em")
        .text(function(d) {
            if($(this).parent().attr("id") == "textoModelo"){
                return formatNumber(d.value - 1000); 
            } else {
               $(this).attr("id","categoria");
               $(this).attr("data-categoria", d.key)
               $(this).attr("data-valor", d.value)
               $("tspan#categoria").each(function(){   // Realiza o loop no elemento tspan.
                var categoria = $(this).data("categoria")
                var valor     = $(this).data("valor")
                var elemento = $(this)
                elemento.closest('g').children('rect').attr('data-categoria', categoria)
                $.ajax({
                    type: "GET",
                    url :"Paineis/qtde_por_categoria.json",
                    dataType: "json",
                    success: function(data) {
                        for(i = 0; i <= data.length ; i++){
                            if(data[i].categoria == categoria){
                                numSubtraido = valor - (data[i].QTDE * 1000); // Extrai o numero subtraido.
                                elemento.text(formatNumber(numSubtraido))
                                elemento.attr("data-valor", formatNumber(numSubtraido))                   
                            }   
                        }
                    }        
                })
           })  
            }
        });         
    t.call(text);
     
    g.selectAll("rect")
        .style("fill", function(d) { return color(d.key); })
        .on("mouseover", function(d){
            if (d.descricao != null) {
                $('#info').html(d.key + " (" + formatNumber(d.value - 1000) + ")<br/>"+ d.descricao);
            } else {
                $('#info').html(d.key + " (" + formatNumber(d.value - 1000) + ")");
            }   
            $('#info').show();
        })
        .on("mouseleave", function(){
            $("#info").hide();
        });
    
    function transition(d) {
      if (transitioning || !d) return;
      transitioning = true;

      var g2 = display(d),
          t1 = g1.transition().duration(750),
          t2 = g2.transition().duration(750);

      // Update the domain only after entering new elements.
      x.domain([d.x, d.x + d.dx]);
      y.domain([d.y, d.y + d.dy]);

      // Enable anti-aliasing during the transition.
      svg.style("shape-rendering", null);

      // Draw child nodes on top of parent nodes.
      svg.selectAll(".depth").sort(function(a, b) { return a.depth - b.depth; });

      // Fade-in entering text.
      g2.selectAll("text").style("fill-opacity", 0);

      // Transition to the new view.
      t1.selectAll(".ptext").call(text).style("fill-opacity", 0);
      t1.selectAll(".ctext").call(text2).style("fill-opacity", 0);
      t2.selectAll(".ptext").call(text).style("fill-opacity", 1);
      t2.selectAll(".ctext").call(text2).style("fill-opacity", 1);
      t1.selectAll("rect").call(rect);
      t2.selectAll("rect").call(rect);

      // Remove the old node when the transition is finished.
      t1.remove().each("end", function() {
        svg.style("shape-rendering", "crispEdges");
        transitioning = false;
      });
    }
    getValoresporArea();
    redirect();
    return g;
  }
  function mensagem(data,msg1,msg2){ 
    classe = null;
    hide = data;
      if(data == "success" || data == true){
        classe  = "alert alert-success";  
        data    = msg1 + " com Sucesso!"  
      }else{
        classe  = "alert alert-danger";
        data    = "Erro ao "+ msg2 +" ! Por favor, tente novamente.";
      }
                
        $('#result, #result2, #result3').removeClass();
        $('#result, #result2, #result3').addClass(classe);
        $('#result, #result2, #result3').html(data);
        $('#result, #result2, #result3').css("display", "block");
        $().ready(function() {
            setTimeout(function () {
                if(hide == "success"){
                    $('#table_').hide();
                }                
                    $('#result, #result2, #result3').hide(); 
            }, 2000);
        });
        $('#ajax_form_add, #ajax_form_edit').trigger("reset");
  } 

  // Script que escode/mostra o campo de nova categoria.
  function salvarRegistro(){
    $(document).on("click", "#novaCategoria", function(){
        $('#campoCategoria').show();
        $(document).on("click", "#disponivel", function(){
            $('#campoCategoria').hide();
        })
    })

    // Ajax de submit do formulário de Registros
    $('#ajax_form_add').submit(function(e) {
    e.preventDefault();  
        var dados = $(this).serialize();
    $.ajax({
        url: 'Controller/salvar_registro.php',
        type: 'POST',
        data: dados,
        success: function(data) {            
            mensagem(data, "Adicionado", "adicionar");
            $('#campoCategoria').hide();
        },
        error: function(xhr, status, error) {
            alert(xhr.responseText);
        }
    });
    return false;
    });
  }
  
  // Ajax de edição de Registros
  function editarRegistro(){
        $('#table_').show();
        $('#ajax_form_edit').submit(function(e) {
        e.preventDefault();  
            var dados = $(this).serialize();
        $.ajax({
            url: 'Controller/salvar_registro.php',
            type: 'POST',
            data: dados,
            success: function(data) {            
                mensagem(data, "Editado", "editar");
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
        return false;
        });
  }
    
  // Ajax de exclusão de Registros.
  function deletarRegistro(){
        $('#ajax_form_del').submit(function(e) {
        e.preventDefault(); 
            var dados = $(this).serialize(); 
        $.ajax({
            url:  'Controller/excluir.php',
            type: 'POST',
            data: dados,
            success: function(data) {                             
                mensagem(data, "Excluído", "excluir");   
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
        return false;
        });
  }
  
  // Obtem os valores com base na categoria
  function getValoresporArea(){
       $("tspan#categoria").text()   
  }
  // Ajax de listagem de opções do select de Edição e Exclusão do painel de Modelos.
  function listarPainel(){
        $.ajax({
            url:'Controller/listar.php',
            type:'GET',
            dataType:"html",
            success: function(response){
                $('#slct, #slct_del').html(response);               
                $('#slct option, #slct_del option').click(function(){
                        editarRegistro();
                        deletarRegistro();
                        $("#altBtn, #delBtn").removeClass('invisible')
                        $('#altBtn, #delBtn').addClass('visible')                    
                })
            }
        })
  }

  function text(text) {
    text.selectAll("tspan")
        .attr("x", function(d) { return x(d.x) + 6; })
    text.attr("x", function(d) { return x(d.x) + 6; })
        .attr("y", function(d) { return y(d.y) + 6; })
        .style("opacity", function(d) { return this.getComputedTextLength() < x(d.x + d.dx) - x(d.x) ? 1 : 0; });
  }

  function text2(text) {
    text.attr("x", function(d) { return x(d.x + d.dx) - this.getComputedTextLength() - 6; })
        .attr("y", function(d) { return y(d.y + d.dy) - 6; })
        .style("opacity", function(d) { return this.getComputedTextLength() < x(d.x + d.dx) - x(d.x) ? 1 : 0; });
  }
  
  // Script de redirecionamento após o click em algum modelo.
  function redirect(){ 
    $('.child, .ctext').click(function(d){
         $('.ptext').attr("data-id", $('.child').data("id"));
         if($('.child, .ctext').data('id') != null && $('.parent').data("link") == null){
            var id = $(this).data('id')
            var link = $(this).data('link')
            $.post("Controller/att_acessos.php",{
               id   : id
            }, function(data){
                $('body').load('index.php')
            });
            window.open(link, "_blank");
         }
    });
  }  

  function rect(rect) {
    rect.attr("x", function(d) { return x(d.x); })
        .attr("y", function(d) { return y(d.y); })
        .attr("width", function(d) { return x(d.x + d.dx) - x(d.x); })
        .attr("height", function(d) { return y(d.y + d.dy) - y(d.y); })
        .attr("data-link", function(d){ return d.link})
        .attr("data-id", function(d){ return d.id });
  }

  // função que exibe o texto do retangulo laranja com os valores padrões na casa do milhar 
  function name(d) {

    $("g.grandparent").children("text").attr("data-valor", function (){ return d.value })  
    return d.parent
        ? name(d.parent) + " / " + d.key + " (" + formatNumber(d.value) + ")"
        : d.key + "("+ formatNumber(d.value) +")";
  }
}
 // Ajax de atualização dos dados para o usuário.
$(document).ready(function(){
    $.ajax({ url: "Controller/att_acessos.php",
            context: document.body,
    });
});

// Função que invoca o json de paineis e retorna a quantidade total de modelos cadastrados no painel de Modelos.
function getTotal(d){
    var filho = $("g.grandparent").children("text")
    var valorAtual = filho.data("valor")
    $.ajax({
        type:"GET",
        url:"Paineis/paineis.json",
        dataType: "json",
        success: function(data){
            alterarValorPadrao(data.length, filho, valorAtual,d) // Chama a função que modifica os valores. Tem por objetivo, printar os valores    
        }                                                        // formatados para a quantidade real de clicks que existem em cada modelo no banco de dados.
    })
}
function alterarValorPadrao(total, filho, valorAtual,d){
    total *= 1000;
    novoValor = valorAtual - total;
    if(d.parent){
        $.ajax({
            type:"GET",
            url:"Paineis/qtde_por_categoria.json",    
            dataType:"json",
            success: function(data){
                for(i = 0;i < data.length; i++){
                    if(d.key == data[i].categoria){ 
                        total = data[i].QTDE * 1000; 
                        novoValorCategoria = d.value - total; // De acordo com a categoria clicada, retorna a quantidade total de clicks q a categoria possui e é subtraido o milhar.
                        filho.html("Total de acessos(" + novoValor + ") / " + d.key + "(" + novoValorCategoria + ")") // printa o valor correto.
                    }
                }
            }
        })
    } else {
        filho.html(d.key + "("+ novoValor + ")") 
    }
}


// Verifica se o usuário está logado ou não no sistema.
function isLoggedIn(){
    $(document).ready(function(){ 
        var dados = $(this).serialize(); 
    $.ajax({
        url:  'http://localhost/analytics/login/session.php',
        type: 'POST',
        data: dados,
        success: function(data) {                                            
            if (data != ""){
            $('button').removeClass('button').show()             
            }
        },
        error: function(xhr, status, error) {
            alert(xhr.responseText);
        }
    });
    return false;
    })
}
if (window.location.hash === "") {
    d3.json("Paineis/paineis.json", function(err, res) {
        if (!err) {
            console.log(res);
            var data = d3.nest().key(function(d) { return d.categoria; }).key(function(d) { return d.key; }).entries(res);
            main({title: "Modelos"}, {key: "Total de acessos", values: data});
        }
    });
} 