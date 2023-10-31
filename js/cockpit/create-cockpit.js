"use strict";
var KTCreateCockpit = function () {
    var e, t, o, r, a, i, n = [];
    return {
        init: function () {
            (e = document.querySelector("#kt_modal_create_cockpit")) && (new bootstrap.Modal(e),
                t = document.querySelector("#kt_modal_create_cockpit_stepper"),
                o = document.querySelector("#kt_modal_create_cockpit_form"),
                r = t.querySelector('[data-kt-stepper-action="submit"]'),
                a = t.querySelector('[data-kt-stepper-action="next"]'),
                (i = new KTStepper(t)).on("kt.stepper.changed", (function (e) {
                    4 === i.getCurrentStepIndex() ? (r.classList.remove("d-none"),
                        r.classList.add("d-inline-block"),
                        a.classList.add("d-none")) : 5 === i.getCurrentStepIndex() ? (r.classList.add("d-none"),
                            a.classList.add("d-none")) : (r.classList.remove("d-inline-block"),
                                r.classList.remove("d-none"),
                                a.classList.remove("d-none"))
                }
                )),
                i.on("kt.stepper.next", (function (e) {
                    console.log("stepper.next");
                    var t = n[e.getCurrentStepIndex() - 1];
// inicio - atualiza a conferencia dos dados:
                    let nome_projeto_cockpit = $('#projeto_cockpit option:selected').text();
                    let nome_cockpit = $('#nome_cockpit').val();

                    let nome_modelo_cockpit_escolhido = $('input[name="modelo_grafico"]:checked').data('nome');

                    let id_modelo_cockpit_escolhido = $('input[name="modelo_grafico"]:checked').val();
                   // var indicadores_cockpit = [];
                    let indicadores_cockpit = [];
                    $('#lista_indicadores_cockpit input:checked').each(function () {
                        indicadores_cockpit.push($(this).data('nome'));
                    });

                    if (id_modelo_cockpit_escolhido == 5) {

                        $('#periodo_analise_cockpit').val('0');
                        $('#periodo_analise_cockpit').trigger('change'); 
                        
                        $('#periodo_analise_cockpit').prop('disabled', true);
                        

                    } else {
                        

                        $('#periodo_analise_cockpit').prop('disabled', false);
                        $('#periodo_analise_cockpit').trigger('change'); 
                    }

                   

                    

                    $("#confere_dados_cockpit").html('<div class="row mb-7"><label class="col-lg-4 fw-semibold text-muted">Projeto Escolhido</label> <div class="col-lg-8">  <span class= "fw-bold fs-6 text-gray-800" >' + nome_projeto_cockpit + '</span > </div > </div> <div class="row mb-7"><label class="col-lg-4 fw-semibold text-muted">Nome do Cockpit</label> <div class="col-lg-8">  <span class= "fw-bold fs-6 text-gray-800" >' + nome_cockpit + '</span > </div ></div > <div class="row mb-7"><label class="col-lg-4 fw-semibold text-muted">Modelo Gráfico</label><div class="col-lg-8">  <span class= "fw-bold fs-6 text-gray-800" >' + nome_modelo_cockpit_escolhido + '</span > </div ></div >  <div class="row mb-7"> <label class="col-lg-4 fw-semibold text-muted">Indicadores Selecionados:</label><div class="col-lg-8">  <span class= "fw-bold fs-6 text-gray-800" >' + indicadores_cockpit +'</span > </div ></div >');

// fim -  atualiza a conferencia dos dados.
                    
                    t ? t.validate().then((function (t) {
                        console.log("validated!"),
                            "Valid" == t ? e.goNext() : Swal.fire({
                                text: "Sinto muito, houveram erros detectados no formulário, verifique para prosseguir.",
                                icon: "error",
                                buttonsStyling: !1,
                                confirmButtonText: "Ok, farei isso!",
                                customClass: {
                                    confirmButton: "btn btn-light"
                                }
                            }).then((function () {

                               
                    //
                             }
                            ))
                    }
                    )) : (e.goNext(),
                        KTUtil.scrollTop())
                }
                )), i.on("kt.stepper.previous", (function (e) {
                    console.log("stepper.previous"),
                        e.goPrevious(),
                        KTUtil.scrollTop();
                   
                }
                )),
                
                r.addEventListener("click", (function (e) {
                    let timerInterval2
                        console.log("validated!"),
                            "Valid" == t ? (e.preventDefault(),
                                r.disabled = !0,
                                r.setAttribute("data-kt-indicator", "on"),
                                setTimeout((function () {
                                    r.removeAttribute("data-kt-indicator"),
                                        r.disabled = !1,
                                        i.goNext()
                                }
                                ), 2e3)) : Swal.fire({
                                    title: 'Gerando seu Cockpit',
                                    html: 'Aguarde, por favor...',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        Swal.showLoading()
                                        const b = Swal.getHtmlContainer().querySelector('b')
                                        timerInterval2 = setInterval(() => {
                                        b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                                        }, 100)
                                    },
                                    willClose: () => {
                                        clearInterval(timerInterval2)
                                    }
                                    }).then((result) => {
                                       

                                        $("#kt_modal_create_cockpit").modal("hide");
                                        
                                    /* Read more about handling dismissals below */
                                    if (result.dismiss === Swal.DismissReason.timer) {
                                        console.log('I was closed by the timer')
                                    }
                                    }).then((function () {

                                    i.goFirst();

                                    var dados = $("#kt_modal_create_cockpit_form").serialize();

                                    $.ajax({
                                        type: "POST",
                                        url: "/crud/dashboard/cockpit/action-item-regra-cockpit.php?acao=novo_item",
                                        data: dados,
                                        dataType: "json",
                                        cache: false,
                                

                                        success: function (data) {

                                            console.log('Novo Item CockPit Criado com Sucesso.');

                                            console.log(data);

                                            console.log(data.codigo)


                                            if (data.codigo == 1) {

                                               
                                                //swal.fire("Parabéns!", data.retorno, "success");
                                                createMetronicToast('Cockpit: '+ data.retorno, 5000, 'success', 'bi bi-check2-square');
							
                                                KTUtil.scrollTop();

                                                let timerInterval
                                                Swal.fire({
                                                title: 'Parabéns',
                                                html: data.retorno,
                                                icon: 'success',
                                                timer: 2000,
                                                timerProgressBar: true,
                                                didOpen: () => {
                                                    Swal.showLoading()
                                                    const b = Swal.getHtmlContainer().querySelector('b')
                                                    timerInterval = setInterval(() => {
                                                    b.textContent = Swal.getTimerLeft()
                                                    }, 100)
                                                },
                                                willClose: () => {
                                                    clearInterval(timerInterval)
                                                }
                                                }).then((result) => {
                                                /* Read more about handling dismissals below */
                                                if (result.dismiss === Swal.DismissReason.timer) {
                                                    console.log('I was closed by the timer')
                                                }
                                                })

                                                $("#projeto_cockpit").select2({
                                                    placeholder: "Selecione o Projeto",
                                                    allowClear: true
                                                });
                                                $("#projeto_cockpit").val('').trigger('change')
                                                $("#nome_cockpit").val("");
                                                // e.goPrevious(); // go previous step


                                                $('#kt_modal_create_cockpit_form').trigger("reset");

                                              

                                                


                                                $.ajax({
                                                    url: "/crud/dashboard/cockpit/consulta-itens-cockpit-usuario.php",
                                                    dataType: 'html',
                                                    success: function (data) {

                                                        createMetronicToast('Cockpit: '+ data.retorno, 5000, 'success', 'bi bi-check2-square');

                                                        $("#kt_modal_create_cockpit").modal("hide");
                                                        $("#div_cockpit").removeClass('d-none');
                                                        $("#div_cockpit").html(data);

                                                    },
                                                    error: function () {

                                                        swal.fire("Erro!", "Falha na Construção do Cockpit. " + data.retorno, "error");
                                                        
                                                    }
                                                });

                                               


                                            }
                                            if (data.codigo == 0) {

                                                swal.fire("Ops!", data.retorno, "warning");

                                            }

                                            e.stopImmediatePropagation();


                                        },
                                        error: function (data) {

                                            swal.fire("Erro!", "Não foi Possível Prosseguir!" + data.retorno, "error");

                                            console.log('Falha no Processamento dos Dados.');


                                            e.stopImmediatePropagation();


                                        }

                                    });

                                    KTUtil.scrollTop()
                                }
                                ))
                    
                    
                }
                )),
                $(o.querySelector('[name="projeto_cockpit"]')).on("change", (function () {


                    
                  // ao selecionar o projeto, atualiza os indicadores
                    
                    var projeto_cockpit = $('#projeto_cockpit').val();

                   
                    const listaIndicadoresDiv = document.getElementById("lista_indicadores_cockpit");
                 
                    // Carrega os indicadores e atualiza a lista
    $(listaIndicadoresDiv).load("/crud/dashboard/cockpit/consulta-indicador.php?id_estacao=" + projeto_cockpit, function() {
        // Oculta a div de loading após o conteúdo ser carregado
        $("#loading-indicadores-cockpit").addClass("d-none");
         // Adiciona novamente o evento de clique aos novos checkboxes carregados
         const checkboxes = document.querySelectorAll('input[name="indicadores_cockpit[]"]');
         for (const checkbox of checkboxes) {
             checkbox.addEventListener("click", limitarSelecao);
         }
     });
                    
                   
                }
                )),
                n.push(FormValidation.formValidation(o, {
                    fields: {
                        nome_cockpit: {
                            validators: {
                                notEmpty: {
                                    message: "O nome do Cockpit é necessário"
                                }
                            }
                        },
                        modelo_grafico: {
                            validators: {
                                notEmpty: {
                                    message: "Selecione o Tipo de Gráfico, para prosseguir"
                                }
                            }
                        }
                        
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger,
                        bootstrap: new FormValidation.plugins.Bootstrap5({
                            rowSelector: ".fv-row",
                            eleInvalidClass: "",
                            eleValidClass: ""
                        })
                    }
                })),
                n.push(FormValidation.formValidation(o, {
                    fields: {
                        projeto_cockpit: {
                            validators: {
                                notEmpty: {
                                    message: "Selecione o Projeto"
                                }
                            }
                        }
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger,
                        bootstrap: new FormValidation.plugins.Bootstrap5({
                            rowSelector: ".fv-row",
                            eleInvalidClass: "",
                            eleValidClass: ""
                        })
                    }
                }))

                
               
            )



            
        }
    }
}();
KTUtil.onDOMContentLoaded((function () {
    KTCreateCockpit.init()
}
));


