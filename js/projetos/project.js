

    var t = KTUtil.getCssVariableValue("--kt-primary");
    var   e = KTUtil.getCssVariableValue("--kt-primary-light");
    var    a = KTUtil.getCssVariableValue("--kt-success");
    var  r = KTUtil.getCssVariableValue("--kt-success-light");
    var   o = KTUtil.getCssVariableValue("--kt-gray-200");
    var  n = KTUtil.getCssVariableValue("--kt-gray-500");

      function grafico1() {

        
                var t = document.getElementById("project_overview_chart");
                if (t) {
                    var e = t.getContext("2d");
                    new Chart(e,{
                        type: "doughnut",
                        data: {
                            datasets: [{
                                data: [30, 45, 25],
                                backgroundColor: ["#00A3FF", "#50CD89", "#E4E6EF"]
                            }],
                            labels: ["Active", "Completed", "Yet to start"]
                        },
                        options: {
                            chart: {
                                fontFamily: "inherit"
                            },
                            cutoutPercentage: 75,
                            responsive: !0,
                            maintainAspectRatio: !1,
                            cutout: "75%",
                            title: {
                                display: !1
                            },
                            animation: {
                                animateScale: !0,
                                animateRotate: !0
                            },
                            tooltips: {
                                enabled: !0,
                                intersect: !1,
                                mode: "nearest",
                                bodySpacing: 5,
                                yPadding: 10,
                                xPadding: 10,
                                caretPadding: 0,
                                displayColors: !1,
                                backgroundColor: "#20D489",
                                titleFontColor: "#ffffff",
                                cornerRadius: 4,
                                footerSpacing: 0,
                                titleSpacing: 0
                            },
                            plugins: {
                                legend: {
                                    display: !1
                                }
                            }
                        }
                    })
                }
            };


                 function unique(array) {
                    return $.grep(array, function(el, index) {
                        return index == $.inArray(el, array);
                    });
                }

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


   // Seleciona o elemento da mensagem de aguarde
const mensagemAguarde = $('#mensagem-aguarde-kt_widget_tarefas');

    
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

             // Esconde a mensagem de aguarde
  mensagemAguarde.addClass('d-none');
        }); 
            
      


        
setTimeout(() => {


    if ($('#kt_widget_tarefas').is(':empty')) {

        $("#mensagem-aguarde-kt_widget_tarefas").addClass("d-none");

        //seu codigo
        console.log('Arquivos sem dados');
        $('#div_lista_arquivos').html('\<div class=\'alert alert-warning d-flex align-items-center p-15 mb-20\'><!--begin::Svg Icon | path: icons/duotune/general/gen048.svg--><span class=\'svg-icon svg-icon-2hx svg-icon-warning me-4\'><svg width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><path opacity=\'0.3\' d=\'M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z\' fill=\'currentColor\'></path><path d=\'M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z\' fill=\'currentColor\'></path></svg></span><!--end::Svg Icon--><div class=\'d-flex flex-column\'><h4 class=\'mb-1 text-warning\'>Tarefas não localizadas.</h4><span>Nenhuma Tarefa gerada para este Projeto, até o momento.</span></div></div>');

    }
    
}, 3500);

}


 function grafico3() {
                var t = document.querySelector("#kt_profile_overview_table");
                if (!t)
                    return;
                t.querySelectorAll("tbody tr").forEach((t=>{
                    const e = t.querySelectorAll("td")
                      , a = moment(e[1].innerHTML, "MMM D, YYYY").format();
                    e[1].setAttribute("data-order", a)
                }
                ));
                const e = $(t).DataTable({
                    info: !1,
                    order: []
                })
                  , a = document.getElementById("kt_filter_orders")
                  , r = document.getElementById("kt_filter_year");
                var o, n;
                a.addEventListener("change", (function(t) {
                    e.column(3).search(t.target.value).draw()
                }
                )),
                r.addEventListener("change", (function(t) {
                    switch (t.target.value) {
                    case "thisyear":
                        o = moment().startOf("year").format(),
                        n = moment().endOf("year").format(),
                        e.draw();
                        break;
                    case "thismonth":
                        o = moment().startOf("month").format(),
                        n = moment().endOf("month").format(),
                        e.draw();
                        break;
                    case "lastmonth":
                        o = moment().subtract(1, "months").startOf("month").format(),
                        n = moment().subtract(1, "months").endOf("month").format(),
                        e.draw();
                        break;
                    case "last90days":
                        o = moment().subtract(30, "days").format(),
                        n = moment().format(),
                        e.draw();
                        break;
                    default:
                        o = moment().subtract(100, "years").startOf("month").format(),
                        n = moment().add(1, "months").endOf("month").format(),
                        e.draw()
                    }
                }
                )),
                $.fn.dataTable.ext.search.push((function(t, e, a) {
                    var r = o
                      , s = n
                      , i = parseFloat(moment(e[1]).format()) || 0;
                    return !!(isNaN(r) && isNaN(s) || isNaN(r) && i <= s || r <= i && isNaN(s) || r <= i && i <= s)
                }
                )),
                document.getElementById("kt_filter_search").addEventListener("keyup", (function(t) {
                    e.search(t.target.value).draw()
                }
                ))
            }


  // grafico1();
   grafico2();
 //  grafico3();  
 
 //===[ Arquivos de Projetos ]====
 (function() {
    // Verifica se o cookie 'projeto_atual' existe
    var projeto = KTCookie.get('projeto_atual');




    if (projeto) {
        // Inicializa o Dropzone se o cookie existir
        var myDropzone = new Dropzone("#kt_dropzonejs_arquivos_projeto", {
            url: '../../crud/projetos/arquivos/upload.php?projeto=' + projeto, // Set the url for your upload script location
            paramName: "file", // The name that will be used to transfer the file
            maxFiles: 10,
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            init: function () {
                this.on("success", function (file, responseText) {
                    // Handle the responseText here. For example, add the text to the preview element:
                    file.previewTemplate.appendChild(document.createTextNode(responseText.retorno));
                    console.log("arquivo enviado");

                    $("#div_lista_arquivos").load('../../views/projetos/arquivos/lista-arquivos.php?id='+projeto);
                });
                this.on("error", function (file, responseText) {
                    // Handle the responseText here. For example, add the text to the preview element:
                    file.previewTemplate.appendChild(document.createTextNode(responseText.retorno));
                    console.log("arquivo não enviado");
                });
            }
        });
    } else {
        console.log("Id do projeto que tem arquivos não foi lido.");
    }
})();




setTimeout(() => {


    if ($('#div_lista_arquivos').is(':empty')) {


        //seu codigo
        console.log('Arquivos sem dados');
        $('#div_lista_arquivos').html('\<div class=\'alert alert-warning d-flex align-items-center p-15 mb-20\'><!--begin::Svg Icon | path: icons/duotune/general/gen048.svg--><span class=\'svg-icon svg-icon-2hx svg-icon-warning me-4\'><svg width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><path opacity=\'0.3\' d=\'M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z\' fill=\'currentColor\'></path><path d=\'M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z\' fill=\'currentColor\'></path></svg></span><!--end::Svg Icon--><div class=\'d-flex flex-column\'><h4 class=\'mb-1 text-warning\'>Tarefas não localizadas.</h4><span>Nenhuma Tarefa gerada para este Projeto, até o momento.</span></div></div>');

    }
    
}, 3500);

     
//===[ Arquivos de Projetos ]====
