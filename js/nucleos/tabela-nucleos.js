"use strict";

// Class definition
var KTProjectTargets = function () {

    var initDatatable = function () {
        const table = document.getElementById('tabela_nucleos');

        // set date data order
        const tableRows = table.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            const realDate = moment(dateRow[1].innerHTML, "MMM D, YYYY").format();
            dateRow[1].setAttribute('data-order', realDate);
        });

        // init datatable --- more info on datatables: https://datatables.net/manual/
        const datatable = $(table).DataTable({
            "language": {
                "lengthMenu": "Exibir _MENU_",
               },
            "info": false,
            'order': [],
            "paging": true,
            "dom":
  "<'row'" +
  "<'col-sm-6 d-flex align-items-center justify-conten-start'l>" +
  "<'col-sm-6 d-flex align-items-center justify-content-end'f>" +
  ">" +

  "<'table-responsive'tr>" +

  "<'row'" +
  "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
  "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
  ">"
        });

    }

    // Public methods
    return {
        init: function () {
            initDatatable();
        }
    }
}();


// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTProjectTargets.init();
});
