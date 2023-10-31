//var dados = $("#kt_modal_create_cockpit_form").serialize(); pega tudo do form

$.ajax({
    url: "processa.php", // URL do arquivo PHP que irá processar a requisição
    type: "POST", // Método da requisição
    data: { param1: "valor1", param2: "valor2" }, // Dados a serem enviados
    beforeSend: function() {
      // Código para executar antes de enviar a requisição
      console.log("Preparando para enviar a requisição...");
    },
    success: function(response) {
      // Código para lidar com a resposta
      console.log("Resposta recebida:", response);
    },
    error: function(jqXHR, textStatus, errorThrown) {
      // Código para lidar com erros na requisição
      console.log("Erro na requisição:", textStatus, errorThrown);
    }
  });
  


function grafico2() {

    var id_projeto = KTCookie.get('projeto_atual');

    var chart = {
        self: null,
        rendered: false
    };

    var element = document.getElementById("kt_widget_tarefas");

    if (!element) {
        return;
    }

    
     $.getJSON('../../crud/projetos/tarefas/consulta-widget-tarefas.php?modelo_grafico=1&periodo=30&id_projeto=' + id_projeto, function (objects) {


//construir primeiro todas as datas de leitura (categories) existentes
const categories = [];
for (let obj of objects){ 
    if (!categories.includes(obj.categories)){
        categories.push(obj.categories);
    }
}

    //agrupar os valores para as series com reduce
    const series = objects.reduce((acc, val) => {
    let index = acc.map((o) => o.name).indexOf(val.name); //posicao do Indicador
    let categoryIndex = categories.indexOf(val.categories); //posicao da data leitura

    if (index === -1){ //se não existe 
        let newSeries = {
            name: val.name,
            data: new Array(categories.length).fill().map(() => Array(0))
            
        }; //novo objeto já com um array de data todo zerados
        
        //coloca o valor na posição correspondente à categoria/data-leitura
        newSeries.data[categoryIndex] = val.data; 
 
        acc.push(newSeries); //e adiciona o novo objeto à serie
    }
    else { 
        acc[index].data[categoryIndex] = val.data; //troca só o valor da data
        
    }

    return acc;
}, []); //inicia o reduce com array vazio

console.log(series, categories);

var options = {
    series: series,
    chart: {
    height: 350,
    type: 'area'
    },
    noData: {
        text: 'Carregando...'
    },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth'
  },
  xaxis: {
   
    categories: categories
  },
    tooltip: {
        enabled: true,
        formatter: undefined,
        offsetY: 0,
        style: {
            fontSize: '12px'
        }
    }
  };

        
            chart.self = new ApexCharts(element, options);


            chart.self.render();
            chart.rendered = true;
        }); 
            


}