"use strict";
var KTCustomersList = function () {
    var t, e, o, n, c = () => {
        n.querySelectorAll('[data-kt-customer-table-filter="delete_row"]').forEach((e => {
            e.addEventListener("click", (function (e) {
                e.preventDefault();
                const o = e.target.closest("tr")
                    , n = o.querySelectorAll("td")[1].innerText;
                    const checkboxValue = e.target.closest("tr").querySelectorAll("td")[0].querySelector("#id_projeto").value;
                Swal.fire({
                    html: '<div class="card border border-danger border-active active d-flex ">Atenção! Esta é uma Operação Avançada no Sistema.<p>Caso prossiga, o STEP irá verificar se há algum registro de leitura deste Projeto, caso não haja, Todos os Cadastros (Núcleos, PLCodes, Indicadores e Tarefas), existentes para este Projeto, serão <span class="text-warning">Excluídos</span>, definitivamente do sistema. <br> Caso haja registro de Leituras, o STEP, simplesmente irá inativar somente o Projeto selecionado.</div>'+
                    'Você têm certeza que deseja Inativar:  ' + n + '?',
                    icon: "warning",
                    showCancelButton: !0,
                    buttonsStyling: !1,
                    confirmButtonText: "Sim, Inativar!",
                    cancelButtonText: "Não, Cancelar",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then((function (e) {
                    e.value   ?  $.ajax({
                        type: 'POST',
                        url: '../../crud/projetos/action-projetos.php',
                        dataType: 'json',
                        data: {
                            id: checkboxValue,
                            acao:'desativa_projeto'
                        },
                        error: function(retorno){
                            Swal.fire({
                                html: retorno.mensagem,
                                icon: "success",
                                buttonsStyling: !1,
                                confirmButtonText: "Ok!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary"
                                }
                            });
                        },
                        success: function(retorno){
                            Swal.fire({
                                html: 'Você inativou o Projeto. '+ n + retorno.mensagem,
                                icon: "success",
                                buttonsStyling: !1,
                                confirmButtonText: "Ok!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary"
                                }
                            });
                        }
                    }).then((function () {
                        t.row($(o)).remove().draw()
                    }
                    )) : "cancel" === e.dismiss && Swal.fire({
                        html: '<b>'+n + '</b>, não foi Inativado.',
                        icon: "error",
                        buttonsStyling: !1,
                        confirmButtonText: "Ok!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary"
                        }
                    })
                }
                ))
            }
            ))
        }
        ))
    }
        , r = () => {
            const e = n.querySelectorAll('[type="checkbox"]')
                , o = document.querySelector('[data-kt-customer-table-select="delete_selected"]');
            e.forEach((t => {
                t.addEventListener("click", (function () {
                    setTimeout((function () {
                        l()
                    }
                    ), 50)
                }
                ))
            }
            )),
                o.addEventListener("click", (function () {
                    Swal.fire({
                        html: "Você têm certeza que irá inativar os Projetos Selecionados?",
                        icon: "warning",
                        showCancelButton: !0,
                        buttonsStyling: !1,
                        confirmButtonText: "Sim, Inativar!",
                        cancelButtonText: "Não, Cancelar",
                        customClass: {
                            confirmButton: "btn fw-bold btn-danger",
                            cancelButton: "btn fw-bold btn-active-light-primary"
                        }
                    }).then((function (o) {

                       o.value ? Swal.fire({
                        html: "Projetos Selecionados, Inativados com Sucesso.",
                            icon: "success",
                            buttonsStyling: !1,
                            confirmButtonText: "Ok!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary"
                            }


                            
                        }).then((function () {
                            e.forEach((e => {
                                e.checked && t.row($(e.closest("tbody tr"))).remove().draw()
                            }
                            ));
                            n.querySelectorAll('[type="checkbox"]')[0].checked = !1
                        }
                        )) : "cancel" === o.dismiss && Swal.fire({
                            html: "Os Projetos Selecionados, não foram Inativados.",
                            icon: "error",
                            buttonsStyling: !1,
                            confirmButtonText: "Ok, confirme!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary"
                            }
                        })
                    }
                    ))
                }
                ))
        }
        ;
    const l = () => {
        const t = document.querySelector('[data-kt-customer-table-toolbar="base"]')
            , e = document.querySelector('[data-kt-customer-table-toolbar="selected"]')
            , o = document.querySelector('[data-kt-customer-table-select="selected_count"]')
            , c = n.querySelectorAll('tbody [type="checkbox"]');
        let r = !1
            , l = 0;
        c.forEach((t => {
            t.checked && (r = !0,
                l++)
        }
        )),
            r ? (o.innerHTML = l,
                t.classList.add("d-none"),
                e.classList.remove("d-none")) : (t.classList.remove("d-none"),
                    e.classList.add("d-none"))
    }
        ;
    return {
        init: function () {
            (n = document.querySelector("#kt_tabela_projetos")) && (n.querySelectorAll("tbody tr").forEach((t => {
                const e = t.querySelectorAll("td")
                    , o = moment(e[5].innerHTML, "DD MMM YYYY, LT").format();
                e[5].setAttribute("data-order", o)
            }
            )),
                (t = $(n).DataTable({
                    info: !1,
                    order: [],
                    columnDefs: [{
                        orderable: !1,
                        targets: 0
                    }, {
                        orderable: !1,
                        targets: 7
                    }]
                })).on("draw", (function () {
                    r(),
                        c(),
                        l()
                }
                )),
                r(),
                document.querySelector('[data-kt-customer-table-filter="search"]').addEventListener("keyup", (function (e) {
                    t.search(e.target.value).draw()
                }
                )),
                e = $('[data-kt-customer-table-filter="status"]'),
                o = document.querySelectorAll('[data-kt-customer-table-filter="payment_type"] [name="payment_type"]'),
                document.querySelector('[data-kt-customer-table-filter="filter"]').addEventListener("click", (function () {
                    const n = e.val();
                    let c = "";
                    o.forEach((t => {
                        t.checked && (c = t.value),
                            "all" === c && (c = "")
                    }
                    ));
                    const r = n + " " + c;
                    t.search(r).draw()
                }
                )),
                c(),
                document.querySelector('[data-kt-customer-table-filter="reset"]').addEventListener("click", (function () {
                    e.val(null).trigger("change"),
                        o[0].checked = !0,
                        t.search("").draw()
                }
                )))
        }
    }
}();
KTUtil.onDOMContentLoaded((function () {
    KTCustomersList.init()
}
));