$('#kt_modal_create_cockpit').on('shown.bs.modal', function() {
    controlarCheckboxes();
    limitarSelecao();
    atualizarIndicadores();
    inicializarEventos();
});

function controlarCheckboxes() {

    const projeto_cockpit = $('#projeto_cockpit').val();

    if (projeto_cockpit) {
        atualizarIndicadores(projeto_cockpit);
    }
    
     

      const radios = document.getElementsByName('modelo_grafico');
      const checkboxes = document.querySelectorAll('input[name="indicadores_cockpit[]"]');
      let selectedValue;

      for (const radio of radios) {
          if (radio.checked) {
              selectedValue = radio.value;
              break;
          }
      }

      for (const checkbox of checkboxes) {
          checkbox.disabled = (selectedValue != "1" && selectedValue != "4" && selectedValue != "6");
      }
  }

  function limitarSelecao(event) {
      const radios = document.getElementsByName('modelo_grafico');
      const checkboxes = document.querySelectorAll('input[name="indicadores_cockpit[]"]');
      let selectedValue;

      for (const radio of radios) {
          if (radio.checked) {
              selectedValue = radio.value;
              break;
          }
      }

      if (selectedValue != "1" && selectedValue != "2" && selectedValue != "3") {
          let checkedCount = 0;

          for (const checkbox of checkboxes) {
              if (checkbox.checked) {
                  checkedCount++;
              }
          }

          if (checkedCount > 1) {
              event.preventDefault(event);
              for (const checkbox of checkboxes) {
                  if (checkbox == event.target) {
                      checkbox.checked = false;
                      break;
                  }
              }
              Swal.fire("Atenção!", "Você só pode selecionar apenas um Indicador, para este Modelo de Gráfico!", "warning");
          }
      }
  }

  function atualizarIndicadores(projetoId) {
  const loadingDiv = document.getElementById("loading-indicadores-cockpit");
  const listaIndicadoresDiv = document.getElementById("lista_indicadores_cockpit");

  // Exibe a div de loading
  $("#loading-indicadores-cockpit").removeClass("d-none");

  createMetronicToast('Preparando a Listagem dos Cockpits ', 5000, 'success', 'bi bi-check2-square');
							
  KTUtil.scrollTop();

  // Carrega os indicadores e atualiza a lista
  $(listaIndicadoresDiv).load("/crud/dashboard/cockpit/consulta-indicador.php?id_estacao=" + projetoId, function() {
     // Oculta a div de loading após o conteúdo ser carregado
     $("#loading-indicadores-cockpit").addClass("d-none");
      // Adiciona novamente o evento de clique aos novos checkboxes carregados
      const checkboxes = document.querySelectorAll('input[name="indicadores_cockpit[]"]');
      for (const checkbox of checkboxes) {
          checkbox.addEventListener("click", limitarSelecao);
      }
  });
}


function inicializarEventos() {
    const projetoSelect = document.querySelector('select[name="projeto_cockpit"]');
    projetoSelect.addEventListener('change', function() {
        const projetoId = this.value;
        atualizarIndicadores(projetoId);
    });

    const checkboxes = document.querySelectorAll('input[name="indicadores_cockpit[]"]');
    for (const checkbox of checkboxes) {
        checkbox.addEventListener("click", limitarSelecao);
    }
}

