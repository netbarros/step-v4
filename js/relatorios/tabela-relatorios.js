"use strict";



"use strict";
var KTDatatablesExample = function() {
    var t, e;
    return {
        init: function() {
            (t = document.querySelector("#tabela_relatorio")) && (t.querySelectorAll("tbody tr").forEach((t=>{
                const e = t.querySelectorAll("td")
                  , a = moment(e[3].innerHTML, "DD MMM YYYY, LT").format();
                e[3].setAttribute("data-order", a)
            }
            )),
            e = $(t).DataTable({
                info: !1,
                order: [],
                pageLength: 10
            }),
            (()=>{
                const e = "Customer Orders Report";
                new $.fn.dataTable.Buttons(t,{
                    buttons: [{
                        extend: "copyHtml5",
                        title: e
                    }, {
                        extend: "excelHtml5",
                        title: e
                    }, {
                        extend: "csvHtml5",
                        title: e
                    }, {
                        extend: "pdfHtml5",
                        title: e
                    }]
                }).container().appendTo($("#kt_datatable_example_buttons")),
                document.querySelectorAll("#kt_datatable_example_export_menu [data-kt-export]").forEach((t=>{
                    t.addEventListener("click", (t=>{
                        t.preventDefault();
                        const e = t.target.getAttribute("data-kt-export");
                        document.querySelector(".dt-buttons .buttons-" + e).click()
                    }
                    ))
                }
                ))
            }
            )(),
            document.querySelector('[data-kt-filter="search"]').addEventListener("keyup", (function(t) {
                e.search(t.target.value).draw()
            }
            )))
        }
    }
}();
KTUtil.onDOMContentLoaded((function() {
    KTDatatablesExample.init()
}
));

