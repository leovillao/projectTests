$(document).ready(function () {
    /* Este codigo muestra la informcion en la tabla al abrirse la pagina de tesoreria*/
    let table = $("#table-pago-prov").DataTable({
        "ajax": {
            "method": "POST",
            "url": "index.php?action=loadproPago",
            "data": {"tipo": 99}
        },
        "columns": [
            {"data": "ruc"},
            {"data": "razon"},
            {"data": "documentos"},
            {"data": "total"},
            {"data": "retenido"},
            {"data": "abono"},
            {"data": "saldo"},
            {"defaultContent": "<button class='btn btn-xs btn-success btn-pago-docs' ><i class=\"fa fa-money\" aria-hidden=\"true\"></i></button>"},
        ],
    });


}) /* Fin de Document Ready*/
