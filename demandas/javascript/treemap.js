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
    .attr("height", "26")

grandparent.append("text")
    .attr("x", 6)
    .attr("y", 6 - margin.top)
    .attr("dy", ".75em");

if (opts.title) {

      // Titulo do painel e botão de adicionar demanda

      $("#chart").prepend("<button id='button' class='btn btn-primary bg-blue button' data-toggle='modal' data-target='#modalRegistro' title='Adicionar demanda'><i class='fas fa-plus'></i></button>"+
                          "<p class='title'>" + opts.title + "<br><div class='sub-title'>(Área = Quantidade de demandas)</div></p>");
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
$("#info").hide();
isLoggedIn();

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
      .text(name(d));

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
    .enter().append("g")

  children.append("rect")
      .attr("class", "child")
      .call(rect)          

  children.append("text")
      .attr("class", "ctext")
      .attr("data-id", function(d){ return d.id})
      .attr("data-demanda",function(d) { return d.demanda})
      .text(function(d) { return d.demanda; })
      .call(text2);

  g.append("rect")
      .attr("class", "parent")
      .call(rect);

  var t = g.append("text")
      .attr("class", "ptext")      
      .attr("dy", ".75em");

  t.append("tspan")
      .text(function(d) {  return d.key;});
  t.append("tspan")
      .attr("dy", "1.0em")
      .text(function(d) {  
          return formatNumber(d.value)
      })
  t.call(text);

  g.selectAll("rect")
      .style("fill", function(d) { return color(d.key); })
      .on("mouseover", function (d){
          if(d.demanda == null){
              $('#info').html(d.key + " (" + formatNumber(d.value) + ")");
          }else{
            $('.child, .ctext').attr("data-toggle", "modal")
            $('.child, .ctext').attr("data-target", "#myModal")
            $('#info').html(d.demanda + "<br>" +d.assunto);
          }
                
          $('#info').show();
      })
      .on("mouseleave", function(){
          $("#info").hide();
      })

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
  return g;  
}

  // Script que abre/edita a demanda ao clicar no rect.
  $(document).on("click", "rect.child, rect.ctext", function(){
    if($(this).data('id') != null && $(this).data("demanda") != null){
        demanda = $(this).data("demanda")
        $.post('../action/subformularios_demandas.php',{
          acao : "abrir",
          demanda : demanda
      },function(data){
          $('#tabelaDemandas').html(data);
          $('#voltarAbrir').hide();      
          $("#alternar").append("<button id='editarDemanda' class='btn btn-warning button' title='Editar Demanda'><i class='fas fa-pencil-alt'></i></button>");
          $.ajax({
            url:  'http://localhost/analytics/login/session.php',
            type: 'POST',
            success: function(data) {                                            
                if (data != ""){
                  $('#editarDemanda').show();
                  $('#editarDemanda').click(function(){
                    $.post("../action/subformularios_demandas.php", {
                      acao : "editar",
                      demanda : demanda
                    }, function(data){
                       $('#tabelaDemandas').html(data);
                       $("#voltarEditar").hide();
                       $('.col').css("padding", "10px")
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
                       $(document).on("submit", "form#form_edit_demanda", function(e) {
                        $('#result').hide();
                        e.preventDefault();  
                            var dados = $(this).serialize();
                        $.ajax({
                            url: '../action/editar.php',
                            type: 'POST',
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
                                alert(error);
                            }
                        });
                        return false;
                        });
                    })    
                });
                }
            }
          });                   
      });
      }
  })   

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

function rect(rect) {
  rect.attr("x", function(d) { return x(d.x); })
      .attr("y", function(d) { return y(d.y); })
      .attr("width", function(d) { return x(d.x + d.dx) - x(d.x); })
      .attr("height", function(d) { return y(d.y + d.dy) - y(d.y); })
      .attr("data-demanda", function(d){ return d.demanda})
      .attr("data-id", function(d){ return d.id });
}

function name(d) {
  return d.parent
      ? name(d.parent) + " / " + d.key + " (" + formatNumber(d.value) + ")"
      : d.key + " (" + formatNumber(d.value) + ")";
}
}

// Ao carregar o grafico, atualiza-se os valores.
$(document).ready(function(){
atualizarValoresGrafico()
});

// ajax que requisita a informação de login do usuário no sistema.
function isLoggedIn() {
$(document).ready(function(){ 
  var dados = $(this).serialize(); 
$.ajax({
  url:  'http://localhost/analytics/login/session.php',
  type: 'POST',
  data: dados,
  success: function(data) {                                            
      if (data != ""){
        $('#button').removeClass('button').show();
      }
  }
});
return false;
})
}

// Submit do formulário de adição de demanda 

$(function(){
$('#ajax_form_add').submit(function(e) {
  e.preventDefault();  
      var dados = $(this).serialize();
  $.ajax({
      url: 'Controller/cadastrar.php',
      type: 'POST',
      data: dados,
      success: function(data) {            
          mensagem(data, "Adicionado", "adicionar");
          atualizarValoresGrafico();
          atualizarValoresGrafico();
          $(".sub-title, svg, p.title").remove();
          criarGrafico()
      },
      error: function(xhr, status, error) {
          alert(xhr.responseText);
      }
  });
  return false;
  });
  
  function mensagem(data,msg1,msg2){ 
    classe = null;
    hide = data;
      if(data == true){
        classe  = "alert alert-success";  
        data    = msg1 + " com Sucesso!"  
      }else{
        classe  = "alert alert-danger";
        data    = "Erro ao "+ msg2 +" ! Por favor, tente novamente.";
      }
                
        $('#resultDem').removeClass();
        $('#resultDem').addClass(classe);
        $('#resultDem').html(data);
        $('#resultDem').css("display", "block");
        $().ready(function() {
            setTimeout(function () {          
                    $('#resultDem').hide(); 
            }, 2000);
        });
        $('#ajax_form_add').trigger("reset"); 
  } 
  // botões de "voltar". Tem a função de atualizar o gráfico.
  $('#add, #edit, #fecharRelatorio').click(function(){
    $('input[type="radio"]').prop('checked', false); 
    atualizarValoresGrafico();
    atualizarValoresGrafico();
    $(".sub-title, svg, p.title").remove();
    criarGrafico();
  });    
})

function atualizarValoresGrafico(){
$.ajax({ url: "Controller/att_demandas.php",
        context: document.body,
});
} 
// Máscara de matrícula
$(document).ready(function(){
  $('#matr').mask('F0000000', {
      placeholder:'F_______'
  })    
})

function criarGrafico(){
if (window.location.hash === "") {
      d3.json("Paineis/gerencias.json", function(err, res) {
          if (!err) {
              console.log(res);
              var data = d3.nest().key(function(d) { return d.gerencia; }).key(function(d) { return d.key; }).entries(res);
              main({title: "Demandas em andamento"}, {key: "Total de demandas", values: data});
          }
      });
} 
}
criarGrafico();
